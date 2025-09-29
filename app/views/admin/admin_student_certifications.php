<?php
// app/views/admin/admin_student_certifications.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../../models/StudentCertificationsPageSettings.php';
require_once __DIR__ . '/../../models/StudentCertification.php';

if (!function_exists('esc')) {
    function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
$user = $_SESSION['user'] ?? [];
$username = $user['username'] ?? 'Admin';

$notice = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (isset($_POST['settings'])) {
      StudentCertificationsPageSettings::updateSettings($_POST['settings']);
      $notice = 'Page settings saved.';
    } elseif (isset($_POST['add_certification'])) {
      StudentCertification::create($_POST['certification'] ?? []);
      $notice = 'Certification added.';
    } elseif (isset($_POST['edit_certification'])) {
      StudentCertification::update((int)$_POST['id'], $_POST['certification'] ?? []);
      $notice = 'Certification updated.';
    } elseif (isset($_POST['delete_certification'])) {
      StudentCertification::delete((int)$_POST['id']);
      $notice = 'Certification deleted.';
    }
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

$settings = StudentCertificationsPageSettings::getSettings();
$certifications = StudentCertification::getAll();
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Student → Certifications</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Student Certifications Management</h1>
      
      <?php if ($notice): ?>
        <div class="alert alert-<?= str_starts_with($notice, 'Error:') ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
          <?= esc($notice) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Page Settings Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Page Settings</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#pageSettingsCard" aria-expanded="false" aria-controls="pageSettingsCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="pageSettingsCard">
          <div class="card-body">
            <form method="post" autocomplete="off">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Hero Image URL</label>
                  <input type="text" class="form-control" name="settings[hero_image_url]" 
                         value="<?= esc($settings['hero_image_url'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Hero Title</label>
                  <input type="text" class="form-control" name="settings[hero_title]" 
                         value="<?= esc($settings['hero_title'] ?? '') ?>">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Hero Lead Text</label>
                <textarea class="form-control" name="settings[hero_lead]" rows="2"><?= esc($settings['hero_lead'] ?? '') ?></textarea>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Settings
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Add New Certification Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Add New Certification</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#addCertificationCard" aria-expanded="false" aria-controls="addCertificationCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="addCertificationCard">
          <div class="card-body">
            <form method="post" autocomplete="off">
              <input type="hidden" name="add_certification" value="1">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Certification Name</label>
                  <input type="text" class="form-control" name="certification[name]" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Issuer</label>
                  <input type="text" class="form-control" name="certification[issuer]" 
                         placeholder="e.g., Microsoft, AWS, Oracle" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="certification[description]" rows="4" required 
                          placeholder="Description of the certification"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Badge URL</label>
                <input type="url" class="form-control" name="certification[badge_url]" 
                       placeholder="Link to certification badge image">
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Certification
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Existing Certifications Card -->
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Existing Certifications (<?= count($certifications) ?>)</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#certificationsListCard" aria-expanded="true" aria-controls="certificationsListCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse show" id="certificationsListCard">
          <div class="card-body">
            <?php if (empty($certifications)): ?>
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No certifications added yet.
              </div>
            <?php else: ?>
              <div class="row">
                <?php foreach ($certifications as $cert): ?>
                  <div class="col-lg-6 mb-4">
                    <div class="card certification-item">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                          <div>
                            <h6 class="card-title mb-1"><?= esc($cert['name']) ?></h6>
                            <div class="certification-meta">
                              <span class="text-muted">Issued by: <strong><?= esc($cert['issuer']) ?></strong></span>
                              <?php if ($cert['badge_url']): ?>
                                <span class="ms-2">
                                  <a href="<?= esc($cert['badge_url']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt"></i> View Badge
                                  </a>
                                </span>
                              <?php endif; ?>
                            </div>
                          </div>
                          <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                    data-bs-toggle="dropdown">
                              <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                              <li>
                                <button class="dropdown-item" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#editCertification<?= $cert['id'] ?>">
                                  <i class="fas fa-edit"></i> Edit
                                </button>
                              </li>
                              <li>
                                <form method="post" style="display: inline;" 
                                      onsubmit="return confirm('Delete this certification?')">
                                  <input type="hidden" name="delete_certification" value="1">
                                  <input type="hidden" name="id" value="<?= (int)$cert['id'] ?>">
                                  <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash"></i> Delete
                                  </button>
                                </form>
                              </li>
                            </ul>
                          </div>
                        </div>
                        
                        <p class="certification-description"><?= esc($cert['description']) ?></p>
                        
                        <!-- Edit Form (Collapsible) -->
                        <div class="collapse mt-3" id="editCertification<?= $cert['id'] ?>">
                          <div class="border-top pt-3">
                            <form method="post">
                              <input type="hidden" name="edit_certification" value="1">
                              <input type="hidden" name="id" value="<?= (int)$cert['id'] ?>">
                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">Certification Name</label>
                                  <input type="text" class="form-control" name="certification[name]" 
                                         value="<?= esc($cert['name']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">Issuer</label>
                                  <input type="text" class="form-control" name="certification[issuer]" 
                                         value="<?= esc($cert['issuer']) ?>" required>
                                </div>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="certification[description]" rows="3" required><?= esc($cert['description']) ?></textarea>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Badge URL</label>
                                <input type="url" class="form-control" name="certification[badge_url]" 
                                       value="<?= esc($cert['badge_url']) ?>">
                              </div>
                              <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                  <i class="fas fa-save"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" 
                                        data-bs-toggle="collapse" data-bs-target="#editCertification<?= $cert['id'] ?>">
                                  Cancel
                                </button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>