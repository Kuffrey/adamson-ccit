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
<main>
  <section class="content" style="padding:20px 0">
    <div class="container">
      <h2>Login — Admin CMS</h2>
      <?php if ($error): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>
      <form method="post" action="/adamson-ccit/public/index.php?page=login_admin" autocomplete="on">
        <label>Username:
          <input type="text" name="username" required>
        </label><br>
        <label>Password:
          <input type="password" name="password" required>
        </label><br>
        <button type="submit">Login</button>
      </form>
      <p style="color:#6b7280;margin-top:8px">Administrators only.</p>
    </div>
  </section>
</main>
