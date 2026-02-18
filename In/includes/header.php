<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user role from session
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        header {
            background: #0D1117;
            /* dark navbar */
            padding: 15px 30px;
            font-family: Arial, sans-serif
        }

        .navbar {
            display: flex;
            justify-content: space-around;
            align-items: center;
            color: white;
            gap: 350px;
        }

        /* Logo styling */
        .logo {
            font-size: 22px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo .highlight {
            color: #7e2ddb;
            font-size: 24px;
            font-weight: 700;
        }

        .logo .text {
            color: #ffffff;
            /* white for "Filipino Sign Language" */
            font-weight: 700px;
            font-size: 18px;
        }

        /* Navigation links */
        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-links li {
            position: relative;
        }

        .nav-links a {
            text-decoration: none;
            color: white;
            font-weight: 500;
            padding: 8px 12px;
            transition: 0.3s;
        }

        .nav-links a:hover {
            color: #2d9cdb;
            /* blue on hover */
        }

        /* Dropdown */
        .dropdown-menu {
            display: none;
            position: absolute;
            background: #1e1e1e;
            top: 20px;
            left: 0;
            min-width: 150px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.25);
            z-index: 500;

        }

        .dropdown-menu li a {
            display: block;
            padding: 10px;
            color: #ffffff;
            transition: 0.3s;
        }

        .dropdown-menu li a:hover {
            background: #2d9cdb;
            color: #ffffff;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .logout-btn {
            color: #ff6b6b;
        }

        .logout-btn:hover {
            background: #ff6b6b !important;
            color: white !important;
        }
.btn {
            background: linear-gradient(135deg, #7bbba4, #739EC9, #8373c9);
            /* blue-purple gradient */
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 19px;
            font-weight: 500;
            transition: 0.3s ease;
            /* smooth transition */
            display: inline-block;
            /* ensures transform works well */
        }

        .btn:hover {
            opacity: 0.9;
            transform: scale(1.1);
            /* grow effect */
        }

        .btn.secondary {
            background: #419ea5;
            color: #ffffff;
            border: 1px solid #419ea5;
            transition: 0.3s ease;
        }

        .btn.secondary:hover {
            background: #337777;
            transform: scale(1.1);
            /* grow effect */
        }
        
    </style>
</head>

<body>
    <!-- Navbar -->
    <header>
        <nav class="navbar">
            <div class="logo">
                <span class="highlight">FSL</span>
                <span class="text">Filipino Sign Language</span>
            </div>
            <ul class="nav-links">
                <!-- Learn Dropdown -->
                <li class="dropdown">
                    <a href="#">Learn ▼</a>
                    <ul class="dropdown-menu">
                        <li><a href="AlphabetFlashcard.php">Alphabet</a></li>
                        <li><a href="NumberFlashcard.php">Numbers</a></li>
                        <li><a href="#">Quizzes</a></li>
                        <li><a href="game.php">Games</a></li>
                    </ul>
                </li>

                <li><a href="fsl.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact Us</a></li>

                <!-- Updated teacher classes dropdown to use teacher_class.php -->
                <?php if ($userRole === 'teacher'): ?>
                <li class="dropdown">
                    <a href="#">Classes ▼</a>
                    <ul class="dropdown-menu">
                        <li><a href="teacher_dashboard.php">Dashboard</a></li>
                        <li><a href="create_class.php">Create Class</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Role-based Classes dropdown for Students -->
                <?php if ($userRole === 'student'): ?>
                <li class="dropdown">
                    <a href="#">Classes ▼</a>
                    <ul class="dropdown-menu">
                        <li><a href="join_class.php">Join Class</a></li>
                        <li><a href="student_class.php">My Classes</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Admin Panel link for admin users -->
                <?php if ($userRole === 'admin'): ?>
                <li class="dropdown">
                    <a href="#">Admin ▼</a>
                    <ul class="dropdown-menu">
                        <li><a href="admin_panel.php">Admin Panel</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Account Dropdown -->
                <li class="dropdown">
                    <a href="#">Account ▼</a>
                    <ul class="dropdown-menu">
                        <?php if ($userRole === 'admin'): ?>
                        <li><a href="admin_profile.php">Profile</a></li>
                        <?php elseif ($userRole === "teacher"): ?>
                        <li><a href="teacher_profile.php">Profile</a></li>
                        <?php elseif ($userRole === "student"): ?>
                        <li><a href="student_profile.php">Profile</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php" class="logout-btn">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>

</body>

</html>
