<?php
session_start();

// Check if user is logged in and is a student
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "fsldb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$class_id = intval($_GET['class_id'] ?? 0);
$student_id = $_SESSION['acc_no'];

if ($class_id == 0) {
    $enrolled_query = "SELECT se.*, c.class_name, c.description, c.access_code FROM student_enrollments se JOIN classes c ON se.class_id = c.class_id WHERE se.student_id = ? ORDER BY se.enrolled_at DESC";
    $stmt = $conn->prepare($enrolled_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $enrolled_result = $stmt->get_result();
    $stmt->close();

    // If no classes enrolled, redirect to join_class
    if ($enrolled_result->num_rows == 0) {
        header("Location: join_class.php");
        exit();
    }

    // Display list of enrolled classes
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Classes - FSL</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%);
                min-height: 100vh;

            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
                margin-top: 20px;
            }

            .header {
                margin-bottom: 30px;
            }

            .header h1 {
                color: #ffffff;
                font-size: 28px;
                margin-bottom: 8px;
            }

            .header p {
                color: #a0aec0;
            }

            .classes-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 20px;
            }

            .class-card {
                background: #232023;
                border: 1px solid rgba(139, 92, 246, 0.1);
                border-radius: 12px;
                padding: 24px;
                transition: all 0.3s ease;
                cursor: pointer;
                text-decoration: none;
                color: inherit;
                display: flex;
                flex-direction: column;
            }

            .class-card:hover {
                border-color: rgba(139, 92, 246, 0.3);
                background: rgba(139, 92, 246, 0.05);
                transform: translateY(-4px);
            }

            .class-card h3 {
                color: #8b5cf6;
                margin-bottom: 12px;
                font-size: 18px;
            }

            .class-card p {
                color: #a0aec0;
                font-size: 14px;
                margin-bottom: 16px;
                flex-grow: 1;
            }

            .class-card-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-top: 16px;
                border-top: 1px solid rgba(139, 92, 246, 0.1);
            }

            .class-code {
                color: #8b5cf6;
                font-size: 12px;
                font-weight: 600;
            }

            .completion-badge {
                background: rgba(34, 197, 94, 0.1);
                border: 1px solid rgba(34, 197, 94, 0.3);
                color: #86efac;
                padding: 6px 12px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 600;
            }

            .completion-badge.pending {
                background: rgba(251, 146, 60, 0.1);
                border-color: rgba(251, 146, 60, 0.3);
                color: #fed7aa;
            }

            .empty-state {
                text-align: center;
                padding: 60px 20px;
                color: #a0aec0;
            }

            .empty-state i {
                font-size: 48px;
                color: #8b5cf6;
                margin-bottom: 16px;
                display: block;
            }

            .empty-state p {
                margin-bottom: 24px;
            }

            .join-btn {
                background: linear-gradient(to right, #8b5cf6, #ec4899);
                color: white;
                border: none;
                padding: 10px 24px;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 600;
                text-decoration: none;
                display: inline-block;
                transition: all 0.3s ease;
            }

            .join-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
            }

            @media (max-width: 768px) {
                .classes-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>

    <body>
        <?php include "includes/header.php" ?>

        <div class="container">
            <div class="header">
                <h1>My Classes</h1>
                <p>View and manage your enrolled classes</p>
            </div>

            <div class="classes-grid">
                <?php while ($class = $enrolled_result->fetch_assoc()): ?>
                    <a href="student_class.php?class_id=<?php echo $class['class_id']; ?>" class="class-card">
                        <h3><?php echo htmlspecialchars($class['class_name']); ?></h3>
                        <p><?php echo htmlspecialchars($class['description'] ?? 'No description'); ?></p>
                        <div class="class-card-footer">
                            <span class="class-code">Code: <?php echo htmlspecialchars($class['access_code']); ?></span>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>

    </body>

    </html>
<?php
    $conn->close();
    exit();
}

// Verify student is enrolled in this class
$verify_query = "SELECT se.*, c.class_name, c.description FROM student_enrollments se JOIN classes c ON se.class_id = c.class_id WHERE se.class_id = ? AND se.student_id = ?";
$stmt = $conn->prepare($verify_query);
$stmt->bind_param("ii", $class_id, $student_id);
$stmt->execute();
$enrollment_result = $stmt->get_result();
$stmt->close();

if ($enrollment_result->num_rows == 0) {
    header("Location: join_class.php");
    exit();
}

$enrollment = $enrollment_result->fetch_assoc();

// Get class content
$content_query = "SELECT * FROM class_content WHERE class_id = ? ORDER BY uploaded_at DESC";
$stmt = $conn->prepare($content_query);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$content_result = $stmt->get_result();
$stmt->close();

$lessons_query = "SELECT * FROM class_lessons WHERE class_id = ? ORDER BY lesson_id";
$stmt = $conn->prepare($lessons_query);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$lessons_result = $stmt->get_result();
$stmt->close();

// Get class tasks with student submissions
$tasks_query = "SELECT ct.*, COALESCE(stp.completed, 0) as task_completed, sts.submission_id as submission_id, sts.file_name as submission_name FROM class_tasks ct LEFT JOIN student_task_progress stp ON ct.task_id = stp.task_id AND stp.student_id = ? LEFT JOIN student_task_submissions sts ON ct.task_id = sts.task_id AND sts.student_id = ? WHERE ct.class_id = ? ORDER BY ct.task_order";
$stmt = $conn->prepare($tasks_query);
$stmt->bind_param("iii", $student_id, $student_id, $class_id);
$stmt->execute();
$tasks_result = $stmt->get_result();
$stmt->close();

$student_pic_result = $conn->query("SELECT profile_picture FROM account_record WHERE acc_no = $student_id");
$student_pic_data = $student_pic_result->fetch_assoc();
$student_profile_picture = $student_pic_data['profile_picture'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($enrollment['class_name']); ?> - FSL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%);
            min-height: 100vh;
            
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Profile header with picture */
        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            background: #232023;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid rgba(139, 92, 246, 0.1);
        }

        .profile-picture {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info h2 {
            color: #ffffff;
            font-size: 20px;
            margin-bottom: 4px;
        }

        .profile-info p {
            color: #a0aec0;
            font-size: 13px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header h1 {
            color: #ffffff;
            font-size: 28px;
        }

        .completion-badge {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }

        .completion-badge.pending {
            background: rgba(251, 146, 60, 0.1);
            border-color: rgba(251, 146, 60, 0.3);
            color: #fed7aa;
        }

        .back-btn {
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(139, 92, 246, 0.2);
        }

        .tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
        }

        .tab-btn {
            background: none;
            border: none;
            color: #a0aec0;
            padding: 12px 20px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            color: #8b5cf6;
            border-bottom-color: #8b5cf6;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .section {
            background: #232023;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid rgba(139, 92, 246, 0.1);
            margin-bottom: 24px;
        }

        .section h2 {
            color: #8b5cf6;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .task-list,
        .lesson-list {
            list-style: none;
        }

        .task-item,
        .lesson-item {
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.1);
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 12px;
        }

        .task-info,
        .lesson-info {
            flex: 1;
        }

        .task-item h4,
        .lesson-item h4 {
            color: #ffffff;
            margin-bottom: 6px;
        }

        .task-item p,
        .lesson-item p {
            color: #a0aec0;
            font-size: 13px;
        }

        .task-action {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .submit-btn,
        .complete-btn {
            background: linear-gradient(to right, #8b5cf6, #ec4899);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .submit-btn:hover,
        .complete-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .submit-btn:disabled,
        .complete-btn:disabled {
            background: rgba(139, 92, 246, 0.3);
            cursor: not-allowed;
            transform: none;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-completed {
            background: rgba(34, 197, 94, 0.1);
            color: #86efac;
        }

        .status-pending {
            background: rgba(251, 146, 60, 0.1);
            color: #fed7aa;
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 16px;
        }

        .content-item {
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.1);
            border-radius: 6px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .content-item:hover {
            border-color: rgba(139, 92, 246, 0.3);
            transform: scale(1.05);
        }

        .content-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }

        .content-item-info {
            padding: 12px;
        }

        .content-item-info p {
            color: #a0aec0;
            font-size: 12px;
            word-break: break-word;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #a0aec0;
        }

        .empty-state i {
            font-size: 32px;
            color: #8b5cf6;
            margin-bottom: 12px;
            display: block;
        }

        /* Modal styles for image zoom */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            position: relative;
            max-width: 90%;
            max-height: 90vh;
        }

        .modal-content img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .modal-close {
            position: absolute;
            top: -30px;
            right: 0;
            color: #ffffff;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            color: #8b5cf6;
        }

        /* File upload form styles */
        .file-upload-form {
            background: rgba(139, 92, 246, 0.05);
            border: 1px dashed rgba(139, 92, 246, 0.3);
            padding: 16px;
            border-radius: 6px;
            margin-top: 12px;
        }

        .file-upload-form input[type="file"] {
            display: block;
            margin-bottom: 12px;
            color: #a0aec0;
        }

        .file-upload-form input[type="file"]::file-selector-button {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .file-upload-form input[type="file"]::file-selector-button:hover {
            background: rgba(139, 92, 246, 0.3);
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .task-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .task-action {
                width: 100%;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
            }
        }

        .lesson-file-info {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(139, 92, 246, 0.1);
            font-size: 12px;
            color: #8b5cf6;
        }

        .sort-btn {
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .sort-btn:hover {
            background: rgba(139, 92, 246, 0.2);
            border-color: rgba(139, 92, 246, 0.5);
        }

        .sort-btn.active {
            background: rgba(139, 92, 246, 0.3);
            border-color: rgba(139, 92, 246, 0.6);
            color: #a78bfa;
        }

        .sort-dropdown {
            position: relative;
            display: inline-block;
        }

        .sort-dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #232023;
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 6px;
            min-width: 150px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            z-index: 100;
            margin-top: 4px;
        }

        .sort-dropdown:hover .sort-dropdown-menu {
            display: block;
        }

        .sort-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            color: #a0aec0;
            cursor: pointer;
            transition: all 0.2s ease;
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
            font-size: 13px;
        }

        .sort-dropdown-item:last-child {
            border-bottom: none;
        }

        .sort-dropdown-item:hover {
            background: rgba(139, 92, 246, 0.15);
            color: #8b5cf6;
        }

        .sort-dropdown-item.active {
            background: rgba(139, 92, 246, 0.2);
            color: #a78bfa;
            font-weight: 600;
        }

        .view-btn {
            background: rgba(59, 130, 246, 0.1);
            color: #93c5fd;
            border: 1px solid rgba(59, 130, 246, 0.3);
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.3s ease;
            white-space: nowrap;
            text-decoration: none;
            display: inline-block;
            margin-right: 8px;
        }

        .view-btn:hover {
            background: rgba(59, 130, 246, 0.2);
            border-color: rgba(59, 130, 246, 0.5);
        }

        .download-btn {
            background: rgba(34, 197, 94, 0.1);
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, 0.3);
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.3s ease;
            white-space: nowrap;
            text-decoration: none;
            display: inline-block;
        }

        .download-btn:hover {
            background: rgba(34, 197, 94, 0.2);
            border-color: rgba(34, 197, 94, 0.5);
        }

        .content-description {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(139, 92, 246, 0.1);
            color: #a0aec0;
            font-size: 13px;
        }

        .submission-form {
            background: rgba(139, 92, 246, 0.05);
            border: 1px dashed rgba(139, 92, 246, 0.3);
            padding: 16px;
            border-radius: 6px;
            margin-top: 12px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            color: #e2e8f0;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .form-group input[type="file"],
        .form-group textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 4px;
            font-size: 12px;
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }
    </style>
</head>

<body>
    <?php include "includes/header.php" ?>

    <div class="container">
        <!-- Add profile header with picture and name -->
        <div class="profile-header">
            <div class="profile-picture">
                <?php if ($student_profile_picture): ?>
                    <img src="<?php echo htmlspecialchars($student_profile_picture); ?>" alt="Profile">
                <?php else: ?>
                    👨‍🎓
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($_SESSION['name']); ?></h2>
                <p><?php echo htmlspecialchars($enrollment['class_name']); ?></p>
            </div>
        </div>

        <div class="header">
            <div>
                <h1><?php echo htmlspecialchars($enrollment['class_name']); ?></h1>
                <p style="color: #a0aec0; margin-top: 8px;"><?php echo htmlspecialchars($enrollment['description'] ?? 'No description'); ?></p>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <a href="student_class.php" class="back-btn">Back</a>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" data-tab="class-content" onclick="switchTab('class-content')">Class Content</button>
            <button class="tab-btn" data-tab="submissions" onclick="switchTab('submissions')">My Submissions</button>
            <button class="tab-btn" data-tab="content" onclick="switchTab('content')">Resources</button>
        </div>

        <!-- Class Content Tab (Combined Tasks & Lessons) -->
        <div id="class-content" class="tab-content active">
            <div class="section">
                <h2>Class Content</h2>
                <p style="color: #a0aec0; font-size: 12px; margin-bottom: 16px;">All tasks, lessons, and files posted by your teacher</p>
                <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center;">
                    <button class="sort-btn active" data-sort="date" onclick="sortContent('date')"><i class="fas fa-arrow-down-short-wide"></i> Newest First</button>
                    <button class="sort-btn" data-sort="oldest" onclick="sortContent('oldest')"><i class="fas fa-arrow-up-short-wide"></i> Oldest First</button>
                    
                    <div class="sort-dropdown">
                        <button class="sort-btn" style="margin: 0;"><i class="fas fa-filter"></i> Filter by Type <i class="fas fa-chevron-down" style="font-size: 10px; margin-left: 4px;"></i></button>
                            <div class="sort-dropdown-menu">
                                <div class="sort-dropdown-item" data-type="all" onclick="filterByType('all')"><i class="fas fa-list"></i> All Content</div>
                                <div class="sort-dropdown-item" data-type="photo" onclick="filterByType('photo')"><i class="fas fa-image"></i> Photos</div>
                                <div class="sort-dropdown-item" data-type="video" onclick="filterByType('video')"><i class="fas fa-video"></i> Videos</div>
                                <div class="sort-dropdown-item" data-type="lesson" onclick="filterByType('lesson')"><i class="fas fa-book"></i> Lessons</div>
                                <div class="sort-dropdown-item" data-type="task" onclick="filterByType('task')"><i class="fas fa-tasks"></i> Tasks</div>
                            </div>
                    </div>
                </div>
                <?php
                // Combine all content items
                $all_content_items = [];

                // Load student's submissions to avoid showing them as separate class content
                $submission_map = [];
                $sub_q = "SELECT submission_id, task_id, file_name FROM student_task_submissions WHERE student_id = ?";
                $sub_stmt = $conn->prepare($sub_q);
                $sub_stmt->bind_param("i", $student_id);
                $sub_stmt->execute();
                $sub_res = $sub_stmt->get_result();
                while ($s = $sub_res->fetch_assoc()) {
                    $submission_map[$s['task_id']] = $s;
                }
                $sub_stmt->close();

                // Fetch all tasks (teacher-posted tasks)
                if ($tasks_result->num_rows > 0) {
                    $tasks_result->data_seek(0);
                    while ($task = $tasks_result->fetch_assoc()) {
                        $created_at = $task['created_at'] ?? date('Y-m-d H:i:s');
                        $ts = strtotime($created_at);
                        $all_content_items[] = [
                            'type' => 'task',
                            'id' => $task['task_id'],
                            'name' => $task['task_name'],
                            'description' => $task['task_description'] ?? 'No description',
                            'file_name' => $task['file_name'] ?? null,
                            'has_file' => !empty($task['file_data']) || !empty($task['file_path']),
                            'created_at' => $created_at,
                            'date' => $ts,
                            'date_ms' => $ts * 1000,
                            'task_completed' => $task['task_completed'],
                            'submitted' => isset($submission_map[$task['task_id']]),
                            'submission_id' => $submission_map[$task['task_id']]['submission_id'] ?? null,
                            'submission_name' => $submission_map[$task['task_id']]['file_name'] ?? null
                        ];
                    }
                }

                // Fetch all lessons
                if ($lessons_result->num_rows > 0) {
                    $lessons_result->data_seek(0);
                    while ($lesson = $lessons_result->fetch_assoc()) {
                        $created_at = $lesson['created_at'] ?? date('Y-m-d H:i:s');
                        $ts = strtotime($created_at);
                        $all_content_items[] = [
                            'type' => 'lesson',
                            'id' => $lesson['lesson_id'],
                            'name' => $lesson['lesson_name'],
                            'description' => $lesson['lesson_description'] ?? 'No description',
                            'file_name' => $lesson['file_name'],
                            'has_file' => !empty($lesson['file_data']) || !empty($lesson['file_path']),
                            'created_at' => $created_at,
                            'date' => $ts,
                            'date_ms' => $ts * 1000
                        ];
                    }
                }

                // Sort by date (newest first)
                usort($all_content_items, function($a, $b) {
                    return $b['date'] - $a['date'];
                });
                
                if (!empty($all_content_items)): ?>
                    <ul class="task-list">
                        <?php foreach ($all_content_items as $item): ?>
                            <?php
                                // Determine file type for filtering
                                $file_type = 'other';
                                if ($item['file_name']) {
                                    $ext = strtolower(pathinfo($item['file_name'], PATHINFO_EXTENSION));
                                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
                                        $file_type = 'photo';
                                    } elseif (in_array($ext, ['mp4', 'avi', 'mov', 'mkv', 'webm', 'ogv'])) {
                                        $file_type = 'video';
                                    }
                                }
                            ?>
                            <li class="task-item" style="cursor: default; transition: all 0.3s ease;" data-type="<?php echo $item['type']; ?>" data-file-type="<?php echo $file_type; ?>" data-date="<?php echo $item['date_ms']; ?>">
                                <div class="task-info">
                                    <h4>
                                        <span style="display: inline-block; margin-right: 8px; padding: 4px 8px; border-radius: 4px; font-size: 11px; background: rgba(139, 92, 246, 0.2); color: #8b5cf6;">
                                            <?php echo ucfirst($item['type']); ?>
                                        </span>
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </h4>
                                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                                    <?php if ($item['file_name']): ?>
                                        <p style="margin-top: 8px; font-size: 12px; color: #ec4899;">
                                            <i class="fas fa-file"></i> <?php echo htmlspecialchars($item['file_name']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <p style="margin-top: 8px; font-size: 11px; color: #a0aec0;">
                                        <i class="fas fa-calendar"></i> <?php echo date('M d, Y H:i', strtotime($item['created_at'])); ?>
                                    </p>
                                </div>
                                <div style="display: flex; gap: 8px; flex-direction: column;">
                                    <?php if ($item['type'] === 'task'): ?>
                                        <span class="status-badge <?php echo $item['task_completed'] ? 'status-completed' : 'status-pending'; ?>">
                                            <?php echo $item['task_completed'] ? '✓ Completed' : '○ Pending'; ?>
                                        </span>
                                        <?php if (!empty($item['submitted'])): ?>
                                            <span class="status-badge" style="background: rgba(59,130,246,0.08); border-color: rgba(59,130,246,0.18); color: #2563eb;">Submitted</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($item['has_file']): 
                                        $ext = strtolower(pathinfo($item['file_name'] ?? '', PATHINFO_EXTENSION));
                                        $is_viewable = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'mp4', 'avi', 'mov', 'mkv', 'webm', 'ogv', 'pdf', 'txt']);
                                    ?>
                                        <?php if ($is_viewable): ?>
                                            <a href="view_content.php?type=<?php echo $item['type']; ?>&id=<?php echo $item['id']; ?>&class_id=<?php echo $class_id; ?>" class="view-btn"><i class="fas fa-eye"></i> View</a>
                                        <?php endif; ?>
                                        <a href="serve_file.php?type=<?php echo $item['type']; ?>&id=<?php echo $item['id']; ?>&class_id=<?php echo $class_id; ?>&download=1" class="download-btn"><i class="fas fa-download"></i> Download</a>
                                    <?php endif; ?>

                                    <?php if (!empty($item['submission_id'])):
                                        $sub_ext = strtolower(pathinfo($item['submission_name'] ?? '', PATHINFO_EXTENSION));
                                        $sub_viewable = in_array($sub_ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'mp4', 'avi', 'mov', 'mkv', 'webm', 'ogv', 'pdf', 'txt']);
                                    ?>
                                        <div style="margin-top:6px; color: #2563eb; font-size: 12px; font-weight: 600;">
                                            <strong>Your Submission:</strong>
                                        </div>
                                        <?php if ($sub_viewable): ?>
                                            <a href="serve_file.php?type=submission&submission_id=<?php echo $item['submission_id']; ?>" target="_blank" class="view-btn"><i class="fas fa-eye"></i> View Submission</a>
                                        <?php endif; ?>
                                        <a href="serve_file.php?type=submission&submission_id=<?php echo $item['submission_id']; ?>&download=1" class="download-btn"><i class="fas fa-download"></i> Download Submission</a>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No content yet. Check back soon!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- My Submissions Tab -->
        <div id="submissions" class="tab-content">
            <div class="section">
                <h2>Submit Your Work</h2>
                <p style="color: #a0aec0; font-size: 12px; margin-bottom: 16px;">Submit files for pending tasks</p>
                <?php $tasks_result->data_seek(0); ?>
                <?php if ($tasks_result->num_rows > 0): ?>
                    <ul class="task-list">
                        <?php while ($task = $tasks_result->fetch_assoc()): ?>
                            <li class="task-item" style="flex-direction: column; align-items: flex-start;">
                                <div class="task-info" style="width: 100%;">
                                    <h4><?php echo htmlspecialchars($task['task_name']); ?></h4>
                                    <p><?php echo htmlspecialchars($task['task_description'] ?? 'No description'); ?></p>
                                    <span class="status-badge <?php echo $task['task_completed'] ? 'status-completed' : 'status-pending'; ?>" style="display: inline-block; margin-top: 8px;">
                                        <?php echo $task['task_completed'] ? '✓ Completed' : '○ Pending'; ?>
                                    </span>
                                </div>
                                
                                <?php if (!$task['task_completed']): ?>
                                    <form method="POST" action="submit_task.php" enctype="multipart/form-data" style="width: 100%; margin-top: 12px;">
                                        <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                        <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                                        <div class="submission-form">
                                            <div class="form-group">
                                                <label for="submission_file_<?php echo $task['task_id']; ?>">Upload Your Work *</label>
                                                <input type="file" id="submission_file_<?php echo $task['task_id']; ?>" name="submission_file" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="submission_desc_<?php echo $task['task_id']; ?>">Description or Comments (Optional)</label>
                                                <textarea id="submission_desc_<?php echo $task['task_id']; ?>" name="submission_description" placeholder="Add any notes about your work..."></textarea>
                                            </div>
                                            <button type="submit" class="submit-btn"><i class="fas fa-paper-plane"></i> Submit Work</button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <div style="margin-top: 12px; width: 100%;">
                                        <?php if (!empty($task['submission_id'])): ?>
                                            <a href="serve_file.php?type=submission&submission_id=<?php echo $task['submission_id']; ?>&download=1" download class="download-btn"><i class="fas fa-download"></i> Download Your Submission</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-tasks"></i>
                        <p>No tasks in this class yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Resources Tab -->
        <div id="content" class="tab-content">
            <div class="section">
                <h2>Lesson Resources</h2>
                <?php $lessons_result->data_seek(0); ?>
                <?php if ($lessons_result->num_rows > 0): ?>
                    <ul class="lesson-list">
                        <?php while ($lesson = $lessons_result->fetch_assoc()): ?>
                            <li class="lesson-item">
                                <div class="lesson-info">
                                    <h4><?php echo htmlspecialchars($lesson['lesson_name']); ?></h4>
                                    <p><?php echo htmlspecialchars($lesson['lesson_description'] ?? 'No description'); ?></p>
                                    <div class="lesson-file-info">
                                        <i class="fas fa-file"></i> <?php echo htmlspecialchars($lesson['file_name']); ?>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <?php
                                        $lesson_ext = strtolower(pathinfo($lesson['file_name'], PATHINFO_EXTENSION));
                                        $lesson_is_viewable = in_array($lesson_ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'mp4', 'avi', 'mov', 'mkv', 'webm', 'ogv']);
                                    ?>
                                    <?php if ($lesson_is_viewable): ?>
                                        <a href="view_content.php?type=lesson&id=<?php echo $lesson['lesson_id']; ?>&class_id=<?php echo $class_id; ?>" class="view-btn"><i class="fas fa-eye"></i> View</a>
                                    <?php endif; ?>
                                    <a href="serve_file.php?type=lesson&id=<?php echo $lesson['lesson_id']; ?>&download=1" download class="download-btn"><i class="fas fa-download"></i> Download</a>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-book"></i>
                        <p>No lesson resources available yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Image zoom modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeImageModal()">&times;</span>
            <img id="modalImage" src="/placeholder.svg" alt="">
        </div>
    </div>

    <script>
        let currentFilter = 'all';

        function switchTab(tabName) {
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));

            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => btn.classList.remove('active'));

            document.getElementById(tabName).classList.add('active');
            const btn = document.querySelector(`.tab-btn[data-tab="${tabName}"]`);
            if (btn) btn.classList.add('active');
        }

        function openImageModal(imagePath, imageName) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imagePath;
            modalImage.alt = imageName;
            modal.classList.add('active');
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.remove('active');
        }

        // Close modal when clicking outside the image
        document.getElementById('imageModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeImageModal();
            }
        });

        function sortContent(sortType) {
            // Update active button using data-sort attribute
            document.querySelectorAll('.sort-btn[data-sort]').forEach(btn => btn.classList.remove('active'));
            const activeBtn = document.querySelector(`.sort-btn[data-sort="${sortType}"]`);
            if (activeBtn) activeBtn.classList.add('active');

            const items = Array.from(document.querySelectorAll('.task-list .task-item'));
            const taskList = document.querySelector('.task-list');

            if (sortType === 'date') {
                items.sort((a, b) => {
                    const dateA = Number(a.dataset.date) || 0;
                    const dateB = Number(b.dataset.date) || 0;
                    return dateB - dateA;
                });
            } else if (sortType === 'oldest') {
                items.sort((a, b) => {
                    const dateA = Number(a.dataset.date) || 0;
                    const dateB = Number(b.dataset.date) || 0;
                    return dateA - dateB;
                });
            }

            // Re-append sorted items respecting current filter
            items.forEach(item => {
                if (currentFilter === 'all' || shouldShowItem(item)) {
                    taskList.appendChild(item);
                }
            });
        }

        function filterByType(type) {
            currentFilter = type;
            
            // Update active filter item
            document.querySelectorAll('.sort-dropdown-item').forEach(item => item.classList.remove('active'));
            const active = document.querySelector(`.sort-dropdown-item[data-type="${type}"]`);
            if (active) active.classList.add('active');

            const items = document.querySelectorAll('.task-list .task-item');
            
            items.forEach(item => {
                if (shouldShowItem(item)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function shouldShowItem(item) {
            if (currentFilter === 'all') return true;
            
            const itemType = item.dataset.type;
            const fileType = item.dataset.fileType || 'other';
            
            if (currentFilter === 'photo') return fileType === 'photo';
            if (currentFilter === 'video') return fileType === 'video';
            if (currentFilter === 'lesson') return itemType === 'lesson';
            if (currentFilter === 'task') return itemType === 'task';
            
            return true;
        }
    </script>
</body>

</html>
<?php $conn->close(); ?>