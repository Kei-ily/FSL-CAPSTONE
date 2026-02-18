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

$class_id = intval($_POST['class_id'] ?? 0);
$teacher_id = $_SESSION['acc_no'];

// Verify teacher owns this class
$verify_query = "SELECT class_id FROM classes WHERE class_id = ? AND teacher_id = ?";
$stmt = $conn->prepare($verify_query);
$stmt->bind_param("ii", $class_id, $teacher_id);
$stmt->execute();
$verify_result = $stmt->get_result();
$stmt->close();

if ($verify_result->num_rows == 0) {
    header("Location: teacher_class.php?class_id=$class_id&error=Unauthorized");
    exit();
}

// Handle file upload
if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_name = basename($_FILES['file']['name']);
    $file_size = $_FILES['file']['size'];
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($finfo, $file_tmp);
    finfo_close($finfo);
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($file_type, $allowed_types)) {
        header("Location: teacher_class.php?class_id=$class_id&error=Invalid file type");
        exit();
    }

    $file_data = file_get_contents($file_tmp);
    $description = trim($_POST['description'] ?? '');

    $insert_query = "INSERT INTO class_content (class_id, content_type, file_data, file_name, file_mime, description) VALUES (?, 'image', ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    if (!$stmt) {
        header("Location: teacher_class.php?class_id=$class_id&error=Database error");
        exit();
    }
    $stmt->bind_param("issss", $class_id, $file_data, $file_name, $file_type, $description);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: teacher_class.php?class_id=$class_id&success=Content uploaded successfully");
        exit();
    } else {
        $stmt->close();
        header("Location: teacher_class.php?class_id=$class_id&error=Database error");
        exit();
    }
} else {
    header("Location: teacher_class.php?class_id=$class_id&error=No file selected");
    exit();
}

?>
