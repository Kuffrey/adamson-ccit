<?php
require_once __DIR__ . '/../../app/lib/Auth.php';
require_once __DIR__ . '/../../app/models/Model.php';
require_once __DIR__ . '/../../app/models/FacultyPortfolio.php';

Auth::requireRole(['dean'], '/adamson-ccit/public/index.php?page=login');
function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$user = Auth::user() ?? [];
$username = $user['username'] ?? 'Dean';

// Get dean's information from database
try {
    $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT id, first_name, last_name FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $dean = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($dean) {
        $deanId = (int)$dean['id'];
        $firstName = $dean['first_name'];
        $lastName = $dean['last_name'];
        $fullName = "Dr. " . $firstName . " " . $lastName;
    } else {
        $deanId = null;
        $firstName = "Dean";
        $lastName = "";
        $fullName = $username;
    }
} catch (PDOException $e) {
    $deanId = null;
    $firstName = "Dean";
    $lastName = "";
    $fullName = $username;
}

class _DBX extends Model { public function d(){ return parent::db(); } }
$_db = (new _DBX())->d();

// Fetch companies for issuing organizations dropdown
$companies = $_db->query("SELECT id, name FROM companies ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch dean certifications / portfolio items using the model
$portfolioItems = [];
$portfolioStats = ['total' => 0, 'active' => 0, 'expired' => 0];

if ($deanId) {
    $portfolioItems = FacultyPortfolio::getByUserId($deanId);
    $portfolioStats = FacultyPortfolio::getStats($deanId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dean Portfolio | Dean Dashboard</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css" />
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/student-profile.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #fafbfc;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    body.modal-open {
      overflow: hidden;
    }
    
    /* Override any inherited styles for checkboxes specifically */
    input[type="checkbox"] {
      width: 16px !important;
      height: 16px !important;
      padding: 0 !important;
      margin: 0 !important;
      border: 1px solid #d1d5db !important;
      border-radius: 3px !important;
      background: white !important;
      transform: none !important;
      -webkit-appearance: checkbox !important;
      -moz-appearance: checkbox !important;
      appearance: checkbox !important;
    }
    
    input[type="checkbox"]:checked {
      background-color: #0080c9 !important;
      border-color: #0080c9 !important;
    }
    
    /* Ensure checkbox labels are properly styled */
    label[for="no-expire"] {
      font-size: 0.9rem !important;
      cursor: pointer !important;
      margin-left: 0.5rem !important;
      font-weight: normal !important;
    }
    
    /* Adapt student profile styles for dean dashboard */
    .admin-cms-section {
      max-width: 100%;
      margin: 0;
      padding: 2rem 3rem;
    }
    
    .page-dean {
      max-width: 100%;
      margin: 0;
      padding: 0;
      background: transparent;
    }
    
    .profile-container {
      max-width: 100%;
    }
    
    .profile-header {
      background: linear-gradient(135deg, #0080c9 0%, #2c3e50 100%);
      border-radius: 16px;
      margin-bottom: 2rem;
    }
    
    .profile-header-content {
      display: flex;
      align-items: center;
      gap: 2rem;
      padding: 2rem;
    }
    
    .profile-avatar .avatar-circle {
      width: 120px;
      height: 120px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      font-weight: 700;
      color: white;
      border: 4px solid rgba(255, 255, 255, 0.3);
    }
    
    .profile-info {
      flex: 1;
      color: white;
    }
    
    .profile-name {
      font-size: 2rem;
      font-weight: 700;
      margin: 0 0 0.5rem 0;
      color: white;
    }
    
    .profile-title {
      font-size: 1.2rem;
      font-weight: 500;
      margin: 0 0 0.5rem 0;
      color: rgba(255, 255, 255, 0.9);
    }
    
    .profile-location {
      font-size: 1rem;
      margin: 0 0 1rem 0;
      color: rgba(255, 255, 255, 0.8);
    }
    
    .profile-stats {
      display: flex;
      align-items: center;
      gap: 1rem;
      flex-wrap: wrap;
    }
    
    .stat-item {
      color: rgba(255, 255, 255, 0.9);
      font-weight: 500;
    }
    
    .stat-divider {
      color: rgba(255, 255, 255, 0.6);
    }
    
    .profile-content {
      display: grid;
      grid-template-columns: 1fr;
      gap: 2rem;
    }
    
    .profile-card {
      background: white;
      border-radius: 16px;
      padding: 1.5rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      border: 1px solid #e5e7eb;
    }
    
    .card-header-flex {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }
    
    .card-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: #111827;
      margin: 0;
    }
    
    .card-subtitle {
      color: #6b7280;
      font-size: 0.9rem;
      margin: 0.5rem 0 1rem 0;
    }
    
    .card-actions {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .add-cert-btn {
      background: #0080c9;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 0.5rem 1rem;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .add-cert-btn:hover {
      background: #2c3e50;
    }
    
    .add-icon {
      font-size: 1.1rem;
      font-weight: 700;
    }
    
    .view-toggle {
      display: flex;
      border-radius: 6px;
      border: 1px solid #e5e7eb;
      overflow: hidden;
    }
    
    .view-btn {
      background: white;
      border: none;
      padding: 0.5rem;
      cursor: pointer;
      color: #6b7280;
      transition: all 0.2s;
    }
    
    .view-btn.active {
      background: #0080c9;
      color: white;
    }
    
    .view-btn:hover:not(.active) {
      background: #f3f4f6;
    }
    
    .certifications-list.compact-view {
      display: block;
    }
    
    .certifications-grid.card-view {
      display: none;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1.5rem;
    }
    
    .cert-list-item {
      display: flex;
      align-items: flex-start;
      padding: 1rem 0;
      border-bottom: 1px solid #e5e7eb;
      position: relative;
      transition: background 0.2s;
    }
    
    .cert-list-item:hover {
      background: #f9fafb;
      border-radius: 8px;
      margin: 0 -1rem;
      padding: 1rem;
    }
    
    .cert-icon-small {
      font-size: 1.5rem;
      margin-right: 1rem;
      flex-shrink: 0;
    }
    
    .cert-content-compact {
      flex: 1;
    }
    
    .cert-header-compact {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 0.5rem;
    }
    
    .cert-name-compact {
      font-size: 1.1rem;
      font-weight: 600;
      color: #111827;
      margin: 0;
    }
    
    .cert-issuer-compact {
      color: #6b7280;
      font-size: 0.9rem;
      margin: 0 0 0.5rem 0;
    }
    
    .cert-meta-compact {
      display: flex;
      align-items: center;
      gap: 1rem;
      flex-wrap: wrap;
    }
    
    .meta-item {
      color: #6b7280;
      font-size: 0.85rem;
    }
    
    .cert-hover-actions {
      position: absolute;
      top: 1rem;
      right: 1rem;
      display: none;
      align-items: center;
      gap: 0.5rem;
    }
    
    .cert-list-item:hover .cert-hover-actions {
      display: flex;
    }
    
    .action-icon-btn {
      background: white;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      padding: 0.5rem;
      cursor: pointer;
      color: #6b7280;
      transition: all 0.2s;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .action-icon-btn:hover {
      background: #f3f4f6;
      color: #111827;
    }
    
    .action-icon-btn.delete-btn:hover {
      background: #fef2f2;
      color: #dc2626;
      border-color: #fecaca;
    }
    
    .empty-state-modern {
      text-align: center;
      padding: 3rem 2rem;
      color: #6b7280;
    }
    
    .empty-icon {
      font-size: 3rem;
      margin-bottom: 1rem;
      display: block;
    }
    
    .empty-state-modern h4 {
      font-size: 1.25rem;
      font-weight: 600;
      color: #111827;
      margin: 0 0 0.5rem 0;
    }
    
    .empty-state-modern p {
      margin: 0 0 1.5rem 0;
      max-width: 400px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .btn-primary {
      background: #0080c9;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s;
    }
    
    .btn-primary:hover {
      background: #2c3e50;
    }
    
    /* Modal styles */
    .modal {
      position: fixed !important;
      inset: 0 !important;
      display: none !important;
      align-items: center !important;
      justify-content: center !important;
      background: rgba(0,0,0,.55) !important;
      z-index: 10000 !important;
    }
    
    .modal.show {
      display: flex !important;
    }
    
    .modal-content {
      width: min(800px, 95vw);
      max-height: 90vh;
      overflow: auto;
      background: #ffffff;
      color: #111827;
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 24px 60px rgba(0,0,0,.18);
    }
    
    .modal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 1rem;
    }
    
    .modal-close {
      background: none;
      border: none;
      color: #6b7280;
      font-size: 1.5rem;
      cursor: pointer;
    }
    
    .modal-close:hover {
      color: #111827;
    }
    
    @media (max-width: 768px) {
      .admin-cms-section {
        padding: 1rem;
      }
      
      .profile-header-content {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
      }
      
      .profile-avatar .avatar-circle {
        width: 100px;
        height: 100px;
        font-size: 2rem;
      }
      
      .profile-name {
        font-size: 1.5rem;
      }
      
      .view-toggle {
        display: none;
      }
    }
  </style>
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/dean/_dean_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Dean Portfolio</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($firstName[0] . $lastName[0])) ?></span>
        <span class="admin-topbar__name"><?= esc($fullName) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <!-- Toast Notifications -->
      <?php if (!empty($_GET['saved']) || !empty($_GET['deleted']) || !empty($_GET['error'])): ?>
        <div class="toast <?= !empty($_GET['error']) ? 'toast--danger' : (!empty($_GET['deleted']) ? 'toast--danger' : 'toast--success') ?>" style="position: fixed; top: 20px; right: 20px; z-index: 10001; background: <?= !empty($_GET['error']) ? '#fee2e2' : (!empty($_GET['deleted']) ? '#fef2f2' : '#f0f9ff') ?>; color: <?= !empty($_GET['error']) ? '#dc2626' : (!empty($_GET['deleted']) ? '#dc2626' : '#1d4ed8') ?>; padding: 1rem 1.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid <?= !empty($_GET['error']) ? '#fecaca' : (!empty($_GET['deleted']) ? '#fecaca' : '#dbeafe') ?>; font-weight: 500; max-width: 400px;">
          <?php if (!empty($_GET['error'])): ?>
            <?= htmlspecialchars($_GET['error']) ?>
          <?php elseif (!empty($_GET['deleted'])): ?>
            Certification deleted successfully.
          <?php elseif ($_GET['saved'] == '1'): ?>
            Certification saved successfully!
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <div class="page-dean">
        <div class="profile-container">
          <!-- Profile Header -->
          <div class="profile-header">
            <div class="profile-header-content">
              <div class="profile-avatar">
                <div class="avatar-circle">
                  <?= esc(strtoupper($firstName[0] . $lastName[0])) ?>
                </div>
              </div>
              <div class="profile-info">
                <h1 class="profile-name"><?= esc($fullName) ?></h1>
                <p class="profile-title">Dean of CCIT</p>
                <p class="profile-location">📍 Adamson University - CCIT</p>
                <div class="profile-stats">
                  <span class="stat-item"><?= $portfolioStats['total'] ?> Certification<?= $portfolioStats['total'] !== 1 ? 's' : '' ?></span>
                  <span class="stat-divider">•</span>
                  <span class="stat-item"><?= $portfolioStats['active'] ?> Active</span>
                  <?php if ($portfolioStats['expired'] > 0): ?>
                    <span class="stat-divider">•</span>
                    <span class="stat-item"><?= $portfolioStats['expired'] ?> Expired</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="profile-content">
            <!-- Licenses & Certifications Section -->
            <div class="profile-card certifications-section">
              <div class="card-header-flex">
                <div class="card-header-left">
                  <h3 class="card-title">Licenses & Certifications</h3>
                  <p class="card-subtitle">
                    <?= $portfolioStats['total'] ?> certification<?= $portfolioStats['total'] !== 1 ? 's' : '' ?>
                    <?php if ($portfolioStats['active'] > 0): ?>
                      • <?= $portfolioStats['active'] ?> active
                    <?php endif; ?>
                    <?php if ($portfolioStats['expired'] > 0): ?>
                      • <span style="color: #dc2626;"><?= $portfolioStats['expired'] ?> expired</span>
                    <?php endif; ?>
                  </p>
                </div>
                <div class="card-actions">
                  <div class="view-toggle">
                    <button class="view-btn active" data-view="compact" title="Compact View">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
                      </svg>
                    </button>
                    <button class="view-btn" data-view="card" title="Card View">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 4H4c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM10 15H4c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2v-3c0-1.1-.9-2-2-2zM21 4h-6c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM21 15h-6c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2v-3c0-1.1-.9-2-2-2z"/>
                      </svg>
                    </button>
                  </div>
                  <button class="add-cert-btn" data-open-cert-modal>
                    <span class="add-icon">+</span>
                    Add certification
                  </button>
                </div>
              </div>

              <?php if (empty($portfolioItems)): ?>
                <div class="empty-state-modern">
                  <div class="empty-icon">🎓</div>
                  <h4>Showcase your expertise</h4>
                  <p>Add professional certifications to highlight your academic credentials and leadership qualifications as Dean.</p>
                  <button class="btn-primary" data-open-cert-modal>Add your first certification</button>
                </div>
              <?php else: ?>
                <!-- Compact List View (Default) -->
                <div class="certifications-list compact-view" id="compact-view">
                  <?php foreach ($portfolioItems as $item): ?>
                    <?php
                      $isExpired = false; // You can add expiration logic here if needed
                      $issueDate = ($item['issue_month'] && $item['issue_year']) 
                        ? date("M Y", mktime(0,0,0,$item['issue_month'],1,$item['issue_year'])) 
                        : 'N/A';
                      $expireDate = (!$item['expires'] && $item['expire_month'] && $item['expire_year']) 
                        ? date("M Y", mktime(0,0,0,$item['expire_month'],1,$item['expire_year']))
                        : null;
                    ?>
                    <div class="cert-list-item <?= $isExpired ? 'expired' : '' ?>">
                      <div class="cert-icon-small">🎓</div>
                      <div class="cert-content-compact">
                        <div class="cert-header-compact">
                          <h4 class="cert-name-compact"><?= htmlspecialchars($item['name']) ?></h4>
                        </div>
                        <p class="cert-issuer-compact"><?= htmlspecialchars($item['company_name']) ?></p>
                        <div class="cert-meta-compact">
                          <span class="meta-item">Issued <?= $issueDate ?></span>
                          <?php if ($expireDate): ?>
                            <span class="meta-item">Expires <?= $expireDate ?></span>
                          <?php else: ?>
                            <span class="meta-item">No expiry</span>
                          <?php endif; ?>
                          <?php if (!empty($item['credential_id'])): ?>
                            <span class="meta-item credential-id">ID: <?= htmlspecialchars($item['credential_id']) ?></span>
                          <?php endif; ?>
                        </div>
                        <?php if (!empty($item['credential_url'])): ?>
                          <div class="cert-actions-compact">
                            <a href="<?= htmlspecialchars($item['credential_url']) ?>" target="_blank" 
                               style="color: #0080c9; text-decoration: none; font-size: 0.9rem; font-weight: 500;">
                              Show credential →
                            </a>
                          </div>
                        <?php endif; ?>
                      </div>
                      <!-- Hover Action Buttons -->
                      <div class="cert-hover-actions">
                        <button 
                          class="action-icon-btn edit-btn" 
                          onclick='editPortfolioItem(<?= json_encode($item, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>)'
                          title="Edit certification">
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                          </svg>
                        </button>
                        <button 
                          class="action-icon-btn delete-btn" 
                          onclick='deletePortfolioItem(<?= (int)$item['id'] ?>, "<?= htmlspecialchars($item['name']) ?>")'
                          title="Delete certification">
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3,6 5,6 21,6"></polyline>
                            <path d="m19,6v14a2,2 0 0,1-2,2H7a2,2 0 0,1-2-2V6m3,0V4a2,2 0 0,1,2-2h4a2,2 0 0,1,2,2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                          </svg>
                        </button>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <!-- Card Grid View -->
                <div class="certifications-grid card-view" id="card-view">
                  <?php foreach ($portfolioItems as $item): ?>
                    <div class="certification-card">
                      <div class="cert-header">
                        <div class="cert-logo">🎓</div>
                        <div class="cert-hover-actions">
                          <button 
                            class="action-icon-btn edit-btn" 
                            onclick='editPortfolioItem(<?= json_encode($item, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>)'
                            title="Edit certification">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                              <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                          </button>
                          <button 
                            class="action-icon-btn delete-btn" 
                            onclick='deletePortfolioItem(<?= (int)$item['id'] ?>, "<?= htmlspecialchars($item['name']) ?>")'
                            title="Delete certification">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                              <polyline points="3,6 5,6 21,6"></polyline>
                              <path d="m19,6v14a2,2 0 0,1-2,2H7a2,2 0 0,1-2-2V6m3,0V4a2,2 0 0,1,2-2h4a2,2 0 0,1,2,2v2"></path>
                            </svg>
                          </button>
                        </div>
                      </div>
                      <div class="cert-content">
                        <h4 class="cert-name"><?= htmlspecialchars($item['name']) ?></h4>
                        <p class="cert-issuer"><?= htmlspecialchars($item['company_name']) ?></p>
                        <?php if (!empty($item['credential_url'])): ?>
                          <a href="<?= htmlspecialchars($item['credential_url']) ?>" target="_blank" 
                             style="color: #0080c9; text-decoration: none; font-size: 0.9rem;">
                            Show credential →
                          </a>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Portfolio Modal -->
  <div id="certModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>License or Certification</h3>
        <button type="button" class="modal-close">✕</button>
      </div>

      <form id="certForm" action="index.php?page=dean_portfolio_save" method="POST">
        <input type="hidden" name="mode" value="create">
        <input type="hidden" name="id" value="">

        <!-- First row: Name and Organization -->
        <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom: 1rem;">
          <div>
            <label>Name*</label>
            <input type="text" name="name" required style="width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-size:.95rem;">
          </div>
          <div>
            <label>Issuing organization*</label>
            <select name="company_id" required style="width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-size:.95rem;">
              <option value="">Select company…</option>
              <?php foreach ($companies as $co): ?>
                <option value="<?= (int)$co['id'] ?>"><?= htmlspecialchars($co['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Second row: Issue date -->
        <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr 2fr; gap:20px; margin-bottom: 1rem;">
          <div>
            <label>Issue month</label>
            <select name="issue_month" style="width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-size:.95rem;">
              <option value="">Month</option><?php for ($m=1;$m<=12;$m++) echo "<option value=\"$m\">$m</option>"; ?>
            </select>
          </div>
          <div>
            <label>Issue year</label>
            <input type="number" name="issue_year" min="1950" max="<?= date('Y')+1 ?>" style="width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-size:.95rem;">
          </div>
          <div style="display: flex; align-items: center; padding-bottom: 10px;">
            <div class="form-row" style="margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem;">
              <input type="checkbox" id="no-expire" name="no_expire" value="1" checked>
              <label for="no-expire">This credential does not expire</label>
            </div>
          </div>
        </div>

        <!-- Third row: Expiration date -->
        <div id="expireRow" class="form-row disabled" style="display:grid; grid-template-columns:1fr 1fr 2fr; gap:20px; margin-bottom: 1rem; opacity:.55; pointer-events:none;">
          <div>
            <label>Expiration month</label>
            <select name="expire_month" style="width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-size:.95rem;">
              <option value="">Month</option><?php for ($m=1;$m<=12;$m++) echo "<option value=\"$m\">$m</option>"; ?>
            </select>
          </div>
          <div>
            <label>Expiration year</label>
            <input type="number" name="expire_year" min="<?= date('Y')-1 ?>" max="<?= date('Y')+15 ?>" style="width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-size:.95rem;">
          </div>
          <div></div>
        </div>

        <!-- Fourth row: Credential details and visibility -->
        <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-bottom: 1rem;">
          <div>
            <label>Credential ID</label>
            <input type="text" name="credential_id" style="width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-size:.95rem;">
          </div>
          <div>
            <label>Credential URL</label>
            <input type="url" name="credential_url" placeholder="https://..." style="width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-size:.95rem;">
          </div>
          <div>
            <label>Visibility</label>
            <select name="visibility" style="width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-size:.95rem;">
              <option value="public">Public</option>
              <option value="private">Only me</option>
            </select>
          </div>
        </div>

        <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:10px; margin-top:14px;">
          <button type="button" class="btn btn--outline modal-close" style="background:none; border:1px solid #e5e7eb; color:#6b7280; padding:10px 16px; border-radius:6px; cursor:pointer;">Exit</button>
          <button type="submit" class="btn btn--solid" style="background:#0080c9; color:white; border:none; padding:10px 16px; border-radius:6px; cursor:pointer;">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Delete Certification</h3>
        <button type="button" class="modal-close" onclick="closeDeleteModal()">✕</button>
      </div>
      <div class="modal-body" style="margin: 1rem 0;">
        <p>Are you sure you want to delete this certification?</p>
        <p><strong id="deleteCertName">Certification Name</strong></p>
        <p style="color: #dc2626; font-size: 0.9rem;">This action cannot be undone.</p>
      </div>
      <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:10px; margin-top:14px;">
        <button type="button" class="btn btn--outline" onclick="closeDeleteModal()" style="background:none; border:1px solid #e5e7eb; color:#6b7280; padding:10px 16px; border-radius:6px; cursor:pointer;">Cancel</button>
        <button type="button" class="btn btn--danger" onclick="confirmDelete()" id="deleteConfirmBtn" style="background:#dc2626; color:white; border:none; padding:10px 16px; border-radius:6px; cursor:pointer;">Delete</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const certModal = document.getElementById('certModal');
  const certForm = document.getElementById('certForm');
  const noExpire = document.getElementById('no-expire');
  const expireRow = document.getElementById('expireRow');
  const deleteModal = document.getElementById('deleteModal');
  let currentDeleteId = null;

  // View toggle functionality
  const viewButtons = document.querySelectorAll('.view-btn');
  const compactView = document.getElementById('compact-view');
  const cardView = document.getElementById('card-view');

  viewButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const view = btn.getAttribute('data-view');
      
      // Update active button
      viewButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      
      // Toggle views
      if (view === 'compact') {
        compactView.style.display = 'block';
        cardView.style.display = 'none';
      } else {
        compactView.style.display = 'none';
        cardView.style.display = 'grid';
      }
    });
  });

  function openModal() {
    certModal.classList.add('show');
    document.body.classList.add('modal-open');
    const first = certForm.querySelector('input[name="name"]');
    if (first) setTimeout(() => first.focus(), 50);
  }

  function closeModal() {
    certModal.classList.remove('show');
    document.body.classList.remove('modal-open');
    if (!certForm.id.value) certForm.reset();
  }

  // Open modal
  document.querySelectorAll('[data-open-cert-modal]').forEach(btn => {
    btn.addEventListener('click', () => {
      certForm.mode.value = 'create';
      certForm.id.value = '';
      certForm.reset();
      noExpire.checked = true;
      expireRow.classList.add('disabled');
      expireRow.style.opacity = '0.55';
      expireRow.style.pointerEvents = 'none';
      openModal();
    });
  });

  // Close modal
  document.querySelectorAll('.modal-close').forEach(b => b.addEventListener('click', closeModal));
  certModal.addEventListener('click', (e) => { if (e.target === certModal) closeModal(); });
  
  // Escape key
  document.addEventListener('keydown', (e) => { 
    if (e.key === 'Escape') {
      if (certModal.classList.contains('show')) closeModal();
      if (deleteModal.classList.contains('show')) closeDeleteModal();
    }
  });

  // Expiration toggle
  if (noExpire && expireRow) {
    const toggle = () => {
      if (noExpire.checked) {
        expireRow.classList.add('disabled');
        expireRow.style.opacity = '0.55';
        expireRow.style.pointerEvents = 'none';
      } else {
        expireRow.classList.remove('disabled');
        expireRow.style.opacity = '1';
        expireRow.style.pointerEvents = 'auto';
      }
    };
    noExpire.addEventListener('change', toggle);
    toggle();
  }

  // Prevent double-submit
  certForm.addEventListener('submit', () => {
    const btn = certForm.querySelector('button[type="submit"]');
    if (btn) { 
      btn.disabled = true; 
      btn.textContent = 'Saving…'; 
    }
  });

  // Edit function
  window.editPortfolioItem = function(item) {
    certForm.mode.value = 'update';
    certForm.id.value = item.id || '';
    certForm.name.value = item.name || '';
    certForm.company_id.value = item.company_id || '';
    certForm.issue_month.value = item.issue_month || '';
    certForm.issue_year.value = item.issue_year || '';
    
    noExpire.checked = (String(item.expires) === '0' || String(item.expires) === 'false');
    if (noExpire.checked) {
      expireRow.classList.add('disabled');
      expireRow.style.opacity = '0.55';
      expireRow.style.pointerEvents = 'none';
      certForm.expire_month.value = '';
      certForm.expire_year.value = '';
    } else {
      expireRow.classList.remove('disabled');
      expireRow.style.opacity = '1';
      expireRow.style.pointerEvents = 'auto';
      certForm.expire_month.value = item.expire_month || '';
      certForm.expire_year.value = item.expire_year || '';
    }

    certForm.credential_id.value = item.credential_id || '';
    certForm.credential_url.value = item.credential_url || '';
    if (certForm.visibility && item.visibility) {
      certForm.visibility.value = item.visibility;
    }
    openModal();
  };

  // Delete functions
  window.deletePortfolioItem = function(certId, certName) {
    currentDeleteId = certId;
    document.getElementById('deleteCertName').textContent = certName;
    deleteModal.classList.add('show');
    document.body.classList.add('modal-open');
  };

  window.closeDeleteModal = function() {
    deleteModal.classList.remove('show');
    document.body.classList.remove('modal-open');
    currentDeleteId = null;
  };

  window.confirmDelete = function() {
    if (!currentDeleteId) return;
    
    const deleteBtn = document.getElementById('deleteConfirmBtn');
    deleteBtn.disabled = true;
    deleteBtn.textContent = 'Deleting...';
    
    // Redirect to delete page
    window.location.href = `index.php?page=dean_portfolio_delete&id=${currentDeleteId}`;
  };

  // Close delete modal on backdrop click
  deleteModal.addEventListener('click', (e) => {
    if (e.target === deleteModal) closeDeleteModal();
  });
});

// Toast notification handling
document.addEventListener("DOMContentLoaded", function(){
  const toast = document.querySelector(".toast");
  if (!toast) return;

  // Auto-hide after 3 seconds
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => toast.remove(), 300);
  }, 3000);

  // Remove query params so toast won't reappear on refresh
  if (history.replaceState) {
    const url = new URL(window.location);
    url.searchParams.delete("saved");
    url.searchParams.delete("deleted");
    url.searchParams.delete("error");
    history.replaceState({}, "", url.toString());
  }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>