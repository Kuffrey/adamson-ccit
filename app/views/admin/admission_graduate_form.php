<?php
// app/views/admin/admission_graduate_form.php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!function_exists('esc')) {
    function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

// Prefer username from Auth if available (keeps header consistent)
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? ($_SESSION['user']['username'] ?? 'Admin');

/** @var array $settings Settings array injected by controller */
/** @var string|null $msg Optional success message from controller */
$success = !empty($msg);
$error = '';
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Admissions → Graduate School &amp; JD</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Graduate School &amp; JD Admission Management</h1>
      
      <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?= esc($msg) ?>
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
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Field</th>
                    <th>Value</th>
                  </tr>
                </thead>
                <tbody>
                  <tr><td>Subhero Lead</td><td><?= esc($settings['subhero_lead'] ?? '—') ?></td></tr>
                  <tr><td>How to Apply (HTML)</td><td><?= esc($settings['how_to_apply'] ?? '—') ?></td></tr>
                  <tr><td>Initial Uploads (HTML)</td><td><?= esc($settings['initial_uploads'] ?? '—') ?></td></tr>
                  <tr><td>Qualifications (Master's) (HTML)</td><td><?= esc($settings['qualifications_masters'] ?? '—') ?></td></tr>
                  <tr><td>Qualifications (Doctoral) (HTML)</td><td><?= esc($settings['qualifications_doctoral'] ?? '—') ?></td></tr>
                  <tr><td>Qualifications (JD) (HTML)</td><td><?= esc($settings['qualifications_jd'] ?? '—') ?></td></tr>
                  <tr><td>Requirements for Enrollment (HTML)</td><td><?= esc($settings['requirements_enrollment'] ?? '—') ?></td></tr>
                  <tr><td>Enrollment Procedure (HTML)</td><td><?= esc($settings['enrollment_procedure'] ?? '—') ?></td></tr>
                  <tr><td>Sidebar Office (HTML)</td><td><?= esc($settings['sidebar_office'] ?? '—') ?></td></tr>
                  <tr><td>Sidebar Links (HTML)</td><td><?= esc($settings['sidebar_links'] ?? '—') ?></td></tr>
                  <tr><td>Sidebar Image URL</td><td><?= esc($settings['sidebar_image_url'] ?? '—') ?></td></tr>
                  <tr><td>Sidebar Image Caption</td><td><?= esc($settings['sidebar_image_caption'] ?? '—') ?></td></tr>
                  <tr><td>CTA Title</td><td><?= esc($settings['cta_title'] ?? '—') ?></td></tr>
                  <tr><td>CTA Description</td><td><?= esc($settings['cta_description'] ?? '—') ?></td></tr>
                  <tr><td>CTA Action Label</td><td><?= esc($settings['cta_action_label'] ?? '—') ?></td></tr>
                  <tr><td>CTA Action URL</td><td><?= esc($settings['cta_action_url'] ?? '—') ?></td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Management Form -->
      <form id="admGradForm" method="post" action="?page=admin_admission_graduate" class="admin-cms-form" autocomplete="off">

        <!-- Subhero Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" id="sec-subhero"><i class="fas fa-heading me-2"></i>Subhero</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Subhero Lead</label>
              <input type="text" name="subhero_lead" class="form-control" value="<?= esc($settings['subhero_lead'] ?? '') ?>" maxlength="255" required>
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
              <label class="form-label">Initial Uploads (HTML)</label>
              <textarea name="initial_uploads" class="form-control" rows="4"><?= esc($settings['initial_uploads'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Qualifications Master's Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" id="sec-qual-m"><i class="fas fa-graduation-cap me-2"></i>Qualifications (Master's) <small class="text-muted">(HTML allowed)</small></h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Qualifications (HTML)</label>
              <textarea name="qualifications_masters" class="form-control" rows="5"><?= esc($settings['qualifications_masters'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Qualifications Doctoral Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" id="sec-qual-d"><i class="fas fa-user-graduate me-2"></i>Qualifications (Doctoral) <small class="text-muted">(HTML allowed)</small></h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Qualifications (HTML)</label>
              <textarea name="qualifications_doctoral" class="form-control" rows="5"><?= esc($settings['qualifications_doctoral'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Qualifications JD Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" id="sec-qual-jd"><i class="fas fa-balance-scale me-2"></i>Qualifications (JD) <small class="text-muted">(HTML allowed)</small></h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Qualifications (HTML)</label>
              <textarea name="qualifications_jd" class="form-control" rows="5"><?= esc($settings['qualifications_jd'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Requirements for Enrollment Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" id="sec-req-enroll"><i class="fas fa-list-check me-2"></i>Requirements for Enrollment <small class="text-muted">(HTML allowed)</small></h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Requirements (HTML)</label>
              <textarea name="requirements_enrollment" class="form-control" rows="5"><?= esc($settings['requirements_enrollment'] ?? '') ?></textarea>
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
              <textarea name="enrollment_procedure" class="form-control" rows="5"><?= esc($settings['enrollment_procedure'] ?? '') ?></textarea>
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
            <button type="submit" form="admGradForm" class="btn btn--primary">Save Changes</button>
          </div>
        </div>
      </div>

    </section>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('admGradForm');
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