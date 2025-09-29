<?php
// app/views/admin/admin_student_scholarships.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../../models/StudentScholarshipsPageSettings.php';
require_once __DIR__ . '/../../models/StudentScholarship.php';

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
      StudentScholarshipsPageSettings::updateSettings($_POST['settings']);
      $notice = 'Page settings saved.';
    } elseif (isset($_POST['add_scholarship'])) {
      StudentScholarship::create($_POST['scholarship'] ?? []);
      $notice = 'Scholarship added.';
    } elseif (isset($_POST['edit_scholarship'])) {
      StudentScholarship::update((int)$_POST['id'], $_POST['scholarship'] ?? []);
      $notice = 'Scholarship updated.';
    } elseif (isset($_POST['delete_scholarship'])) {
      StudentScholarship::delete((int)$_POST['id']);
      $notice = 'Scholarship deleted.';
    }
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

$settings = StudentScholarshipsPageSettings::getSettings();
$scholarships = StudentScholarship::getAll();
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Student → Scholarships</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Student Scholarships Management</h1>
      
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

      <!-- Add New Scholarship Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Add New Scholarship</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#addScholarshipCard" aria-expanded="false" aria-controls="addScholarshipCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="addScholarshipCard">
          <div class="card-body">
            <form method="post" autocomplete="off">
              <input type="hidden" name="add_scholarship" value="1">
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label class="form-label">Scholarship Name</label>
                  <input type="text" class="form-control" name="scholarship[name]" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Type</label>
                  <select class="form-control" name="scholarship[type]" required>
                    <option value="Freshmen">Freshmen</option>
                    <option value="University">University</option>
                    <option value="External">External</option>
                  </select>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Summary</label>
                <textarea class="form-control" name="scholarship[summary]" rows="3" required 
                          placeholder="Brief summary of the scholarship"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Conditions</label>
                <textarea class="form-control" name="scholarship[conditions]" rows="4" 
                          placeholder="Eligibility conditions and requirements"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Requirements</label>
                <textarea class="form-control" name="scholarship[requirements]" rows="3" 
                          placeholder="Documents and requirements needed"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Examples</label>
                <textarea class="form-control" name="scholarship[examples]" rows="3" 
                          placeholder="Examples or additional information"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Learn More URL</label>
                <input type="url" class="form-control" name="scholarship[learn_more_url]" 
                       placeholder="Link to detailed information">
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Scholarship
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Existing Scholarships Card -->
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Existing Scholarships (<?= count($scholarships) ?>)</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#scholarshipsListCard" aria-expanded="true" aria-controls="scholarshipsListCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse show" id="scholarshipsListCard">
          <div class="card-body">
            <?php if (empty($scholarships)): ?>
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No scholarships added yet.
              </div>
            <?php else: ?>
              <div class="row">
                <?php foreach ($scholarships as $scholarship): ?>
                  <div class="col-lg-6 mb-4">
                    <div class="card scholarship-item">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                          <div>
                            <h6 class="card-title mb-1"><?= esc($scholarship['name']) ?></h6>
                            <div class="scholarship-meta">
                              <span class="badge bg-primary"><?= esc($scholarship['type']) ?></span>
                              <?php if ($scholarship['learn_more_url']): ?>
                                <span class="ms-2">
                                  <a href="<?= esc($scholarship['learn_more_url']) ?>" target="_blank" 
                                     class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt"></i> Learn More
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
                                        data-bs-target="#editScholarship<?= $scholarship['id'] ?>">
                                  <i class="fas fa-edit"></i> Edit
                                </button>
                              </li>
                              <li>
                                <form method="post" style="display: inline;" 
                                      onsubmit="return confirm('Delete this scholarship?')">
                                  <input type="hidden" name="delete_scholarship" value="1">
                                  <input type="hidden" name="id" value="<?= (int)$scholarship['id'] ?>">
                                  <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash"></i> Delete
                                  </button>
                                </form>
                              </li>
                            </ul>
                          </div>
                        </div>
                        
                        <p class="scholarship-summary"><?= esc($scholarship['summary']) ?></p>
                        
                        <?php if ($scholarship['conditions']): ?>
                          <div class="mt-2">
                            <small class="text-muted fw-bold">Conditions:</small>
                            <small class="d-block text-muted"><?= esc($scholarship['conditions']) ?></small>
                          </div>
                        <?php endif; ?>
                        
                        <!-- Edit Form (Collapsible) -->
                        <div class="collapse mt-3" id="editScholarship<?= $scholarship['id'] ?>">
                          <div class="border-top pt-3">
                            <form method="post">
                              <input type="hidden" name="edit_scholarship" value="1">
                              <input type="hidden" name="id" value="<?= (int)$scholarship['id'] ?>">
                              <div class="row">
                                <div class="col-md-8 mb-3">
                                  <label class="form-label">Scholarship Name</label>
                                  <input type="text" class="form-control" name="scholarship[name]" 
                                         value="<?= esc($scholarship['name']) ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                  <label class="form-label">Type</label>
                                  <select class="form-control" name="scholarship[type]" required>
                                    <option value="Freshmen" <?= $scholarship['type'] === 'Freshmen' ? 'selected' : '' ?>>Freshmen</option>
                                    <option value="University" <?= $scholarship['type'] === 'University' ? 'selected' : '' ?>>University</option>
                                    <option value="External" <?= $scholarship['type'] === 'External' ? 'selected' : '' ?>>External</option>
                                  </select>
                                </div>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Summary</label>
                                <textarea class="form-control" name="scholarship[summary]" rows="3" required><?= esc($scholarship['summary']) ?></textarea>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Conditions</label>
                                <textarea class="form-control" name="scholarship[conditions]" rows="3"><?= esc($scholarship['conditions']) ?></textarea>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Requirements</label>
                                <textarea class="form-control" name="scholarship[requirements]" rows="3"><?= esc($scholarship['requirements']) ?></textarea>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Examples</label>
                                <textarea class="form-control" name="scholarship[examples]" rows="3"><?= esc($scholarship['examples']) ?></textarea>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Learn More URL</label>
                                <input type="url" class="form-control" name="scholarship[learn_more_url]" 
                                       value="<?= esc($scholarship['learn_more_url']) ?>">
                              </div>
                              <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                  <i class="fas fa-save"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" 
                                        data-bs-toggle="collapse" data-bs-target="#editScholarship<?= $scholarship['id'] ?>">
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