<?php
// admin_about_vision_mission.php — CMS CRUD for About Vision/Mission Content
require_once __DIR__ . '/../models/AboutVisionMission.php';
function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Prefer username from Auth if available (keeps header consistent)
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? ($_SESSION['user']['username'] ?? 'Admin');

// Load current settings
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
  <?php include __DIR__ . '/admin/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">About → Vision & Mission</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username ?? 'Admin'); ?></span>
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

        <!-- Department 1 Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-dept1"><i class="fas fa-laptop-code me-2"></i>IT & Information Systems</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="dept1_title" class="form-label">Department Title</label>
              <input type="text" class="form-control" id="dept1_title" name="dept1_title" 
                     value="<?= esc($about['dept1_title'] ?? '') ?>" 
                     placeholder="Enter department title">
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="dept1_vision" class="form-label">Vision</label>
                <textarea class="form-control" id="dept1_vision" name="dept1_vision" rows="4" 
                          placeholder="Enter department vision"><?= esc($about['dept1_vision'] ?? '') ?></textarea>
              </div>
              <div class="col-md-6 mb-3">
                <label for="dept1_mission" class="form-label">Mission</label>
                <textarea class="form-control" id="dept1_mission" name="dept1_mission" rows="4" 
                          placeholder="Enter department mission"><?= esc($about['dept1_mission'] ?? '') ?></textarea>
              </div>
            </div>
            <div class="mb-3">
              <label for="dept1_objectives" class="form-label">Objectives</label>
              <textarea class="form-control" id="dept1_objectives" name="dept1_objectives" rows="3" 
                        placeholder="Enter department objectives"><?= esc($about['dept1_objectives'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Department 2 Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-dept2"><i class="fas fa-code me-2"></i>Computer Science</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="dept2_title" class="form-label">Department Title</label>
              <input type="text" class="form-control" id="dept2_title" name="dept2_title" 
                     value="<?= esc($about['dept2_title'] ?? '') ?>" 
                     placeholder="Enter department title">
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="dept2_vision" class="form-label">Vision</label>
                <textarea class="form-control" id="dept2_vision" name="dept2_vision" rows="4" 
                          placeholder="Enter department vision"><?= esc($about['dept2_vision'] ?? '') ?></textarea>
              </div>
              <div class="col-md-6 mb-3">
                <label for="dept2_mission" class="form-label">Mission</label>
                <textarea class="form-control" id="dept2_mission" name="dept2_mission" rows="4" 
                          placeholder="Enter department mission"><?= esc($about['dept2_mission'] ?? '') ?></textarea>
              </div>
            </div>
            <div class="mb-3">
              <label for="dept2_objectives" class="form-label">Objectives</label>
              <textarea class="form-control" id="dept2_objectives" name="dept2_objectives" rows="3" 
                        placeholder="Enter department objectives"><?= esc($about['dept2_objectives'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

      </form>

      <div class="page-end-spacer" style="height:160px" aria-hidden="true"></div>

      <!-- Sticky Save Bar -->
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('vmForm');

  // -- smooth scroll to section anchors
  const scrollToSection = (id) => {
    const target = document.getElementById(id);
    if (!target) return;
    const top = target.getBoundingClientRect().top + window.scrollY - 100;
    window.scrollTo({ top, behavior: 'smooth' });
  };

  // -- handle anchor clicks in navigation
  document.querySelectorAll('a[href^="#sec-"]').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const id = link.getAttribute('href').slice(1);
      scrollToSection(id);
      history.pushState(null, '', '#' + id);
    });
  });

  // -- unsaved changes tracking + save feedback
  let dirty = false;
  const saveStatus = document.getElementById('saveStatus');
  form?.addEventListener('input', () => {
    if (!dirty) { dirty = true; saveStatus.textContent = 'Unsaved changes…'; }
  });
  form?.addEventListener('submit', () => {
    saveStatus.textContent = 'Saving…';
    setTimeout(() => { dirty = false; saveStatus.textContent = 'All changes saved'; }, 900);
  });
  window.addEventListener('beforeunload', (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });

  // -- discard
  document.getElementById('discardBtn')?.addEventListener('click', () => {
    if (!dirty || confirm('Discard all unsaved changes?')) location.reload();
  });

  // -- shortcuts
  window.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
      e.preventDefault();
      document.querySelector('.savebar [type="submit"]')?.click();
    }
  });

  // -- auto-hide Bootstrap alerts
  setTimeout(() => {
    const alerts = document.querySelectorAll('.alert:not(.alert-dismissible)');
    alerts.forEach(alert => {
      if (alert.classList.contains('show')) {
        alert.classList.remove('show');
        alert.classList.add('fade');
        setTimeout(() => alert.remove(), 150);
      }
    });
  }, 4000);
});
</script>