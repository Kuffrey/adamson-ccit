<?php
// app/views/admin/admin_faculty_research.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin', 'dean'], true)) {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../../models/FacultyResearchPageSettings.php';
require_once __DIR__ . '/../../models/FacultyResearch.php';

if (!function_exists('esc')) {
  function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = $_SESSION['user'] ?? [];
$username = $user['username'] ?? 'Admin';

$notice = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (isset($_POST['add_research'])) {
      $researchData = [
        'title'      => trim($_POST['research']['title'] ?? ''),
        'authors'    => trim($_POST['research']['authors'] ?? ''),
        'doi'        => trim($_POST['research']['doi'] ?? ''),
        'publisher'  => trim($_POST['research']['publisher'] ?? ''),
        'conference' => trim($_POST['research']['conference'] ?? ''),
        'year'       => trim($_POST['research']['year'] ?? ''),
        'view_url'   => trim($_POST['research']['view_url'] ?? ''),
        'status'     => trim($_POST['research']['status'] ?? 'draft')
      ];

      // Validate required fields
      if ($researchData['title'] === '') {
        throw new Exception('Research title is required.');
      }
      if ($researchData['authors'] === '') {
        throw new Exception('Authors are required.');
      }
      if ($researchData['year'] === '') {
        throw new Exception('Year is required.');
      }

      FacultyResearch::create($researchData);
      $notice = 'Research added successfully.';
    } elseif (isset($_POST['edit_research'])) {
      $id = (int)($_POST['id'] ?? 0);
      if ($id <= 0) throw new Exception('Invalid research ID.');

      $data = [
        'title'      => trim($_POST['research']['title'] ?? ''),
        'authors'    => trim($_POST['research']['authors'] ?? ''),
        'doi'        => trim($_POST['research']['doi'] ?? ''),
        'publisher'  => trim($_POST['research']['publisher'] ?? ''),
        'conference' => trim($_POST['research']['conference'] ?? ''),
        'year'       => trim($_POST['research']['year'] ?? ''),
        'view_url'   => trim($_POST['research']['view_url'] ?? ''),
        'status'     => trim($_POST['research']['status'] ?? 'draft')
      ];

      if ($data['title'] === '' || $data['authors'] === '' || $data['year'] === '') {
        throw new Exception('Title, Authors, and Year are required to update.');
      }

      FacultyResearch::update($id, $data);
      $notice = 'Research updated successfully.';
    } elseif (isset($_POST['delete_research'])) {
      $id = (int)($_POST['id'] ?? 0);
      if ($id <= 0) throw new Exception('Invalid research ID.');
      FacultyResearch::delete($id);
      $notice = 'Research deleted successfully.';
    }
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

$research = FacultyResearch::getAll();

// Retain page settings
$settings = FacultyResearchPageSettings::getSettings();
?>

<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Faculty → Research</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
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

      <!-- Page Settings -->
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

      <!-- Add Research Section -->
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
            <form method="post">
              <input type="hidden" name="add_research" value="1">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Title <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="research[title]" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Authors <span class="text-danger">*</span></label>
                  <textarea class="form-control" name="research[authors]" rows="2" required></textarea>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">DOI</label>
                  <input type="text" class="form-control" name="research[doi]">
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Publisher</label>
                  <input type="text" class="form-control" name="research[publisher]">
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Conference</label>
                  <input type="text" class="form-control" name="research[conference]">
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Year <span class="text-danger">*</span></label>
                  <input type="number" class="form-control" name="research[year]" required min="1900" max="2100">
                </div>
                <div class="col-md-5 mb-3">
                  <label class="form-label">View URL (optional)</label>
                  <input type="url" class="form-control" name="research[view_url]" placeholder="https://example.com/paper">
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">Status</label>
                  <select class="form-control" name="research[status]">
                    <option value="draft">Draft</option>
                    <option value="published" selected>Published</option>
                  </select>
                </div>
              </div>
              <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add Research</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Research Table -->
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Existing Research (<?= count($research) ?>)</h5>
        </div>
        <div class="card-body">
          <?php if (empty($research)): ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i> No research added yet.
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th>Authors</th>
                    <th>DOI</th>
                    <th>Publisher</th>
                    <th>Conference</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($research as $r): ?>
                  <?php
                    $rid    = (int)($r['id'] ?? 0);
                    $rtitle = (string)($r['title'] ?? '');
                    $rauth  = (string)($r['authors'] ?? '');
                    $rdoi   = (string)($r['doi'] ?? '');
                    $rpub   = (string)($r['publisher'] ?? '');
                    $rconf  = (string)($r['conference'] ?? '');
                    $ryear  = (string)($r['year'] ?? '');
                    $rview  = (string)($r['view_url'] ?? '');
                    $rstatus= (string)($r['status'] ?? 'draft');
                  ?>
                  <tr>
                    <td><?= esc($rtitle) ?></td>
                    <td><?= esc($rauth) ?></td>
                    <td><?= esc($rdoi) ?></td>
                    <td><?= esc($rpub) ?></td>
                    <td><?= esc($rconf) ?></td>
                    <td><?= esc($ryear) ?></td>
                    <td>
                      <span class="badge bg-<?= strtolower($rstatus) === 'published' ? 'success' : 'secondary' ?>">
                        <?= esc(ucfirst($rstatus)) ?>
                      </span>
                    </td>
                    <td>
                      <div class="btn-group" role="group">
                        <!-- EDIT now opens modal and passes data-* -->
                        <button
                          type="button"
                          class="btn btn-sm btn-warning"
                          data-bs-toggle="modal"
                          data-bs-target="#editResearchModal"
                          data-id="<?= $rid ?>"
                          data-title="<?= esc($rtitle) ?>"
                          data-authors="<?= esc($rauth) ?>"
                          data-doi="<?= esc($rdoi) ?>"
                          data-publisher="<?= esc($rpub) ?>"
                          data-conference="<?= esc($rconf) ?>"
                          data-year="<?= esc($ryear) ?>"
                          data-view_url="<?= esc($rview) ?>"
                          data-status="<?= esc($rstatus) ?>"
                          aria-label="Edit research"
                        >
                          <i class="fas fa-edit"></i>
                        </button>

                        <form method="post" style="display:inline;" onsubmit="return confirm('Delete this research item?');">
                          <input type="hidden" name="delete_research" value="1">
                          <input type="hidden" name="id" value="<?= $rid ?>">
                          <button type="submit" class="btn btn-sm btn-danger" aria-label="Delete research">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editResearchModal" tabindex="-1" aria-labelledby="editResearchLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form method="post" autocomplete="off">
        <div class="modal-header">
          <h5 class="modal-title" id="editResearchLabel">Edit Research</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="edit_research" value="1">
          <input type="hidden" name="id" id="edit-id">

          <div class="row">
            <div class="col-md-7 mb-3">
              <label class="form-label">Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="research[title]" id="edit-title" required>
            </div>
            <div class="col-md-5 mb-3">
              <label class="form-label">Year <span class="text-danger">*</span></label>
              <input type="number" class="form-control" name="research[year]" id="edit-year" required min="1900" max="2100">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Authors <span class="text-danger">*</span></label>
            <textarea class="form-control" name="research[authors]" id="edit-authors" rows="2" required></textarea>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">DOI</label>
              <input type="text" class="form-control" name="research[doi]" id="edit-doi">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Publisher</label>
              <input type="text" class="form-control" name="research[publisher]" id="edit-publisher">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Conference</label>
              <input type="text" class="form-control" name="research[conference]" id="edit-conference">
            </div>
          </div>

          <div class="row">
            <div class="col-md-8 mb-3">
              <label class="form-label">View URL (optional)</label>
              <input type="url" class="form-control" name="research[view_url]" id="edit-view_url" placeholder="https://example.com/paper">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Status</label>
              <select class="form-control" name="research[status]" id="edit-status">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
              </select>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Fill modal fields from the clicked Edit button's data-attributes
document.getElementById('editResearchModal')?.addEventListener('show.bs.modal', function (event) {
  const button = event.relatedTarget;
  if (!button) return;

  const id         = button.getAttribute('data-id') || '';
  const title      = button.getAttribute('data-title') || '';
  const authors    = button.getAttribute('data-authors') || '';
  const doi        = button.getAttribute('data-doi') || '';
  const publisher  = button.getAttribute('data-publisher') || '';
  const conference = button.getAttribute('data-conference') || '';
  const year       = button.getAttribute('data-year') || '';
  const viewUrl    = button.getAttribute('data-view_url') || '';
  const status     = button.getAttribute('data-status') || 'draft';

  this.querySelector('#edit-id').value          = id;
  this.querySelector('#edit-title').value       = title;
  this.querySelector('#edit-authors').value     = authors;
  this.querySelector('#edit-doi').value         = doi;
  this.querySelector('#edit-publisher').value   = publisher;
  this.querySelector('#edit-conference').value  = conference;
  this.querySelector('#edit-year').value        = year;
  this.querySelector('#edit-view_url').value    = viewUrl;

  const statusEl = this.querySelector('#edit-status');
  if (statusEl) {
    for (const opt of statusEl.options) {
      opt.selected = (opt.value.toLowerCase() === status.toLowerCase());
    }
  }
});
</script>
