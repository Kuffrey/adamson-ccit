<?php
// app/views/admin_manage_programs.php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login_admin'); exit;
}
require_once __DIR__ . '/../models/Program.php';
require_once __DIR__ . '/../models/ProgramsUndergraduateSettings.php';
require_once __DIR__ . '/../models/ProgramsGraduateSettings.php';

function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$user = $_SESSION['user'] ?? [];
$username = $user['username'] ?? 'Admin';
$notice = '';

/* ---------- Handle POST (CRUD) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // DEBUG: Show what's being posted
    error_log("POST Data: " . print_r($_POST, true));
    
    // Update existing programs
    if (!empty($_POST['program']) && is_array($_POST['program'])) {
      foreach ($_POST['program'] as $id => $data) {
        $id = (int)$id;
        // Normalize checkbox
        $data['is_active'] = isset($data['is_active']) && (int)$data['is_active'] === 1 ? 1 : 0;
        Program::update($id, $data);
      }
      $notice = 'Programs updated successfully!';
    }

    // Delete one
    if (!empty($_POST['delete_program'])) {
      Program::delete((int)$_POST['delete_program']);
      $notice = 'Program deleted successfully!';
    }

    // Create new
    if (!empty($_POST['new_program']['name'])) {
      $np = $_POST['new_program'];
      $np['is_active'] = !empty($np['is_active']) ? 1 : 0;
      Program::createFull($np);
      $notice = 'Program added successfully!';
    }
    
    // Redirect to prevent double submission
    if ($notice) {
      header('Location: ?page=admin_manage_programs&success=' . urlencode($notice));
      exit;
    }
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

/* ---------- Fetch data ---------- */
$programs = Program::all();
$ugs      = ProgramsUndergraduateSettings::getSettings();
$gs       = ProgramsGraduateSettings::getSettings();

// Handle success message from redirect
if (!empty($_GET['success'])) {
  $notice = $_GET['success'];
}
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/admin/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Programs → Management</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Programs Management</h1>
      
      <?php if ($notice): ?>
        <div class="alert <?= str_starts_with($notice, 'Error') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show" role="alert">
          <?= esc($notice) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Current Data Preview Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-eye me-2"></i>Current Data Preview</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#currentDataCard" aria-expanded="false" aria-controls="currentDataCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="currentDataCard">
          <div class="card-body">
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i>
              Manage the main <strong>programs database</strong>. For page content, use <a href="?page=admin_programs_undergraduate">Undergraduate</a> and <a href="?page=admin_programs_graduate">Graduate Studies</a>.
            </div>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr><th>Field</th><th>Value</th></tr>
                </thead>
                <tbody>
                  <tr><td>Total Programs</td><td><?= count($programs) ?></td></tr>
                  <tr><td>Active Programs</td><td><?= count(array_filter($programs, fn($p) => !empty($p['is_active']))) ?></td></tr>
                  <tr><td>UG Page Lead</td><td><?= esc($ugs['subhero_lead'] ?? '—') ?></td></tr>
                  <tr><td>Grad Page Lead</td><td><?= esc($gs['subhero_lead'] ?? '—') ?></td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Management Form -->
      <form id="progForm" method="post" autocomplete="off">

        <!-- Add New Program Card -->
        <div class="card mb-4">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0"><i class="fas fa-plus me-2"></i>Add New Program</h5>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                      data-bs-target="#addProgramCollapse" aria-expanded="false" aria-controls="addProgramCollapse">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </div>
          
          <div class="collapse" id="addProgramCollapse">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Program Name</label>
                  <input type="text" name="new_program[name]" class="form-control" placeholder="Program name">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Slug (optional)</label>
                  <input type="text" name="new_program[slug]" class="form-control" placeholder="url-friendly-slug">
                  <div class="form-text">Auto-generated if empty</div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">URL (optional)</label>
                  <input type="text" name="new_program[url]" class="form-control" placeholder="Optional URL">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Image URL</label>
                  <input type="text" name="new_program[image]" class="form-control" placeholder="Image URL">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Image Alt Text</label>
                  <input type="text" name="new_program[image_alt]" class="form-control" placeholder="Alt text for image">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Status</label>
                  <div class="form-check">
                    <input type="checkbox" name="new_program[is_active]" value="1" class="form-check-input" id="new_active" checked>
                    <label class="form-check-label" for="new_active">Active</label>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="new_program[description]" class="form-control" rows="3" placeholder="Program description"></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Programs Table -->
        <div class="card mb-4">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0"><i class="fas fa-table me-2"></i>Programs Database</h5>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                      data-bs-target="#programsTableCollapse" aria-expanded="true" aria-controls="programsTableCollapse">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </div>
          
          <div class="collapse show" id="programsTableCollapse">
            <div class="card-body">
              <?php if (empty($programs)): ?>
                <div class="alert alert-warning" role="alert">
                  <i class="fas fa-exclamation-triangle me-2"></i>No programs found.
                </div>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Active</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>URL</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($programs as $p): ?>
                        <tr>
                          <td><?= esc($p['id']) ?></td>
                          <td><?= !empty($p['is_active']) ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
                          <td>
                            <strong><?= esc($p['name']) ?></strong>
                            <?php if (!empty($p['description'])): ?>
                              <br><small class="text-muted"><?= esc(substr($p['description'], 0, 60)) ?>...</small>
                            <?php endif; ?>
                          </td>
                          <td><code><?= esc($p['slug'] ?? '') ?></code></td>
                          <td>
                            <?php if (!empty($p['url'])): ?>
                              <a href="<?= esc($p['url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt"></i>
                              </a>
                            <?php else: ?>
                              <span class="text-muted">—</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <button class="btn btn-sm btn-warning me-1 edit-program-btn" 
                                    data-program-id="<?= $p['id'] ?>" 
                                    title="Edit Program">
                              <i class="fas fa-edit"></i>
                            </button>
                            <form method="post" class="d-inline">
                              <button type="submit" name="delete_program" value="<?= (int)$p['id'] ?>" class="btn btn-sm btn-danger" 
                                      onclick="return confirm('Really delete this program?')" title="Delete Program">
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
      </form>

      <div class="page-end-spacer" style="height:160px" aria-hidden="true"></div>

      <div class="savebar">
        <div class="savebar__inner">
          <span class="savebar__status" id="saveStatus">All changes saved</span>
          <div class="savebar__actions">
            <button type="button" class="btn" id="discardBtn">Discard</button>
            <button type="submit" form="progForm" class="btn btn--primary">Save Changes</button>
            <a class="btn btn-outline-secondary" href="?page=admin_programs_undergraduate">Undergraduate Page</a>
            <a class="btn btn-outline-secondary" href="?page=admin_programs_graduate">Graduate Page</a>
          </div>
        </div>
      </div>

    </section>
  </main>
</div>

<!-- Edit Program Modals -->
<?php foreach ($programs as $p): ?>
<div class="modal fade" id="editProgramModal<?= $p['id'] ?>" tabindex="-1" aria-labelledby="editProgramModalLabel<?= $p['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title" id="editProgramModalLabel<?= $p['id'] ?>">Edit Program: <?= esc($p['name']) ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="program[<?= (int)$p['id'] ?>][name]" class="form-control" value="<?= esc($p['name']) ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Slug</label>
              <input type="text" name="program[<?= (int)$p['id'] ?>][slug]" class="form-control" value="<?= esc($p['slug'] ?? '') ?>">
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">URL (optional)</label>
              <input type="text" name="program[<?= (int)$p['id'] ?>][url]" class="form-control" value="<?= esc($p['url'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Image URL</label>
              <input type="text" name="program[<?= (int)$p['id'] ?>][image]" class="form-control" value="<?= esc($p['image'] ?? '') ?>">
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Image Alt Text</label>
              <input type="text" name="program[<?= (int)$p['id'] ?>][image_alt]" class="form-control" value="<?= esc($p['image_alt'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <input type="hidden" name="program[<?= (int)$p['id'] ?>][is_active]" value="0">
              <div class="form-check">
                <input type="checkbox" name="program[<?= (int)$p['id'] ?>][is_active]" value="1" class="form-check-input" id="active_<?= (int)$p['id'] ?>" <?= !empty($p['is_active']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="active_<?= (int)$p['id'] ?>">Active</label>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="program[<?= (int)$p['id'] ?>][description]" class="form-control" rows="3"><?= esc($p['description'] ?? '') ?></textarea>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('progForm');
  const saveStatus = document.getElementById('saveStatus');
  let dirty = false;

  // Manual modal handling for edit buttons
  document.querySelectorAll('.edit-program-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const programId = btn.getAttribute('data-program-id');
      const modalId = `editProgramModal${programId}`;
      const modalElement = document.getElementById(modalId);
      
      if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
      } else {
        console.error('Modal not found:', modalId);
      }
    });
  });

  // Track changes
  form?.addEventListener('input', () => {
    dirty = true;
    saveStatus.textContent = 'Unsaved changes';
  });

  form?.addEventListener('submit', () => {
    dirty = false;
    saveStatus.textContent = 'Saving...';
  });

  // Prevent accidental navigation
  window.addEventListener('beforeunload', (e) => { if(dirty){ e.preventDefault(); e.returnValue = ''; } });

  document.getElementById('discardBtn')?.addEventListener('click', () => {
    if(!dirty || confirm('Discard all unsaved changes?')) location.reload();
  });

  window.addEventListener('keydown', (e) => {
    if((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's'){
      e.preventDefault();
      document.querySelector('.savebar [type="submit"]')?.click();
    }
  });

  const notices = document.querySelectorAll('.alert');
  if (notices.length) setTimeout(() => { notices.forEach(n => n.style.display='none'); }, 4000);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>