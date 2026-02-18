<?php
session_start();

// Check if user is logged in and is a teacher
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'teacher') {
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

    // helper to find smallest missing ID
    function get_smallest_missing($conn, $table, $col) {
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
    }

    $class_name = trim($_POST['class_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $teacher_id = $_SESSION['acc_no'];
    
    // Generate random 6-character access code
    $access_code = strtoupper(substr(md5(uniqid()), 0, 6));

    if (empty($class_name)) {
        $error = "Class name is required";
    } else {
        // assign smallest available class_id to fill gaps
        $class_id = get_smallest_missing($conn, 'classes', 'class_id');

        $insert_query = "INSERT INTO classes (class_id, teacher_id, class_name, description, access_code) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iisss", $class_id, $teacher_id, $class_name, $description, $access_code);
        
        if ($stmt->execute()) {
            $success = "Class created successfully!";
            $stmt->close();
            $conn->close();
            header("Location: teacher_class.php?class_id=$class_id&success=1");
            exit();
        } else {
            $error = "Error creating class: " . $stmt->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Class - FSL</title>
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
            max-width: 600px;
            margin: 0 auto;
        }

        .card {
            background: #232023;
            border-radius: 12px;
            padding: 40px;
            border: 1px solid rgba(139, 92, 246, 0.1);
        }

        .card h1 {
            color: #ffffff;
            margin-bottom: 30px;
            font-size: 28px;
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

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #718096;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #8b5cf6;
            background: rgba(139, 92, 246, 0.1);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 32px;
        }

        .submit-btn {
            flex: 1;
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
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
        }

        .cancel-btn {
            flex: 1;
            padding: 14px 24px;
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cancel-btn:hover {
            background: rgba(139, 92, 246, 0.2);
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
            <h1>Create New Class</h1>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="class_name">Class Name *</label>
                    <input type="text" id="class_name" name="class_name" placeholder="e.g., FSL Basics - Level 1" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Describe what students will learn in this class..."></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" class="submit-btn">Create Class</button>
                    <a href="teacher_dashboard.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
