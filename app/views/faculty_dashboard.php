<?php
require_once __DIR__ . '/../../app/lib/Auth.php';
Auth::requireRole(['faculty'], '/adamson-ccit/public/index.php?page=login');

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Get current user info
$user = Auth::user() ?? [];
$username = $user['username'] ?? 'Faculty';

// Get faculty's information from database
try {
    $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $faculty = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($faculty) {
        $firstName = $faculty['first_name'] ?? 'Faculty';
        $lastName  = $faculty['last_name']  ?? '';
        $fullName  = trim($firstName . ' ' . $lastName);
    } else {
        $firstName = "Faculty"; $lastName = ""; $fullName = $username;
    }
} catch (PDOException $e) {
    $firstName = "Faculty"; $lastName = ""; $fullName = $username;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Faculty Dashboard | CCIT CMS</title>

  <!-- NEW: unified faculty theme to match sidebar -->
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/faculty.css" />

  <!-- Vendors -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Faculty Dashboard</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper(($firstName[0] ?? 'F') . ($lastName[0] ?? ''))) ?></span>
        <span class="admin-topbar__name"><?= esc($fullName) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <!-- Page Header -->
      <div class="page-header">
        <div>
          <h1 class="page-title">
            <i class="fas fa-tachometer-alt page-title__icon"></i>
            Welcome, <?= esc($fullName) ?> 👋
          </h1>
          <p class="welcome-text">
            This is your dashboard to manage submissions and portfolio. Submit requests for approval to the Dean.
          </p>
        </div>
      </div>

      <!-- Dashboard Cards -->
      <div class="dashboard-grid">
        <a href="?page=faculty_manage_research" class="dash-card">
          <h2><i class="fas fa-microscope"></i> Submit Research</h2>
          <p>Create and submit research content for review and approval.</p>
        </a>

        <a href="?page=faculty_manage_certifications" class="dash-card">
          <h2><i class="fas fa-certificate"></i> Submit Certifications</h2>
          <p>Add or update faculty certifications. Pending dean approval.</p>
        </a>

        <a href="?page=faculty_manage_news" class="dash-card">
          <h2><i class="fas fa-newspaper"></i> Submit News</h2>
          <p>Submit news articles and updates for publication.</p>
        </a>

        <a href="?page=faculty_portfolio" class="dash-card">
          <h2><i class="fas fa-user"></i> Portfolio</h2>
          <p>View and manage your faculty portfolio and profile.</p>
        </a>
      </div>
    </section>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
