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

$task_id = intval($_POST['task_id'] ?? 0);
$class_id = intval($_POST['class_id'] ?? 0);
$student_id = $_SESSION['acc_no'];
$submission_description = trim($_POST['submission_description'] ?? '');

// Verify student is enrolled in this class
$verify_query = "SELECT se.class_id FROM student_enrollments se JOIN class_tasks ct ON se.class_id = ct.class_id WHERE ct.task_id = ? AND se.student_id = ? AND se.class_id = ?";
$stmt = $conn->prepare($verify_query);
$stmt->bind_param("iii", $task_id, $student_id, $class_id);
$stmt->execute();
$verify_result = $stmt->get_result();
$stmt->close();

if ($verify_result->num_rows == 0) {
    header("Location: student_class.php");
    exit();
}

// Handle file upload (store submission in DB as BLOB)
$file_name = $_FILES['submission_file']['name'] ?? '';
$file_tmp = $_FILES['submission_file']['tmp_name'] ?? '';
$file_error = $_FILES['submission_file']['error'] ?? 1;

if ($file_error === 0 && !empty($file_tmp)) {
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $file_mime = mime_content_type($file_tmp);
    $file_data = file_get_contents($file_tmp);

    // Check if submission already exists
    $check_query = "SELECT submission_id FROM student_task_submissions WHERE task_id = ? AND student_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $task_id, $student_id);
    $stmt->execute();
    $check_result = $stmt->get_result();
    $stmt->close();

    if ($check_result->num_rows > 0) {
        // Update existing submission (store blob)
        $update_query = "UPDATE student_task_submissions SET file_data = ?, file_name = ?, file_mime = ?, submitted_at = NOW() WHERE task_id = ? AND student_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssii", $file_data, $file_name, $file_mime, $task_id, $student_id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Insert new submission (store blob)
        $insert_query = "INSERT INTO student_task_submissions (task_id, student_id, file_data, file_name, file_mime) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iisss", $task_id, $student_id, $file_data, $file_name, $file_mime);
        $stmt->execute();
        $stmt->close();
    }

    // Mark task as completed
    $check_progress_query = "SELECT progress_id FROM student_task_progress WHERE task_id = ? AND student_id = ?";
    $stmt = $conn->prepare($check_progress_query);
    $stmt->bind_param("ii", $task_id, $student_id);
    $stmt->execute();
    $check_progress_result = $stmt->get_result();
    $stmt->close();

    if ($check_progress_result->num_rows > 0) {
        $update_progress_query = "UPDATE student_task_progress SET completed = 1, completed_at = NOW() WHERE task_id = ? AND student_id = ?";
        $stmt = $conn->prepare($update_progress_query);
        $stmt->bind_param("ii", $task_id, $student_id);
        $stmt->execute();
        $stmt->close();
    } else {
        $insert_progress_query = "INSERT INTO student_task_progress (task_id, student_id, completed, completed_at) VALUES (?, ?, 1, NOW())";
        $stmt = $conn->prepare($insert_progress_query);
        $stmt->bind_param("ii", $task_id, $student_id);
        $stmt->execute();
        $stmt->close();
    }

    // Check if all tasks are completed
    $all_tasks_query = "SELECT COUNT(*) as total FROM class_tasks WHERE class_id = ?";
    $stmt = $conn->prepare($all_tasks_query);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $total_result = $stmt->get_result();
    $total_row = $total_result->fetch_assoc();
    $total_tasks = $total_row['total'];
    $stmt->close();

    $completed_tasks_query = "SELECT COUNT(*) as completed FROM student_task_progress WHERE student_id = ? AND completed = 1 AND task_id IN (SELECT task_id FROM class_tasks WHERE class_id = ?)";
    $stmt = $conn->prepare($completed_tasks_query);
    $stmt->bind_param("ii", $student_id, $class_id);
    $stmt->execute();
    $completed_result = $stmt->get_result();
    $completed_row = $completed_result->fetch_assoc();
    $completed_tasks = $completed_row['completed'];
    $stmt->close();

    // If all tasks completed, mark enrollment as completed
    if ($total_tasks > 0 && $completed_tasks == $total_tasks) {
        $complete_enrollment_query = "UPDATE student_enrollments SET completed = 1, completed_at = NOW() WHERE class_id = ? AND student_id = ?";
        $stmt = $conn->prepare($complete_enrollment_query);
        $stmt->bind_param("ii", $class_id, $student_id);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
header("Location: student_class.php?class_id=$class_id");
exit();
?>