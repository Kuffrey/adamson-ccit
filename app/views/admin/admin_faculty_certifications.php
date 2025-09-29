<?php
// app/views/admin/admin_faculty_certifications.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../../models/FacultyCertificationsPageSettings.php';
require_once __DIR__ . '/../../models/FacultyCertification.php';

if (!function_exists('esc')) {
    function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = $_SESSION['user'] ?? [];
$username = $user['username'] ?? 'Admin';
$notice = '';

try {
    $facultyCertification = new FacultyCertification();
    $pageSettings = new FacultyCertificationsPageSettings();
} catch (Exception $e) {
    $notice = 'Error initializing models: ' . $e->getMessage();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'update_settings':
                    $pageSettings->updateSettings($_POST);
                    $notice = 'Page settings updated successfully!';
                    break;
                
                case 'add_certification':
                    $facultyCertification->create($_POST);
                    $notice = 'Faculty certification award added successfully!';
                    break;
                
                case 'update_certification':
                    $facultyCertification->update($_POST['id'], $_POST);
                    $notice = 'Faculty certification award updated successfully!';
                    break;
                
                case 'delete_certification':
                    $facultyCertification->delete($_POST['id']);
                    $notice = 'Faculty certification award deleted successfully!';
                    break;
            }
        }
    } catch (Exception $e) {
        $notice = 'Error: ' . $e->getMessage();
    }
}

// Get current data safely
try {
    $settings = $pageSettings->getSettings();
    $certifications = $facultyCertification->getAll();
    $availableCertifications = FacultyCertification::getAllCertifications();
    $facultyList = FacultyCertification::getAllFaculty();
} catch (Exception $e) {
    $notice = 'Error loading data: ' . $e->getMessage();
    $settings = [];
    $certifications = [];
    $availableCertifications = [];
    $facultyList = [];
}
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Faculty → Certifications</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Faculty Certifications Management</h1>
      
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
                    data-bs-target="#settingsCollapse" aria-expanded="false" aria-controls="settingsCollapse">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="settingsCollapse">
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

      <!-- Add New Faculty Certification Award Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Add New Faculty Certification Award</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#addCertificationCollapse" aria-expanded="false" aria-controls="addCertificationCollapse">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="addCertificationCollapse">
          <div class="card-body">
            <form method="post" autocomplete="off">
              <input type="hidden" name="action" value="add_certification">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Faculty Member</label>
                  <select class="form-control" name="faculty_id" required>
                    <option value="">Select Faculty</option>
                    <?php foreach ($facultyList as $faculty): ?>
                      <option value="<?= $faculty['id'] ?>">
                        <?= esc($faculty['name']) ?> (<?= esc($faculty['dept']) ?>)
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Certification</label>
                  <select class="form-control" name="certification_id" required>
                    <option value="">Select Certification</option>
                    <?php foreach ($availableCertifications as $cert): ?>
                      <option value="<?= $cert['id'] ?>">
                        <?= esc($cert['cert_title']) ?> (<?= esc($cert['issuer']) ?>)
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3 mb-3">
                  <label class="form-label">Year Earned</label>
                  <input type="text" class="form-control" name="year_earned" placeholder="2024" required>
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">Year Expiry</label>
                  <input type="text" class="form-control" name="year_expiry" placeholder="2027">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Status</label>
                  <select class="form-control" name="status" required>
                    <option value="">Select Status</option>
                    <option value="Active">Active</option>
                    <option value="Expired">Expired</option>
                    <option value="Revoked">Revoked</option>
                  </select>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Certification Award
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Existing Faculty Certification Awards Card -->
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Existing Faculty Certification Awards (<?= count($certifications) ?>)</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#certificationsListCollapse" aria-expanded="true" aria-controls="certificationsListCollapse">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse show" id="certificationsListCollapse">
          <div class="card-body">
            <?php if (empty($certifications)): ?>
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No certification awards added yet.
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead class="table-dark">
                    <tr>
                      <th>Faculty</th>
                      <th>Certification</th>
                      <th>Issuer</th>
                      <th>Year Earned</th>
                      <th>Year Expiry</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($certifications as $cert): ?>
                      <tr>
                        <td><?= esc($cert['faculty_name'] ?? 'Unknown') ?></td>
                        <td><?= esc($cert['cert_title'] ?? 'Unknown') ?></td>
                        <td><?= esc($cert['issuer'] ?? 'Unknown') ?></td>
                        <td><?= esc($cert['year_earned'] ?? '') ?></td>
                        <td><?= esc($cert['year_expiry'] ?? 'N/A') ?></td>
                        <td>
                          <span class="badge bg-<?= $cert['status'] === 'Active' ? 'success' : ($cert['status'] === 'Expired' ? 'warning' : 'danger') ?>">
                            <?= esc($cert['status'] ?? '') ?>
                          </span>
                        </td>
                        <td>
                          <button type="button" class="btn btn-sm btn-warning btn-action" 
                                  onclick="editCertification(<?= htmlspecialchars(json_encode($cert)) ?>)">
                            <i class="fas fa-edit"></i>
                          </button>
                          <form method="post" class="d-inline" onsubmit="return confirm('Delete this certification award?')">
                            <input type="hidden" name="action" value="delete_certification">
                            <input type="hidden" name="id" value="<?= $cert['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger btn-action">
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
    </section>
  </main>
</div>

<!-- Edit Certification Modal -->
<div class="modal fade" id="editCertificationModal" tabindex="-1" aria-labelledby="editCertificationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editCertificationModalLabel">
          <i class="fas fa-edit"></i> Edit Certification Award
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" id="editCertificationForm">
        <input type="hidden" name="action" value="update_certification">
        <input type="hidden" name="id" id="edit_cert_id">
        
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <label for="edit_faculty_id" class="form-label">
                <i class="fas fa-user"></i> Faculty Member
              </label>
              <select class="form-select" id="edit_faculty_id" name="faculty_id" required>
                <option value="">Select Faculty</option>
                <?php foreach ($facultyList as $faculty): ?>
                  <option value="<?= $faculty['id'] ?>">
                    <?= htmlspecialchars($faculty['name']) ?> (<?= htmlspecialchars($faculty['dept']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label for="edit_certification_id" class="form-label">
                <i class="fas fa-certificate"></i> Certification
              </label>
              <select class="form-select" id="edit_certification_id" name="certification_id" required>
                <option value="">Select Certification</option>
                <?php foreach ($availableCertifications as $cert): ?>
                  <option value="<?= $cert['id'] ?>">
                    <?= htmlspecialchars($cert['cert_title']) ?> (<?= htmlspecialchars($cert['issuer']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          
          <div class="row mt-3">
            <div class="col-md-3">
              <label for="edit_year_earned" class="form-label">
                <i class="fas fa-calendar"></i> Year Earned
              </label>
              <input type="text" class="form-control" id="edit_year_earned" name="year_earned" 
                     placeholder="2024" required>
            </div>
            <div class="col-md-3">
              <label for="edit_year_expiry" class="form-label">
                <i class="fas fa-calendar-times"></i> Year Expiry
              </label>
              <input type="text" class="form-control" id="edit_year_expiry" name="year_expiry" 
                     placeholder="2027">
            </div>
            <div class="col-md-6">
              <label for="edit_status" class="form-label">
                <i class="fas fa-flag"></i> Status
              </label>
              <select class="form-select" id="edit_status" name="status" required>
                <option value="">Select Status</option>
                <option value="Active">Active</option>
                <option value="Expired">Expired</option>
                <option value="Revoked">Revoked</option>
              </select>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Cancel
          </button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Update Certification
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editCertification(cert) {
    // Populate the modal with certification data
    document.getElementById('edit_cert_id').value = cert.id;
    document.getElementById('edit_faculty_id').value = cert.faculty_id;
    document.getElementById('edit_certification_id').value = cert.certification_id;
    document.getElementById('edit_year_earned').value = cert.year_earned || '';
    document.getElementById('edit_year_expiry').value = cert.year_expiry || '';
    document.getElementById('edit_status').value = cert.status || '';
    
    // Show the modal
    var editModal = new bootstrap.Modal(document.getElementById('editCertificationModal'));
    editModal.show();
}
</script>