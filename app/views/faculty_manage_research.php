<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'faculty') {
    header('Location: ?page=login');
    exit;
}
require_once __DIR__ . '/../models/Research.php';

// Check if user is logged in and get faculty ID
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug: Let's see what's in the session
error_log("Session debug: " . print_r($_SESSION, true));

$faculty_id = $_SESSION['user']['id'] ?? null;
if (!$faculty_id) {
    error_log("Faculty ID not found in session. Redirecting to login.");
    header('Location: ?page=login');
    exit;
}

error_log("Faculty ID found: " . $faculty_id);
$dept_id = $_SESSION['user']['department_id'] ?? 0;
$researchModel = new Research();

if (isset($_POST['add_research'])) {
    // Research is created as 'draft' and requires dean approval
    $researchModel->create([
        'title' => $_POST['title'],
        'abstract' => $_POST['description'],
        'owner_user_id' => $faculty_id,
        'department_id' => $dept_id,
        'status' => 'draft',
        'requires_dean_approval' => 1, // flag for dean approval
    ]);
    header('Location: ?page=faculty_manage_research');
    exit;
}

if (isset($_POST['submit_for_review']) && isset($_POST['research_id'])) {
  $researchId = (int)$_POST['research_id'];
  // Only allow submission if owned by this faculty and status is draft
  $myResearch = $researchModel->listByOwner($faculty_id);
  $target = null;
  foreach ($myResearch as $r) {
    if ($r['id'] == $researchId && $r['status'] === 'draft') {
      $target = $r;
      break;
    }
  }
  if ($target) {
    // Set status to 'review' so dean can approve/reject
    $researchModel->submitForReview($researchId); // This should update status to 'review'

    // Create a faculty_submissions record for dean approval
    require_once __DIR__ . '/../models/FacultySubmissions.php';
    FacultySubmissions::create([
      'faculty_id' => $faculty_id,
      'submission_type' => 'research',
      'title' => $target['title'],
      'description' => $target['abstract'],
      'content' => '',
      'category' => null,
      'status' => 'submitted',
    ]);
  }
  header('Location: ?page=faculty_manage_research');
  exit;
}
if (isset($_GET['delete'])) {
    // Only allow delete if owned by this faculty and status is draft or review
    $myResearch = $researchModel->listByOwner($faculty_id);
    $target = null;
    foreach ($myResearch as $r) {
        if ($r['id'] == (int)$_GET['delete']) $target = $r;
    }
    if ($target && in_array($target['status'], ['draft','review'])) {
        // You need to implement a delete method in Research model if not present
        if (method_exists($researchModel, 'delete')) {
            $researchModel->delete((int)$_GET['delete']);
        }
    }
    header('Location: ?page=faculty_manage_research');
    exit;
}
$research = $researchModel->listByOwner($faculty_id);

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Submit Research | Faculty Dashboard</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f6f8fb;
    }
    .faculty-section-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: #0b234c;
      margin-bottom: 1.2rem;
      letter-spacing: .01em;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .faculty-section-title i {
      color: #008040;
      font-size: 1.1rem;
    }
    .faculty-actions-bar {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      margin-bottom: 1.2rem;
      gap: 0.5rem;
    }
    .faculty-table-card {
      background: #fff;
      border-radius: 12px;
      border: 1px solid #e3e6f0;
      padding: 1.3rem 1.2rem;
      box-shadow: none;
      margin-bottom: 2rem;
    }
    .faculty-table-card-header {
      font-size: 1.07rem;
      font-weight: 600;
      color: #008040;
      margin-bottom: 0.7rem;
      display: flex;
      align-items: center;
      gap: 0.4rem;
      letter-spacing: .01em;
    }
    .faculty-table-card-header i {
      color: #008040;
      font-size: 1rem;
    }
    .table-responsive {
      margin-top: 0.7rem;
    }
    .table {
      background: #fff;
      border-radius: 8px;
      border: 1px solid #e3e6f0;
      font-size: 0.98rem;
      margin-bottom: 0;
    }
    .table thead {
      background: #f8fafc;
      color: #0b234c;
      font-weight: 700;
      border-bottom: 1px solid #e3e6f0;
    }
    .table th, .table td {
      vertical-align: middle;
      padding: 12px 10px;
      border-top: none;
    }
    .table-striped > tbody > tr:nth-of-type(odd) {
      background: #f8fafc;
    }
    .status-badge {
      display: inline-block;
      padding: 3px 12px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      margin-left: 0.5rem;
      letter-spacing: .02em;
      border: none;
      background: #f3f4f6;
      color: #374151;
    }
    .status-draft    { background: #f3f4f6; color: #374151; }
    .status-review   { background: #fffbe6; color: #b45309; }
    .status-approved { background: #e8f5ee; color: #008040; }
    .status-rejected { background: #fee2e2; color: #dc2626; }
    .admin-notice {
      margin: 1rem 0;
      padding: 0.7rem 1.1rem;
      border-radius: 7px;
      background: #f8fafc;
      color: #008040;
      font-weight: 500;
      border: 1px solid #e3e6f0;
      font-size: 0.98rem;
    }
    .btn--primary, .btn.btn--primary {
      background: #008040;
      color: #fff;
      border: none;
      border-radius: 7px;
      font-weight: 600;
      padding: 0.45rem 1.1rem;
      font-size: 0.97rem;
      transition: background 0.18s;
      box-shadow: none;
    }
    .btn--primary:hover, .btn.btn--primary:hover {
      background: #0b234c;
      color: #fff;
    }
    .btn--danger, .btn.btn--danger {
      background: #dc2626;
      color: #fff;
      border: none;
      border-radius: 7px;
      font-weight: 600;
      padding: 0.45rem 1.1rem;
      font-size: 0.97rem;
      transition: background 0.18s;
      box-shadow: none;
    }
    .btn--danger:hover, .btn.btn--danger:hover {
      background: #b91c1c;
      color: #fff;
    }
    .btn--small {
      font-size: 0.93rem;
      padding: 0.32rem 0.8rem;
      border-radius: 7px;
    }
    .btn--primary i, .btn--danger i, .btn.btn--primary i, .btn.btn--danger i {
      color: #fff !important;
    }
    .btn-outline-secondary i {
      color: #475569 !important;
    }
    @media (max-width: 900px) {
      .faculty-table-card {
        padding: 0.7rem;
      }
      .faculty-section-title {
        font-size: 1.08rem;
      }
      .table th, .table td {
        padding: 8px 6px;
      }
    }
  </style>
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Submit Research</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($_SESSION['user']['username'][0] ?? 'F')) ?></span>
        <span class="admin-topbar__name"><?= esc($_SESSION['user']['username'] ?? 'Faculty') ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <div class="faculty-actions-bar">
        <button type="button" class="btn btn--primary" data-bs-toggle="modal" data-bs-target="#addResearchModal">
          <i class="fas fa-plus"></i> Add New Research
        </button>
      </div>
      <div class="faculty-table-card">
        <div class="faculty-table-card-header">
          <i class="fas fa-list"></i> Your Research
        </div>
        <?php if (empty($research)): ?>
          <div class="admin-notice admin-notice--info">
            <i class="fas fa-info-circle"></i> No research entries found. Add your first research above.
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Status</th>
                  <th>Description</th>
                  <th style="width: 120px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($research as $item): ?>
                  <tr>
                    <td>
                      <strong><?= esc($item['title']) ?></strong>
                    </td>
                    <td>
                      <span class="status-badge status-<?= esc($item['status']) ?>">
                        <?= esc(ucfirst($item['status'])) ?>
                      </span>
                    </td>
                    <td>
                      <span style="color: #64748b;"><?= nl2br(esc($item['abstract'])) ?></span>
                    </td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center align-items-center gap-2">
                        <?php if ($item['status'] === 'draft'): ?>
                          <form method="post" style="display:inline;">
                            <input type="hidden" name="research_id" value="<?= $item['id'] ?>">
                            <button type="submit"
                                    name="submit_for_review"
                                    class="btn btn-sm btn--primary"
                                    title="Submit for Review"
                                    aria-label="Submit for Review"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    data-bs-title="Submit for Review"
                                    onclick="return confirm('Submit this research for dean approval?')">
                              <i class="fas fa-paper-plane"></i> Submit
                            </button>
                          </form>
                        <?php endif; ?>
                        <?php if (in_array($item['status'], ['draft','review'])): ?>
                          <a href="?page=faculty_manage_research&delete=<?= $item['id'] ?>"
                             class="btn btn-sm btn--danger"
                             title="Delete Research"
                             aria-label="Delete Research"
                             data-bs-toggle="tooltip"
                             data-bs-placement="top"
                             data-bs-title="Delete Research"
                             onclick="return confirm('Delete this research?')">
                            <i class="fas fa-trash"></i> Delete
                          </a>
                        <?php endif; ?>
                      </div>
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
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post" action="?page=faculty_manage_research">
              <div class="modal-header">
                <h5 class="modal-title" id="addResearchModalLabel">
                  <i class="fas fa-plus"></i> Add New Research
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label for="title" class="form-label">Research Title</label>
                  <input type="text" id="title" name="title" class="form-control" placeholder="Enter research title" required>
                </div>
                <div class="mb-3">
                  <label for="description" class="form-label">Research Description/Abstract</label>
                  <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter research description or abstract" required></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                  <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" name="add_research" class="btn btn--primary">
                  <i class="fas fa-plus"></i> Add Research
                </button>
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
  tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>
</body>
</html>
</body>
</html>
