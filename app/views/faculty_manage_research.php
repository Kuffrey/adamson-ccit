<?php
// app/views/faculty_manage_research.php — Faculty Research Submission (dean layout)
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'faculty') {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../models/FacultySubmissions.php';

$faculty_id       = $_SESSION['user']['id'] ?? null;
$faculty_username = $_SESSION['user']['username'] ?? 'Faculty';
if (!$faculty_id) { header('Location: ?page=login'); exit; }

/* ---------- Helpers ---------- */
function esc($v)   { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function eattr($v) { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

/** Map submission statuses to dean.css badge variants */
function map_status_for_badge($statusRaw) {
  $s = strtolower(trim((string)$statusRaw));
  switch ($s) {
    case 'approved': return ['class' => 'published', 'label' => 'Approved']; // green
    case 'rejected': return ['class' => 'archived',  'label' => 'Rejected']; // gray
    case 'submitted':
    case 'pending':
    default:         return ['class' => 'draft',     'label' => ucfirst($s ?: 'Pending')]; // amber
  }
}

/* ---------- Faculty identity (avatar/name) ---------- */
try {
  $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE username = ? LIMIT 1");
  $stmt->execute([$faculty_username]);
  $faculty = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($faculty) {
    $firstName = $faculty['first_name'] ?? 'Faculty';
    $lastName  = $faculty['last_name']  ?? '';
    $fullName  = trim($firstName . ' ' . $lastName);
  } else {
    $firstName = "Faculty"; $lastName = ""; $fullName = $faculty_username;
  }
} catch (PDOException $e) {
  $firstName = "Faculty"; $lastName = ""; $fullName = $faculty_username;
}

/* ---------- Handle POST ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (isset($_POST['add_research'])) {
      $title      = trim($_POST['title'] ?? '');
      $authors    = trim($_POST['authors'] ?? '');
      $doi        = trim($_POST['doi'] ?? '');
      $publisher  = trim($_POST['publisher'] ?? '');
      $conference = trim($_POST['conference'] ?? '');
      $year       = trim($_POST['year'] ?? '');
      $view_url   = trim($_POST['view_url'] ?? '');

      if ($title === '')   throw new Exception('Research title is required.');
      if ($authors === '') throw new Exception('Authors are required.');
      if ($year  === '')   throw new Exception('Year is required.');

      // Save as submission for dean approval (not published yet)
      $payload = [
        'authors'    => $authors,
        'doi'        => $doi,
        'publisher'  => $publisher,
        'conference' => $conference,
        'year'       => $year,
        'view_url'   => $view_url
      ];
      $submissionData = [
        'faculty_id'      => $faculty_id,
        'submission_type' => 'research',
        'title'           => $title,
        'description'     => $title,
        'category'        => 'publication',
        'content'         => json_encode($payload, JSON_UNESCAPED_SLASHES),
        'status'          => 'submitted'
      ];
      $submissionId = FacultySubmissions::create($submissionData);

      if ($submissionId) {
        $success_message = 'Research submitted for dean approval successfully!';
      } else {
        $error_message = 'Failed to submit research for approval.';
      }

    } elseif (isset($_POST['delete_submission'], $_POST['submission_id'])) {
      $submissionId = (int)$_POST['submission_id'];
      $submission   = FacultySubmissions::getById($submissionId);

      if ($submission && $submission['faculty_id'] == $faculty_id && !in_array(strtolower($submission['status']), ['approved', 'published'], true)) {
        $deleted = FacultySubmissions::delete($submissionId);
        $success_message = $deleted ? 'Research submission deleted successfully!' : 'Error: Failed to delete submission.';
      } else {
        $error_message = 'Error: Cannot delete this research submission.';
      }

    } elseif (isset($_POST['edit_submission'], $_POST['submission_id'])) {
      $submissionId = (int)$_POST['submission_id'];
      $submission   = FacultySubmissions::getById($submissionId);

      if ($submission && $submission['faculty_id'] == $faculty_id && in_array(strtolower($submission['status']), ['submitted', 'pending'], true)) {
        $title      = trim($_POST['title'] ?? '');
        $authors    = trim($_POST['authors'] ?? '');
        $doi        = trim($_POST['doi'] ?? '');
        $publisher  = trim($_POST['publisher'] ?? '');
        $conference = trim($_POST['conference'] ?? '');
        $year       = trim($_POST['year'] ?? '');
        $view_url   = trim($_POST['view_url'] ?? '');

        if ($title === '')   throw new Exception('Research title is required.');
        if ($authors === '') throw new Exception('Authors are required.');
        if ($year  === '')   throw new Exception('Year is required.');

        $payload = [
          'authors'    => $authors,
          'doi'        => $doi,
          'publisher'  => $publisher,
          'conference' => $conference,
          'year'       => $year,
          'view_url'   => $view_url
        ];

        $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("UPDATE faculty_submissions SET title = ?, description = ?, category = ?, content = ? WHERE id = ?");
        $result = $stmt->execute([
          $title,
          $title,
          'publication',
          json_encode($payload, JSON_UNESCAPED_SLASHES),
          $submissionId
        ]);

        $success_message = $result ? 'Research submission updated successfully!' : 'Error: Failed to update submission.';
      } else {
        $error_message = 'Error: Cannot edit this research submission.';
      }
    }
  } catch (Exception $e) {
    $error_message = 'Error: ' . $e->getMessage();
    error_log("Research submission error: " . $e->getMessage());
  }
}

/* ---------- Load data ---------- */
$research = FacultySubmissions::getByFacultyAndType($faculty_id, 'research');

// Partition by status
$pendingResearch  = [];
$reviewedResearch = [];
foreach ($research as $item) {
  $status = strtolower($item['status'] ?? '');
  if (in_array($status, ['submitted', 'pending'])) {
    $pendingResearch[]  = $item;
  } else {
    $reviewedResearch[] = $item;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Faculty Research Submission | CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/dean.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    /* (Optional) ensure action buttons stay neatly aligned */
    td.actions-cell { white-space: nowrap; text-align: center; }
    td.actions-cell .btn-group { vertical-align: middle; }
    td.actions-cell form { display: inline-block; margin: 0; }
    td.actions-cell .btn { line-height: 1; }
  </style>
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <button class="topbar__btn hide-desktop" type="button" aria-label="Open navigation menu" data-sb-open>
        <i class="fas fa-bars"></i>
      </button>
      <span class="admin-topbar__title">Faculty Research Submission</span>
      <span class="admin-topbar__spacer"></span>
    </header>

    <section class="admin-section">
      <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show modern-alert" role="alert">
          <i class="fas fa-check-circle me-2"></i><?= esc($success_message) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show modern-alert" role="alert">
          <i class="fas fa-exclamation-circle me-2"></i><?= esc($error_message) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <!-- Info message at the top -->
      <div class="alert alert-info py-2 mb-4 d-flex align-items-center" style="font-size:0.97rem;">
        <i class="fas fa-info-circle me-2"></i>
        Your research will be reviewed by the dean before publication.
      </div>

      <!-- Header Actions -->
      <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0">
              <i class="fas fa-microscope me-2"></i>Your Research Publications
            </h5>
          </div>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResearchModal">
            <i class="fas fa-plus me-2"></i>Add Publication
          </button>
        </div>
      </div>

      <!-- Pending Dean Review -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="fas fa-clock me-2"></i>Pending Dean Review
          </h5>
        </div>
        <div class="card-body">
          <?php if (empty($pendingResearch)): ?>
            <div class="empty-state-card">
              <i class="fas fa-book fa-3x"></i>
              <h6>No pending research submissions</h6>
              <div class="text-muted">Add your research publications using the button above.</div>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover dashboard-table align-middle" id="researchTable">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th>Authors</th>
                    <th>DOI</th>
                    <th>Publisher</th>
                    <th>Conference</th>
                    <th>Year</th>
                    <th>View URL</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($pendingResearch as $item): ?>
                    <?php
                      $rd = json_decode($item['content'] ?? '{}', true) ?: [];
                      $authors    = $rd['authors']    ?? '';
                      $doi        = $rd['doi']        ?? '';
                      $publisher  = $rd['publisher']  ?? '';
                      $conference = $rd['conference'] ?? '';
                      $year       = $rd['year']       ?? '';
                      $view_url   = $rd['view_url']   ?? '';
                    ?>
                    <tr data-year="<?= esc($year) ?>">
                      <td><?= esc($item['title']) ?></td>
                      <td><?= esc($authors) ?></td>
                      <td><?= esc($doi) ?></td>
                      <td><?= esc($publisher) ?></td>
                      <td><?= esc($conference) ?></td>
                      <td><?= esc($year) ?></td>
                      <td>
                        <?php if (!empty($view_url)): ?>
                          <a href="<?= esc($view_url) ?>" target="_blank" rel="noopener">View</a>
                        <?php else: ?>
                          <span class="no-notes text-muted">No link</span>
                        <?php endif; ?>
                      </td>
                      <td class="actions-cell">
                        <div class="btn-group btn-group-sm" role="group">
                          <button type="button"
                                  class="btn btn-info"
                                  title="View details"
                                  data-bs-toggle="modal"
                                  data-bs-target="#viewResearchModal"
                                  data-title="<?= esc($item['title']) ?>"
                                  data-authors="<?= esc($authors) ?>"
                                  data-doi="<?= esc($doi) ?>"
                                  data-publisher="<?= esc($publisher) ?>"
                                  data-conference="<?= esc($conference) ?>"
                                  data-year="<?= esc($year) ?>"
                                  data-view_url="<?= esc($view_url) ?>">
                            <i class="fas fa-eye"></i>
                          </button>
                          <button type="button"
                                  class="btn btn-warning"
                                  title="Edit"
                                  data-bs-toggle="modal"
                                  data-bs-target="#editResearchModal"
                                  data-id="<?= (int)$item['id'] ?>"
                                  data-title="<?= esc($item['title']) ?>"
                                  data-authors="<?= esc($authors) ?>"
                                  data-doi="<?= esc($doi) ?>"
                                  data-publisher="<?= esc($publisher) ?>"
                                  data-conference="<?= esc($conference) ?>"
                                  data-year="<?= esc($year) ?>"
                                  data-view_url="<?= esc($view_url) ?>">
                          <i class="fas fa-edit"></i>
                        </button>
                        <form method="post" class="d-inline" onsubmit="return confirm('Delete this publication?')">
                          <input type="hidden" name="delete_submission" value="1">
                          <input type="hidden" name="submission_id" value="<?= (int)$item['id'] ?>">
                          <button type="submit" class="btn btn-danger" title="Delete">
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

      <!-- Reviewed by Dean -->
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="fas fa-check-circle me-2"></i>Reviewed by Dean
          </h5>
        </div>
        <div class="card-body">
          <?php if (empty($reviewedResearch)): ?>
            <div class="empty-state-card">
              <i class="fas fa-microscope fa-3x"></i>
              <h6>No reviewed submissions yet</h6>
              <div class="text-muted">Once the dean reviews your research, they will appear here.</div>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover dashboard-table align-middle">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th>Authors</th>
                    <th>DOI</th>
                    <th>Publisher</th>
                    <th>Conference</th>
                    <th>Year</th>
                    <th>View URL</th>
                    <th>Status</th>
                    <th>Review Notes</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($reviewedResearch as $item): ?>
                    <?php
                      $rd = json_decode($item['content'] ?? '{}', true) ?: [];
                      $authors    = $rd['authors']    ?? '';
                      $doi        = $rd['doi']        ?? '';
                      $publisher  = $rd['publisher']  ?? '';
                      $conference = $rd['conference'] ?? '';
                      $year       = $rd['year']       ?? '';
                      $view_url   = $rd['view_url']   ?? '';
                      $status     = ucfirst($item['status'] ?? '');
                      $review_notes = $item['review_notes'] ?? '';
                    ?>
                    <tr>
                      <td><?= esc($item['title']) ?></td>
                      <td><?= esc($authors) ?></td>
                      <td><?= esc($doi) ?></td>
                      <td><?= esc($publisher) ?></td>
                      <td><?= esc($conference) ?></td>
                      <td><?= esc($year) ?></td>
                      <td>
                        <?php if (!empty($view_url)): ?>
                          <a href="<?= esc($view_url) ?>" target="_blank" rel="noopener">View</a>
                        <?php else: ?>
                          <span class="no-notes text-muted">No link</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span class="badge badge--status badge--<?= strtolower($item['status']) ?>">
                          <?= esc($status) ?>
                        </span>
                      </td>
                      <td>
                        <?php if (!empty($review_notes)): ?>
                          <div class="review-notes-full"><?= esc($review_notes) ?></div>
                        <?php else: ?>
                          <span class="no-notes text-muted">No review notes</span>
                        <?php endif; ?>
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

<!-- Move modals outside .admin-layout for proper stacking -->
<!-- Add Research Modal -->
<div class="modal fade" id="addResearchModal" tabindex="-1" aria-labelledby="addResearchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="?page=faculty_manage_research" novalidate>
        <input type="hidden" name="add_research" value="1">
        <div class="modal-header">
          <h5 class="modal-title" id="addResearchModalLabel">
            <i class="fas fa-plus me-2"></i>Add Research Publication
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="title" class="form-label">Research Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required maxlength="255" placeholder="Enter research title">
              </div>
              <div class="mb-3">
                <label for="authors" class="form-label">Authors <span class="text-danger">*</span></label>
                <textarea id="authors" name="authors" class="form-control" rows="2" required placeholder="e.g., John Doe, Jane Smith"></textarea>
              </div>
              <div class="mb-3">
                <label for="doi" class="form-label">DOI <span class="text-muted">(optional)</span></label>
                <input type="text" id="doi" name="doi" class="form-control" placeholder="e.g., 10.1234/abcde.2024.001">
              </div>
              <div class="mb-3">
                <label for="publisher" class="form-label">Publisher <span class="text-muted">(optional)</span></label>
                <input type="text" id="publisher" name="publisher" class="form-control" placeholder="e.g., IEEE">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="conference" class="form-label">Conference <span class="text-muted">(where presented, optional)</span></label>
                <input type="text" id="conference" name="conference" class="form-control" placeholder="e.g., IEEE ICIS 2024">
              </div>
              <div class="mb-3">
                <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                <input type="text" id="year" name="year" class="form-control" inputmode="numeric" pattern="\d{4}" required placeholder="e.g., 2024">
              </div>
              <div class="mb-3">
                <label for="view_url" class="form-label">View URL <span class="text-muted">(optional)</span></label>
                <input type="url" id="view_url" name="view_url" class="form-control" placeholder="https://doi.org/...">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="submitBtn">
            <i class="fas fa-paper-plane me-2"></i>Submit Publication
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Research Modal -->
<div class="modal fade" id="editResearchModal" tabindex="-1" aria-labelledby="editResearchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="?page=faculty_manage_research" novalidate>
        <input type="hidden" name="edit_submission" value="1">
        <input type="hidden" name="submission_id" id="edit_submission_id" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="editResearchModalLabel">
            <i class="fas fa-edit me-2"></i>Edit Research Publication
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="edit_title" class="form-label">Research Title <span class="text-danger">*</span></label>
                <input type="text" id="edit_title" name="title" class="form-control" required maxlength="255">
              </div>
              <div class="mb-3">
                <label for="edit_authors" class="form-label">Authors <span class="text-danger">*</span></label>
                <textarea id="edit_authors" name="authors" class="form-control" rows="2" required></textarea>
              </div>
              <div class="mb-3">
                <label for="edit_doi" class="form-label">DOI <span class="text-muted">(optional)</span></label>
                <input type="text" id="edit_doi" name="doi" class="form-control">
              </div>
              <div class="mb-3">
                <label for="edit_publisher" class="form-label">Publisher <span class="text-muted">(optional)</span></label>
                <input type="text" id="edit_publisher" name="publisher" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="edit_conference" class="form-label">Conference <span class="text-muted">(where presented, optional)</span></label>
                <input type="text" id="edit_conference" name="conference" class="form-control">
              </div>
              <div class="mb-3">
                <label for="edit_year" class="form-label">Year <span class="text-danger">*</span></label>
                <input type="text" id="edit_year" name="year" class="form-control" inputmode="numeric" pattern="\d{4}" required>
              </div>
              <div class="mb-3">
                <label for="edit_view_url" class="form-label">View URL <span class="text-muted">(optional)</span></label>
                <input type="url" id="edit_view_url" name="view_url" class="form-control">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Submission Modal -->
<div class="modal fade" id="viewResearchModal" tabindex="-1" aria-labelledby="viewResearchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewResearchModalLabel">
          <i class="fas fa-eye me-2"></i>Publication Details
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="font-size:1.05rem; color:#222;">
        <div class="mb-3">
          <strong>Title:</strong>
          <div id="view_research_title" class="fw-semibold" style="font-size:1.15rem; color:#003169; margin-top:2px;"></div>
        </div>
        <div class="mb-3">
          <strong>Authors:</strong>
          <span id="view_research_authors" style="font-weight:500; color:#0080c9; margin-left:4px;"></span>
        </div>
        <div class="mb-3">
          <strong>DOI:</strong>
          <span id="view_research_doi" style="margin-left:4px;"></span>
        </div>
        <div class="mb-3">
          <strong>Publisher:</strong>
          <span id="view_research_publisher" style="margin-left:4px;"></span>
        </div>
        <div class="mb-3">
          <strong>Conference:</strong>
          <span id="view_research_conference" style="margin-left:4px;"></span>
        </div>
        <div class="mb-3">
          <strong>Year:</strong>
          <span id="view_research_year" style="margin-left:4px;"></span>
        </div>
        <div class="mb-3">
          <strong>View URL:</strong>
          <span id="view_research_view_url" style="margin-left:4px;"></span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var editModal = document.getElementById('editResearchModal');
  if (editModal) {
    editModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget || {};
      document.getElementById('edit_submission_id').value = button.getAttribute('data-id') || '';
      document.getElementById('edit_title').value        = button.getAttribute('data-title') || '';
      document.getElementById('edit_authors').value      = button.getAttribute('data-authors') || '';
      document.getElementById('edit_doi').value          = button.getAttribute('data-doi') || '';
      document.getElementById('edit_publisher').value    = button.getAttribute('data-publisher') || '';
      document.getElementById('edit_conference').value   = button.getAttribute('data-conference') || '';
      document.getElementById('edit_year').value         = button.getAttribute('data-year') || '';
      document.getElementById('edit_view_url').value     = button.getAttribute('data-view_url') || '';
    });
  }

  // Add modal validation (match news)
  const form = document.querySelector('#addResearchModal form');
  const titleInput = document.querySelector('#title');
  const descInput = document.querySelector('#description');
  const submitBtn = document.querySelector('#submitBtn');
  if (form && titleInput && descInput && submitBtn) {
    form.addEventListener('submit', function(e) {
      const title = (titleInput.value || '').trim();
      const desc = (descInput.value || '').trim();
      if (!title) {
        e.preventDefault();
        alert('Please enter a research title');
        titleInput.focus();
        return false;
      }
      if (desc.length < 10) {
        e.preventDefault();
        alert('Please enter research description/abstract (minimum 10 characters)');
        descInput.focus();
        return false;
      }
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
    });
  }

  // View modal population
  var viewModal = document.getElementById('viewResearchModal');
  if (viewModal) {
    viewModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      document.getElementById('view_research_title').textContent = button.getAttribute('data-title') || '';
      document.getElementById('view_research_authors').textContent = button.getAttribute('data-authors') || '';
      document.getElementById('view_research_doi').textContent = button.getAttribute('data-doi') || '';
      document.getElementById('view_research_publisher').textContent = button.getAttribute('data-publisher') || '';
      document.getElementById('view_research_conference').textContent = button.getAttribute('data-conference') || '';
      document.getElementById('view_research_year').textContent = button.getAttribute('data-year') || '';
      var viewUrl = button.getAttribute('data-view_url') || '';
      document.getElementById('view_research_view_url').innerHTML = viewUrl ? '<a href="' + viewUrl + '" target="_blank" rel="noopener">' + viewUrl + '</a>' : '<span class="no-notes text-muted">No link</span>';
    });
  }

  // Year filter
  var yearSel = document.getElementById('filterYear');
  var tableRows = document.querySelectorAll('#researchTable tbody tr');
  if (yearSel) {
    yearSel.addEventListener('change', function() {
      var y = yearSel.value;
      tableRows.forEach(function(row) {
        if (y === 'all' || row.getAttribute('data-year') === y) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  }
});
</script>
</body>
</html>

