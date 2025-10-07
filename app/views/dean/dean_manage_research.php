<?php
// app/views/dean/dean_manage_research.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['dean'], true)) {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../../models/FacultyResearchPageSettings.php';
require_once __DIR__ . '/../../models/FacultyResearch.php';

if (!function_exists('esc')) {
    function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = $_SESSION['user'] ?? [];
$username = $user['username'] ?? 'Dean';

$notice = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (isset($_POST['settings'])) {
      FacultyResearchPageSettings::updateSettings($_POST['settings']);
      $notice = 'Page settings saved.';
    } elseif (isset($_POST['add_research'])) {
      FacultyResearch::create($_POST['research'] ?? []);
      $notice = 'Research added.';
    } elseif (isset($_POST['edit_research'])) {
      FacultyResearch::update((int)$_POST['id'], $_POST['research'] ?? []);
      $notice = 'Research updated.';
    } elseif (isset($_POST['delete_research'])) {
      FacultyResearch::delete((int)$_POST['id']);
      $notice = 'Research deleted.';
    }
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

$settings = [];
$research = FacultyResearch::getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Research | CCIT Dean</title>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/_dean_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Faculty → Research</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'D')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Faculty Research Management</h1>
      
      <?php if ($notice): ?>
        <div class="alert alert-<?= str_starts_with($notice, 'Error:') ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
          <?= esc($notice) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Page Settings Card removed for dean access -->

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
                <div class="col-md-6 mb-3">
                  <label class="form-label">Title</label>
                  <input type="text" class="form-control" name="research[title]" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Department</label>
                  <select class="form-select" name="research[dept]" required>
                    <option value="">Select Department</option>
                    <option value="itis">IT&IS</option>
                    <option value="cs">CS</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Type</label>
                  <select class="form-select" name="research[type]" required>
                    <option value="">Select Type</option>
                    <option value="journal">Journal Article</option>
                    <option value="conference">Conference Paper</option>
                    <option value="chapter">Book Chapter</option>
                    <option value="patent">Patent</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Year</label>
                  <input type="text" class="form-control" name="research[year]" 
                         required placeholder="e.g., 2024">
                </div>
              </div>
              <div class="mb-3">
                <label for="research_authors" class="form-label">
                  <i class="fas fa-users"></i> Authors
                </label>
                <textarea class="form-control" id="research_authors" name="research[authors]" rows="2" 
                          required placeholder="e.g., John Doe, Jane Smith"></textarea>
              </div>
              <div class="mt-3">
                <label for="research_venue" class="form-label">
                  <i class="fas fa-university"></i> Venue
                </label>
                <input type="text" class="form-control" id="research_venue" name="research[venue]" 
                       placeholder="e.g., IEEE Transactions on Computers" required>
              </div>
              <div class="row mt-3">
                <div class="col-md-6">
                  <label for="research_pdf_url" class="form-label">
                    <i class="fas fa-file-pdf"></i> PDF URL
                  </label>
                  <input type="url" class="form-control" id="research_pdf_url" name="research[pdf_url]" 
                         placeholder="Link to PDF">
                </div>
                <div class="col-md-6">
                  <label for="research_view_url" class="form-label">
                    <i class="fas fa-link"></i> View URL
                  </label>
                  <input type="url" class="form-control" id="research_view_url" name="research[view_url]" 
                         placeholder="Link to publication">
                </div>
              </div>
              <div class="mt-3">
                <label for="research_image_url" class="form-label">
                  <i class="fas fa-image"></i> Image URL
                </label>
                <input type="url" class="form-control" id="research_image_url" name="research[image_url]" 
                       placeholder="Link to research poster/image">
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
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead class="table-dark">
                    <tr>
                      <th>Title</th>
                      <th>Type</th>
                      <th>Department</th>
                      <th>Year</th>
                      <th>Authors</th>
                      <th>Venue</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($research as $r): ?>
                      <tr>
                        <td>
                          <strong><?= esc($r['title']) ?></strong>
                          <?php if ($r['pdf_url'] || $r['view_url']): ?>
                            <br>
                            <?php if ($r['pdf_url']): ?>
                              <a href="<?= esc($r['pdf_url']) ?>" target="_blank" class="btn btn-sm btn-outline-danger me-1">
                                <i class="fas fa-file-pdf"></i> PDF
                              </a>
                            <?php endif; ?>
                            <?php if ($r['view_url']): ?>
                              <a href="<?= esc($r['view_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt"></i> View
                              </a>
                            <?php endif; ?>
                          <?php endif; ?>
                        </td>
                        <td>
                          <span class="badge bg-info"><?= esc(ucfirst($r['type'])) ?></span>
                        </td>
                        <td>
                          <span class="badge bg-secondary"><?= esc(ucfirst($r['dept'])) ?></span>
                        </td>
                        <td><?= esc($r['year']) ?></td>
                        <td>
                          <small><?= esc($r['authors']) ?></small>
                        </td>
                        <td>
                          <small><?= esc($r['venue']) ?></small>
                        </td>
                        <td>
                          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editResearchModal<?= $r['id'] ?>">
                            <i class="fas fa-edit"></i>
                          </button>
                          <form method="post" class="inline-form" onsubmit="return confirm('Delete this research?')" style="display:inline;">
                            <input type="hidden" name="delete_research" value="1">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
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

      <!-- Edit Research Modals -->
      <?php foreach ($research as $r): ?>
        <div class="modal fade" id="editResearchModal<?= $r['id'] ?>" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Edit Research</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <form method="post">
                <div class="modal-body">
                  <input type="hidden" name="edit_research" value="1">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <div class="row">
                    <div class="col-md-6">
                      <label class="form-label">Title</label>
                      <input type="text" class="form-control" name="research[title]" value="<?= esc($r['title']) ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Department</label>
                      <select class="form-select" name="research[dept]" required>
                        <option value="itis" <?= $r['dept'] === 'itis' ? 'selected' : '' ?>>IT&IS</option>
                        <option value="cs" <?= $r['dept'] === 'cs' ? 'selected' : '' ?>>CS</option>
                      </select>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-6">
                      <label class="form-label">Type</label>
                      <select class="form-select" name="research[type]" required>
                        <option value="journal" <?= $r['type'] === 'journal' ? 'selected' : '' ?>>Journal Article</option>
                        <option value="conference" <?= $r['type'] === 'conference' ? 'selected' : '' ?>>Conference Paper</option>
                        <option value="chapter" <?= $r['type'] === 'chapter' ? 'selected' : '' ?>>Book Chapter</option>
                        <option value="patent" <?= $r['type'] === 'patent' ? 'selected' : '' ?>>Patent</option>
                        <option value="other" <?= $r['type'] === 'other' ? 'selected' : '' ?>>Other</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Year</label>
                      <input type="text" class="form-control" name="research[year]" value="<?= esc($r['year']) ?>" required>
                    </div>
                  </div>
                  <div class="mt-3">
                    <label class="form-label">Authors</label>
                    <textarea class="form-control" name="research[authors]" rows="2" required><?= esc($r['authors']) ?></textarea>
                  </div>
                  <div class="mt-3">
                    <label class="form-label">Venue</label>
                    <input type="text" class="form-control" name="research[venue]" value="<?= esc($r['venue']) ?>" required>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-6">
                      <label class="form-label">PDF URL</label>
                      <input type="url" class="form-control" name="research[pdf_url]" value="<?= esc($r['pdf_url']) ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">View URL</label>
                      <input type="url" class="form-control" name="research[view_url]" value="<?= esc($r['view_url']) ?>">
                    </div>
                  </div>
                  <div class="mt-3">
                    <label class="form-label">Image URL</label>
                    <input type="url" class="form-control" name="research[image_url]" value="<?= esc($r['image_url']) ?>">
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
</body>
</html>