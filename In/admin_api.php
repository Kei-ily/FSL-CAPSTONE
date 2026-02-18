<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "fsldb");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// Return next availableccount_record IDs for a, classes, and class_tasks
if ($action === 'next_ids') {
    // Helper: find smallest missing positive iumeric primarnteger in a ny key column
    $find_next = function($table, $col) use ($conn) {
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
    };

    $next = [
        'next_admin_id' => $find_next('account_record', 'acc_no'),
        'next_class_id' => $find_next('classes', 'class_id'),
        'next_task_id'  => $find_next('class_tasks', 'task_id'),
    ];

    echo json_encode(['success' => true, 'next' => $next]);
    $conn->close();
    exit();
}


if ($action === 'get_user') {
    $user_id = intval($_GET['user_id']);
    $result = $conn->query("SELECT `acc_no`, name, grade, email, contact, address, role FROM account_record WHERE `acc_no` = $user_id");
    $user = $result->fetch_assoc();
    
    if ($user) {
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
    exit();
}

if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $student_name = $conn->real_escape_string($_POST['name']);
    $grade = $conn->real_escape_string($_POST['grade']);
    $email = $conn->real_escape_string($_POST['email']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $address = $conn->real_escape_string($_POST['address']);
    $role = $conn->real_escape_string($_POST['role']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }

    $update_query = "UPDATE account_record SET name = '$student_name', grade = '$grade', email = '$email', contact = '$contact', address = '$address', role = '$role' WHERE `acc_no` = $user_id";
    
    if ($conn->query($update_query)) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating user: ' . $conn->error]);
    }
    exit();
}

if ($action === 'password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password)) {
        echo json_encode(['success' => false, 'message' => 'Password cannot be empty']);
        exit();
    }

    if (strlen($new_password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
        exit();
    }

    if ($new_password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit();
    }

    $new_password = $conn->real_escape_string($new_password);
    $update_query = "UPDATE account_record SET password = '$new_password' WHERE `acc_no` = $user_id";
    
    if ($conn->query($update_query)) {
        echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating password: ' . $conn->error]);
    }
    exit();
}

if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $role = $conn->real_escape_string($_POST['role']);

    // Delete related data first
    if ($role === 'student') {
        // Delete submissions
        $conn->query("DELETE FROM student_task_submissions WHERE student_id = '$user_id'");
        // Delete progress
        $conn->query("DELETE FROM student_task_progress WHERE student_id = '$user_id'");
        // Delete enrollments
        $conn->query("DELETE FROM student_enrollments WHERE student_id = '$user_id'");
    } elseif ($role === 'teacher') {
        // Delete classes and related data
        $classes_result = $conn->query("SELECT class_id FROM classes WHERE teacher_id = '$user_id'");
        while ($class = $classes_result->fetch_assoc()) {
            $class_id = $class['class_id'];
            // Delete submissions
            $conn->query("DELETE FROM student_task_submissions WHERE task_id IN (SELECT task_id FROM class_tasks WHERE class_id = $class_id)");
            // Delete progress
            $conn->query("DELETE FROM student_task_progress WHERE task_id IN (SELECT task_id FROM class_tasks WHERE class_id = $class_id)");
            // Delete tasks
            $conn->query("DELETE FROM class_tasks WHERE class_id = $class_id");
            // Delete content
            $conn->query("DELETE FROM class_content WHERE class_id = $class_id");
            // Delete enrollments
            $conn->query("DELETE FROM student_enrollments WHERE class_id = $class_id");
        }
        // Delete classes
        $conn->query("DELETE FROM classes WHERE teacher_id = '$user_id'");
    }

    $delete_query = "DELETE FROM account_record WHERE `acc_no` = $user_id";
    
    if ($conn->query($delete_query)) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting user: ' . $conn->error]);
    }
    exit();
}

$conn->close();
echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
