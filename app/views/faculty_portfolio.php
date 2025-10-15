<?php
require_once __DIR__ . '/../lib/Auth.php';
require_once __DIR__ . '/../models/Model.php';
require_once __DIR__ . '/../models/FacultyPortfolio.php';

Auth::requireRole(['faculty'], '/adamson-ccit/public/index.php?page=login');

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
/**
 * Title-case for display only (does not mutate stored values).
 * Keeps acronyms like IT, CCIT upper if already upper.
 */
function title_case($v) {
  $v = trim((string)$v);
  if ($v === '') return '';
  // If already all caps and short (like IT, CCIT), keep it.
  if (preg_match('/^[A-Z0-9&.\- ]{2,}$/u', $v)) return esc($v);
  // Lower then title-case (UTF-8 safe)
  $lc = mb_strtolower($v, 'UTF-8');
  $tc = mb_convert_case($lc, MB_CASE_TITLE, 'UTF-8');
  return esc($tc);
}

$user = Auth::user() ?? [];
$username = $user['username'] ?? 'Faculty';
$facultyId = $user['id'] ?? 0;

// Ensure tables exist
FacultyPortfolio::migrateFacultyPortfolioTables();

$profile = FacultyPortfolio::getProfile($facultyId);
$fullName = $profile['full_name'] ?? "Prof. " . ($profile['first_name'] ?? $username) . " " . ($profile['last_name'] ?? '');
$firstName = $profile['first_name'] ?? 'Faculty';
$lastName = $profile['last_name'] ?? '';

$facultyCertifications = FacultyPortfolio::getCertifications($facultyId);
$facultyExperience     = FacultyPortfolio::getExperience($facultyId);
$education             = FacultyPortfolio::getEducation($facultyId);
$trainings             = FacultyPortfolio::getTrainings($facultyId);
$performance           = FacultyPortfolio::getPerformance($facultyId);
$awards                = FacultyPortfolio::getAwards($facultyId);

// Initialize to avoid undefined errors
$personal = [];
$research = [];

try {
    $personal = FacultyPortfolio::getPersonal($facultyId) ?: [];
    $research = FacultyPortfolio::getResearch($facultyId) ?: [];
} catch (Exception $e) {
    $personal = [];
    $research = [];
}

// Feedback flags
$success = $_GET['saved']   ?? null;
$deleted = $_GET['deleted'] ?? null;
$error   = $_GET['error']   ?? null;

// Modal flags
$editProfileOpen = isset($_GET['edit_profile']);
$modalOpen = [
  'general'        => isset($_GET['edit_profile']),
  'certifications' => isset($_GET['add_cert']) || isset($_GET['edit_cert']) || isset($_GET['delete_cert']),
  'experience'     => isset($_GET['add_exp'])  || isset($_GET['edit_exp'])  || isset($_GET['delete_exp']),
];

// Section/folder routing
$activeSection = $_GET['section'] ?? 'overview';
$validSections = ['overview', 'certifications', 'education', 'experience', 'personal'];
if (!in_array($activeSection, $validSections, true)) $activeSection = 'overview';

$sectionFolders = [
  'general'        => ['icon' => 'fa-id-card',            'label' => 'General'],
  'education'      => ['icon' => 'fa-graduation-cap',     'label' => 'Education'],
  'certifications' => ['icon' => 'fa-certificate',        'label' => 'Certs'],
  'experience'     => ['icon' => 'fa-briefcase',          'label' => 'Experience'],
  'personal'       => ['icon' => 'fa-user',               'label' => 'Personal'],
  'trainings'      => ['icon' => 'fa-chalkboard-teacher', 'label' => 'Trainings'],
  'performance'    => ['icon' => 'fa-chart-line',         'label' => 'Performance'],
  'awards'         => ['icon' => 'fa-trophy',             'label' => 'Awards'],
  'research'       => ['icon' => 'fa-book',               'label' => 'Research'],
];
$activeFolder = $_GET['folder'] ?? 'general';
if (!isset($sectionFolders[$activeFolder])) $activeFolder = 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Portfolio | CCIT Faculty</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/dean.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

    /* Modal sizing & stacking */
    .modal { z-index: 1055; }
    .btn { position: relative; z-index: 1; pointer-events: auto; }

    .resume-container {
      margin: 0 auto; background: #fff; border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 2rem;
    }
    .resume-header { display:flex; align-items:center; gap:1.2rem; margin-bottom:1.5rem; border-bottom:1px solid #e5e7eb; padding-bottom:1rem; }
    .resume-photo { width:120px; height:120px; border-radius:50%; object-fit:cover; border:3px solid #e5e7eb; background:#f1f5f9; }
    .resume-main-info { flex:1; min-width:0; }
    .resume-name { font-size:1.3rem; font-weight:700; color:var(--navy); margin-bottom:0.2rem; }
    .resume-meta { font-size:1rem; color:#374151; margin-bottom:0.2rem; }
    .resume-section-title {
      font-size:1.1rem; font-weight:700; color:var(--navy);
      margin-top:2rem; margin-bottom:0.7rem; letter-spacing:.01em;
      border-bottom:1px solid #e5e7eb; padding-bottom:.2rem;
    }
  </style>
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>
  <main class="admin-main">
    <header class="admin-topbar">
      <button class="topbar__btn hide-desktop" type="button" aria-label="Open navigation menu" data-sb-open>
        <i class="fas fa-bars"></i>
      </button>
      <span class="admin-topbar__title">My Portfolio</span>
      <span class="admin-topbar__spacer"></span>
    </header>
    <section class="admin-section">
      <div class="resume-container">
        <!-- Alerts -->
        <?php if ($success): ?>
          <div class="alert alert-success modern-alert"><i class="fas fa-check-circle"></i> Saved successfully.</div>
        <?php endif; ?>
        <?php if ($deleted): ?>
          <div class="alert alert-success modern-alert"><i class="fas fa-check-circle"></i> Deleted.</div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert alert-danger modern-alert"><i class="fas fa-exclamation-circle"></i> <?= esc($error) ?></div>
        <?php endif; ?>

        <!-- Header -->
        <div class="resume-header">
          <img src="<?= esc($profile['profile_photo'] ?? "/adamson-ccit/public/assets/images/profile-placeholder.png") ?>" alt="Profile Photo" class="resume-photo" loading="lazy">
          <div class="resume-main-info">
            <div class="resume-name"><?= esc($profile['full_name'] ?? $fullName) ?></div>
            <div class="resume-meta"><?= esc($profile['position'] ?? 'Faculty') ?>, <?= esc($profile['department'] ?? 'College of Computer and Information Technology') ?></div>
            <div class="resume-meta"><?= esc($profile['work_email'] ?? ($username . '@adamson.edu.ph')) ?></div>
          </div>
        </div>

        <!-- Folder Nav -->
        <nav class="portfolio-folder-nav" aria-label="Portfolio Sections">
          <?php foreach ($sectionFolders as $key => $folder): ?>
            <a href="?page=faculty_portfolio&folder=<?= $key ?>" class="portfolio-folder-btn<?= $activeFolder === $key ? ' active' : '' ?>">
              <i class="fas <?= $folder['icon'] ?>"></i>
              <?= esc($folder['label']) ?>
            </a>
          <?php endforeach; ?>
        </nav>

        <!-- GENERAL -->
        <?php if ($activeFolder === 'general'): ?>
          <div class="resume-section-title"><i class="fas fa-id-card"></i> General Information</div>
          <ul class="resume-list">
            <li class="resume-list-item"><div class="resume-label">Full Name</div><div class="resume-value"><?= esc($profile['full_name'] ?? $fullName) ?></div></li>
            <li class="resume-list-item"><div class="resume-label">Employee ID</div><div class="resume-value"><?= esc($profile['employee_id'] ?? '') ?></div></li>
            <li class="resume-list-item"><div class="resume-label">Work Email</div><div class="resume-value"><?= esc($profile['work_email'] ?? ($username . '@adamson.edu.ph')) ?></div></li>
            <li class="resume-list-item"><div class="resume-label">Academic Title</div><div class="resume-value"><?= esc($profile['position'] ?? 'Faculty') ?></div></li>
            <li class="resume-list-item"><div class="resume-label">Dept / College</div><div class="resume-value"><?= esc($profile['department'] ?? 'College of Computer and Information Technology') ?></div></li>
            <li class="resume-list-item"><div class="resume-label">Employment Type</div><div class="resume-value"><?= title_case($profile['employment_type'] ?? '') ?></div></li>
            <li class="resume-list-item"><div class="resume-label">Date Hired</div><div class="resume-value"><?= esc($profile['date_hired'] ?? '') ?></div></li>
            <li class="resume-list-item"><div class="resume-label">Office / Room Location</div><div class="resume-value"><?= esc($profile['office_location'] ?? '') ?></div></li>
            <li class="resume-list-item"><div class="resume-label">Specializations</div><div class="resume-value"><?= esc($profile['specializations'] ?? '') ?></div></li>
            <li class="resume-list-item"><div class="resume-label">Languages</div><div class="resume-value"><?= title_case($profile['languages'] ?? '') ?></div></li>
          </ul>
          <div class="mt-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
              <i class="fas fa-edit"></i> Edit Profile
            </button>
          </div>
        <?php endif; ?>

        <!-- CERTIFICATIONS (Compact) -->
        <?php if ($activeFolder === 'certifications'): ?>
          <div class="resume-section-title"><i class="fas fa-certificate"></i> Licenses & Certifications</div>

          <div class="compact-toolbar">
            <div class="text-muted">Click a row to view full details.</div>
            <span class="ms-auto"></span>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCertModal">
              <i class="fas fa-plus"></i> Add Certification
            </button>
          </div>

          <ul class="compact-grid">
            <?php if (empty($facultyCertifications)): ?>
              <li class="compact-card"><div class="compact-body text-muted">No certifications on file.</div></li>
            <?php else: ?>
              <?php foreach ($facultyCertifications as $cert):
                $cid = 'certBody'.$cert['id'];
                if (!empty($cert['expire_year'])) {
                  $statusChip = ((int)$cert['expire_year'] >= (int)date('Y'))
                    ? '<span class="chip ok">Active</span>' : '<span class="chip warn">Expired</span>';
                } else {
                  $statusChip = '<span class="chip ok">No Expiry</span>';
                }
              ?>
                <li class="compact-card">
                  <div class="compact-head" data-bs-toggle="collapse" data-bs-target="#<?= $cid ?>" aria-expanded="false">
                    <div class="caret"><i class="fa fa-chevron-down"></i></div>
                    <div class="compact-title" title="<?= esc($cert['name']) ?>"><?= esc($cert['name']) ?></div>
                    <div class="compact-meta" title="<?= esc($cert['company_name']) ?>">
                      <?= esc($cert['company_name']) ?> • <?= esc($cert['issue_year']) ?><?= $cert['expire_year'] ? '–'.esc($cert['expire_year']) : '' ?>
                    </div>
                    <?= $statusChip ?>
                    <div class="compact-actions">
                      <button type="button" class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editCertModal<?= $cert['id'] ?>"><i class="fas fa-edit"></i></button>
                      <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCertModal<?= $cert['id'] ?>"><i class="fas fa-trash"></i></button>
                    </div>
                  </div>
                  <div id="<?= $cid ?>" class="compact-body collapse">
                    <!-- ...existing compact card body structure... -->
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        <?php endif; ?>

        <!-- ...existing code for other sections (experience, education, personal, research, trainings, performance, awards)... -->

      </div>
    </section>
  </main>
</div>

<!-- ...existing modals adapted for faculty... -->

<!-- Add Certification Modal -->
<div class="modal fade" id="addCertModal" tabindex="-1" aria-labelledby="addCertModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCertModalLabel">Add Certification</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" action="/adamson-ccit/handlers/faculty_portfolio_handler.php">
        <input type="hidden" name="action" value="add_certification">
        <!-- ...existing form fields... -->
      </form>
    </div>
  </div>
</div>

<!-- ...rest of the modals adapted for faculty... -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
