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
/* Page spacing that plays nice with header/footer */
.page-login { padding: clamp(24px, 4vw, 48px) 20px; background:#f9fafb; }

/* Shell follows your site's container width */
.login-shell { max-width: 980px; margin-inline: auto; }

/* Split layout inside the SAME container */
.login-split{
  display: grid;
  grid-template-columns: 1.1fr 1fr; /* image | form */
  gap: 0;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(0,0,0,.06);
}

/* Left image side */
.login-media{
  position: relative;
  background: #0b234c url('/adamson-ccit/public/assets/images/ccit-banner.jpg') center / cover no-repeat;
  min-height: 420px; /* keeps it tall enough even if form is short */
}
.login-media::after{
  content:"";
  position:absolute; inset:0;
  background: linear-gradient(145deg, rgba(0,18,45,.35), rgba(0,113,61,.28));
}
.login-media__inner{
  position:relative; z-index:1; color:#fff;
  display:flex; flex-direction:column; justify-content:flex-end;
  height:100%; padding: 28px;
}
.login-media__title{ margin:0 0 4px; font-weight:800; font-size: clamp(18px,2.2vw,22px); line-height:1.25 }
.login-media__sub{ margin:0; color:#e6eef7; font-size:.95rem }

/* Right form side – reuse your existing styles, just scoped */
.login-card{
  padding: clamp(28px, 4vw, 44px);
  display:flex; flex-direction:column; justify-content:center;
}

.login-header{ margin-bottom: 24px; }
.login-title{ font-size: 28px; font-weight: 700; color: #1f2937; margin: 0 0 6px; line-height:1.2; }
.login-subtitle{ color:#6b7280; font-size:15px; }

/* Your existing controls */
.error-message{
  background:#fef2f2; color:#dc2626; padding:12px 16px; border-radius:6px;
  margin-bottom:20px; border:1px solid #fecaca; font-size:14px;
}
.form-group{ margin-bottom:20px; }
.form-label{ display:block; margin-bottom:6px; font-weight:500; color:#374151; font-size:14px; }
.form-control{
  width:100%; padding:12px 16px; border:1px solid #d1d5db; border-radius:6px;
  font-size:16px; background:#fff; color:#374151; transition:border-color .2s, box-shadow .2s;
}
.form-control:hover{ border-color:#9ca3af; }
.form-control:focus{ outline:none; border-color:#00713D; box-shadow:0 0 0 3px rgba(0,113,61,.12); }

.remember-me{ display:flex; align-items:center; gap:8px; margin: 6px 0 20px; color:#6b7280; font-size:14px; }

.btn-login{
  width:100%; background:#00713D; color:#fff; border:0; padding:12px 16px; border-radius:6px;
  font-size:16px; font-weight:600; cursor:pointer; transition: background .2s;
}
.btn-login:hover{ background:#005A2F; }
.btn-login:active{ background:#004225; }

.password-help{
  text-align:center; margin-top:20px; padding:16px; background:#f8f9fa; border:1px solid #e9ecef; border-radius:8px;
}
.help-text{ color:#495057; font-size:13px; line-height:1.5; margin:0; }
.help-text i{ color:#00713D; margin-right:6px; }
.help-text a{ color:#00713D; text-decoration:none; font-weight:500; }
.help-text a:hover{ text-decoration:underline; }

/* Responsive: stack on small screens */
@media (max-width: 820px){
  .login-split{ grid-template-columns: 1fr; }
  .login-media{ min-height: 220px; }
  .login-card{ padding: 24px; }
  .login-title{ font-size: 24px; }
}
</style>

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

      <!-- Form side (your original form preserved) -->
      <section class="login-card" aria-label="Login form">
        <div class="login-header">
          <div class="login-title">Welcome back</div>
          <div class="login-subtitle">Login to your AdU-CCIT account</div>
        </div>

        <?php if (isset($error)): ?>
          <div class="error-message"><?= esc($error) ?></div>
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
