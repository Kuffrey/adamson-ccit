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
    .resume-list { list-style:none; margin:0; padding:0; }
    .resume-list-item { border-bottom:1px solid #f3f4f6; display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; padding:.4rem 0; }
    .resume-list-item:last-child { border-bottom:none; margin-bottom:0; padding-bottom:0; }
    .resume-label { font-weight:600; color:#374151; margin-bottom:.2rem; font-size:1rem; min-width:180px; }
    .resume-value { color:#111827; font-size:.98rem; margin-bottom:.1rem; flex:1; }
    .resume-cert-status { display:inline-block; border-radius:8px; padding:.1rem .6rem; font-size:.85rem; font-weight:600; margin-left:.3rem; background:#dcfce7; color:#166534; }
    .resume-cert-status.expired { background:#fef3c7; color:#92400e; }

    .portfolio-folder-nav { display:flex; flex-wrap:wrap; gap:.5rem; margin: .5rem 0 1rem; }
    .portfolio-folder-btn { display:inline-flex; align-items:center; gap:.4rem; padding:.4rem .7rem; border:1px solid #e5e7eb; border-radius:8px; text-decoration:none; color:#1f2937; background:#fff; }
    .portfolio-folder-btn.active { background:#eff6ff; border-color:#bfdbfe; color:#1d4ed8; }

    .btn-edit { background:#f8fafc; color:#6b7280; border:1px solid #e5e7eb; border-radius:6px; font-size:.9rem; padding:4px 10px; }
    .btn-edit:hover { background:#e0f2fe; color:#0369a1; }
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
            <div class="resume-meta"><?= esc($profile['office_location'] ?? "Dean's Office, CCIT Bldg.") ?></div>
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
        <?php if ($activeFolder === 'general'): ?>
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

        <!-- CERTIFICATIONS -->
        <?php if ($activeFolder === 'certifications'): ?>
          <div class="resume-section-title"><i class="fas fa-certificate"></i> Licenses & Certifications</div>
          <ul class="resume-list">
            <?php if (empty($deanCertifications)): ?>
              <li class="resume-list-item"><span class="resume-value text-muted">No certifications on file.</span></li>
            <?php else: ?>
              <?php foreach ($deanCertifications as $cert): ?>
                <li class="resume-list-item">
                  <div>
                    <div class="resume-label">License / Certification Title</div>
                    <div class="resume-value"><?= esc($cert['name']) ?></div>

                    <div class="resume-label">Issuing Body / Organization</div>
                    <div class="resume-value"><?= esc($cert['company_name']) ?></div>

                    <div class="resume-label">Issue Year & Expiry Year</div>
                    <div class="resume-value"><?= esc($cert['issue_year']) ?> - <?= $cert['expire_year'] ? esc($cert['expire_year']) : 'No Expiry' ?></div>

                    <?php if (!empty($cert['credential_id']) || !empty($cert['credential_url'])): ?>
                      <div class="resume-label">Credential ID / Verification Link</div>
                      <div class="resume-value">
                        <?php if (!empty($cert['credential_id'])): ?>
                          <span>ID: <?= esc($cert['credential_id']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($cert['credential_url'])): ?>
                          <span> | <a href="<?= esc($cert['credential_url']) ?>" target="_blank" class="text-primary">View</a></span>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>

                    <div class="resume-label">Status</div>
                    <div class="resume-value">
                      <?php $cStatus = strtolower($cert['status'] ?? 'active'); ?>
                      <span class="resume-cert-status<?= $cStatus === 'expired' ? ' expired' : '' ?>">
                        <?= esc(ucfirst($cStatus)) ?>
                      </span>
                    </div>

                    <div class="resume-label">Visibility</div>
                    <div class="resume-value"><?= esc(ucfirst($cert['visibility'] ?? 'public')) ?></div>
                  </div>
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editCertModal<?= $cert['id'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCertModal<?= $cert['id'] ?>">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
          <div class="mt-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCertModal">
              <i class="fas fa-plus"></i> Add Certification
            </button>
          </div>
        <?php elseif ($activeFolder === 'experience'): ?>
          <div class="resume-section-title"><i class="fas fa-briefcase"></i> Employment & Academic Record</div>
          <ul class="resume-list">
            <?php if (empty($deanExperience)): ?>
              <li class="resume-list-item"><span class="resume-value text-muted">No experience records found.</span></li>
            <?php else: ?>
              <?php foreach ($deanExperience as $exp): ?>
                <li class="resume-list-item">
                  <div class="resume-label">Position</div>
                  <div class="resume-value"><?= esc($exp['position']) ?>, <?= esc($exp['department']) ?> (<?= esc($exp['period']) ?>)</div>
                  <div class="mt-2 d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editExpModal<?= $exp['id'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteExpModal<?= $exp['id'] ?>">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
          <div class="mt-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExpModal">
              <i class="fas fa-plus"></i> Add Experience
            </button>
          </div>

        <?php elseif ($activeFolder === 'education'): ?>
          <div class="resume-section-title"><i class="fas fa-graduation-cap"></i> Educational Background</div>
          <ul class="resume-list">
            <?php if (empty($education)): ?>
              <li class="resume-list-item"><span class="resume-value text-muted">No education records found.</span></li>
            <?php else: ?>
              <?php foreach ($education as $edu): ?>
                <li class="resume-list-item">
                  <div class="resume-label">Degree</div>
                  <div class="resume-value"><?= esc($edu['degree']) ?>, <?= esc($edu['institution']) ?> (<?= esc($edu['year']) ?>)</div>
                  <div class="mt-2 d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editEduModal<?= $edu['id'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteEduModal<?= $edu['id'] ?>">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
          <div class="mt-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEduModal">
              <i class="fas fa-plus"></i> Add Education
            </button>
          </div>

        <?php elseif ($activeFolder === 'personal'): ?>
          <div class="resume-section-title"><i class="fas fa-user"></i> Personal Information</div>
          <ul class="resume-list">
            <?php if (empty($personal)): ?>
              <li class="resume-list-item"><span class="resume-value text-muted">No personal information found.</span></li>
            <?php else: ?>
              <li class="resume-list-item"><div class="resume-label">Birthday</div><div class="resume-value"><?= esc($personal['birthday'] ?? '') ?></div></li>
              <li class="resume-list-item">
                <div class="resume-label">Gender</div>
                <div class="resume-value"><?= title_case($personal['gender'] ?? '') ?></div>
              </li>
              <li class="resume-list-item">
                <div class="resume-label">Marital Status</div>
                <div class="resume-value"><?= title_case($personal['marital_status'] ?? '') ?></div>
              </li>
              <li class="resume-list-item">
                <div class="resume-label">Nationality</div>
                <div class="resume-value"><?= title_case($personal['nationality'] ?? '') ?></div>
              </li>
              <li class="resume-list-item">
                <div class="resume-label">Address</div>
                <div class="resume-value"><?= esc($personal['address'] ?? '') ?></div>
              </li>
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

        <?php elseif ($activeFolder === 'research'): ?>
          <div class="resume-section-title"><i class="fas fa-book"></i> Research & Publications</div>
          <ul class="resume-list">
            <?php if (empty($research)): ?>
              <li class="resume-list-item"><span class="resume-value text-muted">No research records found.</span></li>
            <?php else: ?>
              <?php foreach ($research as $item): ?>
                <li class="resume-list-item">
                  <div class="resume-label">Title</div>
                  <div class="resume-value"><?= esc($item['title']) ?>, <?= esc($item['journal']) ?> (<?= esc($item['year']) ?>)</div>
                  <div class="resume-label">Type</div>
                  <div class="resume-value"><?= title_case($item['type']) ?></div>
                  <div class="resume-label">Authors</div>
                  <div class="resume-value"><?= esc($item['authors']) ?></div>
                  <?php if (!empty($item['doi_url'])): ?>
                    <div class="resume-label">DOI / URL</div>
                    <div class="resume-value"><a href="<?= esc($item['doi_url']) ?>" target="_blank" class="link-cert">View Link</a></div>
                  <?php endif; ?>
                  <div class="mt-2 d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editResearchModal<?= $item['id'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteResearchModal<?= $item['id'] ?>">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
          <div class="mt-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResearchModal">
              <i class="fas fa-plus"></i> Add Research
            </button>
          </div>

        <?php elseif ($activeFolder === 'trainings'): ?>
          <div class="resume-section-title"><i class="fas fa-chalkboard-teacher"></i> Trainings & Seminars</div>
          <ul class="resume-list">
            <?php if (empty($trainings)): ?>
              <li class="resume-list-item"><span class="resume-value text-muted">No trainings found.</span></li>
            <?php else: ?>
              <?php foreach ($trainings as $training): ?>
                <li class="resume-list-item">
                  <div class="resume-label">Title</div>
                  <div class="resume-value"><?= esc($training['title']) ?>, <?= esc($training['provider']) ?> (<?= esc($training['year']) ?>)</div>
                  <?php if (!empty($training['certificate_url'])): ?>
                    <div class="resume-label">Certificate</div>
                    <div class="resume-value"><a href="<?= esc($training['certificate_url']) ?>" target="_blank" class="link-cert">View Certificate</a></div>
                  <?php endif; ?>
                  <div class="resume-label">Status</div>
                  <div class="resume-value"><?= esc(ucfirst(strtolower($training['status']))) ?></div>
                  <div class="mt-2 d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editTrainingModal<?= $training['id'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTrainingModal<?= $training['id'] ?>">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
          <div class="mt-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTrainingModal">
              <i class="fas fa-plus"></i> Add Training
            </button>
          </div>

        <?php elseif ($activeFolder === 'performance'): ?>
          <div class="resume-section-title"><i class="fas fa-chart-line"></i> Performance Evaluations</div>
          <ul class="resume-list">
            <?php if (empty($performance)): ?>
              <li class="resume-list-item"><span class="resume-value text-muted">No performance records found.</span></li>
            <?php else: ?>
              <?php foreach ($performance as $perf): ?>
                <li class="resume-list-item">
                  <div class="resume-label">Title</div>
                  <div class="resume-value"><?= esc($perf['title']) ?> (<?= esc($perf['year']) ?>)</div>
                  <div class="resume-label">Rating</div>
                  <div class="resume-value"><?= esc($perf['rating']) ?></div>
                  <div class="resume-label">Remarks</div>
                  <div class="resume-value"><?= esc($perf['remarks']) ?></div>
                  <div class="mt-2 d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editPerformanceModal<?= $perf['id'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deletePerformanceModal<?= $perf['id'] ?>">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
          <div class="mt-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPerformanceModal">
              <i class="fas fa-plus"></i> Add Performance
            </button>
          </div>

        <?php elseif ($activeFolder === 'awards'): ?>
          <div class="resume-section-title"><i class="fas fa-trophy"></i> Awards & Recognitions</div>
          <ul class="resume-list">
            <?php if (empty($awards)): ?>
              <li class="resume-list-item"><span class="resume-value text-muted">No awards found.</span></li>
            <?php else: ?>
              <?php foreach ($awards as $award): ?>
                <li class="resume-list-item">
                  <div class="resume-label">Title</div>
                  <div class="resume-value"><?= esc($award['title']) ?> (<?= esc($award['year']) ?>)</div>
                  <div class="resume-label">Issuer</div>
                  <div class="resume-value"><?= esc($award['issuer']) ?></div>
                  <div class="resume-label">Description</div>
                  <div class="resume-value"><?= esc($award['description']) ?></div>
                  <?php if (!empty($award['certificate_url'])): ?>
                    <div class="resume-label">Certificate</div>
                    <div class="resume-value"><a href="<?= esc($award['certificate_url']) ?>" target="_blank" class="link-cert">View Certificate</a></div>
                  <?php endif; ?>
                  <div class="mt-2 d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editAwardModal<?= $award['id'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAwardModal<?= $award['id'] ?>">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
          <div class="mt-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAwardModal">
              <i class="fas fa-plus"></i> Add Award
            </button>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </main>
</div>

<!-- MODALS (unchanged functionality, just display tweaks where helpful) -->

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

<!-- Add/Edit/Delete Experience Modals -->
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

<!-- Edit Personal Modal -->
<div class="modal fade" id="editPersonalModal" tabindex="-1" aria-labelledby="editPersonalModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width: 700px; min-width: 600px;">
    <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
      <input type="hidden" name="action" value="edit_personal">
      <input type="hidden" name="user_id" value="<?= esc($deanId) ?>">
      <div class="modal-content">
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
      </div>
    </form>
  </div>
</div>

<!-- Research Modals -->
<div class="modal fade" id="addResearchModal" tabindex="-1" aria-labelledby="addResearchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
      <input type="hidden" name="action" value="add_research">
      <div class="modal-content">
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
      </div>
    </form>
  </div>
</div>

<?php if (!empty($research) && is_array($research)): ?>
  <?php foreach ($research as $item): ?>
    <div class="modal fade" id="editResearchModal<?= $item['id'] ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
          <input type="hidden" name="action" value="edit_research">
          <input type="hidden" name="research_id" value="<?= $item['id'] ?>">
          <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="editResearchModalLabel<?= $item['id'] ?>">Edit Research</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
              <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" value="<?= esc($item['title']) ?>" required></div>
              <div class="mb-3"><label class="form-label">Journal / Conference</label><input type="text" name="journal" class="form-control" value="<?= esc($item['journal']) ?>"></div>
              <div class="mb-3"><label class="form-label">Year</label><input type="number" name="year" class="form-control" value="<?= esc($item['year']) ?>" min="1900" max="2100" step="1"></div>
              <div class="mb-3"><label class="form-label">Type</label><input type="text" name="type" class="form-control" value="<?= esc($item['type']) ?>" placeholder="e.g. Journal, Conference, Book"></div>
              <div class="mb-3"><label class="form-label">Authors</label><input type="text" name="authors" class="form-control" value="<?= esc($item['authors']) ?>" placeholder="e.g. Juan Dela Cruz, Maria Santos"></div>
              <div class="mb-3"><label class="form-label">DOI / URL</label><input type="text" name="doi_url" class="form-control" value="<?= esc($item['doi_url']) ?>" placeholder="e.g. https://doi.org/xxx"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Changes</button></div>
          </div>
        </form>
      </div>
    </div>

    <div class="modal fade" id="deleteResearchModal<?= $item['id'] ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
          <input type="hidden" name="action" value="delete_research">
          <input type="hidden" name="research_id" value="<?= $item['id'] ?>">
          <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="deleteResearchModalLabel<?= $item['id'] ?>">Delete Research</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
              <p>Are you sure you want to delete <strong><?= esc($item['title']) ?></strong>?</p>
              <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Delete</button></div>
          </div>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<!-- Trainings -->
<div class="modal fade" id="addTrainingModal" tabindex="-1" aria-labelledby="addTrainingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
      <input type="hidden" name="action" value="add_training">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="addTrainingModalLabel">Add Training</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Provider</label><input type="text" name="provider" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Year</label><input type="number" name="year" class="form-control" min="1900" max="2100" step="1"></div>
          <div class="mb-3"><label class="form-label">Certificate URL</label><input type="url" name="certificate_url" class="form-control"></div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <?php $tDefault = strtolower($training['status'] ?? 'active'); ?>
              <option value="active" <?= ($tDefault === 'active') ? 'selected' : '' ?>>Active</option>
              <option value="expired" <?= ($tDefault === 'expired') ? 'selected' : '' ?>>Expired</option>
            </select>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add Training</button></div>
      </div>
    </form>
  </div>
</div>

<?php foreach ($trainings as $training): ?>
  <div class="modal fade" id="editTrainingModal<?= $training['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="edit_training">
        <input type="hidden" name="training_id" value="<?= $training['id'] ?>">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Edit Training</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" value="<?= esc($training['title']) ?>" required></div>
            <div class="mb-3"><label class="form-label">Provider</label><input type="text" name="provider" class="form-control" value="<?= esc($training['provider']) ?>"></div>
            <div class="mb-3"><label class="form-label">Year</label><input type="number" name="year" class="form-control" value="<?= esc($training['year']) ?>" min="1900" max="2100" step="1"></div>
            <div class="mb-3"><label class="form-label">Certificate URL</label><input type="url" name="certificate_url" class="form-control" value="<?= esc($training['certificate_url']) ?>"></div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <?php $ts = strtolower($training['status'] ?? 'active'); ?>
              <select name="status" class="form-control">
                <option value="active"  <?= $ts === 'active'  ? 'selected' : '' ?>>Active</option>
                <option value="expired" <?= $ts === 'expired' ? 'selected' : '' ?>>Expired</option>
              </select>
            </div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Changes</button></div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="deleteTrainingModal<?= $training['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="delete_training">
        <input type="hidden" name="training_id" value="<?= $training['id'] ?>">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Delete Training</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <p>Are you sure you want to delete <strong><?= esc($training['title']) ?></strong>?</p>
            <p class="text-danger">This action cannot be undone.</p>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Delete</button></div>
        </div>
      </form>
    </div>
  </div>
<?php endforeach; ?>

<!-- Performance -->
<div class="modal fade" id="addPerformanceModal" tabindex="-1" aria-labelledby="addPerformanceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
      <input type="hidden" name="action" value="add_performance">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="addPerformanceModalLabel">Add Performance</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Year</label><input type="number" name="year" class="form-control" min="1900" max="2100" step="1"></div>
          <div class="mb-3"><label class="form-label">Rating</label><input type="text" name="rating" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Remarks</label><textarea name="remarks" class="form-control"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add Performance</button></div>
      </div>
    </form>
  </div>
</div>

<?php foreach ($performance as $perf): ?>
  <div class="modal fade" id="editPerformanceModal<?= $perf['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="edit_performance">
        <input type="hidden" name="performance_id" value="<?= $perf['id'] ?>">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Edit Performance</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" value="<?= esc($perf['title']) ?>" required></div>
            <div class="mb-3"><label class="form-label">Year</label><input type="number" name="year" class="form-control" value="<?= esc($perf['year']) ?>" min="1900" max="2100" step="1"></div>
            <div class="mb-3"><label class="form-label">Rating</label><input type="text" name="rating" class="form-control" value="<?= esc($perf['rating']) ?>"></div>
            <div class="mb-3"><label class="form-label">Remarks</label><textarea name="remarks" class="form-control"><?= esc($perf['remarks']) ?></textarea></div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Changes</button></div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="deletePerformanceModal<?= $perf['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="delete_performance">
        <input type="hidden" name="performance_id" value="<?= $perf['id'] ?>">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Delete Performance</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <p>Are you sure you want to delete <strong><?= esc($perf['title']) ?></strong>?</p>
            <p class="text-danger">This action cannot be undone.</p>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Delete</button></div>
        </div>
      </form>
    </div>
  </div>
<?php endforeach; ?>

<!-- Awards -->
<div class="modal fade" id="addAwardModal" tabindex="-1" aria-labelledby="addAwardModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
      <input type="hidden" name="action" value="add_award">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="addAwardModalLabel">Add Award</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Year</label><input type="number" name="year" class="form-control" min="1900" max="2100" step="1"></div>
          <div class="mb-3"><label class="form-label">Issuer</label><input type="text" name="issuer" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control"></textarea></div>
          <div class="mb-3"><label class="form-label">Certificate URL</label><input type="url" name="certificate_url" class="form-control"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add Award</button></div>
      </div>
    </form>
  </div>
</div>

<?php foreach ($awards as $award): ?>
  <div class="modal fade" id="editAwardModal<?= $award['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="edit_award">
        <input type="hidden" name="award_id" value="<?= $award['id'] ?>">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Edit Award</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" value="<?= esc($award['title']) ?>" required></div>
            <div class="mb-3"><label class="form-label">Year</label><input type="number" name="year" class="form-control" value="<?= esc($award['year']) ?>" min="1900" max="2100" step="1"></div>
            <div class="mb-3"><label class="form-label">Issuer</label><input type="text" name="issuer" class="form-control" value="<?= esc($award['issuer']) ?>"></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control"><?= esc($award['description']) ?></textarea></div>
            <div class="mb-3"><label class="form-label">Certificate URL</label><input type="url" name="certificate_url" class="form-control" value="<?= esc($award['certificate_url']) ?>"></div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Changes</button></div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="deleteAwardModal<?= $award['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php">
        <input type="hidden" name="action" value="delete_award">
        <input type="hidden" name="award_id" value="<?= $award['id'] ?>">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Delete Award</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <p>Are you sure you want to delete <strong><?= esc($award['title']) ?></strong>?</p>
            <p class="text-danger">This action cannot be undone.</p>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Delete</button></div>
        </div>
      </form>
    </div>
  </div>
<?php endforeach; ?>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <form method="post" action="/adamson-ccit/handlers/dean_portfolio_handler.php" enctype="multipart/form-data">
      <input type="hidden" name="action" value="edit_profile">
      <input type="hidden" name="user_id" value="<?= esc($deanId) ?>">
      <div class="modal-content">
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
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
