<?php
session_start();

$conn = new mysqli("localhost", "root", "", "fsldb");
if ($conn->connect_error) {
    http_response_code(500);
    exit('DB connection error');
}

$type = $_GET['type'] ?? '';
$download = isset($_GET['download']);
$user_id = $_SESSION['acc_no'] ?? null;
$user_role = $_SESSION['role'] ?? null;

function deny() {
    http_response_code(403);
    exit('Forbidden');
}

if (!$user_id) deny();

if ($type === 'task') {
    $id = intval($_GET['id'] ?? 0);
    $class_id = intval($_GET['class_id'] ?? 0);
    if ($user_role === 'teacher') {
        $stmt = $conn->prepare("SELECT ct.file_data, ct.file_mime, ct.file_name, ct.file_path FROM class_tasks ct JOIN classes c ON ct.class_id = c.class_id WHERE ct.task_id = ? AND c.teacher_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
    } else {
        $stmt = $conn->prepare("SELECT ct.file_data, ct.file_mime, ct.file_name, ct.file_path FROM class_tasks ct JOIN student_enrollments se ON ct.class_id = se.class_id WHERE ct.task_id = ? AND se.student_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
} elseif ($type === 'lesson') {
    $id = intval($_GET['id'] ?? 0);
    if ($user_role === 'teacher') {
        $stmt = $conn->prepare("SELECT cl.file_data, cl.file_mime, cl.file_name, cl.file_path FROM class_lessons cl JOIN classes c ON cl.class_id = c.class_id WHERE cl.lesson_id = ? AND c.teacher_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
    } else {
        $stmt = $conn->prepare("SELECT cl.file_data, cl.file_mime, cl.file_name, cl.file_path FROM class_lessons cl JOIN student_enrollments se ON cl.class_id = se.class_id WHERE cl.lesson_id = ? AND se.student_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
} elseif ($type === 'content') {
    $id = intval($_GET['id'] ?? 0);
    if ($user_role === 'teacher') {
        $stmt = $conn->prepare("SELECT cc.file_data, cc.file_mime, cc.file_name, cc.file_path FROM class_content cc JOIN classes c ON cc.class_id = c.class_id WHERE cc.content_id = ? AND c.teacher_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
    } else {
        $stmt = $conn->prepare("SELECT cc.file_data, cc.file_mime, cc.file_name, cc.file_path FROM class_content cc JOIN student_enrollments se ON cc.class_id = se.class_id WHERE cc.content_id = ? AND se.student_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
} elseif ($type === 'submission') {
    $submission_id = intval($_GET['submission_id'] ?? 0);
    $stmt = $conn->prepare("SELECT submission_id, student_id, file_data, file_mime, file_name, file_path FROM student_task_submissions WHERE submission_id = ?");
    $stmt->bind_param("i", $submission_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
    if (!$row) { http_response_code(404); exit('Not found'); }
    // only allow teacher or the submitting student
    if ($user_role !== 'teacher' && intval($row['student_id']) !== intval($user_id)) deny();
} else {
    http_response_code(400);
    exit('Invalid type');
}

if (!$row) { http_response_code(404); exit('Not found'); }

$file_name = $row['file_name'] ?? null;
$file_mime = $row['file_mime'] ?? null;
$file_data = $row['file_data'] ?? null;
$file_path = $row['file_path'] ?? null;

if ($file_data) {
    if (!$file_mime) $file_mime = 'application/octet-stream';
    header('Content-Type: ' . $file_mime);
    if ($download) header('Content-Disposition: attachment; filename="' . basename($file_name ?: 'file') . '"');
    echo $file_data;
    exit();
} elseif ($file_path && file_exists($file_path)) {
    // legacy support: serve from filesystem
    $mime = $file_mime ?: mime_content_type($file_path);
    header('Content-Type: ' . $mime);
    if ($download) header('Content-Disposition: attachment; filename="' . basename($file_name ?: $file_path) . '"');
    readfile($file_path);
    exit();
} else {
    http_response_code(404);
    exit('No file available');
}

?>
