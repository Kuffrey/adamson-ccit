<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$messageType = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once __DIR__ . '/../config/database.php';
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        // Validate form data
        $errors = [];
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $birthday = $_POST['birthday'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $role = $_POST['role'] ?? 'student';
        $studentNumber = trim($_POST['student_number'] ?? '');
        $program = trim($_POST['program'] ?? '');
        $yearLevel = $_POST['year_level'] ?? '';
        
        // Validation
        if (empty($username)) $errors[] = 'Username is required';
        if (empty($password)) $errors[] = 'Password is required';
        if ($password !== $confirmPassword) $errors[] = 'Passwords do not match';
        if (empty($firstName)) $errors[] = 'First name is required';
        if (empty($lastName)) $errors[] = 'Last name is required';
        if (empty($email)) $errors[] = 'Email is required';
        if (empty($birthday)) $errors[] = 'Birthday is required';
        if ($role === 'student') {
            if (empty($studentNumber)) $errors[] = 'Student number is required for students';
            if (empty($program)) $errors[] = 'Program is required for students';
            if (empty($yearLevel)) $errors[] = 'Year level is required for students';
        }
        
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = 'Username already exists';
        }
        
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already exists';
        }
        
        if (empty($errors)) {
            // Insert new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (
                    username, password, first_name, last_name, email, phone, 
                    birthday, gender, student_number, program, year_level, 
                    role, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', CURRENT_TIMESTAMP)
            ");
            
            $stmt->execute([
                $username, $hashedPassword, $firstName, $lastName, $email, $phone,
                $birthday, $gender, 
                ($role === 'student' ? $studentNumber : null),
                ($role === 'student' ? $program : null),
                ($role === 'student' ? $yearLevel : null),
                $role
            ]);
            
            $message = 'Account created successfully! You can now login.';
            $messageType = 'success';
        } else {
            $message = implode('<br>', $errors);
            $messageType = 'error';
        }
        
    } catch (Exception $e) {
        $message = 'Registration failed: ' . $e->getMessage();
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Adamson University CCIT</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .register-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo img {
            width: 80px;
            height: 80px;
        }
        
        .logo h1 {
            color: #333;
            font-size: 24px;
            margin-top: 10px;
        }
        
        .logo p {
            color: #666;
            font-size: 14px;
        }
        
        .message {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .message.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .message.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .role-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .student-fields {
            display: none;
            margin-top: 15px;
        }
        
        .student-fields.active {
            display: block;
        }
        
        .register-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            margin-top: 20px;
        }
        
        .register-btn:hover {
            transform: translateY(-1px);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .register-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold;">AU</div>
            <h1>Create Account</h1>
            <p>Adamson University - College of Computer and Information Technology</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name *</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label>Birthday *</label>
                    <input type="date" name="birthday" value="<?= htmlspecialchars($_POST['birthday'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender">
                        <option value="">Select Gender</option>
                        <option value="male" <?= ($_POST['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= ($_POST['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="other" <?= ($_POST['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" required>
                </div>
            </div>
            
            <div class="role-section">
                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" id="role" onchange="toggleStudentFields()" required>
                        <option value="student" <?= ($_POST['role'] ?? 'student') === 'student' ? 'selected' : '' ?>>Student</option>
                        <option value="faculty" <?= ($_POST['role'] ?? '') === 'faculty' ? 'selected' : '' ?>>Faculty</option>
                    </select>
                </div>
                
                <div class="student-fields" id="studentFields">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Student Number *</label>
                            <input type="text" name="student_number" value="<?= htmlspecialchars($_POST['student_number'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Year Level *</label>
                            <select name="year_level">
                                <option value="">Select Year</option>
                                <option value="1st Year" <?= ($_POST['year_level'] ?? '') === '1st Year' ? 'selected' : '' ?>>1st Year</option>
                                <option value="2nd Year" <?= ($_POST['year_level'] ?? '') === '2nd Year' ? 'selected' : '' ?>>2nd Year</option>
                                <option value="3rd Year" <?= ($_POST['year_level'] ?? '') === '3rd Year' ? 'selected' : '' ?>>3rd Year</option>
                                <option value="4th Year" <?= ($_POST['year_level'] ?? '') === '4th Year' ? 'selected' : '' ?>>4th Year</option>
                                <option value="Graduate" <?= ($_POST['year_level'] ?? '') === 'Graduate' ? 'selected' : '' ?>>Graduate</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Program *</label>
                        <select name="program">
                            <option value="">Select Program</option>
                            <option value="BS Computer Science" <?= ($_POST['program'] ?? '') === 'BS Computer Science' ? 'selected' : '' ?>>BS Computer Science</option>
                            <option value="BS Information Technology" <?= ($_POST['program'] ?? '') === 'BS Information Technology' ? 'selected' : '' ?>>BS Information Technology</option>
                            <option value="BS Computer Engineering" <?= ($_POST['program'] ?? '') === 'BS Computer Engineering' ? 'selected' : '' ?>>BS Computer Engineering</option>
                            <option value="BS Information Systems" <?= ($_POST['program'] ?? '') === 'BS Information Systems' ? 'selected' : '' ?>>BS Information Systems</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="register-btn">Create Account</button>
        </form>
        
        <div class="login-link">
            <p>Already have an account? <a href="?page=login">Login here</a></p>
        </div>
    </div>
    
    <script>
        function toggleStudentFields() {
            const role = document.getElementById('role').value;
            const studentFields = document.getElementById('studentFields');
            
            if (role === 'student') {
                studentFields.classList.add('active');
            } else {
                studentFields.classList.remove('active');
            }
        }
        
        // Initialize on page load
        toggleStudentFields();
    </script>
</body>
</html>