<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'faculty') {
  header('Location: ?page=login'); exit;
}
require_once __DIR__ . '/../models/FacultySubmissions.php';

$faculty_id = $_SESSION['user']['id'] ?? null;
$faculty_username = $_SESSION['user']['username'] ?? 'Faculty';
if (!$faculty_id) { header('Location: ?page=login'); exit; }

// Get faculty info
try {
  $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE username = ? LIMIT 1");
  $stmt->execute([$faculty_username]);
  $faculty = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($faculty) {
    $firstName = $faculty['first_name'];
    $lastName  = $faculty['last_name'];
    $fullName  = trim($firstName . ' ' . $lastName);
  } else {
    $firstName = "Faculty"; $lastName = ""; $fullName = $faculty_username;
  }
} catch (PDOException $e) {
  $firstName = "Faculty"; $lastName = ""; $fullName = $faculty_username;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (isset($_POST['add_research'])) {
      $submissionData = [
        'faculty_id'      => $faculty_id,
        'submission_type' => 'research',
        'title'           => $_POST['title'] ?? '',
        'description'     => $_POST['description'] ?? '',
        'category'        => $_POST['type'] ?? 'journal',
        'content'         => json_encode([
          'dept'       => $_POST['dept'] ?? '',
          'type'       => $_POST['type'] ?? '',
          'year'       => $_POST['year'] ?? '',
          'authors'    => $_POST['authors'] ?? '',
          'venue'      => $_POST['venue'] ?? '',
          'pdf_url'    => $_POST['pdf_url'] ?? '',
          'view_url'   => $_POST['view_url'] ?? '',
          'image_url'  => $_POST['image_url'] ?? '',
          'description'=> $_POST['description'] ?? '',
        ], JSON_UNESCAPED_SLASHES),
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
      $submission = FacultySubmissions::getById($submissionId);
      if ($submission && $submission['faculty_id'] == $faculty_id && !in_array($submission['status'], ['approved', 'published'])) {
        $deleted = FacultySubmissions::delete($submissionId);
        $success_message = $deleted ? 'Research submission deleted successfully!' : 'Error: Failed to delete submission.';
      } else {
        $error_message = 'Error: Cannot delete this research submission.';
      }
    }
  } catch (Exception $e) {
    $error_message = 'Error: ' . $e->getMessage();
    error_log("Research submission error: " . $e->getMessage());
  }
}

$research = FacultySubmissions::getByFacultyAndType($faculty_id, 'research');
function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Research Submission | Faculty Dashboard</title>

  <!-- New shared faculty stylesheet -->
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/faculty.css" />

  <!-- Vendors -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Research Submission</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper(($firstName[0] ?? 'F') . ($lastName[0] ?? ''))) ?></span>
        <span class="admin-topbar__name"><?= esc($fullName) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <div class="page-header">
        <h1 class="page-title"><i class="fas fa-microscope"></i> Research Management</h1>
        <button type="button" class="btn-primary" data-bs-toggle="modal" data-bs-target="#addResearchModal">
          <i class="fas fa-plus"></i> Submit New Research
        </button>
      </div>

      <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= esc($success_message) ?></div>
      <?php endif; ?>
      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= esc($error_message) ?></div>
      <?php endif; ?>

      <div class="content-card">
        <div class="card-header">
          <h2 class="card-title"><i class="fas fa-list"></i> Your Research Submissions</h2>
        </div>

        <?php if (empty($research)): ?>
          <div class="empty-state">
            <i class="fas fa-microscope"></i>
            <h4>No research submissions yet</h4>
            <p>Get started by submitting your first research publication above.</p>
          </div>
        <?php else: ?>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Research Details</th>
                  <th>Type & Department</th>
                  <th>Publication</th>
                  <th>Status</th>
                  <th>Year</th>
                  <th>Links</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($research as $item): ?>
                <?php $rd = json_decode($item['content'] ?? '{}', true) ?: []; ?>
                <tr>
                  <td>
                    <div class="item-title"><?= esc($item['title']) ?></div>
                    <?php if (!empty($rd['authors'])): ?>
                      <div class="item-subtitle">Authors: <?= esc($rd['authors']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($rd['description'])): ?>
                      <div class="item-subtitle" style="margin-top:.25rem;">
                        <?= esc(mb_substr($rd['description'], 0, 100)) ?><?= (mb_strlen($rd['description']) > 100 ? '…' : '') ?>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="type-badge"><?= esc(ucfirst($rd['type'] ?? 'journal')) ?></div>
                    <?php if (!empty($rd['dept'])): ?>
                      <div class="item-subtitle" style="margin-top:.25rem;"><?= esc(strtoupper($rd['dept'])) ?></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="item-subtitle">
                      <?php if (!empty($rd['venue'])): ?>
                        <strong><?= esc($rd['venue']) ?></strong>
                      <?php else: ?>
                        <span class="no-notes">No venue specified</span>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td>
                    <span class="status-badge status-<?= esc($item['status']) ?>">
                      <?= esc(ucfirst($item['status'])) ?>
                    </span>
                    <?php if ($item['status'] === 'rejected' && !empty($item['review_notes'])): ?>
                      <div class="item-subtitle" style="margin-top:.25rem; color:#dc2626;">
                        <i class="fas fa-exclamation-triangle"></i> <?= esc($item['review_notes']) ?>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td><div class="date-text"><?= esc($rd['year'] ?? '-') ?></div></td>
                  <td>
                    <div class="item-subtitle">
                      <?php if (!empty($rd['pdf_url'])): ?>
                        <a class="btn-link-sm btn-pdf" href="<?= esc($rd['pdf_url']) ?>" target="_blank"><i class="fas fa-file-pdf"></i> PDF</a>
                      <?php endif; ?>
                      <?php if (!empty($rd['view_url'])): ?>
                        <a class="btn-link-sm btn-view" href="<?= esc($rd['view_url']) ?>" target="_blank"><i class="fas fa-external-link-alt"></i> View</a>
                      <?php endif; ?>
                      <?php if (empty($rd['pdf_url']) && empty($rd['view_url'])): ?>
                        <span class="no-notes">No links</span>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td>
                    <?php if (in_array($item['status'], ['submitted', 'rejected'])): ?>
                      <form method="post" style="display:inline;">
                        <input type="hidden" name="delete_submission" value="1">
                        <input type="hidden" name="submission_id" value="<?= (int)$item['id'] ?>">
                        <button type="submit" class="btn-danger-sm" title="Delete this research submission"
                                onclick="return confirm('Delete this research submission?')">
                          <i class="fas fa-trash"></i> Delete Submission
                        </button>
                      </form>
                    <?php elseif ($item['status'] === 'approved'): ?>
                      <span class="status-badge status-approved"><i class="fas fa-check"></i> Published</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

      <!-- Add Research Modal -->
      <div class="modal fade" id="addResearchModal" tabindex="-1" aria-labelledby="addResearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <form method="post" action="?page=faculty_manage_research">
              <div class="modal-header">
                <h5 class="modal-title" id="addResearchModalLabel"><i class="fas fa-plus"></i> Submit New Research</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="submission-note"><i class="fas fa-info-circle"></i> Your research will be submitted to the dean for review and approval.</div>
                <div class="form-grid">
                  <div class="form-group">
                    <label for="title" class="form-label">Research Title</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="Enter research title" required>
                  </div>
                  <div class="form-group">
                    <label for="dept" class="form-label">Department</label>
                    <select id="dept" name="dept" class="form-select" required>
                      <option value="">Select Department</option>
                      <option value="itis">IT&IS</option>
                      <option value="cs">CS</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="type" class="form-label">Research Type</label>
                    <select id="type" name="type" class="form-select" required>
                      <option value="">Select Type</option>
                      <option value="journal">Journal Article</option>
                      <option value="conference">Conference Paper</option>
                      <option value="chapter">Book Chapter</option>
                      <option value="patent">Patent</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="year" class="form-label">Year</label>
                    <input type="text" id="year" name="year" class="form-control" placeholder="e.g., 2024" required>
                  </div>
                  <div class="form-group">
                    <label for="authors" class="form-label">Authors</label>
                    <textarea id="authors" name="authors" class="form-control" rows="2" placeholder="e.g., John Doe, Jane Smith" required></textarea>
                  </div>
                  <div class="form-group">
                    <label for="venue" class="form-label">Venue</label>
                    <input type="text" id="venue" name="venue" class="form-control" placeholder="e.g., IEEE Transactions on Computers" required>
                  </div>
                  <div class="form-group">
                    <label for="pdf_url" class="form-label">PDF URL <span class="optional">(Optional)</span></label>
                    <input type="url" id="pdf_url" name="pdf_url" class="form-control" placeholder="Link to PDF">
                  </div>
                  <div class="form-group">
                    <label for="view_url" class="form-label">View URL <span class="optional">(Optional)</span></label>
                    <input type="url" id="view_url" name="view_url" class="form-control" placeholder="Link to publication">
                  </div>
                  <div class="form-group">
                    <label for="image_url" class="form-label">Image URL <span class="optional">(Optional)</span></label>
                    <input type="url" id="image_url" name="image_url" class="form-control" placeholder="Link to research poster/image">
                  </div>
                  <div class="form-group">
                    <label for="description" class="form-label">Research Description/Abstract</label>
                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter research description or abstract" required></textarea>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
                <button type="submit" name="add_research" class="btn-primary"><i class="fas fa-paper-plane"></i> Submit for Approval</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(function (el) { new bootstrap.Tooltip(el); });
});
</script>
</body>
</html>
