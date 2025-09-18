<?php
// app/views/admin/about_vision_mission_form.php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!function_exists('esc')) {
    function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

// Prefer username from Auth if available (keeps header consistent)
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? ($_SESSION['user']['username'] ?? 'Admin');

// Load current settings
require_once __DIR__ . '/../../models/AboutVisionMission.php';
try {
  $model = new AboutVisionMission();
  $about = $model->get();
} catch (Exception $e) {
  $about = [];
}
$success = false;
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $model->update($_POST);
        $about = $model->get(); // reload after save
        $success = true;
    } catch (Exception $e) {
        $error = "Error saving changes: " . $e->getMessage();
    }
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
      <span class="admin-topbar__title">About → Vision & Mission</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h1 class="admin-cms-section__title mb-1">Vision & Mission Management</h1>
          <p class="text-muted mb-0">Edit college-wide and departmental vision, mission, and objectives</p>
        </div>
        <div class="d-flex gap-2">
          <div class="badge bg-info">Vision & Mission Editor</div>
        </div>
      </div>
      
      <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          Changes saved successfully!
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php elseif ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= esc($error) ?>
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
              Manage <strong>vision and mission statements</strong> for the college and individual departments. Changes apply site-wide.
            </div>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead><tr><th>Field</th><th>Value</th></tr></thead>
                <tbody>
                  <tr><td>Intro</td><td><?= esc(substr($about['main_intro'] ?? '—', 0, 60)) ?>...</td></tr>
                  <tr><td>Main Vision</td><td><?= esc(substr($about['main_vision'] ?? '—', 0, 60)) ?>...</td></tr>
                  <tr><td>Main Mission</td><td><?= esc(substr($about['main_mission'] ?? '—', 0, 60)) ?>...</td></tr>
                  <tr><td>Dept1 Title</td><td><?= esc($about['dept1_title'] ?? '—') ?></td></tr>
                  <tr><td>Dept1 Vision</td><td><?= esc(substr($about['dept1_vision'] ?? '—', 0, 60)) ?>...</td></tr>
                  <tr><td>Dept1 Mission</td><td><?= esc(substr($about['dept1_mission'] ?? '—', 0, 60)) ?>...</td></tr>
                  <tr><td>Dept2 Title</td><td><?= esc($about['dept2_title'] ?? '—') ?></td></tr>
                  <tr><td>Dept2 Vision</td><td><?= esc(substr($about['dept2_vision'] ?? '—', 0, 60)) ?>...</td></tr>
                  <tr><td>Dept2 Mission</td><td><?= esc(substr($about['dept2_mission'] ?? '—', 0, 60)) ?>...</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Navigation -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Quick Navigation</h5>
        </div>
        <div class="card-body">
          <nav class="d-flex flex-wrap gap-2" aria-label="Vision & Mission sections">
            <a href="#sec-main" class="btn btn-sm btn-outline-primary">Main (College-wide)</a>
            <a href="#sec-dept1" class="btn btn-sm btn-outline-primary">IT & IS</a>
            <a href="#sec-dept2" class="btn btn-sm btn-outline-primary">Computer Science</a>
          </nav>
        </div>
      </div>

      <!-- Edit Form -->
      <form id="vmForm" method="post" action="?page=admin_about_vision_mission">

        <!-- Main Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-main"><i class="fas fa-university me-2"></i>Main (College-wide)</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="main_intro" class="form-label">Intro / Lead</label>
              <input type="text" class="form-control" id="main_intro" name="main_intro" 
                     value="<?= esc($about['main_intro'] ?? '') ?>" 
                     placeholder="Enter introductory text for vision and mission section">
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="main_vision" class="form-label">Vision</label>
                <textarea class="form-control" id="main_vision" name="main_vision" rows="4" 
                          placeholder="Enter college-wide vision statement"><?= esc($about['main_vision'] ?? '') ?></textarea>
              </div>
              <div class="col-md-6 mb-3">
                <label for="main_mission" class="form-label">Mission</label>
                <textarea class="form-control" id="main_mission" name="main_mission" rows="4" 
                          placeholder="Enter college-wide mission statement"><?= esc($about['main_mission'] ?? '') ?></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- How to Apply Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" id="sec-apply"><i class="fas fa-clipboard-list me-2"></i>How to Apply <small class="text-muted">(HTML allowed)</small></h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Body (HTML)</label>
              <textarea name="how_to_apply" class="form-control" rows="4"><?= esc($settings['how_to_apply'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Initial Uploads Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" id="sec-uploads"><i class="fas fa-upload me-2"></i>Initial Uploads <small class="text-muted">(HTML allowed)</small></h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">List / Body (HTML)</label>
              <textarea name="initial_uploads" class="form-control" rows="4"><?= esc($settings['initial_uploads'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Requirements - Senior High School Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" id="sec-req-shs"><i class="fas fa-school me-2"></i>Requirements — Senior High School <small class="text-muted">(HTML allowed)</small></h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">SHS Requirements (HTML)</label>
              <textarea name="requirements_shs" class="form-control" rows="4"><?= esc($settings['requirements_shs'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Requirements - ALS/PEPT Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" id="sec-req-als"><i class="fas fa-certificate me-2"></i>Requirements — ALS/PEPT <small class="text-muted">(HTML allowed)</small></h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">ALS/PEPT Requirements (HTML)</label>
              <textarea name="requirements_als" class="form-control" rows="4"><?= esc($settings['requirements_als'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Requirements - Graduates from Abroad Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" id="sec-req-abroad"><i class="fas fa-globe me-2"></i>Requirements — Graduates from Abroad <small class="text-muted">(HTML allowed)</small></h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Abroad Requirements (HTML)</label>
              <textarea name="requirements_abroad" class="form-control" rows="3"><?= esc($settings['requirements_abroad'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Enrollment Procedure Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" id="sec-enrollment"><i class="fas fa-tasks me-2"></i>Enrollment Procedure <small class="text-muted">(HTML allowed)</small></h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Procedure (HTML)</label>
              <textarea name="enrollment_procedure" class="form-control" rows="4"><?= esc($settings['enrollment_procedure'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Sidebar Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" id="sec-sidebar"><i class="fas fa-sidebar me-2"></i>Sidebar</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Office Info (HTML)</label>
              <textarea name="sidebar_office" class="form-control" rows="3"><?= esc($settings['sidebar_office'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Quick Links (HTML)</label>
              <textarea name="sidebar_links" class="form-control" rows="3"><?= esc($settings['sidebar_links'] ?? '') ?></textarea>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Image URL</label>
                  <input type="text" name="sidebar_image_url" class="form-control" value="<?= esc($settings['sidebar_image_url'] ?? '') ?>">
                  <div class="form-text">Absolute or site-relative URL</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Image Caption</label>
                  <input type="text" name="sidebar_image_caption" class="form-control" value="<?= esc($settings['sidebar_image_caption'] ?? '') ?>">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Call to Action Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" id="sec-cta"><i class="fas fa-bullhorn me-2"></i>Call to Action</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">CTA Title</label>
              <input type="text" name="cta_title" class="form-control" value="<?= esc($settings['cta_title'] ?? '') ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">CTA Description</label>
              <input type="text" name="cta_description" class="form-control" value="<?= esc($settings['cta_description'] ?? '') ?>">
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Action Label</label>
                  <input type="text" name="cta_action_label" class="form-control" value="<?= esc($settings['cta_action_label'] ?? '') ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Action URL</label>
                  <input type="text" name="cta_action_url" class="form-control" value="<?= esc($settings['cta_action_url'] ?? '') ?>">
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
            <button type="submit" form="vmForm" class="btn btn--primary">Save Changes</button>
            <a class="btn btn-outline-secondary" href="?page=admin_manage_about">About Page</a>
            <a class="btn btn-outline-secondary" href="?page=admin_manage_news">News</a>
          </div>
        </div>
      </div>

    </section>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('vmForm');
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
