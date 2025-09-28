<?php
require_once __DIR__ . '/../../app/lib/Auth.php';
Auth::requireRole(['faculty'], '/adamson-ccit/public/index.php?page=login_faculty');
function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$user = Auth::user() ?? [];
$username = $user['username'] ?? 'Faculty';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Faculty Dashboard | CCIT CMS</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>
  
  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Faculty Dashboard</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'F')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Welcome, <?= esc($username) ?> 👋</h1>
      <p class="intro">This is your dashboard to manage research and certifications. Submit requests for approval to the Dean.</p>

      <div class="dashboard-grid">
        <a href="?page=faculty_manage_research" class="dash-card">
          <h2>🔬 Manage Research</h2>
          <p>Create and submit research content for review.</p>
        </a>
        <a href="?page=faculty_manage_certifications" class="dash-card">
          <h2>📄 Manage Certifications</h2>
          <p>Add or update faculty certifications. Pending dean approval.</p>
        </a>
      </div>
    </section>
  </main>
</div>
</body>
</html>
