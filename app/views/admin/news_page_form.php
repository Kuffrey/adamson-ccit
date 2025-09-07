<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$role = $_SESSION['user']['role'] ?? '';
if (!in_array($role, ['admin','dean'], true)) {
  header('Location: ?page=login_admin'); exit;
}
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
$username = $_SESSION['user']['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News Page Settings | CCIT CMS</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/_admin_sidebar.php'; ?>


  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">News Page Settings</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user" aria-label="Signed in user">
        <span class="admin-topbar__avatar"><?= e(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= e($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">News Page Settings</h1>
      <p class="admin-cms-section__kicker">Control banner text and the sticky announcement.</p>

      <?php if (!empty($msg)): ?>
        <p class="notice success"><?= e($msg) ?></p>
      <?php endif; ?>

      <form class="admin-cms-form" method="post">
        <div class="cms-card">
          <fieldset>
            <legend class="cms-card-legend">Subhero</legend>
            <div class="form-section">
              <div class="field">
                <label for="subhero_lead">Subhero Lead</label>
                <textarea id="subhero_lead" class="textarea" name="subhero_lead" rows="3"><?= e($settings['subhero_lead'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <div class="cms-card">
          <fieldset>
            <legend class="cms-card-legend">Announcement</legend>
            <div class="form-section">
              <div class="field">
                <label for="announcement">Announcement</label>
                <input id="announcement" class="input" type="text" name="announcement" value="<?= e($settings['announcement'] ?? '') ?>">
                <small class="field__help">Shown above the news grid (use for temporary advisories).</small>
              </div>
            </div>
          </fieldset>
        </div>

        <div class="admin-cms-form-actions">
          <button class="btn" type="submit">Save Settings</button>
        </div>
      </form>
    </section>
  </main>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const notices = document.querySelectorAll(".notice");
  if (notices.length) setTimeout(() => notices.forEach(n => n.style.display = "none"), 4000);
});
</script>
</body>
</html>
