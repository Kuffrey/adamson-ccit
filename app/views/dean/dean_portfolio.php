<?php
require_once __DIR__ . '/../../lib/Auth.php';
require_once __DIR__ . '/../../models/Model.php';
require_once __DIR__ . '/../../models/DeanPortfolio.php';

Auth::requireRole(['dean'], '/adamson-ccit/public/index.php?page=login');

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
$username = $user['username'] ?? 'Dean';
$deanId = $user['id'] ?? 0;

// Ensure tables exist
DeanPortfolio::migrateDeanPortfolioTables();

$profile = DeanPortfolio::getProfile($deanId);
$fullName = $profile['full_name'] ?? "Dr. " . ($profile['first_name'] ?? $username) . " " . ($profile['last_name'] ?? '');
$firstName = $profile['first_name'] ?? 'Dean';
$lastName = $profile['last_name'] ?? '';

$deanCertifications = DeanPortfolio::getCertifications($deanId);
$deanExperience     = DeanPortfolio::getExperience($deanId);
$education          = DeanPortfolio::getEducation($deanId);
$trainings          = DeanPortfolio::getTrainings($deanId);
$performance        = DeanPortfolio::getPerformance($deanId);
$awards             = DeanPortfolio::getAwards($deanId);

// Initialize to avoid undefined errors
$personal = [];
$research = [];

try {
    $personal = DeanPortfolio::getPersonal($deanId) ?: [];
    $research = DeanPortfolio::getResearch($deanId) ?: [];
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

$printAll   = isset($_GET['print']) && $_GET['print'] === 'all';
$autoPrint  = isset($_GET['autoprint']) && $_GET['autoprint'] === '1';

// Helper to preserve current query but override some keys
function url_with(array $overrides = []) {
  $base = strtok($_SERVER['REQUEST_URI'], '?');
  $q = $_GET;
  foreach ($overrides as $k => $v) {
    if ($v === null) unset($q[$k]);
    else $q[$k] = $v;
  }
  $qs = http_build_query($q);
  return $base . ($qs ? ('?'.$qs) : '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Portfolio | CCIT Dean</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/dean.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
/* =========================
   RESUME / PRINT REFINEMENTS
   ========================= */

/* ---- Base palette & type ---- */
:root{
  --ink:#0f172a;          /* near-black */
  --sub:#475569;          /* muted slate */
  --rule:#e5e7eb;         /* light divider */
  --accent:#1f2937;       /* headers */
  --accent-light:#f8fafc; /* light background */
  --card-border:#e2e8f0;  /* card border */
  --card-hover:#f1f5f9;   /* card hover state */
}

html,body{
  color:var(--ink);
  font: 14.5px/1.45 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Inter,"Helvetica Neue",Arial,"Noto Sans","Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
}

@media (min-width: 992px){
  .resume-container{
    max-width: 900px;      /* nice readable measure */
    padding: 2.25rem 2.25rem 2.5rem;
  }
}

/* ---- Header (name block) ---- */
.resume-header{
  border-bottom:1px solid var(--rule);
  padding-bottom:1rem;
  margin-bottom:1.25rem;
  gap:1.25rem;
}
.resume-photo{
  width:108px; height:108px; border-radius:12px; border:1px solid var(--rule);
}
.resume-name{
  font-size:1.9rem; letter-spacing:.2px; color:var(--accent);
  margin-bottom:.15rem;
}
.resume-meta{
  color:var(--sub); font-size:1rem;
}

/* ---- Section titles ---- */
.resume-section-title{
  margin-top:1.4rem;
  margin-bottom:.75rem;
  padding-bottom:.4rem;
  border-bottom:1px solid var(--rule);
  font-weight:750;
  font-size:1.1rem;
  text-transform:uppercase;
  letter-spacing:.06em;
  color:var(--accent);
}
.resume-section-title i{ color:var(--sub); margin-right: 0.4rem; }

/* ---- Definition-list layout (General/Personal) ---- */
.resume-list{
  padding-left: 0;
  list-style: none;
  margin-bottom: 1.5rem;
}
.resume-list-item{
  display: flex;
  flex-wrap: wrap;
  padding:.5rem 0;
  border-bottom:1px dashed var(--rule);
  align-items: baseline;
}
.resume-label{
  color:var(--sub);
  text-transform:uppercase;
  font-size:.8rem;
  letter-spacing:.06em;
  width: 140px;
  padding-right: 12px;
  flex-shrink: 0;
}
.resume-value{
  font-size:.97rem;
  flex-grow: 1;
  width: calc(100% - 140px);
}

/* ---- "Compact cards" re-skinned to consistent timeline list ---- */
.compact-grid{ 
  padding-left: 0;
  list-style: none;
  margin-bottom: 1.5rem;
}
.compact-card{
  position: relative;
  border-left:3px solid var(--card-border);
  border-radius:0;
  padding-left: 1rem;
  padding-bottom: 0.5rem;
  margin-bottom: 0.5rem;
  background: transparent;
}
.compact-card:last-child {
  margin-bottom: 0;
}
.compact-head{
  position: relative;
  padding: 0.5rem 0;
  cursor: pointer;
}
.compact-title{
  font-weight:600;
  color:var(--ink);
  font-size: 1rem;
  margin-bottom: 0.1rem;
}
.compact-meta{
  font-size:.85rem;
  color:var(--sub);
}
.compact-actions{
  position: absolute;
  right: 0;
  top: 0.5rem;
  display: flex;
  gap: 0.3rem;
}
.compact-actions .btn{ 
  border-color: var(--rule);
  padding: 0.2rem 0.4rem;
  font-size: 0.8rem;
}
.compact-body{
  padding: 0.5rem 0 0.25rem;
  border-top: 0;
}
.compact-row{
  display: flex;
  flex-wrap: wrap;
  padding: 0.3rem 0;
  align-items: baseline;
}
.compact-row .label{
  width: 140px;
  padding-right: 12px;
  color: var(--sub);
  text-transform: uppercase;
  font-size: .75rem;
  letter-spacing: .06em;
  flex-shrink: 0;
  font-weight: normal;
}
.compact-row .value{
  color: var(--ink);
  font-size: 0.95rem;
  flex-grow: 1;
  width: calc(100% - 140px);
}

/* Better caret indicator */
.compact-head .caret {
  display: inline-block;
  width: 16px;
  height: 16px;
  margin-right: 0.4rem;
  color: var(--sub);
  transition: transform 0.2s;
}
.compact-head[aria-expanded="true"] .caret {
  transform: rotate(90deg);
}

/* ---- Small chips refined ---- */
.chip{ 
  display: inline-block;
  font-weight:600; 
  padding:.12rem .48rem; 
  border-radius:4px; 
  font-size:.72rem;
  margin-left: 0.3rem;
  vertical-align: middle;
}
.chip.ok   { background:#e8f7eb; color:#166534; }
.chip.warn { background:#fff4db; color:#92400e; }

/* ---- Links (screen) ---- */
.resume-value a, .compact-row .value a{
  text-decoration:none; 
  border-bottom:1px dotted #9ca3af;
  color: #2563eb;
}
.resume-value a:hover, .compact-row .value a:hover{
  border-bottom-style:solid;
}

/* ---- Folder navigation ---- */
.portfolio-folder-nav {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
  margin-bottom: 1.5rem;
  border-bottom: 1px solid var(--rule);
  padding-bottom: 0.5rem;
}
.portfolio-folder-btn {
  padding: 0.4rem 0.7rem;
  border-radius: 4px;
  text-decoration: none;
  color: var(--ink);
  font-size: 0.9rem;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  transition: background-color 0.2s;
}
.portfolio-folder-btn:hover {
  background-color: var(--card-hover);
  color: var(--ink);
}
.portfolio-folder-btn.active {
  background-color: var(--accent);
  color: white;
}
.portfolio-folder-btn i {
  font-size: 0.85rem;
}

/* Compact toolbar */
.compact-toolbar {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
  font-size: 0.9rem;
}

/* ---- Utilities for page breaks if ever needed in markup ---- */
.page-break{ page-break-before:always; break-before: page; }
.no-break{ break-inside: avoid; page-break-inside: avoid; }

/* =========================
   PRINT: crisp & compact
   ========================= */

@page{
  size: A4;
  margin: 16mm 14mm;
}

@media print{
  /* App chrome off (you already had most of these) */
  .admin-topbar, .portfolio-folder-nav, .compact-toolbar, .btn, .modal,
  [data-sb-open], .topbar__actions, .alert, .compact-actions { display:none !important; }

  html,body{ font-size:11pt; color:#000; background:#fff !important; }
  .resume-container{ box-shadow:none !important; border-radius:0 !important; padding:0 !important; margin:0 !important; }

  /* Header tuned for paper */
  .resume-header{
    padding-bottom:10px; margin-bottom:18px; border-bottom:1px solid #ddd;
  }
  .resume-photo{
    width:90px; height:90px; border-radius:8px; border:1px solid #ddd;
  }
  .resume-name{ font-size:22pt; }
  .resume-meta{ font-size:10.5pt; }

  /* Section titles on print */
  .resume-section-title{
    font-size:12pt; letter-spacing:.08em; color:#111827;
    margin-top:18pt; margin-bottom:10pt; border-bottom:1px solid #ddd;
    page-break-after:avoid; page-break-inside:avoid;
  }

  /* Show all details; avoid broken cards */
  .collapse{ display:block !important; height:auto !important; }
  .compact-card, .resume-list-item{ break-inside:avoid; page-break-inside:avoid; }

  /* Timeline rule a bit darker for paper */
  .compact-card{ border-left-color:#cbd5e1; margin-bottom: 14pt; }
  
  /* Cards with cleaner spacing on paper */
  .compact-head { padding-top: 0; }
  .compact-title { font-weight: 700; margin-bottom: 2pt; }
  .compact-body { padding-top: 4pt; }
  .compact-row { padding: 2pt 0; }

  /* Muted icons & carets on paper */
  .resume-section-title i, .compact-head .caret { color: #666; }
  
  /* Chips more subtle on paper */
  .chip { background-color: transparent !important; border: 1px solid currentColor; }

  /* Links without trailing URL */
  a[href]:after{ content:""; }
}
  </style>
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/_dean_sidebar.php'; ?>
  <main class="admin-main">
    <header class="admin-topbar">
      <button class="topbar__btn hide-desktop" type="button" aria-label="Open navigation menu" data-sb-open>
        <i class="fas fa-bars"></i>
      </button>
      <span class="admin-topbar__title">My Portfolio</span>
      <span class="admin-topbar__spacer"></span>
      <div class="topbar__actions">
  <!-- Print this section -->
  <a href="<?= esc(url_with(['print' => null, 'autoprint' => null])) ?>" 
     class="btn btn-outline-secondary d-none d-md-inline-block" 
     id="btnPrintSection">
    <i class="fas fa-print"></i> Print This Section
  </a>

  <!-- Print full portfolio (renders all folders, triggers print) -->
  <a href="<?= esc(url_with(['folder'=> 'general', 'print' => 'all', 'autoprint' => '1'])) ?>"
     class="btn btn-primary">
    <i class="fas fa-file-export"></i> Print Full Portfolio
  </a>
</div>

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
            <div class="resume-meta"><?= esc($profile['position'] ?? 'Dean') ?>, <?= esc($profile['department'] ?? 'College of Computer and Information Technology') ?></div>
            <div class="resume-meta"><?= esc($profile['work_email'] ?? ($username . '@adamson.edu.ph')) ?></div>
          </div>
        </div>

        <!-- Folder Nav -->
        <nav class="portfolio-folder-nav" aria-label="Portfolio Sections">
          <?php foreach ($sectionFolders as $key => $folder): ?>
            <a href="?page=dean_portfolio&folder=<?= $key ?>" class="portfolio-folder-btn<?= $activeFolder === $key ? ' active' : '' ?>">
              <i class="fas <?= $folder['icon'] ?>"></i>
              <?= esc($folder['label']) ?>
            </a>
          <?php endforeach; ?>
        </nav>

        <!-- GENERAL -->
        <?php if ($activeFolder === 'general' || $printAll): ?>
          <div class="resume-section-title"><i class="fas fa-id-card"></i> General Information</div>
          <ul class="resume-list">
            <li class="resume-list-item"><div class="resume-label">Full Name</div><div class="resume-value"><?= esc($profile['full_name'] ?? $fullName) ?></div></li>
            <li class="resume-list-item"><div class="resume-label">Employee ID</div><div class="resume-value"><?= esc($profile['employee_id'] ?? '') ?></div></li>
            <li class="resume-list-item"><div class="resume-label">Work Email</div><div class="resume-value"><?= esc($profile['work_email'] ?? ($username . '@adamson.edu.ph')) ?></div></li>
            <li class="resume-list-item"><div class="resume-label">Academic Title</div><div class="resume-value"><?= esc($profile['position'] ?? 'Dean') ?></div></li>
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
        <?php if ($activeFolder === 'certifications' || $printAll): ?>
          <div class="resume-section-title"><i class="fas fa-certificate"></i> Licenses & Certifications</div>

          <div class="compact-toolbar">
            <div class="text-muted">Click a row to view full details.</div>
            <span class="ms-auto"></span>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCertModal">
              <i class="fas fa-plus"></i> Add Certification
            </button>
          </div>

          <ul class="compact-grid">
            <?php if (empty($deanCertifications)): ?>
              <li class="compact-card"><div class="compact-body text-muted">No certifications on file.</div></li>
            <?php else: ?>
              <?php foreach ($deanCertifications as $cert):
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
                    <div class="compact-row"><div class="label">Title</div><div class="value"><?= esc($cert['name']) ?></div></div>
                    <div class="compact-row"><div class="label">Issuing Body</div><div class="value"><?= esc($cert['company_name']) ?></div></div>
                    <div class="compact-row"><div class="label">Issued / Expiry</div><div class="value"><?= esc($cert['issue_year']) ?> – <?= $cert['expire_year'] ? esc($cert['expire_year']) : 'No Expiry' ?></div></div>
                    <?php if (!empty($cert['credential_id']) || !empty($cert['credential_url'])): ?>
                      <div class="compact-row">
                        <div class="label">Credential</div>
                        <div class="value">
                          <?php if (!empty($cert['credential_id'])): ?>ID: <?= esc($cert['credential_id']) ?><?php endif; ?>
                          <?php if (!empty($cert['credential_url'])): ?>
                            <?= !empty($cert['credential_id']) ? ' • ' : '' ?>
                            <a href="<?= esc($cert['credential_url']) ?>" target="_blank" class="link-cert">Verify</a>
                          <?php endif; ?>
                        </div>
                      </div>
                    <?php endif; ?>
                    <div class="compact-row"><div class="label">Visibility</div><div class="value"><?= esc(ucfirst($cert['visibility'] ?? 'Public')) ?></div></div>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        <?php endif; ?>

        <!-- EXPERIENCE (Compact) -->
        <?php if ($activeFolder === 'experience' || $printAll): ?>
          <div class="resume-section-title"><i class="fas fa-briefcase"></i> Employment & Academic Record</div>

          <div class="compact-toolbar">
            <div class="text-muted">Tap a card for details.</div>
            <span class="ms-auto"></span>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExpModal">
              <i class="fas fa-plus"></i> Add Experience
            </button>
          </div>

          <ul class="compact-grid">
            <?php if (empty($deanExperience)): ?>
              <li class="compact-card"><div class="compact-body text-muted">No experience records found.</div></li>
            <?php else: ?>
              <?php foreach ($deanExperience as $exp): $eid='expBody'.$exp['id']; ?>
                <li class="compact-card">
                  <div class="compact-head" data-bs-toggle="collapse" data-bs-target="#<?= $eid ?>" aria-expanded="false">
                    <div class="caret"><i class="fa fa-chevron-down"></i></div>
                    <div class="compact-title" title="<?= esc($exp['position']) ?>"><?= esc($exp['position']) ?></div>
                    <div class="compact-meta"><?= esc($exp['department']) ?> • <?= esc($exp['period']) ?></div>
                    <div class="compact-actions">
                      <button type="button" class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editExpModal<?= $exp['id'] ?>"><i class="fas fa-edit"></i></button>
                      <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteExpModal<?= $exp['id'] ?>"><i class="fas fa-trash"></i></button>
                    </div>
                  </div>
                  <div id="<?= $eid ?>" class="compact-body collapse">
                    <div class="compact-row"><div class="label">Position</div><div class="value"><?= esc($exp['position']) ?></div></div>
                    <div class="compact-row"><div class="label">Department</div><div class="value"><?= esc($exp['department']) ?></div></div>
                    <div class="compact-row"><div class="label">Period</div><div class="value"><?= esc($exp['period']) ?></div></div>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        <?php endif; ?>

        <!-- EDUCATION (Compact) -->
        <?php if ($activeFolder === 'education' || $printAll): ?>
          <div class="resume-section-title"><i class="fas fa-graduation-cap"></i> Educational Background</div>

          <div class="compact-toolbar">
            <div class="text-muted">Degrees at a glance.</div>
            <span class="ms-auto"></span>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEduModal">
              <i class="fas fa-plus"></i> Add Education
            </button>
          </div>

          <ul class="compact-grid">
            <?php if (empty($education)): ?>
              <li class="compact-card"><div class="compact-body text-muted">No education records found.</div></li>
            <?php else: ?>
              <?php foreach ($education as $edu): $bid='eduBody'.$edu['id']; ?>
                <li class="compact-card">
                  <div class="compact-head" data-bs-toggle="collapse" data-bs-target="#<?= $bid ?>" aria-expanded="false">
                    <div class="caret"><i class="fa fa-chevron-down"></i></div>
                    <div class="compact-title"><?= esc($edu['degree']) ?></div>
                    <div class="compact-meta"><?= esc($edu['institution']) ?> • <?= esc($edu['year']) ?></div>
                    <div class="compact-actions">
                      <button type="button" class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editEduModal<?= $edu['id'] ?>"><i class="fas fa-edit"></i></button>
                      <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteEduModal<?= $edu['id'] ?>"><i class="fas fa-trash"></i></button>
                    </div>
                  </div>
                  <div id="<?= $bid ?>" class="compact-body collapse">
                    <div class="compact-row"><div class="label">Degree</div><div class="value"><?= esc($edu['degree']) ?></div></div>
                    <div class="compact-row"><div class="label">Institution</div><div class="value"><?= esc($edu['institution']) ?></div></div>
                    <div class="compact-row"><div class="label">Year</div><div class="value"><?= esc($edu['year']) ?></div></div>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        <?php endif; ?>

        <!-- PERSONAL (kept classic, no "Add" button) -->
        <?php if ($activeFolder === 'personal' || $printAll): ?>
          <div class="resume-section-title"><i class="fas fa-user"></i> Personal Information</div>
          <ul class="resume-list">
            <?php if (empty($personal)): ?>
              <li class="resume-list-item"><span class="resume-value text-muted">No personal information found.</span></li>
            <?php else: ?>
              <li class="resume-list-item"><div class="resume-label">Birthday</div><div class="resume-value"><?= esc($personal['birthday'] ?? '') ?></div></li>
              <li class="resume-list-item"><div class="resume-label">Gender</div><div class="resume-value"><?= title_case($personal['gender'] ?? '') ?></div></li>
              <li class="resume-list-item"><div class="resume-label">Marital Status</div><div class="resume-value"><?= title_case($personal['marital_status'] ?? '') ?></div></li>
              <li class="resume-list-item"><div class="resume-label">Nationality</div><div class="resume-value"><?= title_case($personal['nationality'] ?? '') ?></div></li>
              <li class="resume-list-item"><div class="resume-label">Address</div><div class="resume-value"><?= esc($personal['address'] ?? '') ?></div></li>
              <li class="resume-list-item"><div class="resume-label">Contact Number</div><div class="resume-value"><?= esc($personal['contact_number'] ?? '') ?></div></li>
              <li class="resume-list-item"><div class="resume-label">Emergency Contact Name</div><div class="resume-value"><?= title_case($personal['emergency_contact_name'] ?? '') ?></div></li>
              <li class="resume-list-item"><div class="resume-label">Emergency Contact Number</div><div class="resume-value"><?= esc($personal['emergency_contact_number'] ?? '') ?></div></li>
            <?php endif; ?>
          </ul>
          <div class="mt-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editPersonalModal">
              <i class="fas fa-edit"></i> Edit Personal Info
            </button>
          </div>
        <?php endif; ?>

        <!-- RESEARCH (Compact) -->
        <?php if ($activeFolder === 'research' || $printAll): ?>
          <div class="resume-section-title"><i class="fas fa-book"></i> Research & Publications</div>

          <div class="compact-toolbar">
            <div class="text-muted">Publications at a glance.</div>
            <span class="ms-auto"></span>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResearchModal">
              <i class="fas fa-plus"></i> Add Research
            </button>
          </div>

          <ul class="compact-grid">
            <?php if (empty($research)): ?>
              <li class="compact-card"><div class="compact-body text-muted">No research records found.</div></li>
            <?php else: ?>
              <?php foreach ($research as $item): $rid='resBody'.$item['id']; ?>
                <li class="compact-card">
                  <div class="compact-head" data-bs-toggle="collapse" data-bs-target="#<?= $rid ?>" aria-expanded="false">
                    <div class="caret"><i class="fa fa-chevron-down"></i></div>
                    <div class="compact-title"><?= esc($item['title']) ?></div>
                    <div class="compact-meta"><?= esc($item['journal']) ?> • <?= esc($item['year']) ?></div>
                    <div class="compact-actions">
                      <button type="button" class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editResearchModal<?= $item['id'] ?>"><i class="fas fa-edit"></i></button>
                      <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteResearchModal<?= $item['id'] ?>"><i class="fas fa-trash"></i></button>
                    </div>
                  </div>
                  <div id="<?= $rid ?>" class="compact-body collapse">
                    <div class="compact-row"><div class="label">Title</div><div class="value"><?= esc($item['title']) ?></div></div>
                    <div class="compact-row"><div class="label">Venue</div><div class="value"><?= esc($item['journal']) ?></div></div>
                    <div class="compact-row"><div class="label">Year</div><div class="value"><?= esc($item['year']) ?></div></div>
                    <div class="compact-row"><div class="label">Type</div><div class="value"><?= title_case($item['type']) ?></div></div>
                    <div class="compact-row"><div class="label">Authors</div><div class="value"><?= esc($item['authors']) ?></div></div>
                    <?php if (!empty($item['doi_url'])): ?>
                      <div class="compact-row"><div class="label">DOI / URL</div><div class="value"><a href="<?= esc($item['doi_url']) ?>" target="_blank">Open</a></div></div>
                    <?php endif; ?>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        <?php endif; ?>

        <!-- TRAININGS (Compact) -->
        <?php if ($activeFolder === 'trainings' || $printAll): ?>
          <div class="resume-section-title"><i class="fas fa-chalkboard-teacher"></i> Trainings & Seminars</div>

          <div class="compact-toolbar">
            <div class="text-muted">Training history, condensed.</div>
            <span class="ms-auto"></span>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTrainingModal">
              <i class="fas fa-plus"></i> Add Training
            </button>
          </div>

          <ul class="compact-grid">
            <?php if (empty($trainings)): ?>
              <li class="compact-card"><div class="compact-body text-muted">No trainings found.</div></li>
            <?php else: ?>
              <?php foreach ($trainings as $training): $tid='trBody'.$training['id']; $chip = (strtolower($training['status'] ?? '')==='expired')?'<span class="chip warn">Expired</span>':'<span class="chip ok">Active</span>'; ?>
                <li class="compact-card">
                  <div class="compact-head" data-bs-toggle="collapse" data-bs-target="#<?= $tid ?>" aria-expanded="false">
                    <div class="caret"><i class="fa fa-chevron-down"></i></div>
                    <div class="compact-title"><?= esc($training['title']) ?></div>
                    <div class="compact-meta"><?= esc($training['provider']) ?> • <?= esc($training['year']) ?></div>
                    <?= $chip ?>
                    <div class="compact-actions">
                      <button type="button" class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editTrainingModal<?= $training['id'] ?>"><i class="fas fa-edit"></i></button>
                      <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteTrainingModal<?= $training['id'] ?>"><i class="fas fa-trash"></i></button>
                    </div>
                  </div>
                  <div id="<?= $tid ?>" class="compact-body collapse">
                    <div class="compact-row"><div class="label">Title</div><div class="value"><?= esc($training['title']) ?></div></div>
                    <div class="compact-row"><div class="label">Provider</div><div class="value"><?= esc($training['provider']) ?></div></div>
                    <div class="compact-row"><div class="label">Year</div><div class="value"><?= esc($training['year']) ?></div></div>
                    <?php if (!empty($training['certificate_url'])): ?>
                      <div class="compact-row"><div class="label">Certificate</div><div class="value"><a href="<?= esc($training['certificate_url']) ?>" target="_blank">View</a></div></div>
                    <?php endif; ?>
                    <div class="compact-row"><div class="label">Status</div><div class="value"><?= esc(ucfirst(strtolower($training['status']))) ?></div></div>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        <?php endif; ?>

        <!-- PERFORMANCE (Compact) -->
        <?php if ($activeFolder === 'performance' || $printAll): ?>
          <div class="resume-section-title"><i class="fas fa-chart-line"></i> Performance Evaluations</div>

          <div class="compact-toolbar">
            <div class="text-muted">Performance snapshots.</div>
            <span class="ms-auto"></span>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPerformanceModal">
              <i class="fas fa-plus"></i> Add Performance
            </button>
          </div>

          <ul class="compact-grid">
            <?php if (empty($performance)): ?>
              <li class="compact-card"><div class="compact-body text-muted">No performance records found.</div></li>
            <?php else: ?>
              <?php foreach ($performance as $perf): $pf='perfBody'.$perf['id']; ?>
                <li class="compact-card">
                  <div class="compact-head" data-bs-toggle="collapse" data-bs-target="#<?= $pf ?>" aria-expanded="false">
                    <div class="caret"><i class="fa fa-chevron-down"></i></div>
                    <div class="compact-title"><?= esc($perf['title']) ?></div>
                    <div class="compact-meta"><?= esc($perf['year']) ?> • Rating: <?= esc($perf['rating']) ?></div>
                    <div class="compact-actions">
                      <button type="button" class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editPerformanceModal<?= $perf['id'] ?>"><i class="fas fa-edit"></i></button>
                      <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deletePerformanceModal<?= $perf['id'] ?>"><i class="fas fa-trash"></i></button>
                    </div>
                  </div>
                  <div id="<?= $pf ?>" class="compact-body collapse">
                    <div class="compact-row"><div class="label">Title</div><div class="value"><?= esc($perf['title']) ?></div></div>
                    <div class="compact-row"><div class="label">Year</div><div class="value"><?= esc($perf['year']) ?></div></div>
                    <div class="compact-row"><div class="label">Rating</div><div class="value"><?= esc($perf['rating']) ?></div></div>
                    <div class="compact-row"><div class="label">Remarks</div><div class="value"><?= esc($perf['remarks']) ?></div></div>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        <?php endif; ?>

        <!-- AWARDS (Compact) -->
        <?php if ($activeFolder === 'awards' || $printAll): ?>
          <div class="resume-section-title"><i class="fas fa-trophy"></i> Awards & Recognitions</div>

          <div class="compact-toolbar">
            <div class="text-muted">Awards overview.</div>
            <span class="ms-auto"></span>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAwardModal">
              <i class="fas fa-plus"></i> Add Award
            </button>
          </div>

          <ul class="compact-grid">
            <?php if (empty($awards)): ?>
              <li class="compact-card"><div class="compact-body text-muted">No awards found.</div></li>
            <?php else: ?>
              <?php foreach ($awards as $award): $aw='awBody'.$award['id']; ?>
                <li class="compact-card">
                  <div class="compact-head" data-bs-toggle="collapse" data-bs-target="#<?= $aw ?>" aria-expanded="false">
                    <div class="caret"><i class="fa fa-chevron-down"></i></div>
                    <div class="compact-title"><?= esc($award['title']) ?></div>
                    <div class="compact-meta"><?= esc($award['issuer']) ?> • <?= esc($award['year']) ?></div>
                    <div class="compact-actions">
                      <button type="button" class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editAwardModal<?= $award['id'] ?>"><i class="fas fa-edit"></i></button>
                      <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAwardModal<?= $award['id'] ?>"><i class="fas fa-trash"></i></button>
                    </div>
                  </div>
                  <div id="<?= $aw ?>" class="compact-body collapse">
                    <div class="compact-row"><div class="label">Title</div><div class="value"><?= esc($award['title']) ?></div></div>
                    <div class="compact-row"><div class="label">Issuer</div><div class="value"><?= esc($award['issuer']) ?></div></div>
                    <div class="compact-row"><div class="label">Year</div><div class="value"><?= esc($award['year']) ?></div></div>
                    <div class="compact-row"><div class="label">Description</div><div class="value"><?= esc($award['description']) ?></div></div>
                    <?php if (!empty($award['certificate_url'])): ?>
                      <div class="compact-row"><div class="label">Certificate</div><div class="value"><a href="<?= esc($award['certificate_url']) ?>" target="_blank">View</a></div></div>
                    <?php endif; ?>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        <?php endif; ?>

      </div>
    </section>
  </main>
</div>

<!-- ============================ MODALS (unchanged backend) ============================ -->

<!-- Add Certification Modal -->
<div class="modal fade" id="addCertModal" tabindex="-1" aria-labelledby="addCertModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCertModalLabel">Add Certification</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="add_certification">
        <div class="modal-body">
          <div class="mb-2 text-muted"><span class="text-danger">*</span> Required fields</div>
          <div class="mb-3">
            <label class="form-label">Certification Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required maxlength="255" placeholder="e.g. Certified Information Systems Security Professional (CISSP)">
          </div>
          <div class="mb-3">
            <label class="form-label">Issuing Organization <span class="text-danger">*</span></label>
            <input type="text" name="company_name" class="form-control" required maxlength="255" placeholder="e.g. (ISC)²">
          </div>
          <div class="mb-3">
            <label class="form-label">Issue Year</label>
            <input type="number" name="issue_year" class="form-control" min="1900" max="2100" step="1" placeholder="e.g. 2021">
          </div>
          <div class="mb-3">
            <label class="form-label">Expiry Year</label>
            <input type="number" name="expire_year" class="form-control" min="1900" max="2100" step="1" placeholder="e.g. 2023">
          </div>
          <div class="mb-3">
            <label class="form-label">Credential ID</label>
            <input type="text" name="credential_id" class="form-control" maxlength="128" placeholder="e.g. 123456">
          </div>
          <div class="mb-3">
            <label class="form-label">Credential URL</label>
            <input type="url" name="credential_url" class="form-control" maxlength="255" placeholder="e.g. https://www.example.com/verify">
          </div>
          <div class="mb-3">
            <label class="form-label">Visibility</label>
            <select name="visibility" class="form-control">
              <option value="public">Public</option>
              <option value="private">Private</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Certification</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Dynamic Certification Modals -->
<?php if (!empty($deanCertifications)): ?>
  <?php foreach ($deanCertifications as $cert): ?>
    <div class="modal fade" id="editCertModal<?= $cert['id'] ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Certification</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
            <input type="hidden" name="action" value="edit_certification">
            <input type="hidden" name="cert_id" value="<?= $cert['id'] ?>">
            <div class="modal-body">
              <div class="mb-2 text-muted"><span class="text-danger">*</span> Required fields</div>
              <div class="mb-3"><label class="form-label">Certification Name</label><input type="text" name="name" class="form-control" value="<?= esc($cert['name']) ?>" required maxlength="255"></div>
              <div class="mb-3"><label class="form-label">Issuing Organization</label><input type="text" name="company_name" class="form-control" value="<?= esc($cert['company_name']) ?>" required maxlength="255"></div>
              <div class="mb-3"><label class="form-label">Issue Year</label><input type="number" name="issue_year" class="form-control" value="<?= esc($cert['issue_year']) ?>" min="1900" max="2100" step="1"></div>
              <div class="mb-3"><label class="form-label">Expiry Year</label><input type="number" name="expire_year" class="form-control" value="<?= esc($cert['expire_year']) ?>" min="1900" max="2100" step="1"></div>
              <div class="mb-3"><label class="form-label">Credential ID</label><input type="text" name="credential_id" class="form-control" value="<?= esc($cert['credential_id']) ?>" maxlength="128"></div>
              <div class="mb-3">
                <label class="form-label">Visibility</label>
                <?php $vis = strtolower($cert['visibility'] ?? 'public'); ?>
                <select name="visibility" class="form-control">
                  <option value="public" <?= $vis === 'public' ? 'selected' : '' ?>>Public</option>
                  <option value="private" <?= $vis === 'private' ? 'selected' : '' ?>>Private</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="deleteCertModal<?= $cert['id'] ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Delete Certification</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
            <input type="hidden" name="action" value="delete_certification">
            <input type="hidden" name="cert_id" value="<?= $cert['id'] ?>">
            <div class="modal-body">
              <p>Are you sure you want to delete <strong><?= esc($cert['name']) ?></strong>?</p>
              <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-danger">Delete</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<!-- Add Experience Modals -->
<div class="modal fade" id="addExpModal" tabindex="-1" aria-labelledby="addExpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
      <input type="hidden" name="action" value="add_experience">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="addExpModalLabel">Add Experience</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Position</label><input type="text" name="position" class="form-control" required placeholder="e.g. Assistant Professor"></div>
          <div class="mb-3"><label class="form-label">Department</label><input type="text" name="department" class="form-control" required placeholder="e.g. Computer Science Department"></div>
          <div class="mb-3"><label class="form-label">Period</label><input type="text" name="period" class="form-control" required placeholder="e.g. 2020–Present"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add Experience</button></div>
      </div>
    </form>
  </div>
</div>

<?php foreach ($deanExperience as $exp): ?>
  <div class="modal fade" id="editExpModal<?= $exp['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="edit_experience">
        <input type="hidden" name="exp_id" value="<?= $exp['id'] ?>">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Edit Experience</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="mb-3"><label class="form-label">Position</label><input type="text" name="position" class="form-control" value="<?= esc($exp['position']) ?>" required></div>
            <div class="mb-3"><label class="form-label">Department</label><input type="text" name="department" class="form-control" value="<?= esc($exp['department']) ?>" required></div>
            <div class="mb-3"><label class="form-label">Period</label><input type="text" name="period" class="form-control" value="<?= esc($exp['period']) ?>" required></div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Changes</button></div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="deleteExpModal<?= $exp['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="delete_experience">
        <input type="hidden" name="exp_id" value="<?= $exp['id'] ?>">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Delete Experience</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <p>Are you sure you want to delete <strong><?= esc($exp['position']) ?></strong>?</p>
            <p class="text-danger">This action cannot be undone.</p>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Delete</button></div>
        </div>
      </form>
    </div>
  </div>
<?php endforeach; ?>

<!-- Add/Edit/Delete Education Modals -->
<div class="modal fade" id="addEduModal" tabindex="-1" aria-labelledby="addEduModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
      <input type="hidden" name="action" value="add_education">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="addEduModalLabel">Add Education</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Degree</label><input type="text" name="degree" class="form-control" required placeholder="e.g. Master of Science in Computer Science"></div>
          <div class="mb-3"><label class="form-label">Institution</label><input type="text" name="institution" class="form-control" required placeholder="e.g. University of the Philippines Diliman"></div>
          <div class="mb-3"><label class="form-label">Year</label><input type="number" name="year" class="form-control" min="1900" max="2100" step="1" required placeholder="e.g. 2012"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add Education</button></div>
      </div>
    </form>
  </div>
</div>

<?php foreach ($education as $edu): ?>
  <div class="modal fade" id="editEduModal<?= $edu['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="edit_education">
        <input type="hidden" name="edu_id" value="<?= $edu['id'] ?>">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Edit Education</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="mb-3"><label class="form-label">Degree</label><input type="text" name="degree" class="form-control" value="<?= esc($edu['degree']) ?>" required></div>
            <div class="mb-3"><label class="form-label">Institution</label><input type="text" name="institution" class="form-control" value="<?= esc($edu['institution']) ?>" required></div>
            <div class="mb-3"><label class="form-label">Year</label><input type="number" name="year" class="form-control" value="<?= esc($edu['year']) ?>" min="1900" max="2100" step="1" required></div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Changes</button></div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="deleteEduModal<?= $edu['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="delete_education">
        <input type="hidden" name="edu_id" value="<?= $edu['id'] ?>">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Delete Education</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <p>Are you sure you want to delete <strong><?= esc($edu['degree']) ?></strong>?</p>
            <p class="text-danger">This action cannot be undone.</p>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Delete</button></div>
        </div>
      </form>
    </div>
  </div>
<?php endforeach; ?>

<!-- Add Research Modal -->
<div class="modal fade" id="addResearchModal" tabindex="-1" aria-labelledby="addResearchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="add_research">
        <div class="modal-header"><h5 class="modal-title" id="addResearchModalLabel">Add Research</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required placeholder="e.g. AI in Education"></div>
          <div class="mb-3"><label class="form-label">Journal / Conference</label><input type="text" name="journal" class="form-control" placeholder="e.g. Philippine IT Journal"></div>
          <div class="mb-3"><label class="form-label">Year</label><input type="number" name="year" class="form-control" min="1900" max="2100" step="1" required></div>
          <div class="mb-3"><label class="form-label">Type</label><input type="text" name="type" class="form-control" placeholder="e.g. Journal, Conference, Book"></div>
          <div class="mb-3"><label class="form-label">Authors</label><input type="text" name="authors" class="form-control" placeholder="e.g. Juan Dela Cruz, Maria Santos"></div>
          <div class="mb-3"><label class="form-label">DOI / URL</label><input type="text" name="doi_url" class="form-control" placeholder="e.g. https://doi.org/xxx"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add Research</button></div>
      </form>
    </div>
  </div>
</div>

<!-- Dynamic Research Modals -->
<?php if (!empty($research)): ?>
  <?php foreach ($research as $item): $rid='resBody'.$item['id']; ?>
    <div class="modal fade" id="editResearchModal<?= $item['id'] ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Research</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
            <input type="hidden" name="action" value="edit_research">
            <input type="hidden" name="res_id" value="<?= $item['id'] ?>">
            <div class="modal-body">
              <div class="mb-2 text-muted"><span class="text-danger">*</span> Required fields</div>
              <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" value="<?= esc($item['title']) ?>" required></div>
              <div class="mb-3"><label class="form-label">Journal / Conference</label><input type="text" name="journal" class="form-control" value="<?= esc($item['journal']) ?>"></div>
              <div class="mb-3"><label class="form-label">Year</label><input type="number" name="year" class="form-control" value="<?= esc($item['year']) ?>" min="1900" max="2100" step="1"></div>
              <div class="mb-3"><label class="form-label">Type</label><input type="text" name="type" class="form-control" value="<?= esc($item['type']) ?>"></div>
              <div class="mb-3"><label class="form-label">Authors</label><input type="text" name="authors" class="form-control" value="<?= esc($item['authors']) ?>"></div>
              <div class="mb-3"><label class="form-label">DOI / URL</label><input type="text" name="doi_url" class="form-control" value="<?= esc($item['doi_url']) ?>"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="deleteResearchModal<?= $item['id'] ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Delete Research</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
            <input type="hidden" name="action" value="delete_research">
            <input type="hidden" name="res_id" value="<?= $item['id'] ?>">
            <div class="modal-body">
              <p>Are you sure you want to delete <strong><?= esc($item['title']) ?></strong>?</p>
              <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-danger">Delete</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<!-- Add Training Modal - Fix the incomplete modal -->
<div class="modal fade" id="addTrainingModal" tabindex="-1" aria-labelledby="addTrainingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="add_training">
        <div class="modal-header"><h5 class="modal-title" id="addTrainingModalLabel">Add Training</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Provider</label><input type="text" name="provider" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Year</label><input type="number" name="year" class="form-control" min="1900" max="2100" step="1"></div>
          <div class="mb-3"><label class="form-label">Certificate URL</label><input type="url" name="certificate_url" class="form-control"></div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <option value="active">Active</option>
              <option value="expired">Expired</option>
            </select>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add Training</button></div>
      </form>
    </div>
  </div>
</div>

<!-- Add Performance Modal - Add the missing modal -->
<div class="modal fade" id="addPerformanceModal" tabindex="-1" aria-labelledby="addPerformanceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="add_performance">
        <div class="modal-header"><h5 class="modal-title" id="addPerformanceModalLabel">Add Performance</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Year</label><input type="number" name="year" class="form-control" min="1900" max="2100" step="1"></div>
          <div class="mb-3"><label class="form-label">Rating</label><input type="text" name="rating" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Remarks</label><textarea name="remarks" class="form-control"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add Performance</button></div>
      </form>
    </div>
  </div>
</div>

<!-- Add Award Modal - Add the missing modal -->
<div class="modal fade" id="addAwardModal" tabindex="-1" aria-labelledby="addAwardModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="add_award">
        <div class="modal-header"><h5 class="modal-title" id="addAwardModalLabel">Add Award</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Year</label><input type="number" name="year" class="form-control" min="1900" max="2100" step="1"></div>
          <div class="mb-3"><label class="form-label">Issuer</label><input type="text" name="issuer" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control"></textarea></div>
          <div class="mb-3"><label class="form-label">Certificate URL</label><input type="url" name="certificate_url" class="form-control"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add Award</button></div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Personal Modal - Add the missing modal -->
<div class="modal fade" id="editPersonalModal" tabindex="-1" aria-labelledby="editPersonalModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width: 700px; min-width: 600px;">
    <div class="modal-content">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="edit_personal">
        <input type="hidden" name="user_id" value="<?= esc($deanId) ?>">
        <div class="modal-header"><h5 class="modal-title" id="editPersonalModalLabel">Edit Personal Information</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Birthday</label><input type="date" name="birthday" class="form-control" value="<?= esc($personal['birthday'] ?? '') ?>"></div>

            <?php
              $genderRaw  = (string)($personal['gender'] ?? '');
              $maritalRaw = (string)($personal['marital_status'] ?? '');
              $g = mb_strtolower($genderRaw, 'UTF-8');
              $m = mb_strtolower($maritalRaw, 'UTF-8');
            ?>
            <div class="col-md-6">
              <label class="form-label">Gender</label>
              <select name="gender" class="form-control">
                <option value="">Select</option>
                <option value="male"   <?= $g === 'male'   ? 'selected' : '' ?>>Male</option>
                <option value="female" <?= $g === 'female' ? 'selected' : '' ?>>Female</option>
                <option value="other"  <?= $g === 'other'  ? 'selected' : '' ?>>Other</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Marital Status</label>
              <select name="marital_status" class="form-control">
                <option value="">Select</option>
                <option value="single"    <?= $m === 'single'    ? 'selected' : '' ?>>Single</option>
                <option value="married"   <?= $m === 'married'   ? 'selected' : '' ?>>Married</option>
                <option value="widowed"   <?= $m === 'widowed'   ? 'selected' : '' ?>>Widowed</option>
                <option value="separated" <?= $m === 'separated' ? 'selected' : '' ?>>Separated</option>
              </select>
            </div>

            <div class="col-md-6"><label class="form-label">Nationality</label><input type="text" name="nationality" class="form-control" value="<?= esc($personal['nationality'] ?? '') ?>" placeholder="e.g. Filipino"></div>
            <div class="col-md-12"><label class="form-label">Address</label><input type="text" name="address" class="form-control" value="<?= esc($personal['address'] ?? '') ?>" placeholder="e.g. 123 Main St, Manila"></div>
            <div class="col-md-6"><label class="form-label">Contact Number</label><input type="text" name="contact_number" class="form-control" value="<?= esc($personal['contact_number'] ?? '') ?>" placeholder="e.g. 09171234567"></div>
            <div class="col-md-6"><label class="form-label">Emergency Contact Name</label><input type="text" name="emergency_contact_name" class="form-control" value="<?= esc($personal['emergency_contact_name'] ?? '') ?>" placeholder="e.g. Juan Dela Cruz"></div>
            <div class="col-md-6"><label class="form-label">Emergency Contact Number</label><input type="text" name="emergency_contact_number" class="form-control" value="<?= esc($personal['emergency_contact_number'] ?? '') ?>" placeholder="e.g. 09181234567"></div>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Changes</button></div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Profile Modal - Fix the structure -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit_profile">
        <input type="hidden" name="user_id" value="<?= esc($deanId) ?>">
        <div class="modal-header"><h5 class="modal-title" id="editProfileModalLabel">Edit Profile Information</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Full Name <span class="text-danger">*</span></label><input type="text" name="full_name" class="form-control" value="<?= esc($profile['full_name'] ?? $fullName) ?>" required maxlength="255" placeholder="e.g. Dr. Juan Dela Cruz"></div>
            <div class="col-md-6"><label class="form-label">Employee ID <span class="text-danger">*</span></label><input type="text" name="employee_id" class="form-control" value="<?= esc($profile['employee_id'] ?? '') ?>" required maxlength="32" placeholder="e.g. CCIT-0001"></div>
            <div class="col-md-6"><label class="form-label">Work Email <span class="text-danger">*</span></label><input type="email" name="work_email" class="form-control" value="<?= esc($profile['work_email'] ?? ($username . '@adamson.edu.ph')) ?>" required maxlength="128" placeholder="e.g. juandelacruz@adamson.edu.ph"></div>
            <div class="col-md-6"><label class="form-label">Position <span class="text-danger">*</span></label><input type="text" name="position" class="form-control" value="<?= esc($profile['position'] ?? 'Dean') ?>" required maxlength="128" placeholder="e.g. Dean"></div>
            <div class="col-md-6"><label class="form-label">Department <span class="text-danger">*</span></label><input type="text" name="department" class="form-control" value="<?= esc($profile['department'] ?? 'College of Computer and Information Technology') ?>" required maxlength="128" placeholder="e.g. College of Computer and Information Technology"></div>
            <div class="col-md-6"><label class="form-label">Employment Type</label><input type="text" name="employment_type" class="form-control" value="<?= esc($profile['employment_type'] ?? '') ?>" maxlength="64" placeholder="e.g. Regular / Full-time"></div>
            <div class="col-md-6"><label class="form-label">Date Hired</label><input type="date" name="date_hired" class="form-control" value="<?= esc($profile['date_hired'] ?? '') ?>" min="1950-01-01" max="<?= date('Y-m-d') ?>"></div>
            <div class="col-md-6"><label class="form-label">Office Location</label><input type="text" name="office_location" class="form-control" value="<?= esc($profile['office_location'] ?? '') ?>" maxlength="128" placeholder="e.g. Dean's Office, CCIT Bldg."></div>
            <div class="col-md-6"><label class="form-label">Profile Photo</label><input type="file" name="profile_photo" class="form-control" accept="image/*"></div>
            <div class="col-md-12"><label class="form-label">Specializations</label><input type="text" name="specializations" class="form-control" value="<?= esc($profile['specializations'] ?? '') ?>" maxlength="255" placeholder="e.g. Academic Leadership, IT Education"></div>
            <div class="col-md-12"><label class="form-label">Languages</label><input type="text" name="languages" class="form-control" value="<?= esc($profile['languages'] ?? '') ?>" maxlength="128" placeholder="e.g. English, Filipino"></div>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Changes</button></div>
      </form>
    </div>
  </div>
</div>

<?php if ($autoPrint): ?>
<script>
  // Wait a tick to ensure collapses are expanded by the print CSS override
  window.addEventListener('load', function () {
    window.print();
    // After printing, remove autoprint to avoid repeat on refresh
    const url = new URL(window.location.href);
    url.searchParams.delete('autoprint');
    history.replaceState(null, '', url.toString());
  });
</script>
<?php endif; ?>

<script>
  // "Print This Section" button just opens the browser print dialog
  (function(){
    const btn = document.getElementById('btnPrintSection');
    if (btn) btn.addEventListener('click', function(e){
      e.preventDefault();
      window.print();
    });
  })();
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
