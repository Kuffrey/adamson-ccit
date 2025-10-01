<?php
require_once __DIR__ . '/../lib/Auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        require_once __DIR__ . '/../config/database.php';
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            Auth::login($user);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            
            switch ($user['role']) {
                case 'student':
                    header('Location: ?page=student_profile');
                    break;
                case 'faculty':
                    header('Location: ?page=faculty_dashboard');
                    break;
                case 'dean':
                    header('Location: ?page=dean_dashboard');
                    break;
                case 'admin':
                    header('Location: ?page=admin_dashboard');
                    break;
                default:
                    header('Location: ?page=home');
            }
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    } catch (PDOException $e) {
        $error = 'Database connection failed. Please try again later.';
    }
}

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
.login-page {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: #f9fafb;
    margin: 0;
}
.login-container {
    width: 100%;
    max-width: 400px;
    position: relative;
}
.login-card {
    background: white;
    border-radius: 12px;
    padding: 40px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: 1px solid #e5e7eb;
}
.login-header {
    text-align: left;
    margin-bottom: 32px;
}
.login-title {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 8px;
    line-height: 1.2;
}
.login-subtitle {
    color: #6b7280;
    font-size: 16px;
    font-weight: 400;
    line-height: 1.4;
}
.form-group {
    margin-bottom: 20px;
}
.form-label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: #374151;
    font-size: 14px;
}
.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 16px;
    transition: border-color 0.2s;
    background: white;
    color: #374151;
}
.form-control:focus {
    outline: none;
    border-color: #00713D;
    box-shadow: 0 0 0 3px rgba(0, 113, 61, 0.1);
}
.form-control:hover {
    border-color: #9ca3af;
}
.btn-login {
    width: 100%;
    background: #00713D;
    color: white;
    border: none;
    padding: 12px 16px;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
}
.btn-login:hover {
    background: #005A2F;
}
.btn-login:active {
    background: #004225;
}
.remember-me {
    display: flex;
    align-items: center;
    margin-bottom: 24px;
}
.remember-me input {
    margin-right: 8px;
}
.remember-me label {
    font-size: 14px;
    color: #6b7280;
    cursor: pointer;
}
.forgot-password {
    text-align: center;
    margin-top: 16px;
}
.forgot-password a {
    color: #00713D;
    text-decoration: none;
    font-size: 14px;
}
.forgot-password a:hover {
    text-decoration: underline;
}
.password-help {
    text-align: center;
    margin-top: 20px;
    padding: 16px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
}
.help-text {
    color: #495057;
    font-size: 13px;
    line-height: 1.5;
    margin: 0;
}
.help-text i {
    color: #00713D;
    margin-right: 6px;
}
.help-text a {
    color: #00713D;
    text-decoration: none;
    font-weight: 500;
}
.help-text a:hover {
    text-decoration: underline;
}
.error-message {
    background: #fef2f2;
    color: #dc2626;
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 20px;
    border: 1px solid #fecaca;
    font-size: 14px;
}
@media (max-width: 480px) {
    .login-page {
        padding: 20px 15px;
    }
    .login-card {
        padding: 24px;
    }
    .login-title {
        font-size: 24px;
    }
}
</style>

<div class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-title">Welcome back</div>
                <div class="login-subtitle">Login to your AdU-CCIT account</div>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?= esc($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                
                <button type="submit" class="btn-login">Login</button>
                
                <div class="password-help">
                    <p class="help-text">
                        <i class="fas fa-info-circle"></i>
                        <strong>Forgot your password?</strong><br>
                        Visit the ITC Office or email 
                        <a href="mailto:webmaster@adamson.edu.ph">webmaster@adamson.edu.ph</a> 
                        with your full name & ID number.
                    </p>
                </div>
            </form>
        
        </div>
    </div>
</div>
