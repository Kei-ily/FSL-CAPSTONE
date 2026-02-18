<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: fsl.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "fsldb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$user_id) {
    header("Location: admin_panel.php");
    exit();
}

// Fetch user data
$result = $conn->query("SELECT `acc_no`, student_name, role FROM account_record WHERE `acc_no` = $user_id");
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: admin_panel.php");
    exit();
}

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords
    if (empty($new_password)) {
        $error = "Password cannot be empty";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Update password (storing as plain text as per original system)
        $update_query = "UPDATE account_record SET password = '$new_password' WHERE `acc_no` = $user_id";
        
        if ($conn->query($update_query)) {
            $message = "Password changed successfully!";
        } else {
            $error = "Error updating password: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-container h1 {
            color: #0D1117;
            margin-bottom: 10px;
        }

        .form-container p {
            color: #666;
            margin-bottom: 20px;
        }

        .user-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #7e2ddb;
        }

        .user-info strong {
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }

        input[type="password"]:focus {
            outline: none;
            border-color: #7e2ddb;
            box-shadow: 0 0 0 3px rgba(126, 45, 219, 0.1);
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit {
            background: #7e2ddb;
            color: white;
        }

        .btn-submit:hover {
            background: #6a1fb8;
        }

        .btn-cancel {
            background: #f0f0f0;
            color: #333;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
        }

        .message {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #7e2ddb;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <a href="admin_panel.php?tab=<?php echo $user['role'] === 'student' ? 'students' : 'teachers'; ?>" class="back-link">← Back to Admin Panel</a>

        <div class="form-container">
            <h1>Change Password</h1>
            <p>Set a new password for the user</p>

            <div class="user-info">
                <strong>User:</strong> <?php echo htmlspecialchars($user['student_name']); ?> (<?php echo ucfirst($user['role']); ?>)
            </div>

            <?php if ($message): ?>
                <div class="message success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>New Password *</label>
                    <input type="password" name="new_password" required>
                </div>

                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" required>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-submit">Change Password</button>
                    <button type="button" class="btn-cancel" onclick="history.back()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
