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

$teacher_id = $_SESSION['acc_no'];
$result = $conn->query("SELECT profile_picture, contact, address FROM account_record WHERE acc_no = $teacher_id");
$teacher_data = $result->fetch_assoc();
$profile_picture = $teacher_data['profile_picture'] ?? null;
$contact = $teacher_data['contact'] ?? $_SESSION['contact'] ?? 'Not provided';
$address = $teacher_data['address'] ?? $_SESSION['address'] ?? 'Not provided';
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Profile - Filipino Sign Language</title>
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
        }

        

        .container {
            max-width: 900px;
            margin: 0 auto;
            margin-top: 20px;
        }

        .profile-header {
            background: #232023;
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 30px;
            border: 1px solid rgba(139, 92, 246, 0.1);
            text-align: center;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            margin: 0 auto 20px;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
            overflow: hidden;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-header h1 {
            font-size: 28px;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .profile-header p {
            color: #a0aec0;
            font-size: 14px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .profile-card {
            background: #232023;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid rgba(139, 92, 246, 0.1);
            transition: all 0.3s ease;
        }

        .profile-card:hover {
            border-color: rgba(139, 92, 246, 0.3);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.1);
        }

        .profile-card h3 {
            color: #8b5cf6;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .profile-card p {
            color: #ffffff;
            font-size: 16px;
            font-weight: 500;
        }

        

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 16px;
            }

            .profile-header {
                padding: 24px;
            }

            .profile-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include "includes/header.php" ?>

    <div class="container">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php if ($profile_picture): ?>
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
                <?php else: ?>
                    👨‍🏫
                <?php endif; ?>
            </div>
            <h1><?php echo htmlspecialchars($_SESSION['name']); ?></h1>
            <p>Teacher Profile</p>
        </div>

        <div class="profile-grid">
            <div class="profile-card">
                <h3>Email</h3>
                <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
            </div>
            <div class="profile-card">
                <h3>Contact</h3>
                <p><?php echo htmlspecialchars($contact); ?></p>
            </div>
            <div class="profile-card">
                <h3>Address</h3>
                <p><?php echo htmlspecialchars($address); ?></p>
            </div>
        </div>

        
    </div>
</body>
</html>
