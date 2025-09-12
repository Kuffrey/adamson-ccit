<?php
function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? 'Dean';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dean Dashboard | CCIT CMS</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css" />
</head>
<body>
<div class="admin-cms-layout">

  <!-- Include the dean sidebar instead of admin sidebar -->
  <?php include __DIR__ . '/dean/_dean_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Dean Dashboard</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'D')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Welcome back, <?= esc($username) ?> 👋</h1>
      <p class="intro">Manage news, faculty profiles, research, certifications, and portfolios here.</p>

      <div class="dashboard-grid">
        <a href="?page=dean_manage_news" class="dash-card">
          <h2>📰 News & Announcements</h2>
          <p>Publish and manage updates and announcements.</p>
        </a>

        <a href="?page=dean_manage_events" class="dash-card">
          <h2>📅 Events</h2>
          <p>Create and update upcoming college events.</p>
        </a>

        <a href="?page=dean_manage_faculty_profiles" class="dash-card">
          <h2>👨‍🏫 Faculty Profiles</h2>
          <p>Manage faculty biographies and details.</p>
        </a>

        <a href="?page=dean_manage_faculty_research" class="dash-card">
          <h2>🔬 Faculty Research</h2>
          <p>Track and display faculty research projects and publications.</p>
        </a>

        <a href="?page=dean_manage_faculty_certifications" class="dash-card">
          <h2>🎓 Faculty Certifications</h2>
          <p>Manage certifications and licenses of faculty members.</p>
        </a>

        <a href="?page=dean_manage_faculty_portfolio" class="dash-card">
          <h2>📁 Faculty Portfolio</h2>
          <p>Showcase faculty portfolios similar to student portfolios.</p>
        </a>
      </div>
    </section>
  </main>
</div>
</body>
</html>
