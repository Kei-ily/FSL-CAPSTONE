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

        /* Hide student-specific fields by default */
        .student-only {
            display: none;
        }

        .student-only.show {
            display: block;
        }

        /* Camera capture styles */
        .camera-section {
            display: none;
            margin-bottom: 20px;
            padding: 16px;
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 8px;
        }

        .camera-section.show {
            display: block;
        }

        .camera-section h3 {
            color: #8b5cf6;
            font-size: 14px;
            margin-bottom: 12px;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* Fixed video element styling - ensure it displays properly */
        #cameraPreview {
            width: 100%;
            height: 250px;
            background: #0f0f0f;
            border-radius: 6px;
            margin-bottom: 12px;
            border: 1px solid rgba(139, 92, 246, 0.3);
            object-fit: cover;
            display: none !important;
        }

        #cameraPreview.active {
            display: block !important;
        }

        #capturedImage {
            width: 100%;
            height: 250px;
            border-radius: 6px;
            margin-bottom: 12px;
            display: none;
            object-fit: cover;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        #capturedImage.show {
            display: block;
        }

        .camera-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .camera-btn {
            flex: 1;
            min-width: 100px;
            padding: 10px 12px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .camera-btn.start {
            background: rgba(34, 197, 94, 0.2);
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .camera-btn.start:hover {
            background: rgba(34, 197, 94, 0.3);
        }

        .camera-btn.capture {
            background: rgba(139, 92, 246, 0.2);
            color: #a78bfa;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        .camera-btn.capture:hover {
            background: rgba(139, 92, 246, 0.3);
        }

        .camera-btn.retake {
            background: rgba(251, 146, 60, 0.2);
            color: #fed7aa;
            border: 1px solid rgba(251, 146, 60, 0.3);
        }

        .camera-btn.retake:hover {
            background: rgba(251, 146, 60, 0.3);
        }

        .camera-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
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

            <form method="POST" action="process_signup.php" id="signupForm">
                <div class="form-container">
                    <?php
                    if (isset($_GET['error'])) {
                        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                    }
                    if (isset($_GET['success'])) {
                        echo '<div class="success-message">' . htmlspecialchars($_GET['success']) . '</div>';
                    }
                    ?>

                    <!-- Role selection moved to first position -->
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="">Select your role</option>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                        </select>
                    </div>

                    <!-- Changed label from "Student Name" to "Name" -->
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="Full name" required>
                    </div>

                    <!-- Show camera section for both students and teachers -->
                    <div class="camera-section" id="cameraSection">
                        <h3>Take Profile Picture</h3>
                        <!-- Added playsinline and autoplay attributes for better mobile support -->
                        <video id="cameraPreview" playsinline autoplay muted></video>
                        <img id="capturedImage" alt="Captured profile picture">
                        <canvas id="canvas" style="display: none;"></canvas>
                        <div class="camera-buttons">
                            <button type="button" class="camera-btn start" id="startCameraBtn" onclick="startCamera()">Start Camera</button>
                            <button type="button" class="camera-btn capture" id="captureBtn" onclick="capturePhoto()" style="display: none;">Capture Photo</button>
                            <button type="button" class="camera-btn retake" id="retakeBtn" onclick="retakePhoto()" style="display: none;">Retake</button>
                        </div>
                        <input type="hidden" id="profilePictureData" name="profile_picture">
                    </div>

                    <!-- Wrapped student-specific fields in student-only class -->
                    <div class="form-row student-only" id="studentFields">
                        <div class="form-group">
                            <label for="grade">Grade </label>
                            <input type="number" id="grade" name="grade" placeholder="e.g., 10-A" min="1" max="5" value="1">
                        </div>
                    </div>
                        <div class="form-group">
                            <label for="contact">Contact</label>
                            <input type="text" id="contact" name="contact" placeholder="09XX-XXX-XXXX">
                        </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your@email.com" required autocomplete="off">
                    </div>

                    <div class="form-group" id="addressField">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" placeholder="Street address">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                    </div>
                    <span class="show-pass">
                        <input type="checkbox"> Show Password
                    </span>

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
    let stream = null;
    let photoCaptured = false;

    const passwordInput = document.getElementById('password');
    const toggleCheckbox = document.querySelector('input[type="checkbox"]');
    const roleSelect = document.getElementById('role');
    const studentFields = document.getElementById('studentFields');
    const addressField = document.getElementById('addressField');
    const gradeInput = document.getElementById('grade');
    const contactInput = document.getElementById('contact');
    const addressInput = document.getElementById('address');
    const cameraSection = document.getElementById('cameraSection');
    const cameraPreview = document.getElementById('cameraPreview');
    const capturedImage = document.getElementById('capturedImage');
    const canvas = document.getElementById('canvas');
    const startCameraBtn = document.getElementById('startCameraBtn');
    const captureBtn = document.getElementById('captureBtn');
    const retakeBtn = document.getElementById('retakeBtn');
    const profilePictureData = document.getElementById('profilePictureData');
    const signupForm = document.getElementById('signupForm');

    toggleCheckbox.addEventListener('change', function() {
        if (this.checked) {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    });

    roleSelect.addEventListener('change', function() {
        if (this.value === 'student') {
            studentFields.classList.add('show');
            addressField.classList.add('show');
            cameraSection.classList.add('show');
            gradeInput.setAttribute('required', 'required');
            contactInput.setAttribute('required', 'required');
            addressInput.setAttribute('required', 'required');
        } else if (this.value === 'teacher') {
            studentFields.classList.remove('show');
            addressField.classList.remove('show');
            cameraSection.classList.add('show');
            gradeInput.removeAttribute('required');
            contactInput.removeAttribute('required');
            addressInput.removeAttribute('required');
        } else {
            studentFields.classList.remove('show');
            addressField.classList.remove('show');
            cameraSection.classList.remove('show');
            gradeInput.removeAttribute('required');
            contactInput.removeAttribute('required');
            addressInput.removeAttribute('required');
            stopCamera();
        }
    });

    async function startCamera() {
        try {
            // Stop any existing stream first
            stopCamera();
            
            const constraints = {
                video: { 
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false 
            };
            
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            
            // Ensure video element is visible before attaching stream
            cameraPreview.classList.add('active');
            cameraPreview.srcObject = stream;
            
            // Play the video stream
            cameraPreview.play().catch(err => {
                console.error('Error playing video:', err);
                alert('Error starting camera. Please try again.');
            });
            
            startCameraBtn.style.display = 'none';
            captureBtn.style.display = 'block';
            photoCaptured = false;
        } catch (error) {
            console.error('Camera error:', error);
            alert('Unable to access camera. Please check permissions and try again.');
            cameraPreview.classList.remove('active');
            startCameraBtn.style.display = 'block';
        }
    }

    function capturePhoto() {
        try {
            const context = canvas.getContext('2d');
            canvas.width = cameraPreview.videoWidth;
            canvas.height = cameraPreview.videoHeight;
            
            if (canvas.width === 0 || canvas.height === 0) {
                alert('Camera stream not ready. Please try again.');
                return;
            }
            
            context.drawImage(cameraPreview, 0, 0);
            
            const imageData = canvas.toDataURL('image/jpeg', 0.8);
            capturedImage.src = imageData;
            capturedImage.classList.add('show');
            profilePictureData.value = imageData;
            
            cameraPreview.classList.remove('active');
            captureBtn.style.display = 'none';
            retakeBtn.style.display = 'block';
            photoCaptured = true;
            stopCamera();
        } catch (error) {
            console.error('Capture error:', error);
            alert('Error capturing photo. Please try again.');
        }
    }

    function retakePhoto() {
        capturedImage.classList.remove('show');
        profilePictureData.value = '';
        photoCaptured = false;
        startCamera();
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        cameraPreview.classList.remove('active');
    }

    signupForm.addEventListener('submit', function(e) {
        const role = roleSelect.value;
        if ((role === 'student' || role === 'teacher') && !photoCaptured) {
            e.preventDefault();
            alert('Please capture a profile picture before signing up.');
        }
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        stopCamera();
    });
</script>
    
</body>
</html>
