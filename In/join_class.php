<?php
session_start();

// Check if user is logged in and is a student
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli("localhost", "root", "", "fsldb");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $access_code = strtoupper(trim($_POST['access_code'] ?? ''));
    $student_id = $_SESSION['acc_no'];

    if (empty($access_code)) {
        $error = "Please enter an access code";
    } else {
        // Find class by access code
        $class_query = "SELECT class_id FROM classes WHERE access_code = ? AND status = 'active'";
        $stmt = $conn->prepare($class_query);
        $stmt->bind_param("s", $access_code);
        $stmt->execute();
        $class_result = $stmt->get_result();
        $stmt->close();

        if ($class_result->num_rows == 0) {
            $error = "Invalid access code or class is not active";
        } else {
            $class = $class_result->fetch_assoc();
            $class_id = $class['class_id'];

            // Check if already enrolled
            $check_query = "SELECT enrollment_id FROM student_enrollments WHERE class_id = ? AND student_id = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("ii", $class_id, $student_id);
            $stmt->execute();
            $check_result = $stmt->get_result();
            $stmt->close();

            if ($check_result->num_rows > 0) {
                $error = "You are already enrolled in this class";
            } else {
                // Enroll student
                $enroll_query = "INSERT INTO student_enrollments (class_id, student_id) VALUES (?, ?)";
                $stmt = $conn->prepare($enroll_query);
                $stmt->bind_param("ii", $class_id, $student_id);
                
                if ($stmt->execute()) {
                    $success = "Successfully joined the class!";
                    $stmt->close();
                    $conn->close();
                    header("Location: student_class.php?class_id=$class_id&success=1");
                    exit();
                } else {
                    $error = "Error joining class: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Class - FSL</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            width: 100%;
        }

        .card {
            background: #232023;
            border-radius: 12px;
            padding: 40px;
            border: 1px solid rgba(139, 92, 246, 0.1);
        }

        .card h1 {
            color: #ffffff;
            margin-bottom: 12px;
            font-size: 28px;
        }

        .card p {
            color: #a0aec0;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            color: #e2e8f0;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 8px;
            font-size: 14px;
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder {
            color: #718096;
        }

        .form-group input:focus {
            outline: none;
            border-color: #8b5cf6;
            background: rgba(139, 92, 246, 0.1);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(to right, #8b5cf6, #ec4899);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
        }

        .back-link {
            text-align: center;
            color: #8b5cf6;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #ec4899;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .success-message {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Join a Class</h1>
            <p>Enter the access code provided by your teacher</p>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="access_code">Access Code</label>
                    <input type="text" id="access_code" name="access_code" placeholder="e.g., ABC123" maxlength="6" required>
                </div>

                <button type="submit" class="submit-btn">Join Class</button>
                <a href="student_profile.php" class="back-link">Back to Profile</a>
            </form>
        </div>
    </div>
</body>
</html>
