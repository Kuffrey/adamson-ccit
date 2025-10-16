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
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username']  = $user['username'];

            switch ($user['role']) {
                case 'student': header('Location: ?page=student_profile'); break;
                case 'faculty': header('Location: ?page=faculty_dashboard'); break;
                case 'dean':    header('Location: ?page=dean_dashboard'); break;
                case 'admin':   header('Location: ?page=admin_dashboard'); break;
                default:        header('Location: ?page=home');
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Login | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<main class="page-login">
  <div class="login-shell">
    <div class="login-split">

      <!-- Image side -->
      <aside class="login-media" aria-hidden="true">
        <div class="login-media__inner">
          <h2 class="login-media__title">Welcome to AdU-CCIT</h2>
          <p class="login-media__sub">Access your portal securely and continue where you left off.</p>
        </div>
      </aside>

      <!-- Form side -->
      <section class="login-card" aria-label="Login form">
        <div class="login-header">
          <h1 class="login-title">Welcome back</h1>
          <p class="login-subtitle">Login to your AdU-CCIT account</p>
        </div>

        <?php if (isset($error)): ?>
          <div class="error-message" role="alert"><?= esc($error) ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
          <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" autocomplete="username" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" autocomplete="current-password" required>
          </div>

          <label class="remember-me">
            <input type="checkbox" id="remember" name="remember">
            <span>Remember me</span>
          </label>

          <button type="submit" class="btn-login">Login</button>

          <div class="password-help" role="note" aria-live="polite">
            <p class="help-text">
              <i class="fas fa-info-circle"></i>
              <strong>Forgot your password?</strong><br>
              Visit the ITC Office or email
              <a href="mailto:webmaster@adamson.edu.ph">webmaster@adamson.edu.ph</a>
              with your full name &amp; ID number.
            </p>
          </div>
        </form>
      </section>

    </div>
  </div>
</main>

</body>
</html>
