<?php
session_start();

// Check if user is logged in and is a teacher
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "fsldb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// helper to find smallest missing ID (fill gaps)
function get_smallest_missing($conn, $table, $col) {
    $next = 1;
    $res = $conn->query("SELECT $col FROM $table ORDER BY $col ASC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $val = intval($row[$col]);
            if ($val === $next) {
                $next++;
            } elseif ($val > $next) {
                break;
            }
        }
    }
    return $next;
}

$class_id = intval($_GET['class_id'] ?? 0);
$teacher_id = $_SESSION['acc_no'];

// Verify teacher owns this class
$verify_query = "SELECT * FROM classes WHERE class_id = ? AND teacher_id = ?";
$stmt = $conn->prepare($verify_query);
$stmt->bind_param("ii", $class_id, $teacher_id);
$stmt->execute();
$class_result = $stmt->get_result();
$stmt->close();

if ($class_result->num_rows == 0) {
    header("Location: teacher_dashboard.php");
    exit();
}

$class = $class_result->fetch_assoc();

$teacher_pic_result = $conn->query("SELECT profile_picture FROM account_record WHERE acc_no = $teacher_id");
$teacher_pic_data = $teacher_pic_result->fetch_assoc();
$teacher_profile_picture = $teacher_pic_data['profile_picture'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_task') {
    $task_name = trim($_POST['task_name'] ?? '');
    $task_description = trim($_POST['task_description'] ?? '');
    $task_file = null;
    $task_file_name = null;
    
    // Handle file upload if provided (store as BLOB in DB)
    if (isset($_FILES['task_file']) && $_FILES['task_file']['error'] == 0) {
        $file = $_FILES['task_file'];
        $file_name = basename($file['name']);
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_ext = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'zip', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov', 'mkv'];
        if (in_array(strtolower($file_ext), $allowed_ext)) {
            $task_file = file_get_contents($file['tmp_name']);
            $task_file_name = $file_name;
            $task_file_mime = mime_content_type($file['tmp_name']);
        }
    }
    
    if (!empty($task_name)) {
        // assign smallest available task_id to fill gaps
        $task_id = get_smallest_missing($conn, 'class_tasks', 'task_id');

        if (isset($task_file)) {
            $insert_query = "INSERT INTO class_tasks (task_id, class_id, task_name, task_description, file_data, file_name, file_mime) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("iisssss", $task_id, $class_id, $task_name, $task_description, $task_file, $task_file_name, $task_file_mime);
            $stmt->execute();
            $stmt->close();
        } else {
            $insert_query = "INSERT INTO class_tasks (task_id, class_id, task_name, task_description) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("iiss", $task_id, $class_id, $task_name, $task_description);
            $stmt->execute();
            $stmt->close();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_lesson') {
    $lesson_name = trim($_POST['lesson_name'] ?? '');
    $lesson_description = trim($_POST['lesson_description'] ?? '');
    
    if (!empty($lesson_name) && isset($_FILES['lesson_file']) && $_FILES['lesson_file']['error'] == 0) {
        $file = $_FILES['lesson_file'];
        $file_name = basename($file['name']);
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_ext = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'zip'];
        
        if (in_array(strtolower($file_ext), $allowed_ext)) {
            $lesson_file_data = file_get_contents($file['tmp_name']);
            $lesson_file_mime = mime_content_type($file['tmp_name']);
            $insert_query = "INSERT INTO class_lessons (class_id, lesson_name, lesson_description, file_data, file_name, file_mime) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("isssss", $class_id, $lesson_name, $lesson_description, $lesson_file_data, $file_name, $lesson_file_mime);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Get all tasks for this class
$tasks_query = "SELECT * FROM class_tasks WHERE class_id = ? ORDER BY task_order";
$stmt = $conn->prepare($tasks_query);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$tasks_result = $stmt->get_result();
$stmt->close();

// Get all content for this class
$content_query = "SELECT * FROM class_content WHERE class_id = ? ORDER BY uploaded_at DESC";
$stmt = $conn->prepare($content_query);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$content_result = $stmt->get_result();
$stmt->close();

$lessons_query = "SELECT * FROM class_lessons WHERE class_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($lessons_query);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$lessons_result = $stmt->get_result();
$stmt->close();

// Get enrolled students with their progress
$students_query = "SELECT se.*, ar.name, ar.email, 
                   (SELECT COUNT(*) FROM class_tasks WHERE class_id = ?) as total_tasks,
                   (SELECT COUNT(*) FROM student_task_progress WHERE student_id = se.student_id AND completed = 1 AND task_id IN (SELECT task_id FROM class_tasks WHERE class_id = ?)) as completed_tasks
                   FROM student_enrollments se 
                   JOIN account_record ar ON se.student_id = ar.acc_no 
                   WHERE se.class_id = ?
                   ORDER BY se.enrolled_at DESC";
$stmt = $conn->prepare($students_query);
$stmt->bind_param("iii", $class_id, $class_id, $class_id);
$stmt->execute();
$students_result = $stmt->get_result();
$stmt->close();

$submissions_query = "SELECT sts.*, ct.task_name, ar.name, ar.email 
                      FROM student_task_submissions sts
                      JOIN class_tasks ct ON sts.task_id = ct.task_id
                      JOIN account_record ar ON sts.student_id = ar.acc_no
                      WHERE ct.class_id = ?
                      ORDER BY sts.submitted_at DESC";
$stmt = $conn->prepare($submissions_query);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$submissions_result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Class - FSL</title>
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

        .header-info {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .info-box {
            background: rgba(139, 92, 246, 0.1);
            border: 1px solid rgba(139, 92, 246, 0.3);
            padding: 12px 16px;
            border-radius: 6px;
            text-align: center;
        }

        .info-box-label {
            color: #a0aec0;
            font-size: 12px;
            text-transform: uppercase;
            display: block;
            margin-bottom: 4px;
        }

        .info-box-value {
            color: #8b5cf6;
            font-size: 20px;
            font-weight: bold;
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
            flex-wrap: wrap;
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

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            color: #e2e8f0;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 6px;
            font-size: 13px;
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .add-btn {
            background: linear-gradient(to right, #8b5cf6, #ec4899);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .task-list, .student-list, .submission-list, .lesson-list {
            list-style: none;
        }

        .task-item, .student-item, .submission-item, .lesson-item {
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.1);
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .task-info, .student-info, .submission-info, .lesson-info {
            flex: 1;
        }

        .task-item h4, .student-item h4, .submission-item h4, .lesson-item h4 {
            color: #ffffff;
            margin-bottom: 6px;
        }

        .task-item p, .student-item p, .submission-item p, .lesson-item p {
            color: #a0aec0;
            font-size: 13px;
        }

        .delete-btn, .download-btn {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
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

        .download-btn {
            background: rgba(34, 197, 94, 0.1);
            color: #86efac;
            border-color: rgba(34, 197, 94, 0.3);
        }

        .delete-btn:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.5);
        }

        .download-btn:hover {
            background: rgba(34, 197, 94, 0.2);
            border-color: rgba(34, 197, 94, 0.5);
        }

        .student-status {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: rgba(251, 146, 60, 0.1);
            color: #fed7aa;
        }

        .status-completed {
            background: rgba(34, 197, 94, 0.1);
            color: #86efac;
        }

        .progress-bar {
            width: 150px;
            height: 8px;
            background: rgba(139, 92, 246, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(to right, #8b5cf6, #ec4899);
            transition: width 0.3s ease;
        }

        .progress-text {
            color: #a0aec0;
            font-size: 12px;
            white-space: nowrap;
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
            position: relative;
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

        .content-item-delete {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(239, 68, 68, 0.9);
            color: white;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .content-item:hover .content-item-delete {
            opacity: 1;
        }

        .content-item-delete:hover {
            background: rgba(239, 68, 68, 1);
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

        .submission-meta {
            display: flex;
            gap: 16px;
            margin-top: 8px;
            flex-wrap: wrap;
        }

        .submission-meta-item {
            font-size: 12px;
            color: #a0aec0;
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

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .tabs {
                flex-wrap: wrap;
            }

            .task-item, .student-item, .submission-item, .lesson-item {
                flex-direction: column;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <?php include "includes/header.php" ?>

    <div class="container">
        <div class="profile-header">
            <div class="profile-picture">
                <?php if ($teacher_profile_picture): ?>
                    <img src="<?php echo htmlspecialchars($teacher_profile_picture); ?>" alt="Profile">
                <?php else: ?>
                    👨‍🏫
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($_SESSION['name']); ?></h2>
                <p><?php echo htmlspecialchars($class['class_name']); ?></p>
            </div>
        </div>

        <div class="header">
            <h1><?php echo htmlspecialchars($class['class_name']); ?></h1>
            <a href="teacher_dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>

        <div class="header-info">
            <div class="info-box">
                <span class="info-box-label">Access Code</span>
                <span class="info-box-value"><?php echo htmlspecialchars($class['access_code']); ?></span>
            </div>
            <?php
            $student_count_query = "SELECT COUNT(*) as count FROM student_enrollments WHERE class_id = ?";
            $stmt = $conn->prepare($student_count_query);
            $stmt->bind_param("i", $class_id);
            $stmt->execute();
            $count_result = $stmt->get_result();
            $count_row = $count_result->fetch_assoc();
            $student_count = $count_row['count'];
            $stmt->close();

            $completed_query = "SELECT COUNT(*) as count FROM student_enrollments WHERE class_id = ? AND completed = 1";
            $stmt = $conn->prepare($completed_query);
            $stmt->bind_param("i", $class_id);
            $stmt->execute();
            $completed_result = $stmt->get_result();
            $completed_row = $completed_result->fetch_assoc();
            $completed_count = $completed_row['count'];
            $stmt->close();
            ?>
            <div class="info-box">
                <span class="info-box-label">Total Students</span>
                <span class="info-box-value"><?php echo $student_count; ?></span>
            </div>
            <div class="info-box">
                <span class="info-box-label">Completed</span>
                <span class="info-box-value"><?php echo $completed_count; ?></span>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" data-tab="tasks-content" onclick="switchTab('tasks-content')">Tasks & Content</button>
            <button class="tab-btn" data-tab="lessons" onclick="switchTab('lessons')">Lessons</button>
            <button class="tab-btn" data-tab="submissions" onclick="switchTab('submissions')">Submissions</button>
            <button class="tab-btn" data-tab="students" onclick="switchTab('students')">Students</button>
        </div>

        <!-- Tasks & Content Tab (Combined) -->
        <div id="tasks-content" class="tab-content active">
            <div class="section">
                <h2>Add New Task or Content</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_task">
                    <div class="form-group">
                        <label for="task_name">Task/Content Name</label>
                        <input type="text" id="task_name" name="task_name" placeholder="e.g., Learn Alphabet" required>
                    </div>
                    <div class="form-group">
                        <label for="task_description">Description or Instructions</label>
                        <textarea id="task_description" name="task_description" placeholder="Describe the task or provide instructions..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="task_file">Attach File (Optional - Images, Videos, PDF, or Any Files)</label>
                        <input type="file" id="task_file" name="task_file">
                        <p style="color: #a0aec0; font-size: 11px; margin-top: 6px;">Supported: Images (JPG, PNG, GIF), Videos (MP4, AVI, MOV, MKV), Documents (PDF, DOC, DOCX), Presentations (PPT, PPTX), Spreadsheets (XLS, XLSX), and more</p>
                    </div>
                    <button type="submit" class="add-btn">Add Task/Content</button>
                </form>
            </div>

            <div class="section">
                <h2>Class Content</h2>
                <p style="color: #a0aec0; font-size: 12px; margin-bottom: 16px;">All tasks, lessons, and uploaded files in this classroom</p>
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
                // Get all tasks with files
                $all_content_items = [];
                
                // Fetch all tasks
                if ($tasks_result->num_rows > 0) {
                    $tasks_result->data_seek(0); // Reset pointer
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
                            'date_ms' => $ts * 1000
                        ];
                    }
                }
                
                // Fetch all lessons
                if ($lessons_result->num_rows > 0) {
                    $lessons_result->data_seek(0); // Reset pointer
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
                            <li class="task-item" style="cursor: pointer; transition: all 0.3s ease;" data-type="<?php echo $item['type']; ?>" data-file-type="<?php echo $file_type; ?>" data-date="<?php echo $item['date_ms']; ?>" onclick="viewContent('<?php echo $item['type']; ?>', <?php echo $item['id']; ?>)">
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
                                            <i class="fas fa-file"></i> 
                                            <?php 
                                            $ext = pathinfo($item['file_name'], PATHINFO_EXTENSION);
                                            echo htmlspecialchars($item['file_name']);
                                            ?>
                                        </p>
                                    <?php endif; ?>
                                    <p style="margin-top: 8px; font-size: 11px; color: #a0aec0;">
                                        <i class="fas fa-calendar"></i> <?php echo date('M d, Y H:i', strtotime($item['created_at'])); ?>
                                    </p>
                                </div>
                                <div style="display: flex; gap: 8px; flex-direction: column;" onclick="event.stopPropagation();">
                                    <a href="edit_content.php?type=<?php echo $item['type']; ?>&id=<?php echo $item['id']; ?>&class_id=<?php echo $class_id; ?>" class="download-btn" style="white-space: normal; background: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.3); color: #86efac;"><i class="fas fa-edit"></i> Edit</a>
                                    <?php if ($item['has_file']): 
                                        $ext = strtolower(pathinfo($item['file_name'] ?? '', PATHINFO_EXTENSION));
                                        $is_viewable = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'mp4', 'avi', 'mov', 'mkv', 'webm', 'ogv']);
                                    ?>
                                        <?php if ($is_viewable): ?>
                                            <a href="view_content.php?type=<?php echo $item['type']; ?>&id=<?php echo $item['id']; ?>&class_id=<?php echo $class_id; ?>" class="download-btn" style="white-space: normal; background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.3); color: #93c5fd;"><i class="fas fa-eye"></i> View</a>
                                        <?php endif; ?>
                                        <a href="serve_file.php?type=<?php echo $item['type']; ?>&id=<?php echo $item['id']; ?>&class_id=<?php echo $class_id; ?>&download=1" download class="download-btn" style="white-space: normal;" onclick="event.stopPropagation();"><i class="fas fa-download"></i> Download</a>
                                    <?php endif; ?>
                                    <form method="POST" action="delete_handler.php" style="display: inline;" onsubmit="event.stopPropagation(); return confirm('Are you sure you want to delete this?');">
                                        <input type="hidden" name="type" value="<?php echo $item['type']; ?>">
                                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                                        <button type="submit" class="delete-btn" style="width: 100%;"><i class="fas fa-trash"></i> Delete</button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No content yet. Add a task or lesson to get started!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lessons Tab (New) -->
        <div id="lessons" class="tab-content">
            <div class="section">
                <h2>Add New Lesson</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_lesson">
                    <div class="form-group">
                        <label for="lesson_name">Lesson Name</label>
                        <input type="text" id="lesson_name" name="lesson_name" placeholder="e.g., Introduction to French" required>
                    </div>
                    <div class="form-group">
                        <label for="lesson_description">Description</label>
                        <textarea id="lesson_description" name="lesson_description" placeholder="Describe this lesson..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="lesson_file">Upload File (PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT, ZIP)</label>
                        <input type="file" id="lesson_file" name="lesson_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip" required>
                    </div>
                    <button type="submit" class="add-btn">Add Lesson</button>
                </form>
            </div>

            <div class="section">
                <h2>Class Lessons</h2>
                <?php $lessons_result->data_seek(0); ?>
                <?php if ($lessons_result->num_rows > 0): ?>
                    <ul class="lesson-list">
                        <?php while ($lesson = $lessons_result->fetch_assoc()): ?>
                            <li class="lesson-item">
                                <div class="lesson-info">
                                    <h4><?php echo htmlspecialchars($lesson['lesson_name']); ?></h4>
                                    <p><?php echo htmlspecialchars($lesson['lesson_description'] ?? 'No description'); ?></p>
                                    <p style="margin-top: 8px; font-size: 12px;"><i class="fas fa-file"></i> <?php echo htmlspecialchars($lesson['file_name']); ?></p>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <!-- removed prepended / from file path since it already starts with / -->
                                    <a href="serve_file.php?type=lesson&id=<?php echo $lesson['lesson_id']; ?>&download=1" download class="download-btn"><i class="fas fa-download"></i> Download</a>
                                    <form method="POST" action="delete_handler.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this lesson?');">
                                        <input type="hidden" name="type" value="lesson">
                                        <input type="hidden" name="item_id" value="<?php echo $lesson['lesson_id']; ?>">
                                        <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                                        <button type="submit" class="delete-btn"><i class="fas fa-trash"></i> Delete</button>
                                    </form>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-book"></i>
                        <p>No lessons yet. Add one to get started!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Submissions Tab -->
        <div id="submissions" class="tab-content">
            <div class="section">
                <h2>Student Submissions</h2>
                <?php if ($submissions_result->num_rows > 0): ?>
                    <ul class="submission-list">
                        <?php while ($submission = $submissions_result->fetch_assoc()): ?>
                            <li class="submission-item">
                                <div class="submission-info">
                                    <h4><?php echo htmlspecialchars($submission['task_name']); ?></h4>
                                    <p><strong>Student:</strong> <?php echo htmlspecialchars($submission['name']); ?> (<?php echo htmlspecialchars($submission['email']); ?>)</p>
                                    <p><strong>File:</strong> <?php echo htmlspecialchars($submission['file_name']); ?></p>
                                    <div class="submission-meta">
                                        <span class="submission-meta-item"><i class="fas fa-calendar"></i> <?php echo date('M d, Y H:i', strtotime($submission['submitted_at'])); ?></span>
                                    </div>
                                </div>
                                <!-- removed prepended / from file path since it already starts with / -->
                                <a href="serve_file.php?type=submission&submission_id=<?php echo $submission['submission_id']; ?>&download=1" download class="download-btn"><i class="fas fa-download"></i> Download</a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-file-upload"></i>
                        <p>No submissions yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Students Tab -->
        <div id="students" class="tab-content">
            <div class="section">
                <h2>Student Progress</h2>
                <?php if ($students_result->num_rows > 0): ?>
                    <ul class="student-list">
                        <?php while ($student = $students_result->fetch_assoc()): ?>
                            <?php
                            $progress_percent = $student['total_tasks'] > 0 ? round(($student['completed_tasks'] / $student['total_tasks']) * 100) : 0;
                            ?>
                            <li class="student-item">
                                <div class="student-info">
                                    <h4><?php echo htmlspecialchars($student['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($student['email']); ?></p>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $progress_percent; ?>%"></div>
                                    </div>
                                    <span class="progress-text"><?php echo $progress_percent; ?>%</span>
                                    <form method="POST" action="delete_handler.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this student from the class?');">
                                        <input type="hidden" name="type" value="student">
                                        <input type="hidden" name="item_id" value="<?php echo $student['student_id']; ?>">
                                        <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                                        <button type="submit" class="delete-btn"><i class="fas fa-trash"></i> Remove</button>
                                    </form>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No students enrolled yet. Share the access code to invite students!</p>
                    </div>
                <?php endif; ?>
            </div>
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
            // Activate the button with matching data-tab attribute
            const btn = document.querySelector(`.tab-btn[data-tab="${tabName}"]`);
            if (btn) btn.classList.add('active');
        }

        function viewContent(type, id) {
            window.location.href = `view_content.php?type=${type}&id=${id}&class_id=<?php echo $class_id; ?>`;
        }

        function sortContent(sortType) {
            // Update active button using data-sort attribute
            document.querySelectorAll('.sort-btn[data-sort]').forEach(btn => btn.classList.remove('active'));
            const activeBtn = document.querySelector(`.sort-btn[data-sort="${sortType}"]`);
            if (activeBtn) activeBtn.classList.add('active');

            const items = Array.from(document.querySelectorAll('.task-list .task-item'));
            const taskList = document.querySelector('.task-list');

            if (sortType === 'date') {
                // Sort by newest first (default) using numeric timestamps
                items.sort((a, b) => {
                    const dateA = Number(a.dataset.date) || 0;
                    const dateB = Number(b.dataset.date) || 0;
                    return dateB - dateA;
                });
            } else if (sortType === 'oldest') {
                // Sort by oldest first
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
            
            // Update active filter item (use data-type selector)
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
