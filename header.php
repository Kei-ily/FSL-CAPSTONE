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
            justify-content: space-between;
            align-items: center;
            color: white;
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
                        <li><a href="#">Numbers</a></li>
                        <li><a href="#">Quizzes</a></li>
                        <li><a href="#">Games</a></li>
                    </ul>
                </li>

                <li><a href="fsl.php">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#about">Contact Us</a></li>

                <!-- Role-based Classes dropdown for Teachers -->
                <?php if ($userRole === 'teacher'): ?>
                <li class="dropdown">
                    <a href="#">Classes ▼</a>
                    <ul class="dropdown-menu">
                        <li><a href="teacher_dashboard.php">Dashboard</a></li>
                        <li><a href="create_class.php">Create Class</a></li>
                        <li><a href="manage_class.php">Manage Classes</a></li>
                        <li><a href="view_class.php">View Class</a></li>
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

                <!-- Account Dropdown -->
                <li class="dropdown">
                    <a href="#">Account ▼</a>
                    <ul class="dropdown-menu">
                        <li><a href="logout.php" class="logout-btn">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>

</body>

</html>
