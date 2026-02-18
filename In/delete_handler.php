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

$type = $_POST['type'] ?? $_GET['type'] ?? '';
$item_id = intval($_POST['item_id'] ?? $_GET['item_id'] ?? 0);
$class_id = intval($_POST['class_id'] ?? $_GET['class_id'] ?? 0);
$teacher_id = $_SESSION['acc_no'];

if ($type == 'task') {
    $verify_query = "SELECT c.class_id FROM classes c JOIN class_tasks ct ON c.class_id = ct.class_id WHERE ct.task_id = ? AND c.teacher_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("ii", $item_id, $teacher_id);
    $stmt->execute();
    $verify_result = $stmt->get_result();
    $stmt->close();

    if ($verify_result->num_rows == 0) {
        header("Location: teacher_dashboard.php");
        exit();
    }

    // Get file path to delete from server if it exists
    // Files are stored in DB as BLOBs; no filesystem unlink needed

    // Delete task and related submissions/progress
    $delete_submissions_query = "DELETE FROM student_task_submissions WHERE task_id = ?";
    $stmt = $conn->prepare($delete_submissions_query);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->close();

    $delete_progress_query = "DELETE FROM student_task_progress WHERE task_id = ?";
    $stmt = $conn->prepare($delete_progress_query);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->close();

    $delete_task_query = "DELETE FROM class_tasks WHERE task_id = ?";
    $stmt = $conn->prepare($delete_task_query);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->close();

} elseif ($type == 'content') {
    $verify_query = "SELECT c.class_id FROM classes c JOIN class_content cc ON c.class_id = cc.class_id WHERE cc.content_id = ? AND c.teacher_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("ii", $item_id, $teacher_id);
    $stmt->execute();
    $verify_result = $stmt->get_result();
    $stmt->close();

    if ($verify_result->num_rows == 0) {
        header("Location: teacher_dashboard.php");
        exit();
    }

    // Files are stored in DB as BLOBs; no filesystem unlink needed

    // Delete from database
    $delete_query = "DELETE FROM class_content WHERE content_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->close();

} elseif ($type == 'lesson') {
    $verify_query = "SELECT c.class_id FROM classes c JOIN class_lessons cl ON c.class_id = cl.class_id WHERE cl.lesson_id = ? AND c.teacher_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("ii", $item_id, $teacher_id);
    $stmt->execute();
    $verify_result = $stmt->get_result();
    $stmt->close();

    if ($verify_result->num_rows == 0) {
        header("Location: teacher_dashboard.php");
        exit();
    }

    // Files are stored in DB as BLOBs; no filesystem unlink needed

    // Delete from database
    $delete_query = "DELETE FROM class_lessons WHERE lesson_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->close();

} elseif ($type == 'class') {
    $verify_query = "SELECT teacher_id FROM classes WHERE class_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $verify_result = $stmt->get_result();

    if ($verify_result->num_rows == 0) {
        header("Location: teacher_dashboard.php");
        exit();
    }

    $class_row = $verify_result->fetch_assoc();
    if ($class_row['teacher_id'] != $teacher_id) {
        header("Location: teacher_dashboard.php");
        exit();
    }
    $stmt->close();

    // Delete all related data in correct order (respecting foreign keys)
    $delete_submissions = "DELETE FROM student_task_submissions WHERE task_id IN (SELECT task_id FROM class_tasks WHERE class_id = ?)";
    $stmt = $conn->prepare($delete_submissions);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $stmt->close();

    $delete_progress = "DELETE FROM student_task_progress WHERE task_id IN (SELECT task_id FROM class_tasks WHERE class_id = ?)";
    $stmt = $conn->prepare($delete_progress);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $stmt->close();

    $delete_tasks = "DELETE FROM class_tasks WHERE class_id = ?";
    $stmt = $conn->prepare($delete_tasks);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $stmt->close();

    $delete_content = "DELETE FROM class_content WHERE class_id = ?";
    $stmt = $conn->prepare($delete_content);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $stmt->close();

    $delete_enrollments = "DELETE FROM student_enrollments WHERE class_id = ?";
    $stmt = $conn->prepare($delete_enrollments);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $stmt->close();

    $delete_class = "DELETE FROM classes WHERE class_id = ?";
    $stmt = $conn->prepare($delete_class);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $stmt->close();

    $conn->close();
    header("Location: teacher_dashboard.php?deleted=1");
    exit();
} elseif ($type == 'student') {
    $student_id = intval($_POST['item_id'] ?? 0);
    
    // Verify the student is enrolled in this class and teacher owns the class
    $verify_query = "SELECT se.enrollment_id FROM student_enrollments se 
                     JOIN classes c ON se.class_id = c.class_id 
                     WHERE se.student_id = ? AND se.class_id = ? AND c.teacher_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("iii", $student_id, $class_id, $teacher_id);
    $stmt->execute();
    $verify_result = $stmt->get_result();
    $stmt->close();

    if ($verify_result->num_rows == 0) {
        header("Location: teacher_dashboard.php");
        exit();
    }

    // Delete student submissions for this class
    $delete_submissions = "DELETE FROM student_task_submissions 
                          WHERE student_id = ? AND task_id IN (SELECT task_id FROM class_tasks WHERE class_id = ?)";
    $stmt = $conn->prepare($delete_submissions);
    $stmt->bind_param("ii", $student_id, $class_id);
    $stmt->execute();
    $stmt->close();

    // Delete student progress for this class
    $delete_progress = "DELETE FROM student_task_progress 
                       WHERE student_id = ? AND task_id IN (SELECT task_id FROM class_tasks WHERE class_id = ?)";
    $stmt = $conn->prepare($delete_progress);
    $stmt->bind_param("ii", $student_id, $class_id);
    $stmt->execute();
    $stmt->close();

    // Delete student enrollment
    $delete_enrollment = "DELETE FROM student_enrollments WHERE student_id = ? AND class_id = ?";
    $stmt = $conn->prepare($delete_enrollment);
    $stmt->bind_param("ii", $student_id, $class_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: teacher_class.php?class_id=$class_id");
exit();
?>
