<?php
require_once __DIR__ . '/../../app/lib/Auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Demo only — replace with DB lookup
    if ($username === 'admin' && $password === 'admin123') {
        Auth::login(['username' => 'admin', 'role' => 'admin']);
        $dest = '/adamson-ccit/public/index.php?page=admin_dashboard';
        if (!headers_sent()) { header('Location: ' . $dest); exit; }
        echo '<script>location.href=' . json_encode($dest) . ';</script>'; exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<main class="auth page-login">
  <section class="content">
    <div class="container auth__wrap">
      <div class="auth__card" role="form" aria-labelledby="auth-title">
        <header class="auth__head">
          <h2 id="auth-title" class="auth__title">Login — Admin CMS</h2>
          <p class="auth__sub">Administrators only</p>
        </header>

        <?php if (!empty($error)): ?>
          <div class="alert alert--danger" role="alert" aria-live="polite">
            <span class="alert__icon" aria-hidden="true"></span>
            <span class="alert__text"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
          </div>
        <?php endif; ?>

        <form class="auth__form" method="post" action="/adamson-ccit/public/index.php?page=login_admin" autocomplete="on">
          <div class="field">
            <label for="username" class="field__label">Username</label>
            <input id="username" class="input" type="text" name="username" required autocomplete="username" />
          </div>

          <div class="field">
            <label for="password" class="field__label">Password</label>
            <input id="password" class="input" type="password" name="password" required autocomplete="current-password" />
          </div>

          <button type="submit" class="btn btn--solid auth__submit">Login</button>
        </form>

        <p class="auth__note">Need access? Contact the CCIT web admin.</p>
      </div>
    </div>
  </section>
</main>
