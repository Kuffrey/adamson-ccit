<?php
// app/views/faculty_manage_news.php — Faculty News Submission (dean layout)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'faculty') {
    header('Location: ?page=login');
    exit;
}

$faculty_id       = $_SESSION['user']['id'] ?? null;
$faculty_username = $_SESSION['user']['username'] ?? 'Faculty';
if (!$faculty_id) {
    header('Location: ?page=login');
    exit;
}

/* ---------- Faculty profile (for header/avatar, if needed later) ---------- */
try {
    $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$faculty_id]);
    $faculty = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($faculty) {
        $firstName = $faculty['first_name'] ?? 'Faculty';
        $lastName  = $faculty['last_name']  ?? '';
        $fullName  = trim($firstName . ' ' . $lastName);
    } else {
        $firstName = "Faculty";
        $lastName  = "";
        $fullName  = $faculty_username;
    }
} catch (PDOException $e) {
    error_log("Error getting faculty info: " . $e->getMessage());
    $firstName = "Faculty";
    $lastName  = "";
    $fullName  = $faculty_username;
}

/* ---------- Handle POST: Add/Edit/Delete submission ---------- */
if (isset($_POST['add_news'])) {
    try {
        $title    = trim($_POST['title'] ?? '');
        $content  = trim($_POST['content'] ?? '');
        $category = $_POST['category'] ?? 'news';

        if ($title === '')        throw new Exception("Article title is required and cannot be empty");
        if ($content === '')      throw new Exception("Article content is required and cannot be empty");
        if (empty($faculty_id))   throw new Exception("Faculty ID is missing from session");

        require_once __DIR__ . '/../models/FacultySubmissions.php';

        $submissionData = [
            'faculty_id'      => (int)$faculty_id,
            'submission_type' => 'news',
            'title'           => $title,
            'description'     => $content,
            'content'         => $content,
            'category'        => $category,
            'status'          => 'submitted'
        ];

        $submissionId = FacultySubmissions::create($submissionData);

        if ($submissionId && $submissionId > 0) {
            $success_message = "News submitted for dean approval successfully! (ID: $submissionId)";
        } else {
            $error_message = "Failed to create submission record. Please contact the system administrator.";
        }
    } catch (Exception $e) {
        $error_message = "Submission failed: " . $e->getMessage();
        error_log("EXCEPTION in news submission: " . $e->getMessage());
    }

    // PRG pattern
    $redirect_url = "?page=faculty_manage_news";
    if (isset($success_message)) {
        $redirect_url .= "&success=" . urlencode($success_message);
    } elseif (isset($error_message)) {
        $redirect_url .= "&error=" . urlencode($error_message);
    }
    header('Location: ' . $redirect_url);
    exit;
} elseif (isset($_POST['edit_news'], $_POST['submission_id'])) {
    try {
        $submissionId = (int)$_POST['submission_id'];
        require_once __DIR__ . '/../models/FacultySubmissions.php';
        $submission = FacultySubmissions::getById($submissionId);

        if ($submission && $submission['faculty_id'] == $faculty_id && in_array(strtolower($submission['status']), ['submitted', 'pending'])) {
            $title    = trim($_POST['title'] ?? '');
            $content  = trim($_POST['content'] ?? '');
            $category = $_POST['category'] ?? 'news';

            if ($title === '')   throw new Exception("Article title is required and cannot be empty");
            if ($content === '') throw new Exception("Article content is required and cannot be empty");

            $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("UPDATE faculty_submissions SET title = ?, description = ?, content = ?, category = ? WHERE id = ?");
            $result = $stmt->execute([
                $title,
                $content,
                $content,
                $category,
                $submissionId
            ]);
            $success_message = $result ? "News submission updated successfully!" : "Error: Failed to update submission.";
        } else {
            $error_message = "Error: Cannot edit this news submission.";
        }
    } catch (Exception $e) {
        $error_message = "Edit failed: " . $e->getMessage();
        error_log("EXCEPTION in news edit: " . $e->getMessage());
    }
    header('Location: ?page=faculty_manage_news' . (isset($success_message) ? "&success=" . urlencode($success_message) : "&error=" . urlencode($error_message)));
    exit;
} elseif (isset($_POST['delete_news'], $_POST['submission_id'])) {
    try {
        $submissionId = (int)$_POST['submission_id'];
        require_once __DIR__ . '/../models/FacultySubmissions.php';
        $submission = FacultySubmissions::getById($submissionId);

        if ($submission && $submission['faculty_id'] == $faculty_id && in_array(strtolower($submission['status']), ['submitted', 'pending'])) {
            $deleted = FacultySubmissions::delete($submissionId);
            $success_message = $deleted ? "News submission deleted successfully!" : "Error: Failed to delete submission.";
        } else {
            $error_message = "Error: Cannot delete this news submission.";
        }
    } catch (Exception $e) {
        $error_message = "Delete failed: " . $e->getMessage();
        error_log("EXCEPTION in news delete: " . $e->getMessage());
    }
    header('Location: ?page=faculty_manage_news' . (isset($success_message) ? "&success=" . urlencode($success_message) : "&error=" . urlencode($error_message)));
    exit;
}

/* ---------- Redirect messages ---------- */
if (isset($_GET['success'])) $success_message = $_GET['success'];
if (isset($_GET['error']))   $error_message   = $_GET['error'];

/* ---------- Load submissions for this faculty ---------- */
try {
    require_once __DIR__ . '/../models/FacultySubmissions.php';
    $submittedNews = FacultySubmissions::getByFacultyAndType($faculty_id, 'news');
} catch (Exception $e) {
    $submittedNews = [];
    error_log("Error fetching news submissions: " . $e->getMessage());
}

/* ---------- Partition by status (Pending vs. Reviewed) ---------- */
$pendingNews  = [];
$assessedNews = [];
foreach ($submittedNews as $item) {
    $status = strtolower($item['status'] ?? '');
    if (in_array($status, ['submitted', 'pending'])) {
        $pendingNews[] = $item;
    } else {
        $assessedNews[] = $item;
    }
}

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

/**
 * Map faculty submission statuses to dean CSS badge variants.
 * Dean CSS supports: published (green), draft (amber), archived (gray).
 */
function map_status_for_badge($statusRaw) {
  $s = strtolower(trim((string)$statusRaw));
  switch ($s) {
    case 'approved':
      return ['class' => 'published', 'label' => 'Approved'];
    case 'rejected':
      return ['class' => 'archived',  'label' => 'Rejected'];
    case 'submitted':
    case 'pending':
    default:
      return ['class' => 'draft',     'label' => ucfirst($s ?: 'Pending')];
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Faculty News Submission | CCIT</title>

  <!-- Match dean layout assets -->
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/dean.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <button class="topbar__btn hide-desktop" type="button" aria-label="Open navigation menu" data-sb-open>
        <i class="fas fa-bars"></i>
      </button>
      <span class="admin-topbar__title">Faculty News Submission</span>
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
        Your article will be reviewed by the dean before publication.
      </div>

      <!-- Header Actions (match dean: card w/ body, left tabs placeholder, right action button) -->
      <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0">
              <i class="fas fa-newspaper me-2"></i>Your News Submissions
            </h5>
          </div>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
            <i class="fas fa-plus me-2"></i>Add News
          </button>
        </div>
      </div>

      <!-- Pending Review -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="fas fa-clock me-2"></i>Pending Dean Review (<?= count($pendingNews) ?>)
          </h5>
        </div>
        <div class="card-body">
          <?php if (empty($pendingNews)): ?>
            <div class="empty-state-card">
              <i class="fas fa-hourglass-half fa-3x"></i>
              <h6>No pending submissions</h6>
              <div class="text-muted">All your news submissions have been reviewed.</div>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <!-- Pending Review Table -->
              <table class="table table-hover dashboard-table align-middle">
                <thead>
                  <tr>
                    <th style="width:40%;">Title &amp; Content</th>
                    <th style="width:10%;">Category</th>
                    <th style="width:10%;">Status</th>
                    <th style="width:15%;">Date</th>
                    <th style="width:5%;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($pendingNews as $item): ?>
                  <?php $m = map_status_for_badge($item['status'] ?? 'pending'); ?>
                  <tr>
                    <td>
                      <div class="item-title fw-semibold"><?= esc($item['title']) ?></div>
                      <div class="item-preview text-muted">
                        <?= esc(mb_substr((string)($item['content'] ?? ''), 0, 150)) ?><?= mb_strlen((string)($item['content'] ?? '')) > 150 ? '…' : '' ?>
                      </div>
                    </td>
                    <td><span class="badge badge--category"><?= esc($item['category'] ?? 'News') ?></span></td>
                    <td>
                      <span class="badge badge--status badge--<?= esc($m['class']) ?>">
                        <?= esc($m['label']) ?>
                      </span>
                    </td>
                    <td>
                      <div class="date-text">
                        <?= !empty($item['submitted_at']) ? date('M j, Y', strtotime($item['submitted_at'])) : '-' ?>
                        <?php if (!empty($item['submitted_at'])): ?>
                          <div class="date-sub"><?= date('g:i A', strtotime($item['submitted_at'])) ?></div>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td class="actions-cell">
                      <div class="btn-group btn-group-sm" role="group" aria-label="Submission actions">
                        <button type="button"
                                class="btn btn-info"
                                title="View submission"
                                data-bs-toggle="modal"
                                data-bs-target="#viewNewsModal"
                                data-title="<?= esc($item['title']) ?>"
                                data-content="<?= esc($item['content']) ?>"
                                data-category="<?= esc($item['category']) ?>"
                                data-date="<?= !empty($item['submitted_at']) ? date('M j, Y g:i A', strtotime($item['submitted_at'])) : '-' ?>"
                                data-status="<?= esc($m['label']) ?>"
                                data-review="<?= esc($item['review_notes'] ?? '') ?>">
                          <i class="fas fa-eye"></i>
                        </button>
                        <button type="button"
                                class="btn btn-warning"
                                title="Edit submission"
                                data-bs-toggle="modal"
                                data-bs-target="#editNewsModal"
                                data-id="<?= (int)$item['id'] ?>"
                                data-title="<?= esc($item['title']) ?>"
                                data-content="<?= esc($item['content']) ?>"
                                data-category="<?= esc($item['category']) ?>">
                          <i class="fas fa-edit"></i>
                        </button>
                        <form method="post" class="d-inline" onsubmit="return confirm('Delete this news submission?')">
                          <input type="hidden" name="delete_news" value="1">
                          <input type="hidden" name="submission_id" value="<?= (int)$item['id'] ?>">
                          <button type="submit" class="btn btn-danger" title="Delete submission">
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

      <!-- Reviewed -->
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="fas fa-check-circle me-2"></i>Reviewed by Dean (<?= count($assessedNews) ?>)
          </h5>
        </div>
        <div class="card-body">
          <?php if (empty($assessedNews)): ?>
            <div class="empty-state-card">
              <i class="fas fa-newspaper fa-3x"></i>
              <h6>No reviewed submissions yet</h6>
              <div class="text-muted">Once the dean reviews your news, they will appear here.</div>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <!-- Reviewed Table -->
              <table class="table table-hover dashboard-table align-middle">
                <thead>
                  <tr>
                    <th style="width:45%;">Title &amp; Content</th>
                    <th style="width:10%;">Category</th>
                    <th style="width:10%;">Status</th>
                    <th style="width:15%;">Date</th>
                    <th style="width:20%;">Review Notes</th>
                    <th style="width:5%;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($assessedNews as $item): ?>
                  <?php $m = map_status_for_badge($item['status'] ?? 'approved'); ?>
                  <tr>
                    <td>
                      <div class="item-title fw-semibold"><?= esc($item['title']) ?></div>
                      <div class="item-preview text-muted">
                        <?= esc(mb_substr((string)($item['content'] ?? ''), 0, 150)) ?><?= mb_strlen((string)($item['content'] ?? '')) > 150 ? '…' : '' ?>
                      </div>
                    </td>
                    <td><span class="badge badge--category"><?= esc($item['category'] ?? 'News') ?></span></td>
                    <td>
                      <span class="badge badge--status badge--<?= esc($m['class']) ?>">
                        <?= esc($m['label']) ?>
                      </span>
                    </td>
                    <td>
                      <div class="date-text">
                        <?= !empty($item['submitted_at']) ? date('M j, Y', strtotime($item['submitted_at'])) : '-' ?>
                        <?php if (!empty($item['submitted_at'])): ?>
                          <div class="date-sub"><?= date('g:i A', strtotime($item['submitted_at'])) ?></div>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>
                      <?php if (!empty($item['review_notes'])): ?>
                        <div class="review-notes-full"><?= esc($item['review_notes']) ?></div>
                      <?php else: ?>
                        <span class="no-notes text-muted">No review notes</span>
                      <?php endif; ?>
                    </td>
                    <td class="actions-cell">
                      <button type="button"
                              class="btn btn-info btn-sm"
                              title="View submission"
                              data-bs-toggle="modal"
                              data-bs-target="#viewNewsModal"
                              data-title="<?= esc($item['title']) ?>"
                              data-content="<?= esc($item['content']) ?>"
                              data-category="<?= esc($item['category']) ?>"
                              data-date="<?= !empty($item['submitted_at']) ? date('M j, Y g:i A', strtotime($item['submitted_at'])) : '-' ?>"
                              data-status="<?= esc($m['label']) ?>"
                              data-review="<?= esc($item['review_notes'] ?? '') ?>">
                        <i class="fas fa-eye"></i>
                      </button>
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

<!-- Add News Modal (match dean modal sizing/structure) -->
<div class="modal fade" id="addNewsModal" tabindex="-1" aria-labelledby="addNewsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="?page=faculty_manage_news" enctype="multipart/form-data">
        <input type="hidden" name="add_news" value="1">
        <div class="modal-header">
          <h5 class="modal-title" id="addNewsModalLabel">
            <i class="fas fa-plus me-2"></i>Submit News Article
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-8">
              <div class="mb-3">
                <label for="title" class="form-label">Article Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required maxlength="255" placeholder="Enter article title">
                <small class="form-text text-muted">Maximum 255 characters</small>
              </div>
              <div class="mb-3">
                <label for="content" class="form-label">Article Content <span class="text-danger">*</span></label>
                <textarea class="form-control" id="content" name="content" rows="7" required minlength="10" placeholder="Write your article content here..."></textarea>
                <small class="form-text text-muted">Minimum 10 characters required</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                  <option value="news">General News</option>
                  <option value="research">Research</option>
                  <option value="achievement">Achievement</option>
                  <option value="student">Student News</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="image" class="form-label">Image <span class="text-muted">(optional)</span></label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="submitBtn">
            <i class="fas fa-paper-plane me-2"></i>Submit Article
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit News Modal -->
<div class="modal fade" id="editNewsModal" tabindex="-1" aria-labelledby="editNewsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="?page=faculty_manage_news" enctype="multipart/form-data">
        <input type="hidden" name="edit_news" value="1">
        <input type="hidden" name="submission_id" id="edit_news_submission_id" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="editNewsModalLabel">
            <i class="fas fa-edit me-2"></i>Edit News Article
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8">
              <div class="mb-3">
                <label for="edit_news_title" class="form-label">Article Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="edit_news_title" name="title" required maxlength="255" placeholder="Enter article title">
                <small class="form-text text-muted">Maximum 255 characters</small>
              </div>
              <div class="mb-3">
                <label for="edit_news_content" class="form-label">Article Content <span class="text-danger">*</span></label>
                <textarea class="form-control" id="edit_news_content" name="content" rows="7" required minlength="10" placeholder="Write your article content here..."></textarea>
                <small class="form-text text-muted">Minimum 10 characters required</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="edit_news_category" class="form-label">Category</label>
                <select class="form-select" id="edit_news_category" name="category" required>
                  <option value="news">General News</option>
                  <option value="research">Research</option>
                  <option value="achievement">Achievement</option>
                  <option value="student">Student News</option>
                </select>
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
<div class="modal fade" id="viewNewsModal" tabindex="-1" aria-labelledby="viewNewsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewNewsModalLabel">
          <i class="fas fa-eye me-2"></i>Submission Details
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="font-size:1.05rem; color:#222;">
        <div class="mb-3">
          <strong>Title:</strong>
          <div id="view_news_title" class="fw-semibold" style="font-size:1.15rem; color:#003169; margin-top:2px;"></div>
        </div>
        <div class="mb-3">
          <strong>Category:</strong>
          <span id="view_news_category" style="font-weight:500; color:#0080c9; margin-left:4px;"></span>
        </div>
        <div class="mb-3">
          <strong>Status:</strong>
          <span id="view_news_status" style="font-weight:500; color:#00713D; margin-left:4px;"></span>
        </div>
        <div class="mb-3">
          <strong>Date Submitted:</strong>
          <span id="view_news_date" style="margin-left:4px;"></span>
        </div>
        <div class="mb-3">
          <strong>Content:</strong>
          <div id="view_news_content" style="white-space:pre-line; background:#f8fafc; border-radius:8px; padding:12px; margin-top:4px; color:#222;"></div>
        </div>
        <div class="mb-3" id="view_news_review_notes_wrap" style="display:none;">
          <strong>Review Notes:</strong>
          <div id="view_news_review_notes" style="background:#fffbe6; border-radius:8px; padding:10px; margin-top:4px; color:#92400e;"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('#addNewsModal form');
  const titleInput = document.querySelector('#title');
  const contentInput = document.querySelector('#content');
  const submitBtn = document.querySelector('#submitBtn');

  if (form && titleInput && contentInput && submitBtn) {
    form.addEventListener('submit', function(e) {
      const title = (titleInput.value || '').trim();
      const content = (contentInput.value || '').trim();

      if (!title) {
        e.preventDefault();
        alert('Please enter an article title');
        titleInput.focus();
        return false;
      }

      if (content.length < 10) {
        e.preventDefault();
        alert('Please enter article content (minimum 10 characters)');
        contentInput.focus();
        return false;
      }

      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
    });
  }

  // Edit modal population
  var editModal = document.getElementById('editNewsModal');
  if (editModal) {
    editModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      document.getElementById('edit_news_submission_id').value = button.getAttribute('data-id') || '';
      document.getElementById('edit_news_title').value = button.getAttribute('data-title') || '';
      document.getElementById('edit_news_content').value = button.getAttribute('data-content') || '';
      document.getElementById('edit_news_category').value = button.getAttribute('data-category') || 'news';
    });
  }

  // View modal population
  var viewModal = document.getElementById('viewNewsModal');
  if (viewModal) {
    viewModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      document.getElementById('view_news_title').textContent = button.getAttribute('data-title') || '';
      document.getElementById('view_news_category').textContent = button.getAttribute('data-category') || '';
      document.getElementById('view_news_status').textContent = button.getAttribute('data-status') || '';
      document.getElementById('view_news_date').textContent = button.getAttribute('data-date') || '';
      document.getElementById('view_news_content').textContent = button.getAttribute('data-content') || '';
      var reviewNotes = button.getAttribute('data-review') || '';
      if (reviewNotes) {
        document.getElementById('view_news_review_notes').textContent = reviewNotes;
        document.getElementById('view_news_review_notes_wrap').style.display = '';
      } else {
        document.getElementById('view_news_review_notes').textContent = '';
        document.getElementById('view_news_review_notes_wrap').style.display = 'none';
      }
    });
  }
});
</script>
</body>
</html>
