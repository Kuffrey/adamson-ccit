<?php
// app/views/admin/admin_student_testimonials.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../../models/StudentTestimonialsPageSettings.php';
require_once __DIR__ . '/../../models/StudentTestimonial.php';

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
      StudentTestimonialsPageSettings::updateSettings($_POST['settings']);
      $notice = 'Page settings saved.';
    } elseif (isset($_POST['add_testimonial'])) {
      StudentTestimonial::create($_POST['testimonial'] ?? []);
      $notice = 'Testimonial added.';
    } elseif (isset($_POST['edit_testimonial'])) {
      StudentTestimonial::update((int)$_POST['id'], $_POST['testimonial'] ?? []);
      $notice = 'Testimonial updated.';
    } elseif (isset($_POST['delete_testimonial'])) {
      StudentTestimonial::delete((int)$_POST['id']);
      $notice = 'Testimonial deleted.';
    }
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

$settings = StudentTestimonialsPageSettings::getSettings();
$testimonials = StudentTestimonial::getAll();
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Student → Testimonials</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Student Testimonials Management</h1>
      
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

      <!-- Add New Testimonial Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Add New Testimonial</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#addTestimonialCard" aria-expanded="false" aria-controls="addTestimonialCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="addTestimonialCard">
          <div class="card-body">
            <form method="post" autocomplete="off">
              <input type="hidden" name="add_testimonial" value="1">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Student Name</label>
                  <input type="text" class="form-control" name="testimonial[name]" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Program</label>
                  <select class="form-control" name="testimonial[program]" required>
                    <option value="bsit">BSIT</option>
                    <option value="bscs">BSCS</option>
                    <option value="bsis">BSIS</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Graduation Year</label>
                  <input type="text" class="form-control" name="testimonial[grad_year]" required 
                         placeholder="e.g., 2024">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Current Role/Position</label>
                  <input type="text" class="form-control" name="testimonial[role]" 
                         placeholder="e.g., Software Engineer at Google">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Quote/Testimonial</label>
                <textarea class="form-control" name="testimonial[quote]" rows="5" required 
                          placeholder="What the student says about their experience at CCIT..."></textarea>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Avatar URL</label>
                  <input type="url" class="form-control" name="testimonial[avatar_url]" 
                         placeholder="Link to student's photo">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Avatar Initials</label>
                  <input type="text" class="form-control" name="testimonial[avatar_initials]" 
                         maxlength="4" placeholder="e.g., JD">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-control" name="testimonial[is_alumni]">
                  <option value="0">Current Student</option>
                  <option value="1">Alumni</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Testimonial
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Existing Testimonials Card -->
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Existing Testimonials (<?= count($testimonials) ?>)</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#testimonialsListCard" aria-expanded="true" aria-controls="testimonialsListCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse show" id="testimonialsListCard">
          <div class="card-body">
            <?php if (empty($testimonials)): ?>
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No testimonials added yet.
              </div>
            <?php else: ?>
              <div class="row">
                <?php foreach ($testimonials as $testimonial): ?>
                  <div class="col-lg-6 mb-4">
                    <div class="card testimonial-item">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                          <div>
                            <h6 class="card-title mb-1"><?= esc($testimonial['name']) ?></h6>
                            <div class="testimonial-meta">
                              <span class="badge bg-primary"><?= esc(strtoupper($testimonial['program'])) ?></span>
                              <span class="text-muted ms-2">Class of <?= esc($testimonial['grad_year']) ?></span>
                              <?php if ($testimonial['is_alumni']): ?>
                                <span class="badge bg-success ms-2">Alumni</span>
                              <?php else: ?>
                                <span class="badge bg-info ms-2">Current Student</span>
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
                                        data-bs-target="#editTestimonial<?= $testimonial['id'] ?>">
                                  <i class="fas fa-edit"></i> Edit
                                </button>
                              </li>
                              <li>
                                <form method="post" style="display: inline;" 
                                      onsubmit="return confirm('Delete this testimonial?')">
                                  <input type="hidden" name="delete_testimonial" value="1">
                                  <input type="hidden" name="id" value="<?= (int)$testimonial['id'] ?>">
                                  <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash"></i> Delete
                                  </button>
                                </form>
                              </li>
                            </ul>
                          </div>
                        </div>
                        
                        <p class="testimonial-role text-muted mb-2"><?= esc($testimonial['role']) ?></p>
                        <p class="testimonial-quote">"<?= esc($testimonial['quote']) ?>"</p>
                        
                        <!-- Edit Form (Collapsible) -->
                        <div class="collapse mt-3" id="editTestimonial<?= $testimonial['id'] ?>">
                          <div class="border-top pt-3">
                            <form method="post">
                              <input type="hidden" name="edit_testimonial" value="1">
                              <input type="hidden" name="id" value="<?= (int)$testimonial['id'] ?>">
                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">Student Name</label>
                                  <input type="text" class="form-control" name="testimonial[name]" 
                                         value="<?= esc($testimonial['name']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">Program</label>
                                  <select class="form-control" name="testimonial[program]" required>
                                    <option value="bsit" <?= $testimonial['program'] === 'bsit' ? 'selected' : '' ?>>BSIT</option>
                                    <option value="bscs" <?= $testimonial['program'] === 'bscs' ? 'selected' : '' ?>>BSCS</option>
                                    <option value="bsis" <?= $testimonial['program'] === 'bsis' ? 'selected' : '' ?>>BSIS</option>
                                  </select>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">Graduation Year</label>
                                  <input type="text" class="form-control" name="testimonial[grad_year]" 
                                         value="<?= esc($testimonial['grad_year']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">Current Role/Position</label>
                                  <input type="text" class="form-control" name="testimonial[role]" 
                                         value="<?= esc($testimonial['role']) ?>">
                                </div>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Quote/Testimonial</label>
                                <textarea class="form-control" name="testimonial[quote]" rows="3" required><?= esc($testimonial['quote']) ?></textarea>
                              </div>
                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">Avatar URL</label>
                                  <input type="url" class="form-control" name="testimonial[avatar_url]" 
                                         value="<?= esc($testimonial['avatar_url']) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">Avatar Initials</label>
                                  <input type="text" class="form-control" name="testimonial[avatar_initials]" 
                                         value="<?= esc($testimonial['avatar_initials']) ?>" maxlength="4">
                                </div>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="testimonial[is_alumni]">
                                  <option value="0" <?= !$testimonial['is_alumni'] ? 'selected' : '' ?>>Current Student</option>
                                  <option value="1" <?= $testimonial['is_alumni'] ? 'selected' : '' ?>>Alumni</option>
                                </select>
                              </div>
                              <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                  <i class="fas fa-save"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" 
                                        data-bs-toggle="collapse" data-bs-target="#editTestimonial<?= $testimonial['id'] ?>">
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