<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Email and password are required";
    } else {
        $conn = new mysqli("localhost", "root", "", "fsldb");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $query = "SELECT `acc_no`, `name`, `grade`, `contact`, `email`, `password`, `address`, `role` FROM account_record WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if ($password === $user['password'] || password_verify($password, $user['password'])) {
                $_SESSION['acc_no'] = $user['acc_no'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['grade'] = $user['grade'];
                $_SESSION['contact'] = $user['contact'];
                $_SESSION['address'] = $user['address'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 'teacher') {
                    header("Location: In/teacher_dashboard.php");
                } elseif ($user['role'] == 'student') {
                    header("Location: In/student_class.php");
                } elseif ($user['role'] == 'admin') {
                    header("Location: In/admin_profile.php");
                } else {
                    header("Location: fsl.php");
                }
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }

        $stmt->close();
        $conn->close();
    }

    header("Location: login.php?error=" . urlencode($error));
    exit();
}
?>
