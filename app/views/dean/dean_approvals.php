<?php
// app/views/dean/dean_approvals.php - Dean Approvals Dashboard
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../lib/Auth.php';

// Check authentication using Auth class
if (!Auth::check() || !Auth::is('dean')) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

require_once __DIR__ . '/../../models/FacultySubmissions.php';
require_once __DIR__ . '/../../models/DeanLogs.php';

// Helper function for escaping
if (!function_exists('esc')) {
    function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = Auth::user();
$username = $user['username'] ?? 'Dean';
$notice = '';

// Handle approval/rejection actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action']) && !empty($_POST['submission_id'])) {
    try {
        $submissionId = (int)$_POST['submission_id'];
        $action = $_POST['action'];
        $reviewNotes = trim($_POST['review_notes'] ?? '');

        // Fetch submission for content
        $submission = FacultySubmissions::getById($submissionId);

        if ($action === 'approve') {
            FacultySubmissions::updateStatus($submissionId, 'approved', $user['id'] ?? null, $reviewNotes);

            // Publish research to faculty_research table
            if ($submission && $submission['submission_type'] === 'research') {
                require_once __DIR__ . '/../../models/FacultyResearch.php';
                $rd = json_decode($submission['content'] ?? '{}', true) ?: [];
                $data = [
                    'title'      => $submission['title'] ?? '',
                    'authors'    => $rd['authors'] ?? '',
                    'doi'        => $rd['doi'] ?? '',
                    'publisher'  => $rd['publisher'] ?? '',
                    'conference' => $rd['conference'] ?? '',
                    'year'       => $rd['year'] ?? '',
                    'view_url'   => $rd['view_url'] ?? '',
                    'status'     => 'published' // Ensure status is set to 'published'
                ];
                FacultyResearch::create($data); // Insert into faculty_research table
            }

            $notice = 'Submission approved successfully!';
            DeanLogs::logApprove(
                'faculty_submissions',
                $submissionId,
                $user['id'] ?? null,
                'Submission approved' . ($reviewNotes ? ': ' . substr($reviewNotes, 0, 100) : '')
            );

        } elseif ($action === 'reject') {
            FacultySubmissions::updateStatus($submissionId, 'rejected', $user['id'] ?? null, $reviewNotes);
            $notice = 'Submission rejected successfully!';
            DeanLogs::logReject(
                'faculty_submissions',
                $submissionId,
                $user['id'] ?? null,
                'Submission rejected' . ($reviewNotes ? ': ' . substr($reviewNotes, 0, 100) : '')
            );
        }
    } catch (Exception $e) {
        $notice = 'Error processing action: ' . $e->getMessage();
    }
}

// Get pending submissions for dean approval
try {
    $pendingSubmissions = FacultySubmissions::getPendingByReviewer();
    // Filter research, news, and certifications submissions only
    $pendingSubmissions = array_filter($pendingSubmissions, function($submission) {
        return in_array($submission['submission_type'], ['research', 'news', 'certification']);
    });
    
    $submissionCounts = FacultySubmissions::getPendingCountsByType();
} catch (Exception $e) {
    $pendingSubmissions = [];
    $submissionCounts = ['research' => 0, 'news' => 0];
    $notice = 'Error loading submissions: ' . $e->getMessage();
}

// Group submissions by type for better organization
$groupedSubmissions = [
    'research' => [],
    'news' => [],
    'certification' => []
];

foreach ($pendingSubmissions as $submission) {
    if (isset($groupedSubmissions[$submission['submission_type']])) {
        $groupedSubmissions[$submission['submission_type']][] = $submission;
    }
}

// Get filter from URL
$filter = $_GET['filter'] ?? 'all';
$totalPending = count($pendingSubmissions);

// Create tables for submissions and logs
try {
    $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS faculty_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            faculty_id INT NOT NULL,
            submission_type VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            content TEXT,
            category VARCHAR(100),
            status VARCHAR(30) DEFAULT 'submitted',
            review_notes TEXT,
            submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS dean_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            table_name VARCHAR(100) NOT NULL,
            record_id INT NOT NULL,
            dean_id INT NOT NULL,
            action VARCHAR(30) NOT NULL,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
} catch (PDOException $e) {
    // Handle migration error (optional: log or display)
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approvals | CCIT Dean</title>
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
                <span class="admin-topbar__title">Pending Approvals</span>
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
                                    <a class="nav-link <?= $filter === 'all' ? 'active' : '' ?>" href="?page=dean_approvals&filter=all">
                                        All Pending (<?= $totalPending ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $filter === 'research' ? 'active' : '' ?>" href="?page=dean_approvals&filter=research">
                                        Research (<?= count($groupedSubmissions['research']) ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $filter === 'news' ? 'active' : '' ?>" href="?page=dean_approvals&filter=news">
                                        News (<?= count($groupedSubmissions['news']) ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $filter === 'certification' ? 'active' : '' ?>" href="?page=dean_approvals&filter=certification">
                                        Certifications (<?= count($groupedSubmissions['certification']) ?>)
                                    </a>
                                </li>
                            </ul>
                            <div class="d-flex gap-2">
                                <span class="badge badge--status badge--draft">Total: <?= $totalPending ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php 
                // Filter which sections to show based on filter parameter
                $sectionsToShow = [];
                if ($filter === 'all') {
                    $sectionsToShow = ['research', 'news', 'certification'];
                } else {
                    $sectionsToShow = [$filter];
                }
                ?>

                <?php if (in_array('research', $sectionsToShow)): ?>
                <!-- Research Publications Submissions -->
                <div class="card">
                  <div class="card-header">
                    <h5 class="card-title mb-0">
                      <i class="fas fa-microscope me-2"></i>Research Publications Submissions (<?= count($groupedSubmissions['research']) ?>)
                    </h5>
                  </div>
                  <div class="card-body">
                    <?php if (empty($groupedSubmissions['research'])): ?>
                      <div class="empty-state-card">
                        <i class="fas fa-microscope fa-3x"></i>
                        <h6>No Pending Research Publications</h6>
                        <div class="text-muted">All research publications have been processed.</div>
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
                            <?php foreach ($groupedSubmissions['research'] as $submission): ?>
                              <?php
                                $id         = (int)$submission['id'];
                                $title      = esc($submission['title'] ?? '');
                                $content    = json_decode($submission['content'] ?? '{}', true) ?: [];
                                $authors    = esc($content['authors'] ?? '');
                                $doi        = esc($content['doi'] ?? '');
                                $publisher  = esc($content['publisher'] ?? '');
                                $conference = esc($content['conference'] ?? '');
                                $year       = esc($content['year'] ?? '');
                                $view_url   = esc($content['view_url'] ?? '');
                              ?>
                              <tr>
                                <td><?= $title ?></td>
                                <td><?= $authors ?></td>
                                <td><?= $doi ?></td>
                                <td><?= $publisher ?></td>
                                <td><?= $conference ?></td>
                                <td><?= $year ?></td>
                                <td>
                                  <?php if (!empty($view_url)): ?>
                                    <a href="<?= $view_url ?>" target="_blank" rel="noopener">View</a>
                                  <?php else: ?>
                                    <span class="no-notes text-muted">No link</span>
                                  <?php endif; ?>
                                </td>
                                <td>
                                  <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal" data-bs-target="#viewModal<?= $id ?>">
                                      <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-success"
                                            data-bs-toggle="modal" data-bs-target="#approveModal<?= $id ?>">
                                      <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal<?= $id ?>">
                                      <i class="fas fa-times"></i>
                                    </button>
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
                <?php endif; ?>

                <?php if (in_array('news', $sectionsToShow)): ?>
                <!-- News Submissions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-newspaper me-2"></i>News Submissions (<?= count($groupedSubmissions['news']) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($groupedSubmissions['news'])): ?>
                            <div class="empty-state-card">
                                <i class="fas fa-newspaper fa-3x"></i>
                                <h6>No Pending News Submissions</h6>
                                <div class="text-muted">All news submissions have been processed.</div>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Faculty</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Submitted</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($groupedSubmissions['news'] as $submission): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($submission['faculty_name']) ?></strong>
                                                    <br><small class="text-muted"><?= esc($submission['department_name']) ?></small>
                                                </td>
                                                <td>
                                                    <strong><?= esc($submission['title']) ?></strong>
                                                    <?php if (!empty($submission['description'])): ?>
                                                        <br><small class="text-muted"><?= esc(substr($submission['description'], 0, 80)) ?>...</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($submission['category']): ?>
                                                        <span class="badge badge--category"><?= esc($submission['category']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small><?= $submission['submitted_at'] ? date('M j, Y', strtotime($submission['submitted_at'])) : '' ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge badge--status badge--draft"><?= esc($submission['status']) ?></span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" data-bs-target="#viewModal<?= $submission['id'] ?>">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-success" 
                                                                data-bs-toggle="modal" data-bs-target="#approveModal<?= $submission['id'] ?>">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                data-bs-toggle="modal" data-bs-target="#rejectModal<?= $submission['id'] ?>">
                                                            <i class="fas fa-times"></i>
                                                        </button>
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
                <?php endif; ?>

                <?php if (in_array('certification', $sectionsToShow)): ?>
                <!-- Certification Submissions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-certificate me-2"></i>Certification Submissions (<?= count($groupedSubmissions['certification']) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($groupedSubmissions['certification'])): ?>
                            <div class="empty-state-card">
                                <i class="fas fa-certificate fa-3x"></i>
                                <h6>No Pending Certification Submissions</h6>
                                <div class="text-muted">All certification submissions have been processed.</div>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Faculty</th>
                                            <th>Title</th>
                                            <th>Issuer</th>
                                            <th>Submitted</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($groupedSubmissions['certification'] as $submission): ?>
                                            <?php $certData = json_decode($submission['content'] ?? '{}', true) ?: []; ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($submission['faculty_name']) ?></strong>
                                                    <br><small class="text-muted"><?= esc($submission['department_name'] ?? '') ?></small>
                                                </td>
                                                <td>
                                                    <strong><?= esc($submission['title']) ?></strong>
                                                    <?php if (!empty($submission['description'])): ?>
                                                        <br><small class="text-muted"><?= esc(substr($submission['description'], 0, 80)) ?>...</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge--category"><?= esc($certData['issuer'] ?? 'Unknown') ?></span>
                                                </td>
                                                <td>
                                                    <small><?= $submission['submitted_at'] ? date('M j, Y', strtotime($submission['submitted_at'])) : '' ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge badge--status badge--draft"><?= esc($submission['status']) ?></span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" data-bs-target="#viewModal<?= $submission['id'] ?>">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-success" 
                                                                data-bs-toggle="modal" data-bs-target="#approveModal<?= $submission['id'] ?>">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                data-bs-toggle="modal" data-bs-target="#rejectModal<?= $submission['id'] ?>">
                                                            <i class="fas fa-times"></i>
                                                        </button>
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
                <?php endif; ?>
            </section>
        </main>
    </div>

    <!-- Modals for each submission -->
    <?php foreach ($groupedSubmissions['research'] as $submission): ?>
      <?php
        $id         = (int)$submission['id'];
        $title      = esc($submission['title'] ?? '');
        $content    = json_decode($submission['content'] ?? '{}', true) ?: [];
        $authors    = esc($content['authors'] ?? '');
        $doi        = esc($content['doi'] ?? '');
        $publisher  = esc($content['publisher'] ?? '');
        $conference = esc($content['conference'] ?? '');
        $year       = esc($content['year'] ?? '');
        $view_url   = esc($content['view_url'] ?? '');
      ?>
      <!-- View Modal -->
      <div class="modal fade" id="viewModal<?= $id ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                <i class="fas fa-eye me-2"></i>View Publication
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3"><strong>Title:</strong> <?= $title ?></div>
              <div class="mb-3"><strong>Authors:</strong> <?= $authors ?></div>
              <div class="mb-3"><strong>DOI:</strong> <?= $doi ?></div>
              <div class="mb-3"><strong>Publisher:</strong> <?= $publisher ?></div>
              <div class="mb-3"><strong>Conference:</strong> <?= $conference ?></div>
              <div class="mb-3"><strong>Year:</strong> <?= $year ?></div>
              <div class="mb-3"><strong>View URL:</strong>
                <?php if (!empty($view_url)): ?>
                  <a href="<?= $view_url ?>" target="_blank" rel="noopener"><?= $view_url ?></a>
                <?php else: ?>
                  <span class="no-notes text-muted">No link</span>
                <?php endif; ?>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Approve Modal -->
      <div class="modal fade" id="approveModal<?= $id ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                <i class="fas fa-check me-2"></i>Approve Publication
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
              <input type="hidden" name="action" value="approve">
              <input type="hidden" name="submission_id" value="<?= $id ?>">
              <div class="modal-body">
                <p>Are you sure you want to approve this research publication?</p>
                <p><strong><?= $title ?></strong></p>
                <p class="text-muted">This will publish the research immediately.</p>
                <div class="mb-3">
                  <label for="review_notes_<?= $id ?>" class="form-label">Review Notes (Optional)</label>
                  <textarea class="form-control" id="review_notes_<?= $id ?>" name="review_notes" rows="3"
                            placeholder="Add any notes about this approval..."></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-check"></i> Approve & Publish
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Reject Modal -->
      <div class="modal fade" id="rejectModal<?= $id ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                <i class="fas fa-times me-2"></i>Reject Publication
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
              <input type="hidden" name="action" value="reject">
              <input type="hidden" name="submission_id" value="<?= $id ?>">
              <div class="modal-body">
                <p>Are you sure you want to reject this research publication?</p>
                <p><strong><?= $title ?></strong></p>
                <div class="mb-3">
                  <label for="reject_notes_<?= $id ?>" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                  <textarea class="form-control" id="reject_notes_<?= $id ?>" name="review_notes" rows="3"
                            placeholder="Please provide a reason for rejection..." required></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">
                  <i class="fas fa-times"></i> Reject
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

    <!-- Modals for news and certification sections (unchanged) -->
    <?php foreach ($pendingSubmissions as $submission): ?>
        <!-- View Modal -->
        <div class="modal fade" id="viewModal<?= $submission['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-eye me-2"></i>View Submission
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><strong>Title:</strong></h6>
                                <p><?= esc($submission['title']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Type:</strong></h6>
                                <p><span class="badge badge--category"><?= esc($submission['submission_type']) ?></span></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6><strong>Faculty:</strong></h6>
                                <p><?= esc($submission['faculty_name']) ?> (<?= esc($submission['department_name']) ?>)</p>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Submitted:</strong></h6>
                                <p><?= $submission['submitted_at'] ? date('M j, Y g:i A', strtotime($submission['submitted_at'])) : '' ?></p>
                            </div>
                        </div>
                        <?php if ($submission['category']): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><strong>Category:</strong></h6>
                                    <p><?= esc($submission['category']) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($submission['description']): ?>
                            <h6><strong>Description:</strong></h6>
                            <div class="card">
                                <div class="card-body">
                                    <?= nl2br(esc($submission['description'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($submission['content']): ?>
                            <h6><strong>Content:</strong></h6>
                            <div class="card">
                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                    <?= nl2br(esc($submission['content'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <div class="modal fade" id="approveModal<?= $submission['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-check me-2"></i>Approve Submission
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="approve">
                        <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                        <div class="modal-body">
                            <p>Are you sure you want to approve this <?= esc($submission['submission_type']) ?> submission?</p>
                            <p><strong><?= esc($submission['title']) ?></strong></p>
                            <p class="text-muted">This will publish the submission immediately.</p>
                            <div class="mb-3">
                                <label for="review_notes_<?= $submission['id'] ?>" class="form-label">Review Notes (Optional)</label>
                                <textarea class="form-control" id="review_notes_<?= $submission['id'] ?>" name="review_notes" rows="3" 
                                          placeholder="Add any notes about this approval..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Approve & Publish
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal<?= $submission['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-times me-2"></i>Reject Submission
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                        <div class="modal-body">
                            <p>Are you sure you want to reject this <?= esc($submission['submission_type']) ?> submission?</p>
                            <p><strong><?= esc($submission['title']) ?></strong></p>
                            <div class="mb-3">
                                <label for="reject_notes_<?= $submission['id'] ?>" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="reject_notes_<?= $submission['id'] ?>" name="review_notes" rows="3" 
                                          placeholder="Please provide a reason for rejection..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>