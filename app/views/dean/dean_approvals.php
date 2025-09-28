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
        
        if ($action === 'approve') {
            FacultySubmissions::updateStatus($submissionId, 'approved', $user['id'] ?? null, $reviewNotes);
            $notice = 'Submission approved successfully!';
            
            // Log the approval action (essential CRUD operation)
            DeanLogs::logApprove(
                $user['id'] ?? null,
                'faculty_submissions',
                $submissionId,
                'Submission approved' . ($reviewNotes ? ': ' . substr($reviewNotes, 0, 100) : '')
            );
            
        } elseif ($action === 'reject') {
            FacultySubmissions::updateStatus($submissionId, 'rejected', $user['id'] ?? null, $reviewNotes);
            $notice = 'Submission rejected successfully!';
            
            // Log the rejection action (essential CRUD operation)  
            DeanLogs::logReject(
                $user['id'] ?? null,
                'faculty_submissions',
                $submissionId,
                'Submission rejected' . ($reviewNotes ? ': ' . substr($reviewNotes, 0, 100) : '')
            );
        }
    } catch (Exception $e) {
        $notice = 'Error processing action: ' . $e->getMessage();
    }
}

// Get pending submissions for dean approval (research and news only)
try {
    $pendingSubmissions = FacultySubmissions::getPendingByReviewer();
    // Filter only research and news submissions
    $pendingSubmissions = array_filter($pendingSubmissions, function($submission) {
        return in_array($submission['submission_type'], ['research', 'news']);
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
    'news' => []
];

foreach ($pendingSubmissions as $submission) {
    if (isset($groupedSubmissions[$submission['submission_type']])) {
        $groupedSubmissions[$submission['submission_type']][] = $submission;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approvals | CCIT Dean</title>
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-cms-layout">
        <?php include __DIR__ . '/_dean_sidebar.php'; ?>
        
        <main class="admin-main">
            <header class="admin-topbar">
                <span class="admin-topbar__title">Pending Approvals</span>
                <div class="admin-topbar__spacer"></div>
                <div class="admin-topbar__user">
                    <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'D')) ?></span>
                    <span class="admin-topbar__name"><?= esc($username) ?></span>
                </div>
            </header>

            <section class="admin-cms-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="admin-cms-section__title mb-1">Pending Approvals</h1>
                        <p class="text-muted mb-0">Review and approve faculty submissions</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="badge bg-warning">Research: <?= $submissionCounts['research'] ?></div>
                        <div class="badge bg-info">News: <?= $submissionCounts['news'] ?></div>
                        <div class="badge bg-primary">Total: <?= ($submissionCounts['research'] + $submissionCounts['news']) ?></div>
                    </div>
                </div>

                <?php if ($notice): ?>
                    <div class="alert <?= str_starts_with($notice, 'Error') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show" role="alert">
                        <i class="fas <?= str_starts_with($notice, 'Error') ? 'fa-exclamation-triangle' : 'fa-check-circle' ?> me-2"></i>
                        <?= esc($notice) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Research Submissions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-microscope me-2"></i>Research Submissions (<?= count($groupedSubmissions['research']) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($groupedSubmissions['research'])): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No pending research submissions.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
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
                                        <?php foreach ($groupedSubmissions['research'] as $submission): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($submission['faculty_name']) ?></strong>
                                                    <br><small class="text-muted"><?= esc($submission['dept']) ?></small>
                                                </td>
                                                <td>
                                                    <strong><?= esc($submission['title']) ?></strong>
                                                    <?php if (!empty($submission['description'])): ?>
                                                        <br><small class="text-muted"><?= esc(substr($submission['description'], 0, 80)) ?>...</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($submission['category']): ?>
                                                        <span class="badge bg-secondary"><?= esc($submission['category']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= $submission['submitted_at'] ? date('M j, Y', strtotime($submission['submitted_at'])) : '' ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning"><?= esc($submission['status']) ?></span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-info" 
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

                <!-- News Submissions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-newspaper me-2"></i>News Submissions (<?= count($groupedSubmissions['news']) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($groupedSubmissions['news'])): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No pending news submissions.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
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
                                                    <br><small class="text-muted"><?= esc($submission['dept']) ?></small>
                                                </td>
                                                <td>
                                                    <strong><?= esc($submission['title']) ?></strong>
                                                    <?php if (!empty($submission['description'])): ?>
                                                        <br><small class="text-muted"><?= esc(substr($submission['description'], 0, 80)) ?>...</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($submission['category']): ?>
                                                        <span class="badge bg-info"><?= esc($submission['category']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= $submission['submitted_at'] ? date('M j, Y', strtotime($submission['submitted_at'])) : '' ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning"><?= esc($submission['status']) ?></span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-info" 
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
            </section>
        </main>
    </div>

    <!-- Modals for each submission -->
    <?php foreach ($pendingSubmissions as $submission): ?>
        <!-- View Modal -->
        <div class="modal fade" id="viewModal<?= $submission['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">View Submission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <h6><strong>Title:</strong> <?= esc($submission['title']) ?></h6>
                        <p><strong>Faculty:</strong> <?= esc($submission['faculty_name']) ?> (<?= esc($submission['dept']) ?>)</p>
                        <p><strong>Type:</strong> <?= esc($submission['submission_type']) ?></p>
                        <?php if ($submission['category']): ?>
                            <p><strong>Category:</strong> <?= esc($submission['category']) ?></p>
                        <?php endif; ?>
                        <p><strong>Submitted:</strong> <?= $submission['submitted_at'] ? date('M j, Y g:i A', strtotime($submission['submitted_at'])) : '' ?></p>
                        <?php if ($submission['description']): ?>
                            <p><strong>Description:</strong></p>
                            <div class="border p-3 rounded"><?= nl2br(esc($submission['description'])) ?></div>
                        <?php endif; ?>
                        <?php if ($submission['content']): ?>
                            <p><strong>Content:</strong></p>
                            <div class="border p-3 rounded" style="max-height: 300px; overflow-y: auto;"><?= nl2br(esc($submission['content'])) ?></div>
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
                        <h5 class="modal-title">Approve Submission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="approve">
                        <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                        <div class="modal-body">
                            <p>Are you sure you want to approve this <?= esc($submission['submission_type']) ?> submission?</p>
                            <p><strong><?= esc($submission['title']) ?></strong></p>
                            <div class="mb-3">
                                <label for="review_notes_<?= $submission['id'] ?>" class="form-label">Review Notes (Optional)</label>
                                <textarea class="form-control" id="review_notes_<?= $submission['id'] ?>" name="review_notes" rows="3" 
                                          placeholder="Add any notes about this approval..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Approve</button>
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
                        <h5 class="modal-title">Reject Submission</h5>
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
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>