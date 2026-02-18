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

// Get current tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'students';

// Fetch data based on tab
$students = [];
$teachers = [];
$admins = [];
$classes = [];
$tasks = [];

$students_with_pics = [];
$teachers_with_pics = [];

if ($tab === 'students') {
    $result = $conn->query("SELECT ar.`acc_no`, ar.name, ar.grade, ar.contact, ar.email, ar.address, ar.role, ar.profile_picture, c.class_name, c.class_id FROM account_record ar LEFT JOIN student_enrollments se ON ar.`acc_no` = se.student_id LEFT JOIN classes c ON se.class_id = c.class_id WHERE ar.role = 'student' ORDER BY ar.`acc_no` ASC");
    $students_with_pics = $result->fetch_all(MYSQLI_ASSOC);
    $students = $students_with_pics;
} elseif ($tab === 'teachers') {
    $result = $conn->query("SELECT `acc_no`, name, grade, contact, email, address, role, profile_picture FROM account_record WHERE role = 'teacher' ORDER BY `acc_no` ASC");
    $teachers_with_pics = $result->fetch_all(MYSQLI_ASSOC);
    $teachers = $teachers_with_pics;
} elseif ($tab === 'admins') {
    $result = $conn->query("SELECT `acc_no`, name, grade, contact, email, address, role FROM account_record WHERE role = 'admin' ORDER BY `acc_no` ASC");
    $admins = $result->fetch_all(MYSQLI_ASSOC);
} elseif ($tab === 'classes') {
    $result = $conn->query("
        SELECT c.class_id, c.class_name, c.description, c.access_code, c.status, c.created_at,
               ar.name as teacher_name, 
               COUNT(se.enrollment_id) as student_count
        FROM classes c
        LEFT JOIN account_record ar ON c.teacher_id = ar.`acc_no`
        LEFT JOIN student_enrollments se ON c.class_id = se.class_id
        GROUP BY c.class_id
        ORDER BY c.class_id ASC
    ");
    $classes = $result->fetch_all(MYSQLI_ASSOC);
} elseif ($tab === 'tasks') {
    $result = $conn->query("
        SELECT ct.task_id, ct.task_name, ct.task_description, ct.created_at,
               c.class_name, ar.name as teacher_name,
               COUNT(DISTINCT stp.student_id) as total_students,
               SUM(CASE WHEN stp.completed = 1 THEN 1 ELSE 0 END) as completed_count
        FROM class_tasks ct
        LEFT JOIN classes c ON ct.class_id = c.class_id
        LEFT JOIN account_record ar ON c.teacher_id = ar.`acc_no`
        LEFT JOIN student_task_progress stp ON ct.task_id = stp.task_id
        GROUP BY ct.task_id
        ORDER BY ct.task_id ASC
    ");
    $tasks = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - FSL</title>
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .admin-header {
            
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background: #232023;
        }

        .admin-header h1 {
            color: white;
            margin-bottom: 10px;
        }

        .admin-header p {
            color: #666;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            flex-wrap: wrap;
            background: #232023;
        }

        .tab-btn {
            padding: 10px 20px;
            border: none;
            background: #f0f0f0;
            color: #333;
            cursor: pointer;
            border-radius: 4px;
            font-weight: 500;
            transition: 0.3s;
        }

        .tab-btn:hover {
            background: #e0e0e0;
        }

        .tab-btn.active {
            background: #7e2ddb;
            color: white;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #0D1117;
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        th:first-child {
            width: 50px;
            text-align: center;
        }

        td:first-child {
            width: 50px;
            text-align: center;
            font-weight: 600;
            color: #8b5cf6;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #1a1919ff;
            background: #232023;
            color: white;
        }

        tbody tr:hover {
            background: #0D1117;
        }

        .profile-pic-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-pic-thumb {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .profile-pic-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-completed {
            background: #cce5ff;
            color: #004085;
        }

        .status-archived {
            background: #f8d7da;
            color: #721c24;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #7e2ddb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: 600;   
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #606060;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
             color: white;
        }

        /* Added action button styles */
        .action-btn {
            display: inline-block;
            padding: 6px 12px;
            margin: 2px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            border: none;
            background: none;
        }

        .edit-btn {
            background: #007bff;
            color: white;
        }

        .edit-btn:hover {
            background: #0056b3;
        }

        .password-btn {
            background: #ffc107;
            color: #333;
        }

        .password-btn:hover {
            background: #e0a800;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background: #c82333;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: #0D1117;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }

        .close-btn:hover {
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

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #7e2ddb;
            box-shadow: 0 0 0 3px rgba(126, 45, 219, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .btn-submit {
            flex: 1;
            padding: 12px;
            background: #7e2ddb;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #6a1fb8;
        }

        .btn-cancel {
            flex: 1;
            padding: 12px;
            background: #f0f0f0;
            color: #333;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
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
    </style>
</head>
<body>

    <div class="container">
        <a href="fsl.php" class="back-link">← Back to Home</a>

        <div class="admin-header">
            <h1>Admin Panel</h1>
            <p>Manage and monitor all students, teachers, classes, and tasks</p>
        </div>

        <div class="tabs">
            <button class="tab-btn <?php echo $tab === 'students' ? 'active' : ''; ?>" onclick="location.href='?tab=students'">Students</button>
            <button class="tab-btn <?php echo $tab === 'teachers' ? 'active' : ''; ?>" onclick="location.href='?tab=teachers'">Teachers</button>
            <button class="tab-btn <?php echo $tab === 'admins' ? 'active' : ''; ?>" onclick="location.href='?tab=admins'">Admins</button>
            <button class="tab-btn <?php echo $tab === 'classes' ? 'active' : ''; ?>" onclick="location.href='?tab=classes'">Classes</button>
            <button class="tab-btn <?php echo $tab === 'tasks' ? 'active' : ''; ?>" onclick="location.href='?tab=tasks'">Tasks</button>
        </div>

        <!-- Students Tab -->
        <?php if ($tab === 'students'): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Section</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($students) > 0): ?>
                        <?php $counter = 1; foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $counter; ?></td>
                            <td>
                                <div class="profile-pic-cell">
                                    <div class="profile-pic-thumb">
                                        <?php if (!empty($student['profile_picture'])): ?>
                                            <img src="<?php echo htmlspecialchars($student['profile_picture']); ?>" alt="Profile">
                                        <?php else: ?>
                                            👨‍🎓
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo is_numeric($student['grade']) ? htmlspecialchars($student['grade']) : '<span style="color: #a0aec0;">-</span>'; ?></td>
                            <td>
                                <?php if (!empty($student['class_name'])): ?>
                                    <span class="status-badge status-active"><?php echo htmlspecialchars($student['class_name']); ?></span>
                                <?php else: ?>
                                    <span style="color: #a0aec0; font-size: 12px;">Not enrolled</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo htmlspecialchars($student['contact']); ?></td>
                            <td><?php echo htmlspecialchars($student['address']); ?></td>
                            <td>
                                <button class="action-btn edit-btn" onclick="openEditModal(<?php echo $student['acc_no']; ?>, 'student')">Edit</button>
                                <button class="action-btn password-btn" onclick="openPasswordModal(<?php echo $student['acc_no']; ?>)">Password</button>
                                <button class="action-btn delete-btn" onclick="deleteUser(<?php echo $student['acc_no']; ?>, 'student')">Remove</button>
                            </td>
                        </tr>
                        <?php $counter++; endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="no-data">No students found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Teachers Tab -->
        <?php if ($tab === 'teachers'): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($teachers) > 0): ?>
                        <?php $counter = 1; foreach ($teachers as $teacher): ?>
                        <tr>
                            <td><?php echo $counter; ?></td>
                            <td>
                                <div class="profile-pic-cell">
                                    <div class="profile-pic-thumb">
                                        <?php if (!empty($teacher['profile_picture'])): ?>
                                            <img src="<?php echo htmlspecialchars($teacher['profile_picture']); ?>" alt="Profile">
                                        <?php else: ?>
                                            👨‍🏫
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($teacher['name']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['grade']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['contact']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['address']); ?></td>
                            <td>
                                <button class="action-btn edit-btn" onclick="openEditModal(<?php echo $teacher['acc_no']; ?>, 'teacher')">Edit</button>
                                <button class="action-btn password-btn" onclick="openPasswordModal(<?php echo $teacher['acc_no']; ?>)">Password</button>
                                <button class="action-btn delete-btn" onclick="deleteUser(<?php echo $teacher['acc_no']; ?>, 'teacher')">Remove</button>
                            </td>
                        </tr>
                        <?php $counter++; endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="no-data">No tasks found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Admins Tab -->
        <?php if ($tab === 'admins'): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($admins) > 0): ?>
                        <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($admin['acc_no']); ?></td>
                            <td><?php echo htmlspecialchars($admin['name']); ?></td>
                            <td><?php echo htmlspecialchars($admin['grade']); ?></td>
                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td><?php echo htmlspecialchars($admin['contact']); ?></td>
                            <td><?php echo htmlspecialchars($admin['address']); ?></td>
                            <td>
                                <button class="action-btn edit-btn" onclick="openEditModal(<?php echo $admin['acc_no']; ?>, 'admin')">Edit</button>
                                <button class="action-btn password-btn" onclick="openPasswordModal(<?php echo $admin['acc_no']; ?>)">Password</button>
                                <button class="action-btn delete-btn" onclick="deleteUser(<?php echo $admin['acc_no']; ?>, 'admin')">Remove</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="no-data">No admins found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Classes Tab -->
        <?php if ($tab === 'classes'): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Class ID</th>
                        <th>Class Name</th>
                        <th>Teacher</th>
                        <th>Access Code</th>
                        <th>Students</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($classes) > 0): ?>
                        <?php foreach ($classes as $class): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($class['class_id']); ?></td>
                            <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($class['teacher_name'] ?? 'N/A'); ?></td>
                            <td><strong><?php echo htmlspecialchars($class['access_code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($class['student_count']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($class['status']); ?>">
                                    <?php echo ucfirst($class['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($class['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="no-data">No classes found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Tasks Tab -->
        <?php if ($tab === 'tasks'): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Task ID</th>
                        <th>Task Name</th>
                        <th>Class</th>
                        <th>Teacher</th>
                        <th>Total Students</th>
                        <th>Completed</th>
                        <th>Progress</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($tasks) > 0): ?>
                        <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['task_id']); ?></td>
                            <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                            <td><?php echo htmlspecialchars($task['class_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($task['teacher_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($task['total_students']); ?></td>
                            <td><?php echo htmlspecialchars($task['completed_count'] ?? 0); ?></td>
                            <td>
                                <div class="progress-bar">
                                    <?php 
                                        $percentage = $task['total_students'] > 0 ? round(($task['completed_count'] / $task['total_students']) * 100) : 0;
                                    ?>
                                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%">
                                        <?php echo $percentage; ?>%
                                    </div>
                                </div>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($task['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="no-data">No tasks found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>


    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit User</h2>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <div id="editMessage"></div>
            <form id="editForm">
                <input type="hidden" id="editUserId" name="user_id">
                <input type="hidden" name="action" value="edit">
                
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" id="editName" name="name" required>
                </div>

                <div class="form-group">
                    <label>Grade</label>
                    <input type="number" id="editGrade" name="grade" min="0" max="12">
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" id="editEmail" name="email" required>
                </div>

                <div class="form-group">
                    <label>Contact</label>
                    <input type="text" id="editContact" name="contact">
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea id="editAddress" name="address"></textarea>
                </div>

                <div class="form-group">
                    <label>Role *</label>
                    <select id="editRole" name="role" required>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-submit">Save Changes</button>
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Password Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Change Password</h2>
                <button class="close-btn" onclick="closePasswordModal()">&times;</button>
            </div>
            <div id="passwordMessage"></div>
            <form id="passwordForm">
                <input type="hidden" id="passwordUserId" name="user_id">
                <input type="hidden" name="action" value="password">
                
                <div class="form-group">
                    <label>New Password *</label>
                    <input type="password" id="newPassword" name="new_password" required>
                </div>

                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" id="confirmPassword" name="confirm_password" required>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-submit">Change Password</button>
                    <button type="button" class="btn-cancel" onclick="closePasswordModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        
        function openEditModal(userId, role) {
            // Show loading state
            const editModal = document.getElementById('editModal');
            editModal.classList.add('show');
            
            fetch('admin_api.php?action=get_user&user_id=' + userId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.user) {
                        document.getElementById('editUserId').value = userId;
                        document.getElementById('editName').value = data.user.name || '';
                        document.getElementById('editGrade').value = data.user.grade || '';
                        document.getElementById('editEmail').value = data.user.email || '';
                        document.getElementById('editContact').value = data.user.contact || '';
                        document.getElementById('editAddress').value = data.user.address || '';
                        document.getElementById('editRole').value = data.user.role || 'student';
                    } else {
                        document.getElementById('editMessage').innerHTML = '<div class="message error">Error loading user data. Please try again.</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('editMessage').innerHTML = '<div class="message error">Failed to load user data. Please try again.</div>';
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('show');
            document.getElementById('editForm').reset();
            document.getElementById('editMessage').innerHTML = '';
        }

        function openPasswordModal(userId) {
            document.getElementById('passwordUserId').value = userId;
            document.getElementById('passwordModal').classList.add('show');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.remove('show');
            document.getElementById('passwordForm').reset();
            document.getElementById('passwordMessage').innerHTML = '';
        }

        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('admin_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('editMessage');
                if (data.success) {
                    messageDiv.innerHTML = '<div class="message success">User updated successfully!</div>';
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    messageDiv.innerHTML = '<div class="message error">' + (data.message || 'An error occurred') + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('editMessage').innerHTML = '<div class="message error">Failed to update user. Please try again.</div>';
            });
        });

        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('admin_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('passwordMessage');
                if (data.success) {
                    messageDiv.innerHTML = '<div class="message success">Password changed successfully!</div>';
                    setTimeout(() => {
                        closePasswordModal();
                    }, 1500);
                } else {
                    messageDiv.innerHTML = '<div class="message error">' + (data.message || 'An error occurred') + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('passwordMessage').innerHTML = '<div class="message error">Failed to change password. Please try again.</div>';
            });
        });

        function deleteUser(userId, role) {
            if (confirm('Are you sure you want to delete this ' + role + '? This action cannot be undone.')) {
                fetch('admin_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=delete&user_id=' + userId + '&role=' + role
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'An error occurred'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete user. Please try again.');
                });
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editModal');
            const passwordModal = document.getElementById('passwordModal');
            if (event.target == editModal) {
                closeEditModal();
            }
            if (event.target == passwordModal) {
                closePasswordModal();
            }
        }
    </script>
</body>
</html>
