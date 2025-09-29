<?php
// app/views/admin/admin_student_research.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../../models/StudentResearchPageSettings.php';
require_once __DIR__ . '/../../models/StudentResearch.php';

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
      StudentResearchPageSettings::updateSettings($_POST['settings']);
      $notice = 'Page settings saved.';
    } elseif (isset($_POST['add_research'])) {
      StudentResearch::create($_POST['research'] ?? []);
      $notice = 'Research added.';
    } elseif (isset($_POST['edit_research'])) {
      StudentResearch::update((int)$_POST['id'], $_POST['research'] ?? []);
      $notice = 'Research updated.';
    } elseif (isset($_POST['delete_research'])) {
      StudentResearch::delete((int)$_POST['id']);
      $notice = 'Research deleted.';
    }
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

$settings = StudentResearchPageSettings::getSettings();
$research = StudentResearch::getAll();
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Student → Research</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Student Research Management</h1>
      
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

      <!-- Add New Research Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Add New Research</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#addResearchCard" aria-expanded="false" aria-controls="addResearchCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="addResearchCard">
          <div class="card-body">
            <form method="post" autocomplete="off">
              <input type="hidden" name="add_research" value="1">
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label class="form-label">Research Title</label>
                  <input type="text" class="form-control" name="research[title]" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Category</label>
                  <select class="form-control" name="research[category]" required>
                    <option value="publication">Publication</option>
                    <option value="project">Project</option>
                    <option value="award">Award</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Year</label>
                  <input type="number" class="form-control" name="research[year]" 
                         min="2000" max="2030" value="<?= date('Y') ?>" required>
                </div>
                <div class="col-md-8 mb-3">
                  <label class="form-label">Authors</label>
                  <input type="text" class="form-control" name="research[authors]" 
                         placeholder="Author names">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="research[description]" rows="4" 
                          placeholder="Research description or abstract"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Meta Information</label>
                <textarea class="form-control" name="research[meta]" rows="2" 
                          placeholder="Conference, DOI, publication details"></textarea>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Image URL</label>
                  <input type="url" class="form-control" name="research[image_url]" 
                         placeholder="Research image or thumbnail">
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Link Label</label>
                  <input type="text" class="form-control" name="research[link_label]" 
                         placeholder="e.g., Read on IEEE Xplore">
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Link URL</label>
                  <input type="url" class="form-control" name="research[link_url]" 
                         placeholder="Link to full research document">
                </div>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Research
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Existing Research Card -->
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Existing Research (<?= count($research) ?>)</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#researchListCard" aria-expanded="true" aria-controls="researchListCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse show" id="researchListCard">
          <div class="card-body">
            <?php if (empty($research)): ?>
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No research added yet.
              </div>
            <?php else: ?>
              <div class="row">
                <?php foreach ($research as $item): ?>
                  <div class="col-lg-6 mb-4">
                    <div class="card research-item">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                          <div>
                            <h6 class="card-title mb-1"><?= esc($item['title']) ?></h6>
                            <div class="research-meta">
                              <span class="badge bg-primary"><?= esc(ucfirst($item['category'])) ?></span>
                              <span class="text-muted ms-2">Year: <?= esc($item['year']) ?></span>
                              <?php if ($item['authors']): ?>
                                <br><small class="text-muted">Authors: <?= esc($item['authors']) ?></small>
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
                                        data-bs-target="#editResearch<?= $item['id'] ?>">
                                  <i class="fas fa-edit"></i> Edit
                                </button>
                              </li>
                              <?php if ($item['link_url']): ?>
                              <li>
                                <a href="<?= esc($item['link_url']) ?>" target="_blank" class="dropdown-item">
                                  <i class="fas fa-external-link-alt"></i> View Research
                                </a>
                              </li>
                              <?php endif; ?>
                              <li>
                                <form method="post" style="display: inline;" 
                                      onsubmit="return confirm('Delete this research?')">
                                  <input type="hidden" name="delete_research" value="1">
                                  <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                  <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash"></i> Delete
                                  </button>
                                </form>
                              </li>
                            </ul>
                          </div>
                        </div>
                        
                        <?php if ($item['description']): ?>
                          <p class="research-description"><?= esc($item['description']) ?></p>
                        <?php endif; ?>
                        
                        <?php if ($item['meta']): ?>
                          <p class="research-meta-info"><small class="text-muted"><?= esc($item['meta']) ?></small></p>
                        <?php endif; ?>
                        
                        <!-- Edit Form (Collapsible) -->
                        <div class="collapse mt-3" id="editResearch<?= $item['id'] ?>">
                          <div class="border-top pt-3">
                            <form method="post">
                              <input type="hidden" name="edit_research" value="1">
                              <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                              <div class="row">
                                <div class="col-md-8 mb-3">
                                  <label class="form-label">Research Title</label>
                                  <input type="text" class="form-control" name="research[title]" 
                                         value="<?= esc($item['title']) ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                  <label class="form-label">Category</label>
                                  <select class="form-control" name="research[category]" required>
                                    <option value="publication" <?= $item['category'] === 'publication' ? 'selected' : '' ?>>Publication</option>
                                    <option value="project" <?= $item['category'] === 'project' ? 'selected' : '' ?>>Project</option>
                                    <option value="award" <?= $item['category'] === 'award' ? 'selected' : '' ?>>Award</option>
                                  </select>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-4 mb-3">
                                  <label class="form-label">Year</label>
                                  <input type="number" class="form-control" name="research[year]" 
                                         value="<?= esc($item['year']) ?>" min="2000" max="2030" required>
                                </div>
                                <div class="col-md-8 mb-3">
                                  <label class="form-label">Authors</label>
                                  <input type="text" class="form-control" name="research[authors]" 
                                         value="<?= esc($item['authors']) ?>">
                                </div>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="research[description]" rows="3"><?= esc($item['description']) ?></textarea>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Meta Information</label>
                                <textarea class="form-control" name="research[meta]" rows="2"><?= esc($item['meta']) ?></textarea>
                              </div>
                              <div class="row">
                                <div class="col-md-4 mb-3">
                                  <label class="form-label">Image URL</label>
                                  <input type="url" class="form-control" name="research[image_url]" 
                                         value="<?= esc($item['image_url']) ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                  <label class="form-label">Link Label</label>
                                  <input type="text" class="form-control" name="research[link_label]" 
                                         value="<?= esc($item['link_label']) ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                  <label class="form-label">Link URL</label>
                                  <input type="url" class="form-control" name="research[link_url]" 
                                         value="<?= esc($item['link_url']) ?>">
                                </div>
                              </div>
                              <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                  <i class="fas fa-save"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" 
                                        data-bs-toggle="collapse" data-bs-target="#editResearch<?= $item['id'] ?>">
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