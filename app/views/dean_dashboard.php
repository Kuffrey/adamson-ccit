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
      <p class="intro">Manage content, approvals, and college operations from your dashboard.</p>

      <div class="dashboard-grid">
        <a href="?page=dean_manage_news" class="dash-card">
          <h2>📰 News Articles</h2>
          <p>Create and publish news articles.</p>
        </a>

        <a href="?page=dean_manage_events" class="dash-card">
          <h2>📅 Events</h2>
          <p>Schedule and manage college events.</p>
        </a>

        <a href="?page=dean_manage_announcements" class="dash-card">
          <h2>📢 Announcements</h2>
          <p>Post important college announcements.</p>
        </a>

        <a href="?page=dean_manage_research" class="dash-card">
          <h2>🔬 Research Publications</h2>
          <p>Oversee faculty research and publications.</p>
        </a>

        <a href="?page=dean_manage_certifications" class="dash-card">
          <h2>🎓 Faculty Certifications</h2>
          <p>Manage faculty certifications and credentials.</p>
        </a>

        <a href="?page=dean_approvals" class="dash-card">
          <h2>✅ All Pending Approvals</h2>
          <p>Review all pending submissions.</p>
        </a>

        <a href="?page=dean_certifications" class="dash-card">
          <h2>🎓 Certification Approvals</h2>
          <p>Approve faculty certification requests.</p>
        </a>

        <a href="?page=dean_research_approvals" class="dash-card">
          <h2>🔬 Research Approvals</h2>
          <p>Review faculty research submissions.</p>
        </a>

        <a href="?page=dean_news_approvals" class="dash-card">
          <h2>📰 News Approvals</h2>
          <p>Approve news and content submissions.</p>
        </a>

        <a href="?page=dean_pending_submissions" class="dash-card">
          <h2>📥 Submission Queue</h2>
          <p>Manage the submission workflow.</p>
        </a>

        <a href="?page=dean_logs" class="dash-card">
          <h2>📋 Activity Logs</h2>
          <p>Monitor system activity and logs.</p>
        </a>
      </div>
    </section>
  </main>
</div>
</body>
</html>