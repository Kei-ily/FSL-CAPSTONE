<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Filipino Sign Language</title>
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
            width: 100%;
            min-height: 100vh;
            max-width: 500px;
            margin-top: 20px;
        }

        main{
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #232023;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            border: 1px solid rgba(139, 92, 246, 0.1);
        }

        .header {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(236, 72, 153, 0.1));
            padding: 40px 30px;
            text-align: center;
            border-bottom: 1px solid rgba(139, 92, 246, 0.2);
        }

        .logo {
            font-size: 48px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            gap: 20px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
            background: linear-gradient(to right, #8b5cf6, #ec4899);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .header p {
            font-size: 14px;
            color: #a0aec0;
            font-weight: 400;
        }

        .form-container {
            padding: 32px 30px;
        }

        .form-group {
            margin-bottom: 20px;
            margin-top: 8px;
        }

        .form-group:first-child {
            margin-top: 0;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #e2e8f0;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group select {
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

        .form-group input::placeholder {
            color: #718096;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #8b5cf6;
            background: rgba(139, 92, 246, 0.1);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .form-group select option {
            background: #232023;
            color: #ffffff;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-row .form-group {
            margin-bottom: 0;
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
            margin-top: 12px;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
            background: linear-gradient(to right, #7c3aed, #db2777);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .footer-text {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: #a0aec0;
        }

        .footer-text a {
            color: #8b5cf6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .footer-text a:hover {
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

        @media (max-width: 480px) {
            .form-container {
                padding: 24px 20px;
            }

            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .show-pass {
            color: #a0aec0;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: -10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
     <?php include "includes/header.php" ?>
     <main>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="logo">🤟</div>
                <h1>Create Account</h1>
                <p>Join our Filipino Sign Language community</p>
            </div>

            <form method="POST" action="process_signup.php">
                <div class="form-container">
                    <?php
                    if (isset($_GET['error'])) {
                        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                    }
                    if (isset($_GET['success'])) {
                        echo '<div class="success-message">' . htmlspecialchars($_GET['success']) . '</div>';
                    }
                    ?>

                    <div class="form-group">
                        <label for="student_name">Student Name</label>
                        <input type="text" id="student_name" name="student_name" placeholder="Full name" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="grade">Grade & Section</label>
                            <input type="text" id="grade" name="grade" placeholder="e.g., 10-A" required>
                        </div>
                        <div class="form-group">
                            <label for="contact">Contact</label>
                            <input type="text" id="contact" name="contact" placeholder="09XX-XXX-XXXX" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your@email.com" required autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" placeholder="Street address" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                        
                    </div>
                    <span class="show-pass">
                            <input type="checkbox"> Show Password
                        </span>

                    <!-- Added role selection dropdown -->
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="">Select your role</option>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                        </select>
                    </div>

                    <button type="submit" class="submit-btn">Sign Up</button>

                    <div class="footer-text">
                        Already have an account? <a href="login.php">Log in here</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </main>
<script>
    const passwordInput = document.getElementById('password');
    const toggleCheckbox = document.querySelector('input[type="checkbox"]');        
    toggleCheckbox.addEventListener('change', function() {
        if (this.checked) {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    });
</script>
    
</body>
</html>
