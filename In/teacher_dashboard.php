<?php
session_start();

// Check if user is logged in and is a teacher
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "fsldb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$teacher_id = $_SESSION['acc_no'];
$classes_query = "SELECT * FROM classes WHERE teacher_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($classes_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$classes_result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - FSL</title>
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
            

        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .dashboard-header {
            display: flex;
            margin-top: 20px;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .dashboard-header h1 {
            color: #ffffff;
            font-size: 32px;
        }

        .create-class-btn {
            background: linear-gradient(to right, #8b5cf6, #ec4899);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .create-class-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
        }

        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .class-card {
            background: #232023;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid rgba(139, 92, 246, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .class-card:hover {
            border-color: rgba(139, 92, 246, 0.3);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.1);
            transform: translateY(-2px);
        }

        .class-card h3 {
            color: #8b5cf6;
            margin-bottom: 12px;
            font-size: 18px;
        }

        .class-card p {
            color: #a0aec0;
            font-size: 14px;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .class-code {
            background: rgba(139, 92, 246, 0.1);
            border: 1px solid rgba(139, 92, 246, 0.3);
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            text-align: center;
        }

        .class-code label {
            display: block;
            color: #718096;
            font-size: 12px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .class-code .code {
            color: #8b5cf6;
            font-size: 20px;
            font-weight: bold;
            font-family: monospace;
        }

        .class-stats {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .stat {
            flex: 1;
            background: rgba(139, 92, 246, 0.05);
            padding: 8px;
            border-radius: 4px;
            color: #a0aec0;
        }

        .stat-value {
            color: #8b5cf6;
            font-weight: bold;
            display: block;
        }

        .class-actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .action-btn.primary {
            background: #8b5cf6;
            color: white;
        }

        .action-btn.primary:hover {
            background: #7c3aed;
        }

        .action-btn.secondary {
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        .action-btn.secondary:hover {
            background: rgba(139, 92, 246, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #a0aec0;
        }

        .empty-state i {
            font-size: 48px;
            color: #8b5cf6;
            margin-bottom: 20px;
            display: block;
        }

        .empty-state h2 {
            color: #ffffff;
            margin-bottom: 12px;
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .classes-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include "includes/header.php" ?>

    <div class="container">
        <div class="dashboard-header">
            <h1>My Classes</h1>
            <a href="create_class.php" class="create-class-btn">
                <i class="fas fa-plus"></i> Create New Class
            </a>
        </div>

        <?php if ($classes_result->num_rows > 0): ?>
            <div class="classes-grid">
                <?php while ($class = $classes_result->fetch_assoc()): ?>
                    <?php
                    // Get student count
                    $student_count_query = "SELECT COUNT(*) as count FROM student_enrollments WHERE class_id = ?";
                    $stmt = $conn->prepare($student_count_query);
                    $stmt->bind_param("i", $class['class_id']);
                    $stmt->execute();
                    $count_result = $stmt->get_result();
                    $count_row = $count_result->fetch_assoc();
                    $student_count = $count_row['count'];
                    $stmt->close();

                    // Get completed count
                    $completed_query = "SELECT COUNT(*) as count FROM student_enrollments WHERE class_id = ? AND completed = 1";
                    $stmt = $conn->prepare($completed_query);
                    $stmt->bind_param("i", $class['class_id']);
                    $stmt->execute();
                    $completed_result = $stmt->get_result();
                    $completed_row = $completed_result->fetch_assoc();
                    $completed_count = $completed_row['count'];
                    $stmt->close();
                    ?>
                    <div class="class-card">
                        <h3><?php echo htmlspecialchars($class['class_name']); ?></h3>
                        <p><?php echo htmlspecialchars($class['description'] ?? 'No description'); ?></p>
                        
                        <div class="class-code">
                            <label>Access Code</label>
                            <div class="code"><?php echo htmlspecialchars($class['access_code']); ?></div>
                        </div>

                        <div class="class-stats">
                            <div class="stat">
                                <span>Students</span>
                                <span class="stat-value"><?php echo $student_count; ?></span>
                            </div>
                            <div class="stat">
                                <span>Completed</span>
                                <span class="stat-value"><?php echo $completed_count; ?></span>
                            </div>
                        </div>

                        <div class="class-actions">
                            <a href="teacher_class.php?class_id=<?php echo $class['class_id']; ?>" class="action-btn primary">Manage</a>
                            <!-- Updated delete button to use unified delete_handler.php -->
                            <button class="action-btn secondary" onclick="deleteClass(<?php echo $class['class_id']; ?>, '<?php echo htmlspecialchars($class['class_name']); ?>')">Delete</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-book"></i>
                <h2>No Classes Yet</h2>
                <p>Create your first class to get started with teaching!</p>
                <a href="create_class.php" class="create-class-btn" style="margin-top: 20px;">
                    <i class="fas fa-plus"></i> Create Class
                </a>
            </div>
        <?php endif; ?>
    </div>
    <script>
        function deleteClass(classId, className) {
            if (confirm(`Are you sure you want to delete the class "${className}"?`)) {
                window.location.href = `delete_handler.php?type=class&class_id=${classId}`;
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
