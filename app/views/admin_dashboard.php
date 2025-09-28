<?php
// Admin Dashboard Page
function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | CCIT CMS</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/admin/_admin_sidebar.php'; ?>
  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Dashboard</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Welcome back, <?= esc($username) ?> 👋</h1>
      <p class="intro">This is your content management dashboard. Use the sidebar to manage different sections of the CCIT website.</p>

      <div class="dashboard-grid">
        <a href="?page=admin_manage_homepage" class="dash-card">
          <h2>🏠 Home CMS</h2>
          <p>Manage the homepage hero, highlights, and featured content.</p>
        </a>
        <a href="?page=admin_manage_about" class="dash-card">
          <h2>ℹ️ About</h2>
          <p>Edit the About section including mission, vision, and history.</p>
        </a>
        <a href="?page=admin_manage_news" class="dash-card">
          <h2>📰 News</h2>
          <p>Publish and manage CCIT updates and announcements.</p>
        </a>
        <a href="?page=admin_manage_admission" class="dash-card">
          <h2>🎓 Admission</h2>
          <p>Update admission requirements and enrollment details.</p>
        </a>
        <a href="?page=admin_manage_programs" class="dash-card">
          <h2>📚 Programs</h2>
          <p>Manage program offerings, courses, and descriptions.</p>
        </a>
        <a href="?page=admin_manage_student" class="dash-card">
          <h2>👩‍🎓 Student</h2>
          <p>Maintain student services and resources content.</p>
        </a>
        <a href="?page=admin_manage_faculty" class="dash-card">
          <h2>👨‍🏫 Faculty</h2>
          <p>Showcase faculty profiles and achievements.</p>
        </a>
        <a href="?page=admin_manage_users" class="dash-card">
          <h2>👥 User Management</h2>
          <p>Manage system users and their roles.</p>
        </a>
      </div>
    </section>
  </main>
</div>
</body>
</html>
