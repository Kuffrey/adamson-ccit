<?php
// app/views/admin/admin_student_organizations.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login_admin'); exit;
}

require_once __DIR__ . '/../../models/StudentOrganizationsPageSettings.php';
require_once __DIR__ . '/../../models/StudentOrganization.php';

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
      StudentOrganizationsPageSettings::updateSettings($_POST['settings']);
      $notice = 'Page settings saved.';
    } elseif (isset($_POST['add_org'])) {
      StudentOrganization::create($_POST['org'] ?? []);
      $notice = 'Organization added.';
    } elseif (isset($_POST['edit_org'])) {
      StudentOrganization::update((int)$_POST['id'], $_POST['org'] ?? []);
      $notice = 'Organization updated.';
    } elseif (isset($_POST['delete_org'])) {
      StudentOrganization::delete((int)$_POST['id']);
      $notice = 'Organization deleted.';
    }
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

$settings = StudentOrganizationsPageSettings::getSettings();
$organizations = StudentOrganization::getAll();
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Student → Organizations</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Student Organizations Management</h1>
      
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

      <!-- Add New Organization Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Add New Organization</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#addOrganizationCard" aria-expanded="false" aria-controls="addOrganizationCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="addOrganizationCard">
          <div class="card-body">
            <form method="post" autocomplete="off">
              <input type="hidden" name="add_org" value="1">
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label class="form-label">Organization Name</label>
                  <input type="text" class="form-control" name="org[name]" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Type</label>
                  <select class="form-control" name="org[type]" required>
                    <option value="Academic">Academic</option>
                    <option value="Co-Academic">Co-Academic</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Logo URL</label>
                  <input type="url" class="form-control" name="org[logo_url]">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Audience</label>
                  <input type="text" class="form-control" name="org[audience]" 
                         placeholder="e.g., All CCIT Students">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Summary</label>
                <textarea class="form-control" name="org[summary]" rows="3" required></textarea>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Facebook URL</label>
                  <input type="url" class="form-control" name="org[facebook_url]">
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Instagram URL</label>
                  <input type="url" class="form-control" name="org[instagram_url]">
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">X (Twitter) URL</label>
                  <input type="url" class="form-control" name="org[x_url]">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Learn More URL</label>
                <input type="url" class="form-control" name="org[learn_more_url]">
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Organization
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Existing Organizations Card -->
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Existing Organizations (<?= count($organizations) ?>)</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#organizationsListCard" aria-expanded="true" aria-controls="organizationsListCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse show" id="organizationsListCard">
          <div class="card-body">
            <?php if (empty($organizations)): ?>
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No organizations added yet.
              </div>
            <?php else: ?>
              <div class="row">
                <?php foreach ($organizations as $org): ?>
                  <div class="col-lg-6 mb-4">
                    <div class="card organization-item">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                          <div>
                            <h6 class="card-title mb-1"><?= esc($org['name']) ?></h6>
                            <div class="organization-meta">
                              <span class="badge bg-primary"><?= esc($org['type']) ?></span>
                              <span class="text-muted ms-2">Audience: <?= esc($org['audience']) ?></span>
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
                                        data-bs-target="#editOrganization<?= $org['id'] ?>">
                                  <i class="fas fa-edit"></i> Edit
                                </button>
                              </li>
                              <?php if ($org['learn_more_url']): ?>
                              <li>
                                <a href="<?= esc($org['learn_more_url']) ?>" target="_blank" class="dropdown-item">
                                  <i class="fas fa-external-link-alt"></i> Learn More
                                </a>
                              </li>
                              <?php endif; ?>
                              <li>
                                <form method="post" style="display: inline;" 
                                      onsubmit="return confirm('Delete this organization?')">
                                  <input type="hidden" name="delete_org" value="1">
                                  <input type="hidden" name="id" value="<?= (int)$org['id'] ?>">
                                  <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash"></i> Delete
                                  </button>
                                </form>
                              </li>
                            </ul>
                          </div>
                        </div>
                        
                        <p class="organization-summary"><?= esc($org['summary']) ?></p>
                        
                        <?php if ($org['facebook_url'] || $org['instagram_url'] || $org['x_url']): ?>
                          <div class="organization-social mt-2">
                            <?php if ($org['facebook_url']): ?>
                              <a href="<?= esc($org['facebook_url']) ?>" target="_blank" class="btn btn-outline-primary btn-sm me-1">
                                <i class="fab fa-facebook"></i>
                              </a>
                            <?php endif; ?>
                            <?php if ($org['instagram_url']): ?>
                              <a href="<?= esc($org['instagram_url']) ?>" target="_blank" class="btn btn-outline-primary btn-sm me-1">
                                <i class="fab fa-instagram"></i>
                              </a>
                            <?php endif; ?>
                            <?php if ($org['x_url']): ?>
                              <a href="<?= esc($org['x_url']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-x-twitter"></i>
                              </a>
                            <?php endif; ?>
                          </div>
                        <?php endif; ?>
                        
                        <!-- Edit Form (Collapsible) -->
                        <div class="collapse mt-3" id="editOrganization<?= $org['id'] ?>">
                          <div class="border-top pt-3">
                            <form method="post">
                              <input type="hidden" name="edit_org" value="1">
                              <input type="hidden" name="id" value="<?= (int)$org['id'] ?>">
                              <div class="row">
                                <div class="col-md-8 mb-3">
                                  <label class="form-label">Organization Name</label>
                                  <input type="text" class="form-control" name="org[name]" 
                                         value="<?= esc($org['name']) ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                  <label class="form-label">Type</label>
                                  <select class="form-control" name="org[type]" required>
                                    <option value="Academic" <?= $org['type'] === 'Academic' ? 'selected' : '' ?>>Academic</option>
                                    <option value="Co-Academic" <?= $org['type'] === 'Co-Academic' ? 'selected' : '' ?>>Co-Academic</option>
                                  </select>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">Logo URL</label>
                                  <input type="url" class="form-control" name="org[logo_url]" 
                                         value="<?= esc($org['logo_url']) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">Audience</label>
                                  <input type="text" class="form-control" name="org[audience]" 
                                         value="<?= esc($org['audience']) ?>">
                                </div>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Summary</label>
                                <textarea class="form-control" name="org[summary]" rows="3" required><?= esc($org['summary']) ?></textarea>
                              </div>
                              <div class="row">
                                <div class="col-md-4 mb-3">
                                  <label class="form-label">Facebook URL</label>
                                  <input type="url" class="form-control" name="org[facebook_url]" 
                                         value="<?= esc($org['facebook_url']) ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                  <label class="form-label">Instagram URL</label>
                                  <input type="url" class="form-control" name="org[instagram_url]" 
                                         value="<?= esc($org['instagram_url']) ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                  <label class="form-label">X (Twitter) URL</label>
                                  <input type="url" class="form-control" name="org[x_url]" 
                                         value="<?= esc($org['x_url']) ?>">
                                </div>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Learn More URL</label>
                                <input type="url" class="form-control" name="org[learn_more_url]" 
                                       value="<?= esc($org['learn_more_url']) ?>">
                              </div>
                              <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                  <i class="fas fa-save"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" 
                                        data-bs-toggle="collapse" data-bs-target="#editOrganization<?= $org['id'] ?>">
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