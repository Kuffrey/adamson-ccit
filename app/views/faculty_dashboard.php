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
        $firstName = $faculty['first_name'];
        $lastName = $faculty['last_name'];
        $fullName = $firstName . ' ' . $lastName;
    } else {
        $firstName = "Faculty";
        $lastName = "";
        $fullName = $username;
    }
} catch (PDOException $e) {
    $firstName = "Faculty";
    $lastName = "";
    $fullName = $username;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Faculty Dashboard | CCIT CMS</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #fafbfc;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    .admin-cms-section {
      max-width: 100%;
      margin: 0;
      padding: 2rem 3rem;
    }
    
    /* Page Header */
    .page-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .page-title {
      margin: 0;
      font-size: 1.875rem;
      font-weight: 600;
      color: #111827;
    }
    
    .welcome-text {
      font-size: 1rem;
      color: #6b7280;
      margin-top: 0.5rem;
      line-height: 1.5;
    }
    
    /* Dashboard Grid */
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }
    
    .dash-card {
      background: white;
      border-radius: 12px;
      border: 1px solid #e5e7eb;
      padding: 1.5rem;
      text-decoration: none;
      transition: all 0.2s;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
      position: relative;
      overflow: hidden;
    }
    
    .dash-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      border-color: #008040;
      text-decoration: none;
    }
    
    .dash-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, #008040, #006d37);
      transform: scaleX(0);
      transition: transform 0.2s;
    }
    
    .dash-card:hover::before {
      transform: scaleX(1);
    }
    
    .dash-card h2 {
      margin: 0 0 0.75rem 0;
      font-size: 1.25rem;
      font-weight: 600;
      color: #111827;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .dash-card p {
      margin: 0;
      font-size: 0.875rem;
      color: #6b7280;
      line-height: 1.5;
    }
    
    .dash-card:hover h2 {
      color: #008040;
    }
    
    /* Icons */
    .dash-card h2::before {
      font-size: 1.5rem;
      margin-right: 0.25rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .admin-cms-section {
        padding: 1rem;
      }
      
      .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }
      
      .page-title {
        font-size: 1.5rem;
      }
      
      .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .dash-card {
        padding: 1.25rem;
      }
      
      .dash-card h2 {
        font-size: 1.125rem;
      }
    }
    
    @media (max-width: 480px) {
      .admin-cms-section {
        padding: 1rem 0.75rem;
      }
      
      .dash-card {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>
  
  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Faculty Dashboard</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($firstName[0] . $lastName[0])) ?></span>
        <span class="admin-topbar__name"><?= esc($fullName) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <!-- Page Header -->
      <div class="page-header">
        <div>
          <h1 class="page-title">
            <i class="fas fa-tachometer-alt" style="color: #008040; margin-right: 0.5rem;"></i>
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
          <h2>🔬 Submit Research</h2>
          <p>Create and submit research content for review and approval.</p>
        </a>
        <a href="?page=faculty_manage_certifications" class="dash-card">
          <h2>📄 Submit Certifications</h2>
          <p>Add or update faculty certifications. Pending dean approval.</p>
        </a>
        <a href="?page=faculty_manage_news" class="dash-card">
          <h2>📰 Submit News</h2>
          <p>Submit news articles and updates for publication.</p>
        </a>
        <a href="?page=faculty_manage_events" class="dash-card">
          <h2>📅 Submit Events</h2>
          <p>Create and submit event information for approval.</p>
        </a>
        <a href="?page=faculty_manage_announcements" class="dash-card">
          <h2>📢 Submit Announcements</h2>
          <p>Submit announcements and important notices.</p>
        </a>
        <a href="?page=faculty_portfolio" class="dash-card">
          <h2>👤 Portfolio</h2>
          <p>View and manage your faculty portfolio and profile.</p>
        </a>
      </div>
    </section>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
