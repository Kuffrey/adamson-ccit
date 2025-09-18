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
$username = $_SESSION['user']['username'] ?? 'Admin';
$notice   = '';

/* ---------- Handle POST (CRUD) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // Update existing programs
    if (!empty($_POST['program']) && is_array($_POST['program'])) {
      foreach ($_POST['program'] as $id => $data) {
        $id = (int)$id;
        // Normalize checkbox
        $data['is_active'] = isset($data['is_active']) && (int)$data['is_active'] === 1 ? 1 : 0;
        Program::update($id, $data);
      }
      $notice = 'Programs updated.';
    }

    // Delete one
    if (!empty($_POST['delete_program'])) {
      Program::delete((int)$_POST['delete_program']);
      $notice = 'Program deleted.';
    }

    // Create new
    if (!empty($_POST['new_program']['name'])) {
      $np = $_POST['new_program'];
      $np['is_active'] = !empty($np['is_active']) ? 1 : 0;
      Program::createFull($np);
      $notice = 'Program added.';
    }
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

/* ---------- Fetch data ---------- */
$programs = Program::all();
$ugs      = ProgramsUndergraduateSettings::getSettings();
$gs       = ProgramsGraduateSettings::getSettings();
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
      
      <div class="alert alert-info" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        Edit the Programs list here. For page content, use
        <a class="alert-link" href="?page=admin_programs_undergraduate">Undergraduate</a>
        and
        <a class="alert-link" href="?page=admin_programs_graduate">Graduate Studies</a>.
      </div>

      <?php if ($notice): ?>
        <div class="alert alert-<?= str_starts_with($notice,'Error:') ? 'danger':'success' ?> alert-dismissible fade show" role="alert">
          <?= esc($notice) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Current Data Preview Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-chart-bar me-2"></i>Overview Snapshot</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#overviewCard" aria-expanded="false" aria-controls="overviewCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="overviewCard">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Field</th>
                    <th>Value</th>
                  </tr>
                </thead>
                <tbody>
                  <tr><td>Programs (count)</td><td><?= count($programs) ?></td></tr>
                  <tr><td>UG Lead</td><td><?= esc($ugs['subhero_lead'] ?? '—') ?></td></tr>
                  <tr><td>Grad Lead</td><td><?= esc($gs['subhero_lead'] ?? '—') ?></td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Management Form -->
      <form id="progForm" method="post" class="admin-cms-form" autocomplete="off">
        <!-- Programs List Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" id="sec-programs"><i class="fas fa-list me-2"></i>Programs List</h5>
          </div>
          <div class="card-body">

            <?php if (empty($programs)): ?>
              <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>No programs found.
              </div>
            <?php else: foreach ($programs as $p): ?>
              <div class="card mb-3 border">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="program[<?= (int)$p['id'] ?>][name]" class="form-control" value="<?= esc($p['name']) ?>">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="program[<?= (int)$p['id'] ?>][slug]" class="form-control" value="<?= esc($p['slug'] ?? '') ?>">
                        <div class="form-text">URL-friendly identifier</div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label class="form-label">URL (optional)</label>
                        <input type="text" name="program[<?= (int)$p['id'] ?>][url]" class="form-control" value="<?= esc($p['url'] ?? '') ?>">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="mb-3">
                        <label class="form-label">Status</label>
                        <input type="hidden" name="program[<?= (int)$p['id'] ?>][is_active]" value="0">
                        <div class="form-check">
                          <input type="checkbox" name="program[<?= (int)$p['id'] ?>][is_active]" value="1" class="form-check-input" id="active_<?= (int)$p['id'] ?>" <?= !empty($p['is_active']) ? 'checked' : '' ?>>
                          <label class="form-check-label" for="active_<?= (int)$p['id'] ?>">Active</label>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-1">
                      <div class="mb-3">
                        <label class="form-label">Actions</label>
                        <div class="d-grid">
                          <button type="submit" name="delete_program" value="<?= (int)$p['id'] ?>" class="btn btn-outline-danger btn-sm" 
                                  onclick="return confirm('Really delete this program?')">
                            <i class="fas fa-trash me-1"></i>Del
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                
                  <div class="row">
                    <div class="col-12">
                      <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="program[<?= (int)$p['id'] ?>][description]" class="form-control" rows="2"><?= esc($p['description'] ?? '') ?></textarea>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Image URL</label>
                        <input type="text" name="program[<?= (int)$p['id'] ?>][image]" class="form-control" value="<?= esc($p['image'] ?? '') ?>">
                        <div class="form-text">Absolute or site-relative URL</div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Image Alt Text</label>
                        <input type="text" name="program[<?= (int)$p['id'] ?>][image_alt]" class="form-control" value="<?= esc($p['image_alt'] ?? '') ?>">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; endif; ?>
          </div>
        </div>

        <!-- Add New Program Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Add New Program</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <div class="mb-3">
                  <label class="form-label">Name</label>
                  <input type="text" name="new_program[name]" class="form-control" placeholder="Program name">
                </div>
              </div>
              <div class="col-md-3">
                <div class="mb-3">
                  <label class="form-label">Slug (optional)</label>
                  <input type="text" name="new_program[slug]" class="form-control" placeholder="url-friendly-slug">
                  <div class="form-text">Auto-generated if empty</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="mb-3">
                  <label class="form-label">URL (optional)</label>
                  <input type="text" name="new_program[url]" class="form-control" placeholder="Optional URL">
                </div>
              </div>
              <div class="col-md-3">
                <div class="mb-3">
                  <label class="form-label">Status</label>
                  <div class="form-check">
                    <input type="checkbox" name="new_program[is_active]" value="1" class="form-check-input" id="new_active" checked>
                    <label class="form-check-label" for="new_active">Active</label>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <textarea name="new_program[description]" class="form-control" rows="2" placeholder="Program description"></textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Image URL</label>
                  <input type="text" name="new_program[image]" class="form-control" placeholder="Image URL">
                  <div class="form-text">Absolute or site-relative URL</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Image Alt Text</label>
                  <input type="text" name="new_program[image_alt]" class="form-control" placeholder="Alt text for image">
                </div>
              </div>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('progForm');
  const saveStatus = document.getElementById('saveStatus');
  let dirty = false;

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