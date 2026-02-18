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

$type = $_GET['type'] ?? '';
$item_id = intval($_GET['id'] ?? 0);
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

// Fetch the content based on type
$content = null;
if ($type == 'task') {
    $query = "SELECT * FROM class_tasks WHERE task_id = ? AND class_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $item_id, $class_id);
} elseif ($type == 'lesson') {
    $query = "SELECT * FROM class_lessons WHERE lesson_id = ? AND class_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $item_id, $class_id);
} else {
    header("Location: teacher_class.php?class_id=$class_id");
    exit();
}

$stmt->execute();
$result = $stmt->get_result();
$content = $result->fetch_assoc();
$stmt->close();

if (!$content) {
    header("Location: teacher_class.php?class_id=$class_id");
    exit();
}

// Handle update request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $old_file_path = $content['file_path'] ?? null;
    $old_file_name = $content['file_name'] ?? null;
    $new_file_path = $old_file_path;
    $new_file_name = $old_file_name;

    // Handle file upload if a new file is provided (store as BLOB)
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file = $_FILES['file'];
        $file_name = basename($file['name']);
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_mime = mime_content_type($file['tmp_name']);
        $file_data = file_get_contents($file['tmp_name']);
        
        // Determine allowed extensions based on type
        if ($type == 'task') {
            $allowed_ext = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'zip', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov', 'mkv'];
        } else {
            $allowed_ext = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'zip'];
        }

        if (in_array(strtolower($file_ext), $allowed_ext)) {
            // Overwrite old DB blob (no filesystem operations)
            $new_file_path = null;
            $new_file_name = $file_name;
            $new_file_data = $file_data;
            $new_file_mime = $file_mime;
        }
    }

    // Update database
    if ($type == 'task') {
        if (isset($new_file_data)) {
            $update_query = "UPDATE class_tasks SET task_name = ?, task_description = ?, file_data = ?, file_name = ?, file_mime = ?, file_path = NULL WHERE task_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sssssi", $name, $description, $new_file_data, $new_file_name, $new_file_mime, $item_id);
        } else {
            $update_query = "UPDATE class_tasks SET task_name = ?, task_description = ? WHERE task_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssi", $name, $description, $item_id);
        }
    } else {
        if (isset($new_file_data)) {
            $update_query = "UPDATE class_lessons SET lesson_name = ?, lesson_description = ?, file_data = ?, file_name = ?, file_mime = ?, file_path = NULL WHERE lesson_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sssssi", $name, $description, $new_file_data, $new_file_name, $new_file_mime, $item_id);
        } else {
            $update_query = "UPDATE class_lessons SET lesson_name = ?, lesson_description = ? WHERE lesson_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssi", $name, $description, $item_id);
        }
    }
    
    $stmt->execute();
    $stmt->close();
    
    header("Location: teacher_class.php?class_id=$class_id");
    exit();
}

// Handle file removal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'remove_file') {
    // Remove file blob/meta from DB (no filesystem unlink)
    if ($type == 'task') {
        $update_query = "UPDATE class_tasks SET file_data = NULL, file_name = NULL, file_mime = NULL, file_path = NULL WHERE task_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $item_id);
    } else {
        $update_query = "UPDATE class_lessons SET file_data = NULL, file_name = NULL, file_mime = NULL, file_path = NULL WHERE lesson_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $item_id);
    }
    
    $stmt->execute();
    $stmt->close();
    
    header("Location: edit_content.php?type=$type&id=$item_id&class_id=$class_id");
    exit();
}

$teacher_pic_result = $conn->query("SELECT profile_picture FROM account_record WHERE acc_no = $teacher_id");
$teacher_pic_data = $teacher_pic_result->fetch_assoc();
$teacher_profile_picture = $teacher_pic_data['profile_picture'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Content - FSL</title>
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
            padding: 20px;
        }

        .container {
            max-width: 900px;
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
            font-family: Arial, sans-serif;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: rgba(139, 92, 246, 0.8);
            background: rgba(255, 255, 255, 0.08);
        }

        .file-info {
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.2);
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 12px;
        }

        .file-info-item {
            color: #a0aec0;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .file-info-item strong {
            color: #8b5cf6;
        }

        .file-info-item:last-child {
            margin-bottom: 0;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background: linear-gradient(to right, #8b5cf6, #ec4899);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        .btn-secondary {
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(139, 92, 246, 0.2);
        }

        .content-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 11px;
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
            margin-bottom: 16px;
        }

        .timestamp {
            color: #a0aec0;
            font-size: 12px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(139, 92, 246, 0.1);
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
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
            <h1>Edit <?php echo ucfirst($type); ?></h1>
            <a href="teacher_class.php?class_id=<?php echo $class_id; ?>" class="back-btn">Back to Class</a>
        </div>

        <div class="section">
            <div class="content-badge">
                <i class="fas fa-tag"></i> <?php echo strtoupper($type); ?>
            </div>
            <h2>Content Details</h2>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                
                <div class="form-group">
                    <label for="name"><?php echo ucfirst($type); ?> Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($content[$type == 'task' ? 'task_name' : 'lesson_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars($content[$type == 'task' ? 'task_description' : 'lesson_description'] ?? ''); ?></textarea>
                </div>

                <!-- Current File Info -->
                <?php if ($content['file_name']): ?>
                    <div class="file-info">
                        <div class="file-info-item">
                            <strong>Current File:</strong> <?php echo htmlspecialchars($content['file_name']); ?>
                        </div>
                        <div class="file-info-item">
                            <a href="<?php echo htmlspecialchars($content['file_path']); ?>" target="_blank" style="color: #8b5cf6; text-decoration: none;">
                                <i class="fas fa-download"></i> Download Current File
                            </a>
                        </div>
                        <div class="file-info-item">
                            <a href="view_content.php?type=<?php echo $type; ?>&id=<?php echo $item_id; ?>&class_id=<?php echo $class_id; ?>" style="color: #8b5cf6; text-decoration: none;">
                                <i class="fas fa-eye"></i> View Current File
                            </a>
                        </div>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this file from the <?php echo $type; ?>?');">
                            <input type="hidden" name="action" value="remove_file">
                            <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px; margin: 0;">
                                <i class="fas fa-trash"></i> Remove File
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- File Upload -->
                <div class="form-group">
                    <label for="file">
                        <?php if ($content['file_name']): ?>
                            Replace File (Optional)
                        <?php else: ?>
                            Attach File
                        <?php endif; ?>
                    </label>
                    <input type="file" id="file" name="file">
                    <p style="color: #a0aec0; font-size: 11px; margin-top: 6px;">
                        <?php if ($type == 'task'): ?>
                            Supported: Images, Videos, PDFs, Documents, Presentations, Spreadsheets, and more
                        <?php else: ?>
                            Supported: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT, ZIP
                        <?php endif; ?>
                    </p>
                </div>

                <div class="timestamp">
                    <i class="fas fa-clock"></i> Created: <?php echo date('M d, Y H:i', strtotime($content['created_at'])); ?>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="teacher_class.php?class_id=<?php echo $class_id; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
