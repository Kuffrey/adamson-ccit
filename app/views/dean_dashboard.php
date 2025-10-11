<?php
require_once __DIR__ . '/dean/_dean_sidebar.php';
require_once __DIR__ . '/../lib/Auth.php';
require_once __DIR__ . '/../models/DeanDashboard.php';

function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$user = Auth::user() ?? [];
$username = $user['username'] ?? 'Dean';
$initials = strtoupper($username[0] ?? 'D');

// Fetch dynamic dashboard data
$dashboard = new DeanDashboard();
$stats = $dashboard->getStats();
$recent = $dashboard->getRecentSubmissions();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dean Dashboard | CCIT CMS</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/dean.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
  <!-- Sidebar already included above -->

  <main class="admin-main">
    <header class="admin-topbar">
      <button class="topbar__btn hide-desktop" type="button" aria-label="Open menu" data-sb-open>☰</button>
      <span class="admin-topbar__title">CCIT Dean Dashboard</span>
      <span class="admin-topbar__spacer"></span>
    </header>

    <section class="admin-section">
      <div class="dashboard-hero">
        <div class="dashboard-hero-content">
          <div class="dashboard-hero-title">Welcome, <?= esc($username) ?> 👋</div>
          <div class="dashboard-hero-desc">Oversee college-wide submissions, monitor faculty activity, and manage approvals efficiently.</div>
          <div class="dashboard-hero-desc">Track and approve news, research, and certifications in one place.</div>
        </div>
        <a class="dashboard-hero-cta" href="?page=dean_approvals">
          <i class="fa-regular fa-circle-check"></i> Review Pending
        </a>
      </div>

      <div class="dashboard-stats-grid">
        <div class="dashboard-stat-card">
          <h3><?= esc($stats['pending_approvals']) ?></h3>
          <p>Pending Approvals</p>
        </div>
        <div class="dashboard-stat-card">
          <h3><?= esc($stats['research_submissions']) ?></h3>
          <p>Research Submissions</p>
        </div>
        <div class="dashboard-stat-card">
          <h3><?= esc($stats['upcoming_events']) ?></h3>
          <p>Upcoming Events</p>
        </div>
        <div class="dashboard-stat-card">
          <h3><?= esc($stats['certification_requests']) ?></h3>
          <p>Certification Requests</p>
        </div>
        <div class="dashboard-stat-card">
          <h3><?= esc($stats['news_articles']) ?></h3>
          <p>News Articles</p>
        </div>
        <div class="dashboard-stat-card">
          <h3><?= esc($stats['announcements']) ?></h3>
          <p>Announcements</p>
        </div>
      </div>

      <div class="dashboard-section-title"><i class="fa-regular fa-clock"></i> Recent Submissions</div>
      <table class="dashboard-table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Submitted By</th>
            <th>Type</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recent as $item): ?>
            <tr>
              <td><?= esc($item['title']) ?></td>
              <td><?= esc($item['submitted_by']) ?></td>
              <td><?= esc(ucfirst($item['type'])) ?></td>
              <td><?= esc(date('M j, Y', strtotime($item['date']))) ?></td>
              <td>
                <span class="badge<?= $item['status'] === 'Pending' ? ' badge--pending' : ' badge--approved' ?>">
                  <?= esc($item['status']) ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
  </main>
</div>
</body>
</html>
