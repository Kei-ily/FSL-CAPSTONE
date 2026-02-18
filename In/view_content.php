<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
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
$user_id = $_SESSION['acc_no'];
$user_role = $_SESSION['role'];

// Verify user has access to this class
if ($user_role === 'teacher') {
    // Teachers: verify they own this class
    $verify_query = "SELECT c.* FROM classes c WHERE c.class_id = ? AND c.teacher_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("ii", $class_id, $user_id);
} elseif ($user_role === 'student') {
    // Students: verify they're enrolled in this class and get class info
    $verify_query = "SELECT c.* FROM classes c JOIN student_enrollments se ON c.class_id = se.class_id WHERE c.class_id = ? AND se.student_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("ii", $class_id, $user_id);
} else {
    header("Location: login.php");
    exit();
}

$stmt->execute();
$class_result = $stmt->get_result();
$stmt->close();

if ($class_result->num_rows == 0) {
    header("Location: " . ($user_role === 'teacher' ? 'teacher_dashboard.php' : 'student_profile.php'));
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
    $redirect = $user_role === 'teacher' ? "teacher_class.php?class_id=$class_id" : "student_class.php?class_id=$class_id";
    header("Location: $redirect");
    exit();
}

$stmt->execute();
$result = $stmt->get_result();
$content = $result->fetch_assoc();
$stmt->close();

if (!$content || (empty($content['file_data']) && empty($content['file_path']) && empty($content['file_name']))) {
    $redirect = $user_role === 'teacher' ? "teacher_class.php?class_id=$class_id" : "student_class.php?class_id=$class_id";
    header("Location: $redirect");
    exit();
}

// Get file information
$file_name = $content['file_name'] ?? null;
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
// Build serve URL (will stream from DB or fallback to filesystem)
$serve_type = $type;
$serve_id = intval($item_id ?? $item_id);
$serve_url = "serve_file.php?type=" . urlencode($serve_type) . "&id=" . $serve_id;

$user_pic_result = $conn->query("SELECT profile_picture FROM account_record WHERE acc_no = $user_id");
$user_pic_data = $user_pic_result->fetch_assoc();
$user_profile_picture = $user_pic_data['profile_picture'] ?? null;
$user_name = $_SESSION['name'] ?? 'User';

// Determine file type category
$is_image = in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
$is_pdf = $file_ext === 'pdf';
$is_video = in_array($file_ext, ['mp4', 'avi', 'mov', 'mkv', 'webm', 'ogv']);
$is_text = in_array($file_ext, ['txt', 'csv']);
$is_document = in_array($file_ext, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);

$title = $content[$type == 'task' ? 'task_name' : 'lesson_name'];
$description = $content[$type == 'task' ? 'task_description' : 'lesson_description'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Content - FSL</title>
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
           padding-bottom: 40px;
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

        .viewer-section {
            background: #232023;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid rgba(139, 92, 246, 0.1);
            margin-bottom: 24px;
        }

        .viewer-header {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
        }

        .viewer-title {
            color: #8b5cf6;
            font-size: 20px;
            margin-bottom: 8px;
        }

        .viewer-meta {
            color: #a0aec0;
            font-size: 12px;
        }

        .viewer-meta span {
            margin-right: 16px;
        }

        .file-info {
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.2);
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .file-info-item {
            color: #a0aec0;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .file-info-item:last-child {
            margin-bottom: 0;
        }

        .file-info-item strong {
            color: #8b5cf6;
        }

        .viewer-content {
            margin: 20px 0;
            border-radius: 6px;
            overflow: auto;
        }

        .image-viewer {
            max-width: 100%;
            max-height: 600px;
            margin: 0 auto;
            display: block;
        }

        .video-viewer {
            width: 100%;
            max-width: 800px;
            height: auto;
            margin: 0 auto;
        }

        .text-viewer {
            background: rgba(0, 0, 0, 0.3);
            padding: 20px;
            border-radius: 6px;
            overflow-x: auto;
            max-height: 600px;
            font-family: 'Courier New', monospace;
            color: #e2e8f0;
            font-size: 13px;
            line-height: 1.5;
        }

        .pdf-viewer {
            width: 100%;
            height: 600px;
            border: none;
            border-radius: 6px;
        }

        .iframe-viewer {
            width: 100%;
            height: 600px;
            border: none;
            border-radius: 6px;
        }

        .unsupported {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 20px;
            border-radius: 6px;
            text-align: center;
            color: #fca5a5;
        }

        .button-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .btn-secondary {
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(139, 92, 246, 0.2);
        }

        .btn-primary {
            background: linear-gradient(to right, #8b5cf6, #ec4899);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .description {
            background: rgba(139, 92, 246, 0.05);
            border-left: 4px solid rgba(139, 92, 246, 0.3);
            padding: 16px;
            border-radius: 4px;
            margin-bottom: 20px;
            color: #a0aec0;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .video-viewer, .pdf-viewer, .iframe-viewer {
                height: 400px;
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
                <?php if ($user_profile_picture): ?>
                    <img src="<?php echo htmlspecialchars($user_profile_picture); ?>" alt="Profile">
                <?php else: ?>
                    <?php echo $user_role === 'teacher' ? '👨‍🏫' : '👨‍🎓'; ?>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($user_name); ?></h2>
                <p><?php echo htmlspecialchars($class['class_name']); ?></p>
            </div>
        </div>

        <div class="header">
            <h1>View Content</h1>
            <a href="<?php echo $user_role === 'teacher' ? 'teacher_class.php' : 'student_class.php'; ?>?class_id=<?php echo $class_id; ?>" class="back-btn">Back to Class</a>
        </div>

        <div class="viewer-section">
            <div class="viewer-header">
                <div class="viewer-title">
                    <i class="fas fa-file"></i> <?php echo htmlspecialchars($title); ?>
                </div>
                <div class="viewer-meta">
                    <span><i class="fas fa-tag"></i> <?php echo strtoupper($type); ?></span>
                    <span><i class="fas fa-file-<?php 
                        if ($is_image) echo 'image'; 
                        elseif ($is_pdf) echo 'pdf'; 
                        elseif ($is_video) echo 'video'; 
                        else echo 'alt'; 
                    ?>"></i> <?php echo strtoupper($file_ext); ?></span>
                    <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y H:i', strtotime($content['created_at'])); ?></span>
                </div>
            </div>

            <?php if (!empty($description)): ?>
            <div class="description">
                <strong>Description:</strong><br>
                <?php echo nl2br(htmlspecialchars($description)); ?>
            </div>
            <?php endif; ?>

            <div class="file-info">
                <div class="file-info-item">
                    <strong>File Name:</strong> <?php echo htmlspecialchars($file_name); ?>
                </div>
                <div class="file-info-item">
                    <strong>File Type:</strong> <?php echo strtoupper($file_ext); ?>
                </div>
                <div class="file-info-item">
                    <strong>File Size:</strong> <?php 
                    if (!empty($content['file_data'])) {
                        $size = strlen($content['file_data']);
                    } elseif (!empty($content['file_path']) && file_exists($content['file_path'])) {
                        $size = filesize($content['file_path']);
                    } else {
                        $size = 0;
                    }
                    if ($size < 1024) {
                        echo $size . ' B';
                    } elseif ($size < 1024 * 1024) {
                        echo round($size / 1024, 2) . ' KB';
                    } else {
                        echo round($size / (1024 * 1024), 2) . ' MB';
                    }
                    ?>
                </div>
            </div>

            <!-- Image Viewer -->
            <?php if ($is_image): ?>
                <div class="viewer-content">
                    <img src="<?php echo htmlspecialchars($serve_url); ?>" alt="<?php echo htmlspecialchars($file_name); ?>" class="image-viewer">
                </div>
            <?php endif; ?>

            <!-- PDF Viewer -->
            <?php if ($is_pdf): ?>
                <div class="viewer-content">
                    <iframe class="pdf-viewer" src="<?php echo htmlspecialchars($serve_url); ?>#toolbar=1&navpanes=0&scrollbar=1"></iframe>
                </div>
            <?php endif; ?>

            <!-- Video Viewer -->
            <?php if ($is_video): ?>
                <div class="viewer-content">
                    <video class="video-viewer" controls>
                        <source src="<?php echo htmlspecialchars($serve_url); ?>" type="<?php echo htmlspecialchars($content['file_mime'] ?? 'video/' . ($file_ext === 'mkv' ? 'x-matroska' : $file_ext)); ?>">
                        Your browser does not support the video tag.
                    </video>
                </div>
            <?php endif; ?>

            <!-- Text Viewer -->
            <?php if ($is_text): ?>
                <div class="viewer-content">
                    <div class="text-viewer">
                        <?php if (!empty($content['file_data'])) { echo htmlspecialchars($content['file_data']); } else { echo htmlspecialchars(file_get_contents($content['file_path'])); } ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Document Viewer (Google Docs) -->
            <?php if ($is_document): ?>
                <div class="viewer-content">
                    <iframe class="iframe-viewer" src="<?php echo htmlspecialchars($serve_url); ?>" ></iframe>
                </div>
            <?php endif; ?>

            <!-- Unsupported File Type -->
            <?php if (!$is_image && !$is_pdf && !$is_video && !$is_text && !$is_document): ?>
                <div class="unsupported">
                    <i class="fas fa-exclamation-circle" style="font-size: 32px; margin-bottom: 12px; display: block;"></i>
                    <p>This file type cannot be previewed in the browser.</p>
                    <p style="font-size: 12px; margin-top: 8px;">Please download the file to view it.</p>
                </div>
            <?php endif; ?>

            <div class="button-group">
                <a href="<?php echo htmlspecialchars($serve_url); ?>&download=1" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download File
                </a>
                <?php if ($user_role === 'teacher'): ?>
                    <a href="edit_content.php?type=<?php echo $type; ?>&id=<?php echo $item_id; ?>&class_id=<?php echo $class_id; ?>" class="btn btn-secondary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
