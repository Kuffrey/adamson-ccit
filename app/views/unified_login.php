<?php
/**
 * login.php — Secure login (CSRF, throttling, no auto-login)
 * "Remember me" now only remembers the USERNAME via a cookie. No persistent auth.
 */

require_once __DIR__ . '/../lib/Auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/* ---------------- Helpers ---------------- */
function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function b64u($bytes){ return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '='); }
function b64u_rand($len){ return b64u(random_bytes($len)); }
function hash_str($s){ return hash('sha256', $s); }

const REMEMBER_USERNAME_COOKIE = 'ccit_ru';
const REMEMBER_USERNAME_DAYS   = 180;

/** set/clear username cookie */
function set_username_cookie(?string $username): void {
    if ($username === null || $username === '') {
        setcookie(REMEMBER_USERNAME_COOKIE, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => false, // must be readable by browser for autofill if you ever switch to JS; still fine as it holds non-sensitive data
            'samesite' => 'Lax',
        ]);
        return;
    }
    setcookie(REMEMBER_USERNAME_COOKIE, $username, [
        'expires'  => time() + (REMEMBER_USERNAME_DAYS * 86400),
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => false, // username only
        'samesite' => 'Lax',
    ]);
}

/* ---------------- CSRF ---------------- */
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = b64u_rand(32);
}

/* ---------------- Basic throttle (per session+IP) ---------------- */
function throttle_key(){ return 'login_throttle_' . hash_str(($_SERVER['REMOTE_ADDR'] ?? '0') . session_id()); }
function throttle_maybe_block(){
    $d = $_SESSION[throttle_key()] ?? ['fails'=>0,'next'=>0];
    if (time() < ($d['next'] ?? 0)) {
        $wait = max(1, ($d['next'] - time()));
        throw new RuntimeException("Please wait {$wait}s before trying again.");
    }
}
function throttle_on_fail(){
    $k = throttle_key();
    $d = $_SESSION[$k] ?? ['fails'=>0,'next'=>0];
    $d['fails'] = min(10, ($d['fails'] ?? 0) + 1);
    $d['next']  = time() + min(60, 2 ** $d['fails']);
    $_SESSION[$k] = $d;
}
function throttle_on_success(){ unset($_SESSION[throttle_key()]); }

/* ---------------- Database ---------------- */
require_once __DIR__ . '/../config/database.php';
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (Throwable $e) {
    error_log('[LOGIN PDO] ' . $e->getMessage());
    $pdo = null;
}

/* ---------------- Prefill username from cookie ---------------- */
$rememberedUsername = isset($_COOKIE[REMEMBER_USERNAME_COOKIE]) ? (string)$_COOKIE[REMEMBER_USERNAME_COOKIE] : '';
$rememberChecked    = $rememberedUsername !== '';

/* ---------------- Handle POST login ---------------- */
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // small constant delay to reduce timing oracles
    usleep(200000); // 200ms

    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $remember = isset($_POST['remember']); // remember username only
    $csrf     = (string)($_POST['csrf'] ?? '');

    try {
        if (!$pdo) throw new RuntimeException('Service temporarily unavailable.');

        throttle_maybe_block();

        // CSRF
        if (!hash_equals($_SESSION['csrf'] ?? '', $csrf)) {
            throw new RuntimeException('Invalid request.');
        }

        // Basic validation
        if ($username === '' || $password === '') {
            throw new RuntimeException('Invalid username or password.');
        }
        if (strlen($username) > 64 || strlen($password) > 1024) {
            throw new RuntimeException('Invalid username or password.');
        }

        // Fetch user
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Verify; if no user, verify against dummy hash to normalize timing
        $valid = false;
        if ($user) {
            $valid = password_verify($password, $user['password']);
        } else {
            password_verify($password, '$2y$10$ABCDEFGHIJKLMNOPQRSTUVabcdefghijklmno12/34abcdEfghijklmno'); // never true
        }

        if (!$valid) {
            throttle_on_fail();
            throw new RuntimeException('Invalid username or password.');
        }

        throttle_on_success();

        // Login session
        Auth::login($user);
        session_regenerate_id(true);
        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['username']  = $user['username'];

        // Remember ONLY the username (no persistent auth)
        if ($remember) {
            set_username_cookie($username);
        } else {
            set_username_cookie(null); // clear if unchecked
        }

        // Redirect by role
        $dest = [
            'student' => '?page=student_profile',
            'faculty' => '?page=faculty_dashboard',
            'dean'    => '?page=dean_dashboard',
            'admin'   => '?page=admin_dashboard',
        ][strtolower($user['role'])] ?? '?page=home';

        header("Location: {$dest}");
        exit;

    } catch (Throwable $e) {
        $error = $e->getMessage(); // already generic
        // Keep the typed username visible after failure:
        $rememberedUsername = $username;
        $rememberChecked    = $rememberChecked || $remember;
    }
}
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

        <?php if (!empty($error)): ?>
          <div class="error-message" role="alert"><?= esc($error) ?></div>
        <?php endif; ?>

        <form method="post" novalidate autocomplete="on">
          <input type="hidden" name="csrf" value="<?= esc($_SESSION['csrf'] ?? '') ?>">

          <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input
              type="text"
              id="username"
              name="username"
              class="form-control"
              placeholder="Enter your username"
              autocomplete="username"
              maxlength="64"
              value="<?= esc($rememberedUsername) ?>"
              required
            >
          </div>

          <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input
              type="password"
              id="password"
              name="password"
              class="form-control"
              placeholder="Enter your password"
              autocomplete="current-password"
              required
            >
          </div>

          <label class="remember-me">
            <input type="checkbox" id="remember" name="remember" <?= $rememberChecked ? 'checked' : '' ?>>
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
