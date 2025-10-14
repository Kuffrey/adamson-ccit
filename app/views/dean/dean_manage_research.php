<?php
// app/views/dean/dean_manage_research.php - Dean Research Management
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../lib/Auth.php';

// Check authentication using Auth class
if (!Auth::check() || !Auth::is('dean')) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

require_once __DIR__ . '/../../models/FacultyResearchPageSettings.php';
require_once __DIR__ . '/../../models/FacultyResearch.php';
require_once __DIR__ . '/../../models/DeanLogs.php';
require_once __DIR__ . '/../../models/FacultySubmissions.php';

if (!function_exists('esc')) {
    function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = Auth::user();
$username = $user['username'] ?? 'Dean';
$notice = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (isset($_POST['settings'])) {
      FacultyResearchPageSettings::updateSettings($_POST['settings']);
      $notice = 'Page settings saved successfully!';
    } elseif (isset($_POST['add_research'])) {
      $researchId = FacultyResearch::create($_POST['research'] ?? []);
      if ($researchId) {
        DeanLogs::logCreate('faculty_research', $researchId, $user['id'] ?? null, "Created research: " . ($_POST['research']['title'] ?? 'Unknown'));
      }
      $notice = 'Research added successfully!';
    } elseif (isset($_POST['edit_research'])) {
      $id = (int)$_POST['id'];
      FacultyResearch::update($id, $_POST['research'] ?? []);
      DeanLogs::logUpdate('faculty_research', $id, $user['id'] ?? null, "Updated research: " . ($_POST['research']['title'] ?? 'Unknown'));
      $notice = 'Research updated successfully!';
    } elseif (isset($_POST['delete_research'])) {
      $id = (int)$_POST['id'];
      
      // Get research title before deletion for logging
      $research = FacultyResearch::getById($id);
      $researchTitle = $research['title'] ?? "Research ID {$id}";
      
      FacultyResearch::delete($id);
      DeanLogs::logDelete($user['id'] ?? null, 'faculty_research', $id, "Deleted research: {$researchTitle}");
      $notice = 'Research deleted successfully!';
    } elseif (isset($_POST['delete_submission'], $_POST['submission_id'])) {
      $submissionId = (int)$_POST['submission_id'];
      $deleted = FacultySubmissions::delete($submissionId);
      $notice = $deleted ? 'Research submission deleted successfully!' : 'Error: Failed to delete submission.';
    } elseif (isset($_POST['edit_submission'], $_POST['submission_id'])) {
      $submissionId = (int)$_POST['submission_id'];
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

      $notice = $result ? 'Research submission updated successfully!' : 'Error: Failed to update submission.';
    } elseif (isset($_POST['faculty_action'])) {
      $submissionId = (int)$_POST['submission_id'];
      $action = $_POST['faculty_action'];
      $reviewNotes = $_POST['review_notes'] ?? '';

      if ($action === 'approve') {
        // Update status to approved in faculty_submissions
        FacultySubmissions::updateStatus($submissionId, 'approved', $user['id'] ?? null, $reviewNotes);
        
        // Publish to faculty_research table
        $submission = FacultySubmissions::getById($submissionId);
        if ($submission && $submission['submission_type'] === 'research') {
          $rd = json_decode($submission['content'] ?? '{}', true) ?: [];
          $data = [
            'title'      => $submission['title'] ?? '',
            'authors'    => $rd['authors'] ?? '',
            'doi'        => $rd['doi'] ?? '',
            'publisher'  => $rd['publisher'] ?? '',
            'conference' => $rd['conference'] ?? '',
            'year'       => $rd['year'] ?? '',
            'view_url'   => $rd['view_url'] ?? ''
          ];
          require_once __DIR__ . '/../../models/FacultyResearch.php';
          FacultyResearch::create($data);
        }
        
        $notice = 'Research approved and published successfully!';
      } elseif ($action === 'reject') {
        FacultySubmissions::updateStatus($submissionId, 'rejected', $user['id'] ?? null, $reviewNotes);
        $notice = 'Research rejected successfully!';
      }
    }
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

// Get current data
try {
    $status = $_GET['status'] ?? 'all';

    // Show published research from faculty_research table (approved by dean)
    require_once __DIR__ . '/../../models/FacultyResearch.php';
    $allResearch = FacultyResearch::getAll();
    if ($status === 'all' || $status === 'published') {
        $research = $allResearch;
    } else {
        $research = array_filter($allResearch, function($r) use ($status) {
            return isset($r['status']) && $r['status'] === $status;
        });
    }

    $counts = [
        'all' => count($allResearch),
        'published' => count($allResearch),
        'draft' => 0,
        'archived' => 0
    ];

    $pendingResearchSubmissions = FacultySubmissions::getPendingByType('research');
} catch (Exception $e) {
    $notice = 'Error loading data: ' . $e->getMessage();
    $research = [];
    $counts = ['all' => 0, 'draft' => 0, 'published' => 0, 'archived' => 0];
    $pendingResearchSubmissions = [];
}
?>

<?php
// Collect modal markup and print once at end of <body> so modals stay clickable
$__researchCollectedModals = '';

// Safe esc() helper in case not already defined
if (!function_exists('esc')) {
    function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Research | CCIT Dean</title>
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/dean.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/_dean_sidebar.php'; ?>
        <main class="admin-main">
            <header class="admin-topbar">
                <button class="topbar__btn hide-desktop" type="button" aria-label="Open navigation menu" data-sb-open>
                    <i class="fas fa-bars"></i>
                </button>
                <span class="admin-topbar__title">CCIT Research Management</span>
                <span class="admin-topbar__spacer"></span>
            </header>
            <section class="admin-section">

                <!-- Enhanced message handling -->
                <?php if ($notice): ?>
                    <div class="alert <?= str_starts_with($notice, 'Error') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show modern-alert" role="alert">
                        <i class="fas <?= str_starts_with($notice, 'Error') ? 'fa-exclamation-circle' : 'fa-check-circle' ?>"></i>
                        <?= esc($notice) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Filter Tabs -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link <?= $status === 'all' ? 'active' : '' ?>" href="?page=dean_manage_research&status=all">
                                        All Research (<?= $counts['all'] ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $status === 'published' ? 'active' : '' ?>" href="?page=dean_manage_research&status=published">
                                        Published (<?= $counts['published'] ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $status === 'draft' ? 'active' : '' ?>" href="?page=dean_manage_research&status=draft">
                                        Draft (<?= $counts['draft'] ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $status === 'archived' ? 'active' : '' ?>" href="?page=dean_manage_research&status=archived">
                                        Archived (<?= $counts['archived'] ?>)
                                    </a>
                                </li>
                            </ul>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResearchModal">
                                <i class="fas fa-plus me-2"></i>Add Research
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Faculty Research Submissions for Approval -->
                <?php if (!empty($pendingResearchSubmissions)): ?>
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock text-warning me-2"></i>
                            Pending Faculty Research Submissions (<?= count($pendingResearchSubmissions) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Faculty</th>
                                        <th>Research Title</th>
                                        <th>Type</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingResearchSubmissions as $submission): ?>
                                        <?php
                                            $sid   = (int)$submission['id'];
                                            $sfac  = esc($submission['faculty_name'] ?? 'Unknown Faculty');
                                            $sdept = esc($submission['department_name'] ?? '');
                                            $stitle= esc($submission['title'] ?? '');
                                            $stype = esc($submission['type'] ?? 'Research');
                                            $ssub  = !empty($submission['submitted_at']) ? date('M j, Y g:i A', strtotime($submission['submitted_at'])) : '';
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?= $sfac ?></strong>
                                                <br><small class="text-muted"><?= $sdept ?></small>
                                            </td>
                                            <td><strong><?= $stitle ?></strong></td>
                                            <td><span class="badge badge--category"><?= $stype ?></span></td>
                                            <td><small><?= esc($ssub) ?></small></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-success btn-sm" 
                                                            data-bs-toggle="modal" data-bs-target="#approveResearchModal<?= $sid ?>">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            data-bs-toggle="modal" data-bs-target="#rejectResearchModal<?= $sid ?>">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php ob_start(); ?>
                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveResearchModal<?= $sid ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Approve Research Submission</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="faculty_action" value="approve">
                                                            <input type="hidden" name="submission_id" value="<?= $sid ?>">
                                                            <p>Approve "<strong><?= $stitle ?></strong>" by <?= $sfac ?>?</p>
                                                            <p class="text-muted">This will publish the research immediately.</p>
                                                            <div class="mb-3">
                                                                <label class="form-label">Review Notes (Optional)</label>
                                                                <textarea class="form-control" name="review_notes" rows="3" placeholder="Add any feedback or notes..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success">Approve &amp; Publish</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectResearchModal<?= $sid ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Research Submission</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="faculty_action" value="reject">
                                                            <input type="hidden" name="submission_id" value="<?= $sid ?>">
                                                            <p>Reject "<strong><?= $stitle ?></strong>" by <?= $sfac ?>?</p>
                                                            <div class="mb-3">
                                                                <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" name="review_notes" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Reject</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $__researchCollectedModals .= ob_get_clean(); ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Published Research (remove the Research Publications section) -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-microscope me-2"></i>Published Research (<?= count($research) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($research)): ?>
                            <div class="empty-state-card">
                                <i class="fas fa-microscope fa-3x"></i>
                                <h6>No Published Research Found</h6>
                                <div class="text-muted">Research will appear here after dean approval.</div>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
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
                                        <?php foreach ($research as $r): ?>
                                            <?php
                                                $rid    = (int)$r['id'];
                                                $rtitle = esc($r['title'] ?? '');
                                                $rauth  = esc($r['authors'] ?? '');
                                                $rdoi   = esc($r['doi'] ?? '');
                                                $rpub   = esc($r['publisher'] ?? '');
                                                $rconf  = esc($r['conference'] ?? '');
                                                $ryear  = esc($r['year'] ?? '');
                                                $rview  = esc($r['view_url'] ?? '');
                                            ?>
                                            <tr>
                                                <td><?= $rtitle ?></td>
                                                <td><?= $rauth ?></td>
                                                <td><?= $rdoi ?></td>
                                                <td><?= $rpub ?></td>
                                                <td><?= $rconf ?></td>
                                                <td><?= $ryear ?></td>
                                                <td>
                                                    <?php if (!empty($rview)): ?>
                                                        <a href="<?= $rview ?>" target="_blank" rel="noopener">View</a>
                                                    <?php else: ?>
                                                        <span class="no-notes text-muted">No link</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <?php if (!empty($rview)): ?>
                                                            <a href="<?= $rview ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="View Publication">
                                                                <i class="fas fa-external-link-alt"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this research?');">
                                                            <input type="hidden" name="delete_research" value="1">
                                                            <input type="hidden" name="id" value="<?= $rid ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger">
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

    <!-- =========================
         ALL MODALS RENDER HERE
         ========================= -->

    <!-- Add Research Modal (moved here unchanged) -->
    <div class="modal fade" id="addResearchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="add_research" value="1">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Research</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="research[title]" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <select class="form-control" name="research[dept]" required>
                                        <option value="">Select Department</option>
                                        <option value="itis">IT&amp;IS</option>
                                        <option value="cs">CS</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Type</label>
                                    <select class="form-control" name="research[type]" required>
                                        <option value="">Select Type</option>
                                        <option value="journal">Journal Article</option>
                                        <option value="conference">Conference Paper</option>
                                        <option value="chapter">Book Chapter</option>
                                        <option value="patent">Patent</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Year</label>
                                    <input type="text" class="form-control" name="research[year]" required placeholder="e.g., 2024">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Authors</label>
                            <textarea class="form-control" name="research[authors]" rows="2" required placeholder="e.g., John Doe, Jane Smith"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Venue</label>
                            <input type="text" class="form-control" name="research[venue]" placeholder="e.g., IEEE Transactions on Computers" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">PDF URL</label>
                                    <input type="url" class="form-control" name="research[pdf_url]" placeholder="Link to PDF">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">View URL</label>
                                    <input type="url" class="form-control" name="research[view_url]" placeholder="Link to publication">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Image URL</label>
                                    <input type="url" class="form-control" name="research[image_url]" placeholder="Link to research poster/image">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" name="research[status]">
                                        <option value="draft">Draft</option>
                                        <option value="published" selected>Published</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Research
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Collected Approve/Reject/Edit modals -->
    <?= $__researchCollectedModals ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
