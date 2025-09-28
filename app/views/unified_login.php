<?php
require_once __DIR__ . '/../lib/Auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Database authentication
    try {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Use Auth class to properly set session
            Auth::login($user);
            
            // Set additional compatibility variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect to appropriate dashboard based on role
            switch ($user['role']) {
                case 'student':
                    header('Location: ?page=student_dashboard');
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AdU-CCIT</title>
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
    <style>
        .login-container {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0b234c 0%, #1e40af 100%);
        }
        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo {
            font-size: 24px;
            font-weight: 900;
            color: #0b234c;
            margin-bottom: 8px;
        }
        .login-subtitle {
            color: #64748b;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #374151;
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .btn-login {
            width: 100%;
            background: #0b234c;
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-login:hover {
            background: #1e40af;
        }
        .error-message {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
        }
        .demo-accounts {
            margin-top: 30px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .demo-accounts h4 {
            margin: 0 0 12px 0;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
        }
        .demo-accounts ul {
            margin: 0;
            padding-left: 16px;
            font-size: 13px;
            color: #6b7280;
        }
        .demo-accounts li {
            margin-bottom: 4px;
        }
        .demo-accounts code {
            background: #e5e7eb;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Monaco', 'Menlo', monospace;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">AdU-CCIT</div>
                <div class="login-subtitle">Content Management System</div>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?= esc($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn-login">Sign In</button>
            </form>
            
            <div class="demo-accounts">
                <h4>Demo Accounts</h4>
                <ul>
                    <li><strong>Student:</strong> <code>student</code> / <code>student123</code></li>
                    <li><strong>Faculty:</strong> <code>faculty</code> / <code>faculty123</code></li>
                    <li><strong>Dean:</strong> <code>dean</code> / <code>dean123</code></li>
                    <li><strong>Admin:</strong> <code>admin</code> / <code>admin123</code></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>