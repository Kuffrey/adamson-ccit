<?php
require_once __DIR__ . '/../../lib/Auth.php';
Auth::requireRole(['faculty'], '/adamson-ccit/public/index.php?page=login');

if (!function_exists('esc')) {
    function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

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
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/dean.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .dashboard-hero {
      background: linear-gradient(90deg, #f6faff 60%, #e9f4ff 100%);
      border-radius: 16px;
      box-shadow: 0 4px 24px rgba(0,128,201,0.07);
      padding: 1.5rem 2rem;
      margin-bottom: 1.5rem;
      border: 1px solid #e5e7eb;
      display: flex;
      align-items: center;
      gap: 2rem;
    }
    .dashboard-hero-content {
      flex: 1;
    }
    .dashboard-hero-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--navy);
      margin-bottom: 0.3rem;
    }
    .dashboard-hero-desc {
      font-size: 1rem;
      color: #374151;
      margin-bottom: 0.2rem;
    }
    .dashboard-hero-cta {
      background: var(--blue);
      color: #fff;
      border-radius: 10px;
      padding: 0.8rem 2rem;
      font-weight: 700;
      font-size: 1.05rem;
      text-decoration: none;
      box-shadow: 0 2px 8px rgba(0,128,201,0.08);
      transition: background 0.2s;
      display: flex;
      align-items: center;
      gap: 0.7rem;
    }
    .dashboard-hero-cta:hover {
      background: #0369a1;
      color: #fff;
      text-decoration: none;
    }
    .dashboard-stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1.2rem;
      margin-bottom: 2rem;
    }
    .dashboard-stat-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 1px 4px rgba(0,128,201,0.04);
      border: 1px solid #e5e7eb;
      padding: 1.2rem 1rem;
      text-align: center;
    }
    .dashboard-stat-card h3 {
      font-size: 2rem;
      font-weight: 800;
      color: var(--navy);
      margin-bottom: 0.2rem;
    }
    .dashboard-stat-card p {
      font-size: 1rem;
      color: #374151;
      margin: 0;
      font-weight: 600;
    }
    .dashboard-section-title {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--navy);
      margin-top: 2rem;
      margin-bottom: 0.7rem;
      letter-spacing: 0.01em;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .dashboard-table {
      width: 100%;
      border-radius: 10px;
      overflow: hidden;
      font-size: .95rem;
      background: #fff;
      box-shadow: 0 1px 3px rgba(0,0,0,.03);
      border: 1px solid #e5e7eb;
      margin-bottom: 0;
      border-collapse: collapse;
    }
    .dashboard-table th {
      background: #f1f5f9;
      color: var(--navy);
      font-weight: 700;
      border-bottom: 2px solid #e5e7eb;
      padding: 16px 20px;
      text-transform: uppercase;
      font-size: .8rem;
      letter-spacing: .04em;
    }
    .dashboard-table td {
      padding: 16px 20px;
      border-bottom: 1px solid #f1f5f9;
      color: var(--ink);
      vertical-align: middle;
      font-weight: 500;
    }
    .dashboard-table tbody tr:hover {
      background: #f8fafc;
    }
    .dashboard-table tbody tr:last-child td {
      border-bottom: none;
    }
    .badge--pending {
      background: #fef3c7;
      color: #92400e;
      border-radius: 8px;
      padding: 0.2rem 0.8rem;
      font-weight: 700;
      font-size: .9rem;
    }
    .badge--approved {
      background: #dcfce7;
      color: #166534;
      border-radius: 8px;
      padding: 0.2rem 0.8rem;
      font-weight: 700;
      font-size: .9rem;
    }
    @media (max-width: 900px) {
      .dashboard-hero { flex-direction: column; align-items: flex-start; padding: 1rem 0.3rem; }
      .dashboard-hero-title { font-size: 1.1rem; }
      .dashboard-stats-grid { grid-template-columns: 1fr; gap: 0.7rem; }
      .dashboard-stat-card h3 { font-size: 1.3rem; }
      .dashboard-table th, .dashboard-table td { padding: 0.7rem 0.5rem; font-size: 0.95rem; }
    }
    @media (max-width: 600px) {
      .dashboard-hero { padding: 0.5rem 0.1rem; }
      .dashboard-hero-title { font-size: 0.92rem; }
      .dashboard-table th, .dashboard-table td { padding: 0.15rem 0.2rem; font-size: 0.9rem; }
    }
  </style>
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/_faculty_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <button class="topbar__btn hide-desktop" type="button" aria-label="Open menu" data-sb-open>☰</button>
      <span class="admin-topbar__title">Faculty Dashboard</span>
      <span class="admin-topbar__spacer"></span>
      <div class="admin-topbar__user">
        
      </div>
    </header>

    <section class="admin-section">
      <div class="dashboard-hero">
        <div class="dashboard-hero-content">
          <div class="dashboard-hero-title">Welcome, <?= esc($fullName) ?> 👋</div>
          <div class="dashboard-hero-desc">
            This is your dashboard to manage submissions and portfolio. Submit requests for approval to the Dean.
          </div>
          <div class="dashboard-hero-desc">
            Track your research, certifications, and news submissions in one place.
          </div>
        </div>
        <a class="dashboard-hero-cta" href="?page=faculty_portfolio">
          <i class="fa-regular fa-user"></i> View Portfolio
        </a>
      </div>

      <div class="dashboard-stats-grid">
        <a href="?page=faculty_manage_research" class="dashboard-stat-card">
          <h3><i class="fas fa-microscope"></i></h3>
          <p>Submit Research</p>
        </a>
        <a href="?page=faculty_manage_certifications" class="dashboard-stat-card">
          <h3><i class="fas fa-certificate"></i></h3>
          <p>Submit Certifications</p>
        </a>
        <a href="?page=faculty_manage_news" class="dashboard-stat-card">
          <h3><i class="fas fa-newspaper"></i></h3>
          <p>Submit News</p>
        </a>
        <a href="?page=faculty_portfolio" class="dashboard-stat-card">
          <h3><i class="fas fa-user"></i></h3>
          <p>Portfolio</p>
        </a>
      </div>

      <!-- Optionally add a section for recent submissions if you have the data -->
      <!--
      <div class="dashboard-section-title"><i class="fa-regular fa-clock"></i> Recent Submissions</div>
      <table class="dashboard-table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Type</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php // foreach ($recent as $item): ?>
            <tr>
              <td><?php // esc($item['title']) ?></td>
              <td><?php // esc(ucfirst($item['type'])) ?></td>
              <td><?php // esc(date('M j, Y', strtotime($item['date']))) ?></td>
              <td>
                <span class="badge<?php // $item['status'] === 'Pending' ? ' badge--pending' : ' badge--approved' ?>">
                  <?php // esc($item['status']) ?>
                </span>
              </td>
            </tr>
          <?php // endforeach; ?>
        </tbody>
      </table>
      -->
    </section>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
