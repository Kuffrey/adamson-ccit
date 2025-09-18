<?php
// app/views/admin/admin_faculty_profile.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login_admin'); exit;
}

require_once __DIR__ . '/../../models/FacultyProfilePageSettings.php';
require_once __DIR__ . '/../../models/FacultyProfile.php';

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
      FacultyProfilePageSettings::updateSettings($_POST['settings']);
      $notice = 'Page settings saved.';
    } elseif (isset($_POST['add_faculty'])) {
      FacultyProfile::create($_POST['faculty'] ?? []);
      $notice = 'Faculty member added.';
    } elseif (isset($_POST['edit_faculty'])) {
      FacultyProfile::update((int)$_POST['id'], $_POST['faculty'] ?? []);
      $notice = 'Faculty member updated.';
    } elseif (isset($_POST['delete_faculty'])) {
      FacultyProfile::delete((int)$_POST['id']);
      $notice = 'Faculty member deleted.';
    }
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

$settings = FacultyProfilePageSettings::getSettings();
$faculty = FacultyProfile::getAll();
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Faculty → Profile</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Faculty Profile Management</h1>
      
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
                  <label class="form-label">Subhero Image URL</label>
                  <input type="text" class="form-control" name="settings[subhero_image_url]" 
                         value="<?= esc($settings['subhero_image_url'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Subhero Lead Text</label>
                  <input type="text" class="form-control" name="settings[subhero_lead]" 
                         value="<?= esc($settings['subhero_lead'] ?? '') ?>">
                </div>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Settings
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Add New Faculty Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Add New Faculty Member</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#addFacultyCard" aria-expanded="false" aria-controls="addFacultyCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="addFacultyCard">
          <div class="card-body">
            <form method="post" autocomplete="off">
              <input type="hidden" name="add_faculty" value="1">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Name</label>
                  <input type="text" class="form-control" name="faculty[name]" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Department</label>
                  <select class="form-select" name="faculty[dept]" required>
                    <option value="">Select Department</option>
                    <option value="admin">Administration</option>
                    <option value="itis">IT&IS</option>
                    <option value="cs">CS</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Role</label>
                  <select class="form-select" name="faculty[role]" required>
                    <option value="">Select Role</option>
                    <option value="dean">Dean</option>
                    <option value="chair">Chairperson</option>
                    <option value="full">Full-Time Faculty</option>
                    <option value="part">Part-Time Faculty</option>
                    <option value="lecturer">Special Lecturer</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Title</label>
                  <input type="text" class="form-control" name="faculty[title]" 
                         placeholder="e.g., Professor, PhD in Computer Science" required>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Avatar URL</label>
                  <input type="url" class="form-control" name="faculty[avatar_url]" 
                         placeholder="Link to profile photo">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Avatar Initials</label>
                  <input type="text" class="form-control" name="faculty[avatar_initials]" 
                         maxlength="4" placeholder="e.g., JD">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Badges</label>
                  <input type="text" class="form-control" name="faculty[badges]" 
                         placeholder="e.g., Administration,PhD,Certified">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Ordering</label>
                  <input type="number" class="form-control" name="faculty[ordering]" 
                         value="0" min="0">
                </div>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Faculty Member
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Existing Faculty Card -->
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Existing Faculty Members (<?= count($faculty) ?>)</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#facultyListCard" aria-expanded="true" aria-controls="facultyListCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse show" id="facultyListCard">
          <div class="card-body">
            <?php if (empty($faculty)): ?>
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No faculty members added yet.
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead class="table-dark">
                    <tr>
                      <th>Name</th>
                      <th>Department</th>
                      <th>Role</th>
                      <th>Title</th>
                      <th>Order</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($faculty as $f): ?>
                      <tr>
                        <td>
                          <strong><?= esc($f['name']) ?></strong>
                          <?php if ($f['badges']): ?>
                            <br><small class="text-muted"><?= esc($f['badges']) ?></small>
                          <?php endif; ?>
                        </td>
                        <td><span class="badge bg-secondary"><?= esc(ucfirst($f['dept'])) ?></span></td>
                        <td><?= esc($f['role']) ?></td>
                        <td><?= esc($f['title']) ?></td>
                        <td><?= esc($f['ordering']) ?></td>
                        <td>
                          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $f['id'] ?>">
                            <i class="fas fa-edit"></i>
                          </button>
                          <form method="post" class="inline-form" onsubmit="return confirm('Delete this faculty member?')">>
                            <input type="hidden" name="delete_faculty" value="1">
                            <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">
                              <i class="fas fa-trash"></i>
                            </button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Edit Modals -->
      <?php foreach ($faculty as $f): ?>
        <div class="modal fade" id="editModal<?= $f['id'] ?>" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Edit Faculty Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <form method="post">
                <div class="modal-body">
                  <input type="hidden" name="edit_faculty" value="1">
                  <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                  <div class="row">
                    <div class="col-md-6">
                      <label class="form-label">Name</label>
                      <input type="text" class="form-control" name="faculty[name]" value="<?= esc($f['name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Department</label>
                      <select class="form-select" name="faculty[dept]" required>
                        <option value="admin" <?= $f['dept'] === 'admin' ? 'selected' : '' ?>>Administration</option>
                        <option value="itis" <?= $f['dept'] === 'itis' ? 'selected' : '' ?>>IT&IS</option>
                        <option value="cs" <?= $f['dept'] === 'cs' ? 'selected' : '' ?>>CS</option>
                      </select>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-6">
                      <label class="form-label">Role</label>
                      <select class="form-select" name="faculty[role]" required>
                        <option value="dean" <?= $f['role'] === 'dean' ? 'selected' : '' ?>>Dean</option>
                        <option value="chair" <?= $f['role'] === 'chair' ? 'selected' : '' ?>>Chairperson</option>
                        <option value="full" <?= $f['role'] === 'full' ? 'selected' : '' ?>>Full-Time Faculty</option>
                        <option value="part" <?= $f['role'] === 'part' ? 'selected' : '' ?>>Part-Time Faculty</option>
                        <option value="lecturer" <?= $f['role'] === 'lecturer' ? 'selected' : '' ?>>Special Lecturer</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Title</label>
                      <input type="text" class="form-control" name="faculty[title]" value="<?= esc($f['title']) ?>" required>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-6">
                      <label class="form-label">Avatar URL</label>
                      <input type="url" class="form-control" name="faculty[avatar_url]" value="<?= esc($f['avatar_url']) ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Avatar Initials</label>
                      <input type="text" class="form-control" name="faculty[avatar_initials]" value="<?= esc($f['avatar_initials']) ?>" maxlength="4">
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-6">
                      <label class="form-label">Badges</label>
                      <input type="text" class="form-control" name="faculty[badges]" value="<?= esc($f['badges']) ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Ordering</label>
                      <input type="number" class="form-control" name="faculty[ordering]" value="<?= esc($f['ordering']) ?>" min="0">
                    </div>
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
      <?php endforeach; ?>
    </section>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>