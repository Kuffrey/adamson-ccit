<?php
require_once __DIR__ . '/../../app/lib/Auth.php';
require_once __DIR__ . '/../../app/models/Model.php';
require_once __DIR__ . '/../../app/models/FacultyPortfolio.php';

Auth::requireRole(['faculty'], '/adamson-ccit/public/index.php?page=login');

if (!function_exists('esc')) {
  function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = Auth::user() ?? [];
$username = $user['username'] ?? 'Faculty';

// Get faculty's information from database
try {
    $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT id, first_name, last_name FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $faculty = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($faculty) {
        $facultyId = (int)$faculty['id'];
        $firstName = (string)$faculty['first_name'];
        $lastName  = (string)$faculty['last_name'];
        $fullName  = trim($firstName . ' ' . $lastName);
    } else {
        $facultyId = null;
        $firstName = "Faculty";
        $lastName  = "";
        $fullName  = $username;
    }
} catch (PDOException $e) {
    $facultyId = null;
    $firstName = "Faculty";
    $lastName  = "";
    $fullName  = $username;
}

class _DBX extends Model { public function d(){ return parent::db(); } }
$_db = (new _DBX())->d();

// Fetch companies for issuing organizations dropdown
$companies = $_db->query("SELECT id, name FROM companies ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch faculty certifications / portfolio items
$portfolioItems = [];
$portfolioStats = ['total' => 0, 'active' => 0, 'expired' => 0];

if ($facultyId) {
    $portfolioItems = FacultyPortfolio::getByUserId($facultyId);
    $portfolioStats = FacultyPortfolio::getStats($facultyId);
}

// Helper for initials
$initials = strtoupper(($firstName[0] ?? 'F') . ($lastName[0] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Faculty Portfolio | Faculty Dashboard</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/dean.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* ===== Faculty Portfolio — polish & parity (CSS-only) ===== */
    :root{
      --topbar-h:64px; --edge:#e5e7eb; --ink:#0b234c; --muted:#6b7280; --hi:#008040;
      --bg:#fafbfc;
    }
    html,body{height:100%}
    body{
      background:var(--bg);
      font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
      margin:0;
    }
    body.modal-open{overflow:hidden}

    /* Layout shell / topbar */
    .admin-cms-layout{display:flex;min-height:100vh;width:100%;overflow-x:hidden}
    .admin-main{flex:1 1 auto;display:flex;flex-direction:column;min-width:0}
    .admin-topbar{
      position:sticky;top:0;z-index:1000;height:var(--topbar-h);
      display:flex;align-items:center;gap:12px;padding:0 16px;background:#fff;border-bottom:1px solid var(--edge)
    }
    .admin-topbar__title{font-weight:800;font-size:1.06rem;color:var(--ink)}
    .admin-topbar__spacer{flex:1}
    .admin-topbar__user{display:flex;align-items:center;gap:.6rem;font-weight:700;color:#111827}
    .admin-topbar__avatar{width:32px;height:32px;border-radius:50%;background:var(--hi);color:#fff;
      display:inline-flex;align-items:center;justify-content:center;font-size:.85rem}

    /* Section padding */
    .admin-cms-section{padding:24px clamp(16px,2vw,24px)}

    /* Profile header — tighter, crisper */
    .profile-header{
      background:linear-gradient(135deg,#008040 0%,#0b234c 100%);
      border-radius:16px;margin-bottom:20px;color:#fff;
      border:1px solid rgba(255,255,255,.08);box-shadow:0 1px 2px rgba(0,0,0,.04)
    }
    .profile-header-content{gap:20px;padding:22px;display:flex;align-items:center}
    .profile-avatar .avatar-circle{
      width:112px;height:112px;background:rgba(255,255,255,.2);
      border-radius:50%;display:flex;align-items:center;justify-content:center;
      font-size:2.25rem;font-weight:700;color:#fff;border:4px solid rgba(255,255,255,.3)
    }
    .profile-info{flex:1}
    .profile-name{margin:0 0 6px;font-size:2rem;font-weight:800}
    .profile-title{margin:0 0 6px;color:rgba(255,255,255,.92)}
    .profile-location{color:rgba(255,255,255,.86)}
    .profile-stats{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
    .stat-item{color:rgba(255,255,255,.95);font-weight:600}
    .stat-divider{color:rgba(255,255,255,.7)}

    /* Card chrome parity */
    .profile-content{display:grid;grid-template-columns:1fr;gap:16px}
    .profile-card{
      background:#fff;border:1px solid var(--edge);border-radius:14px;box-shadow:0 1px 2px rgba(0,0,0,.04);
      padding:0
    }
    .profile-card .card-header-flex{
      margin:0;padding:14px 16px;border-bottom:1px solid var(--edge);
      display:flex;align-items:center;justify-content:space-between;gap:10px
    }
    .card-title{margin:0;font-weight:800;color:#0f172a}
    .card-subtitle{margin:.25rem 0 0;color:#64748b;font-weight:500}

    /* Actions / buttons */
    .card-actions{gap:10px;display:flex;align-items:center}
    .add-cert-btn{
      background:var(--hi);color:#fff;border:0;border-radius:10px;padding:10px 14px;font-weight:800;
      box-shadow:0 1px 0 rgba(0,0,0,.03);transition:filter .15s ease, transform .05s ease
    }
    .add-cert-btn:hover{filter:brightness(.95);transform:translateY(-1px)}
    .add-icon{font-size:1.05rem}
    .view-toggle{border:1px solid var(--edge);border-radius:10px;overflow:hidden}
    .view-btn{padding:8px 10px;color:#6b7280;background:#fff;border:0;cursor:pointer}
    .view-btn.active{background:var(--hi);color:#fff}

    /* Compact list view → read like rows */
    .certifications-list.compact-view{display:block;padding:4px 0}
    .cert-list-item{
      display:grid;grid-template-columns:24px 1fr auto;gap:12px;align-items:flex-start;
      padding:14px 16px;border-bottom:1px solid #eef2f7;margin:0;border-radius:0;position:relative
    }
    .cert-list-item:last-child{border-bottom:none}
    .cert-list-item:hover{background:#f9fafb}
    .cert-icon-small{font-size:20px;margin:0;align-self:center}
    .cert-name-compact{font-size:1rem}
    .cert-issuer-compact{margin:.2rem 0 .45rem;color:#64748b}
    .cert-meta-compact .meta-item{color:#6b7280;font-size:.86rem}
    .cert-hover-actions{position:static;display:flex;gap:8px;align-items:center}
    .action-icon-btn{
      background:#fff;border:1px solid var(--edge);border-radius:8px;padding:.5rem;
      color:#6b7280;transition:all .2s;box-shadow:0 1px 3px rgba(0,0,0,.08);cursor:pointer
    }
    .action-icon-btn:hover{background:#f3f4f6;color:#111827}
    .action-icon-btn.delete-btn:hover{background:#fef2f2;color:#dc2626;border-color:#fecaca}

    /* Card grid view polish (handles inline styles via !important) */
    .certifications-grid.card-view{padding:12px;display:none;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.5rem}
    .certification-card{
      border:1px solid var(--edge) !important;border-radius:12px !important;box-shadow:0 1px 2px rgba(0,0,0,.04) !important;
      padding:14px !important;position:relative
    }
    .certification-card .cert-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}

    /* Empty state parity */
    .empty-state-modern{text-align:center;padding:42px 18px;color:var(--muted)}
    .empty-state-modern .empty-icon{font-size:30px;margin-bottom:8px}
    .empty-state-modern h4{font-size:1.08rem;font-weight:800;color:#0f172a;margin:6px 0}
    .empty-state-modern p{color:#6b7280}

    /* Primary utility button */
    .btn-primary{
      background:var(--hi);color:#fff;border:0;border-radius:10px;padding:10px 14px;font-weight:800
    }
    .btn-primary:hover{filter:brightness(.95)}

    /* Toast look */
    .toast{
      border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,.12);
      border:1px solid var(--edge); font-weight:700
    }

    /* Modal tidy (custom modal used here) */
    .modal{position:fixed !important;inset:0 !important;display:none !important;align-items:center !important;justify-content:center !important;background:rgba(0,0,0,.55) !important;z-index:10000 !important}
    .modal.show{display:flex !important}
    .modal .modal-content{width:min(800px,95vw);max-height:90vh;overflow:auto;background:#fff;color:#111827;border:1px solid var(--edge);border-radius:16px;padding:2rem;box-shadow:0 24px 60px rgba(0,0,0,.16)}
    .modal-header{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:1rem}
    .modal-close{background:none;border:0;color:#6b7280;font-size:1.5rem;cursor:pointer}
    .modal-close:hover{color:#111827}
    #certModal label{font-weight:800;color:#0f172a;margin-bottom:6px;display:inline-block}
    #certModal input[type="text"],#certModal input[type="number"],#certModal input[type="url"],#certModal select{
      border:1px solid var(--edge);border-radius:10px;padding:10px 12px;font-size:.95rem
    }
    #certModal .form-row{margin-bottom:12px}

    /* Checkbox normalization */
    input[type="checkbox"]{width:16px;height:16px;border:1px solid #d1d5db;border-radius:3px;background:#fff;appearance:checkbox}
    input[type="checkbox"]:checked{background-color:var(--hi);border-color:var(--hi)}
    label[for="no-expire"]{font-size:.9rem;margin-left:.5rem}

    /* Responsive */
    @media (max-width: 768px){
      .admin-cms-section{padding:16px}
      .profile-header-content{flex-direction:column;text-align:center;gap:16px}
      .profile-avatar .avatar-circle{width:100px;height:100px;font-size:2rem}
      .cert-list-item{grid-template-columns:20px 1fr}
      .view-toggle{display:none}
    }
  </style>
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>
  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">My Portfolio</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
      </div>
    </header>

    <section class="admin-cms-section">
      <!-- Toast Notifications -->
      <?php if (!empty($_GET['saved']) || !empty($_GET['deleted']) || !empty($_GET['error'])): ?>
        <div class="toast <?= !empty($_GET['error']) ? 'toast--danger' : (!empty($_GET['deleted']) ? 'toast--danger' : 'toast--success') ?>"
             style="position: fixed; top: 20px; right: 20px; z-index: 11000; background: <?= !empty($_GET['error']) ? '#fee2e2' : (!empty($_GET['deleted']) ? '#fef2f2' : '#f0f9ff') ?>; color: <?= !empty($_GET['error']) ? '#dc2626' : (!empty($_GET['deleted']) ? '#dc2626' : '#1d4ed8') ?>; padding: 1rem 1.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid <?= !empty($_GET['error']) ? '#fecaca' : (!empty($_GET['deleted']) ? '#fecaca' : '#dbeafe') ?>; font-weight: 500; max-width: 400px;">
          <?php if (!empty($_GET['error'])): ?>
            <?= esc($_GET['error']) ?>
          <?php elseif (!empty($_GET['deleted'])): ?>
            Certification deleted successfully.
          <?php elseif (!empty($_GET['saved'])): ?>
            Certification saved successfully!
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <div class="page-faculty">
        <div class="profile-container">
          <!-- Profile Header -->
          <div class="profile-header">
            <div class="profile-header-content">
              <div class="profile-avatar">
                <div class="avatar-circle"><?= esc($initials) ?></div>
              </div>
              <div class="profile-info">
                <h1 class="profile-name"><?= esc($fullName) ?></h1>
                <p class="profile-title">Faculty Member</p>
                <p class="profile-location">📍 Adamson University - CCIT</p>
                <div class="profile-stats">
                  <span class="stat-item"><?= (int)$portfolioStats['total'] ?> Certification<?= $portfolioStats['total'] !== 1 ? 's' : '' ?></span>
                  <span class="stat-divider">•</span>
                  <span class="stat-item"><?= (int)$portfolioStats['active'] ?> Active</span>
                  <?php if ((int)$portfolioStats['expired'] > 0): ?>
                    <span class="stat-divider">•</span>
                    <span class="stat-item"><?= (int)$portfolioStats['expired'] ?> Expired</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="profile-content">
            <div class="profile-card certifications-section">
              <div class="card-header-flex">
                <div class="card-header-left">
                  <h3 class="card-title">Licenses & Certifications</h3>
                  <p class="card-subtitle">
                    <?= (int)$portfolioStats['total'] ?> certification<?= $portfolioStats['total'] !== 1 ? 's' : '' ?>
                    <?php if ((int)$portfolioStats['active'] > 0): ?>
                      • <?= (int)$portfolioStats['active'] ?> active
                    <?php endif; ?>
                    <?php if ((int)$portfolioStats['expired'] > 0): ?>
                      • <span style="color:#dc2626;"><?= (int)$portfolioStats['expired'] ?> expired</span>
                    <?php endif; ?>
                  </p>
                </div>
                <div class="card-actions">
                  <div class="view-toggle">
                    <button class="view-btn active" data-view="compact" title="Compact View" aria-pressed="true">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
                      </svg>
                    </button>
                    <button class="view-btn" data-view="card" title="Card View" aria-pressed="false">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M10 4H4c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM10 15H4c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2v-3c0-1.1-.9-2-2-2zM21 4h-6c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM21 15h-6c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2z"/>
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
                  <p>Add professional certifications to highlight your skills and credentials as a faculty member.</p>
                  <button class="btn-primary" data-open-cert-modal>Add your first certification</button>
                </div>
              <?php else: ?>
                <!-- Compact List View (Default) -->
                <div class="certifications-list compact-view" id="compact-view">
                  <?php foreach ($portfolioItems as $item): ?>
                    <?php
                      $issueDate = ($item['issue_month'] && $item['issue_year'])
                        ? date("M Y", mktime(0,0,0,(int)$item['issue_month'],1,(int)$item['issue_year']))
                        : 'N/A';
                      $hasExpiry = !empty($item['expires']); // truthy means it DOES expire
                      $expireDate = ($hasExpiry && $item['expire_month'] && $item['expire_year'])
                        ? date("M Y", mktime(0,0,0,(int)$item['expire_month'],1,(int)$item['expire_year']))
                        : null;
                    ?>
                    <div class="cert-list-item">
                      <div class="cert-icon-small">🎓</div>
                      <div class="cert-content-compact">
                        <div class="cert-header-compact">
                          <h4 class="cert-name-compact"><?= esc($item['name']) ?></h4>
                        </div>
                        <p class="cert-issuer-compact"><?= esc($item['company_name']) ?></p>
                        <div class="cert-meta-compact">
                          <span class="meta-item">Issued <?= esc($issueDate) ?></span>
                          <?php if ($expireDate): ?>
                            <span class="meta-item">Expires <?= esc($expireDate) ?></span>
                          <?php else: ?>
                            <span class="meta-item">No expiry</span>
                          <?php endif; ?>
                          <?php if (!empty($item['credential_id'])): ?>
                            <span class="meta-item credential-id">ID: <?= esc($item['credential_id']) ?></span>
                          <?php endif; ?>
                        </div>
                        <?php if (!empty($item['credential_url'])): ?>
                          <div class="cert-actions-compact">
                            <a href="<?= esc($item['credential_url']) ?>" target="_blank" rel="noopener noreferrer"
                               style="color:#008040;text-decoration:none;font-size:.9rem;font-weight:500;">
                              Show credential →
                            </a>
                          </div>
                        <?php endif; ?>
                      </div>

                      <div class="cert-hover-actions">
                        <button class="action-icon-btn edit-btn"
                          onclick='editPortfolioItem(<?= json_encode($item, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>)'
                          title="Edit certification" aria-label="Edit certification">
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                          </svg>
                        </button>
                        <button class="action-icon-btn delete-btn"
                          onclick='deletePortfolioItem(<?= (int)$item["id"] ?>, "<?= esc($item["name"]) ?>")'
                          title="Delete certification" aria-label="Delete certification">
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
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
                    <div class="certification-card" style="border:1px solid #e5e7eb;border-radius:12px;padding:1rem;position:relative;">
                      <div class="cert-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
                        <div class="cert-logo" aria-hidden="true">🎓</div>
                        <div class="cert-hover-actions" style="display:flex;gap:.5rem;">
                          <button class="action-icon-btn edit-btn"
                            onclick='editPortfolioItem(<?= json_encode($item, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>)'
                            title="Edit certification" aria-label="Edit certification">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                              <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                          </button>
                          <button class="action-icon-btn delete-btn"
                            onclick='deletePortfolioItem(<?= (int)$item["id"] ?>, "<?= esc($item["name"]) ?>")'
                            title="Delete certification" aria-label="Delete certification">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                              <polyline points="3,6 5,6 21,6"></polyline>
                              <path d="m19,6v14a2,2 0 0,1-2,2H7a2,2 0 0,1-2-2V6m3,0V4a2,2 0 0,1,2-2h4a2,2 0 0,1,2,2v2"></path>
                            </svg>
                          </button>
                        </div>
                      </div>
                      <div class="cert-content">
                        <h4 class="cert-name" style="margin:.25rem 0 .35rem 0;font-size:1.05rem;font-weight:700;"><?= esc($item['name']) ?></h4>
                        <p class="cert-issuer" style="margin:0 0 .5rem 0;color:#6b7280;"><?= esc($item['company_name']) ?></p>
                        <?php if (!empty($item['credential_url'])): ?>
                          <a href="<?= esc($item['credential_url']) ?>" target="_blank" rel="noopener noreferrer"
                             style="color:#008040;text-decoration:none;font-size:.9rem;">
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
  <div id="certModal" class="modal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="certModalTitle">
      <div class="modal-header">
        <h3 id="certModalTitle">License or Certification</h3>
        <button type="button" class="modal-close" aria-label="Close">✕</button>
      </div>

      <form id="certForm" action="index.php?page=faculty_portfolio_save" method="POST" novalidate>
        <input type="hidden" name="mode" value="create">
        <!-- avoid shadowing HTMLFormElement.id -->
        <input type="hidden" name="portfolio_id" id="portfolio_id" value="">

        <!-- First row: Name and Organization -->
        <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:1rem;">
          <div>
            <label for="cert_name">Name*</label>
            <input id="cert_name" type="text" name="name" required
                   style="width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:10px 12px;font-size:.95rem;">
          </div>
          <div>
            <label for="company_id">Issuing organization*</label>
            <select id="company_id" name="company_id" required
                    style="width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:10px 12px;font-size:.95rem;">
              <option value="">Select company…</option>
              <?php foreach ($companies as $co): ?>
                <option value="<?= (int)$co['id'] ?>"><?= esc($co['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Second row: Issue date -->
        <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr 2fr;gap:20px;margin-bottom:1rem;">
          <div>
            <label for="issue_month">Issue month</label>
            <select id="issue_month" name="issue_month"
                    style="width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:10px 12px;font-size:.95rem;">
              <option value="">Month</option><?php for ($m=1;$m<=12;$m++) echo "<option value=\"$m\">$m</option>"; ?>
            </select>
          </div>
          <div>
            <label for="issue_year">Issue year</label>
            <input id="issue_year" type="number" name="issue_year" min="1950" max="<?= date('Y')+1 ?>"
                   style="width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:10px 12px;font-size:.95rem;">
          </div>
          <div style="display:flex;align-items:center;padding-bottom:10px;">
            <div class="form-row" style="margin-bottom:0;display:flex;align-items:center;gap:.5rem;">
              <input type="checkbox" id="no-expire" name="no_expire" value="1" checked>
              <label for="no-expire">This credential does not expire</label>
            </div>
          </div>
        </div>

        <!-- Third row: Expiration date -->
        <div id="expireRow" class="form-row disabled"
             style="display:grid;grid-template-columns:1fr 1fr 2fr;gap:20px;margin-bottom:1rem;opacity:.55;pointer-events:none;">
          <div>
            <label for="expire_month">Expiration month</label>
            <select id="expire_month" name="expire_month"
                    style="width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:10px 12px;font-size:.95rem;">
              <option value="">Month</option><?php for ($m=1;$m<=12;$m++) echo "<option value=\"$m\">$m</option>"; ?>
            </select>
          </div>
          <div>
            <label for="expire_year">Expiration year</label>
            <input id="expire_year" type="number" name="expire_year" min="<?= date('Y')-1 ?>" max="<?= date('Y')+15 ?>"
                   style="width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:10px 12px;font-size:.95rem;">
          </div>
          <div></div>
        </div>

        <!-- Fourth row: Credential details and visibility -->
        <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:1rem;">
          <div>
            <label for="credential_id">Credential ID</label>
            <input id="credential_id" type="text" name="credential_id"
                   style="width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:10px 12px;font-size:.95rem;">
          </div>
          <div>
            <label for="credential_url">Credential URL</label>
            <input id="credential_url" type="url" name="credential_url" placeholder="https://..."
                   style="width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:10px 12px;font-size:.95rem;">
          </div>
          <div>
            <label for="visibility">Visibility</label>
            <select id="visibility" name="visibility"
                    style="width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:10px 12px;font-size:.95rem;">
              <option value="public">Public</option>
              <option value="private">Only me</option>
            </select>
          </div>
        </div>

        <div class="modal-actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:14px;">
          <button type="button" class="btn btn--outline modal-close"
                  style="background:none;border:1px solid #e5e7eb;color:#6b7280;padding:10px 16px;border-radius:6px;cursor:pointer;">
            Exit
          </button>
          <button type="submit" class="btn btn--solid"
                  style="background:#008040;color:#fff;border:none;padding:10px 16px;border-radius:6px;cursor:pointer;">
            Save
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="modal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
      <div class="modal-header">
        <h3 id="deleteModalTitle">Delete Certification</h3>
        <button type="button" class="modal-close" onclick="closeDeleteModal()" aria-label="Close">✕</button>
      </div>
      <div class="modal-body" style="margin: 1rem 0;">
        <p>Are you sure you want to delete this certification?</p>
        <p><strong id="deleteCertName">Certification Name</strong></p>
        <p style="color:#dc2626;font-size:.9rem;">This action cannot be undone.</p>
      </div>
      <div class="modal-actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:14px;">
        <button type="button" class="btn btn--outline" onclick="closeDeleteModal()
        " style="background:none;border:1px solid #e5e7eb;color:#6b7280;padding:10px 16px;border-radius:6px;cursor:pointer;">
          Cancel
        </button>
        <button type="button" class="btn btn--danger" onclick="confirmDelete()" id="deleteConfirmBtn"
                style="background:#dc2626;color:#fff;border:none;padding:10px 16px;border-radius:6px;cursor:pointer;">
          Delete
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const certModal   = document.getElementById('certModal');
  const certForm    = document.getElementById('certForm');
  const idField     = certForm.querySelector('input[name="portfolio_id"]');
  const noExpire    = document.getElementById('no-expire');
  const expireRow   = document.getElementById('expireRow');
  const deleteModal = document.getElementById('deleteModal');
  const viewButtons = document.querySelectorAll('.view-btn');
  const compactView = document.getElementById('compact-view');
  const cardView    = document.getElementById('card-view');
  let currentDeleteId = null;

  // Toggle view
  viewButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const view = btn.getAttribute('data-view');
      viewButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
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
    if (!idField.value) certForm.reset();
  }

  // Open modal
  document.querySelectorAll('[data-open-cert-modal]').forEach(btn => {
    btn.addEventListener('click', () => {
      certForm.mode.value = 'create';
      idField.value = '';
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

  // Esc key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      if (certModal.classList.contains('show')) closeModal();
      if (deleteModal.classList.contains('show')) closeDeleteModal();
    }
  });

  // Expiration toggle
  const toggleExpire = () => {
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
    }
  };
  noExpire.addEventListener('change', toggleExpire);
  toggleExpire();

  // Prevent double-submit
  certForm.addEventListener('submit', () => {
    const btn = certForm.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }
  });

  // Edit
  window.editPortfolioItem = function(item) {
    certForm.mode.value = 'update';
    idField.value = item.id || '';
    certForm.name.value = item.name || '';
    certForm.company_id.value = item.company_id || '';
    certForm.issue_month.value = item.issue_month || '';
    certForm.issue_year.value = item.issue_year || '';

    // if item.expires truthy -> it DOES expire
    const doesExpire = String(item.expires) === '1' || String(item.expires).toLowerCase() === 'true';
    noExpire.checked = !doesExpire;
    if (doesExpire) {
      expireRow.classList.remove('disabled');
      expireRow.style.opacity = '1';
      expireRow.style.pointerEvents = 'auto';
      certForm.expire_month.value = item.expire_month || '';
      certForm.expire_year.value   = item.expire_year || '';
    } else {
      expireRow.classList.add('disabled');
      expireRow.style.opacity = '0.55';
      expireRow.style.pointerEvents = 'none';
      certForm.expire_month.value = '';
      certForm.expire_year.value = '';
    }

    certForm.credential_id.value  = item.credential_id || '';
    certForm.credential_url.value = item.credential_url || '';
    if (certForm.visibility && item.visibility) {
      certForm.visibility.value = item.visibility;
    }
    openModal();
  };

  // Delete
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
    window.location.href = `index.php?page=faculty_portfolio_delete&id=${currentDeleteId}`;
  };
  deleteModal.addEventListener('click', (e) => { if (e.target === deleteModal) closeDeleteModal(); });
});

// Toast: auto-hide + strip params
document.addEventListener("DOMContentLoaded", function(){
  const toast = document.querySelector(".toast");
  if (!toast) return;
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
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
</html>
