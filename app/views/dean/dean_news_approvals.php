<?php
// app/views/dean/dean_news_approvals.php - Dean News & Content Approval Interface
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

// Helper function for escaping
if (!function_exists('esc')) {
    function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = Auth::user();
$username = $user['username'] ?? 'Dean';
$notice = '';

// Handle approval/rejection actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submissionId = $_POST['submission_id'] ?? null;
    $action = $_POST['action'] ?? null;
    $reviewNotes = $_POST['review_notes'] ?? '';
    
    if ($submissionId && in_array($action, ['approve', 'reject', 'publish'])) {
        try {
            $status = ($action === 'approve') ? 'approved' : (($action === 'publish') ? 'published' : 'rejected');
            $reviewerId = $user['id'];
            
            $success = FacultySubmissions::updateStatus($submissionId, $status, $reviewerId, $reviewNotes);
            
            if ($success) {
                $notice = ucfirst($action) . 'd news content successfully!';
            } else {
                $notice = 'Error: Failed to ' . $action . ' news content.';
            }
        } catch (Exception $e) {
            $notice = 'Error: ' . $e->getMessage();
        }
    }
}

// Get news submissions
try {
    $newsSubmissions = FacultySubmissions::getSubmissionsByType('news');
    
    // Sort by most recent first
    usort($newsSubmissions, function($a, $b) {
        return strtotime($b['submitted_at'] ?? '0') - strtotime($a['submitted_at'] ?? '0');
    });
    
    // Filter submissions by status
    $pendingNews = array_filter($newsSubmissions, function($sub) {
        return in_array($sub['status'], ['submitted', 'under_review']);
    });
    
    $approvedNews = array_filter($newsSubmissions, function($sub) {
        return $sub['status'] === 'approved';
    });
    
    $publishedNews = array_filter($newsSubmissions, function($sub) {
        return $sub['status'] === 'published';
    });
    
    $rejectedNews = array_filter($newsSubmissions, function($sub) {
        return $sub['status'] === 'rejected';
    });
    
} catch (Exception $e) {
    $newsSubmissions = [];
    $pendingNews = [];
    $approvedNews = [];
    $publishedNews = [];
    $rejectedNews = [];
    $notice = 'Error loading news submissions: ' . $e->getMessage();
}

// Get filter from URL
$filter = $_GET['filter'] ?? 'pending';
$filteredSubmissions = [];
switch ($filter) {
    case 'approved':
        $filteredSubmissions = $approvedNews;
        break;
    case 'published':
        $filteredSubmissions = $publishedNews;
        break;
    case 'rejected':
        $filteredSubmissions = $rejectedNews;
        break;
    default:
        $filteredSubmissions = $pendingNews;
        break;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News & Content Approvals | CCIT Dean</title>
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
                <span class="admin-topbar__title">News & Content Approvals</span>
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
                                    <a class="nav-link <?= $filter === 'pending' ? 'active' : '' ?>" href="?page=dean_news_approvals&filter=pending">
                                        Pending Review (<?= count($pendingNews) ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $filter === 'approved' ? 'active' : '' ?>" href="?page=dean_news_approvals&filter=approved">
                                        Approved (<?= count($approvedNews) ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $filter === 'published' ? 'active' : '' ?>" href="?page=dean_news_approvals&filter=published">
                                        Published (<?= count($publishedNews) ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $filter === 'rejected' ? 'active' : '' ?>" href="?page=dean_news_approvals&filter=rejected">
                                        Rejected (<?= count($rejectedNews) ?>)
                                    </a>
                                </li>
                            </ul>
                            <div class="d-flex gap-2">
                                <span class="badge badge--status badge--draft">Total: <?= count($newsSubmissions) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- News Submissions List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-newspaper me-2"></i>News Submissions (<?= count($filteredSubmissions) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($filteredSubmissions)): ?>
                            <div class="empty-state-card">
                                <i class="fas fa-newspaper fa-3x"></i>
                                <h6>No News Submissions Found</h6>
                                <div class="text-muted">No <?= $filter ?> news submissions at this time.</div>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Author</th>
                                            <th>Title & Category</th>
                                            <th>Summary</th>
                                            <th>Submitted</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($filteredSubmissions as $submission): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($submission['faculty_name'] ?? $submission['faculty_username']) ?></strong>
                                                    <?php if (!empty($submission['dept'])): ?>
                                                        <br><small class="text-muted"><?= esc($submission['dept']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?= esc($submission['title']) ?></strong>
                                                    <?php if (!empty($submission['category'])): ?>
                                                        <br><span class="badge badge--category"><?= esc($submission['category']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($submission['description'])): ?>
                                                        <span class="text-muted"><?= esc(substr($submission['description'], 0, 120)) ?>...</span>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small><?= $submission['submitted_at'] ? date('M j, Y', strtotime($submission['submitted_at'])) : '' ?></small>
                                                    <?php if ($submission['submitted_at']): ?>
                                                        <br><small class="text-muted"><?= date('g:i A', strtotime($submission['submitted_at'])) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge--status badge--<?= 
                                                        $submission['status'] === 'approved' ? 'published' : 
                                                        ($submission['status'] === 'published' ? 'published' :
                                                        ($submission['status'] === 'rejected' ? 'archived' : 'draft'))
                                                    ?>">
                                                        <?= esc(ucfirst($submission['status'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" data-bs-target="#reviewModal<?= $submission['id'] ?>">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <?php if (in_array($submission['status'], ['submitted', 'under_review'])): ?>
                                                            <button type="button" class="btn btn-sm btn-success" 
                                                                    data-bs-toggle="modal" data-bs-target="#approveModal<?= $submission['id'] ?>">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger" 
                                                                    data-bs-toggle="modal" data-bs-target="#rejectModal<?= $submission['id'] ?>">
                                                                <i class="fas fa-times"></i>
                                                            </button>
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
                </div>
            </section>
        </main>
    </div>

    <!-- Modals for each submission -->
    <?php foreach ($newsSubmissions as $submission): ?>
        <!-- View Modal -->
        <div class="modal fade" id="reviewModal<?= $submission['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-eye me-2"></i>Review News Content
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
                                <p><span class="badge badge--category">News & Content</span></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6><strong>Author:</strong></h6>
                                <p><?= esc($submission['faculty_name'] ?? $submission['faculty_username']) ?>
                                   <?php if (!empty($submission['dept'])): ?>
                                       (<?= esc($submission['dept']) ?>)
                                   <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Submitted:</strong></h6>
                                <p><?= $submission['submitted_at'] ? date('M j, Y g:i A', strtotime($submission['submitted_at'])) : 'Not submitted' ?></p>
                            </div>
                        </div>
                        <?php if (!empty($submission['category'])): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><strong>Category:</strong></h6>
                                    <p><?= esc($submission['category']) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($submission['description']): ?>
                            <h6><strong>Description/Summary:</strong></h6>
                            <div class="card">
                                <div class="card-body">
                                    <?= nl2br(esc($submission['description'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($submission['content']): ?>
                            <h6><strong>Full Content:</strong></h6>
                            <div class="card">
                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                    <?= nl2br(esc($submission['content'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($submission['review_notes']): ?>
                            <h6><strong>Review Notes:</strong></h6>
                            <div class="alert alert-<?= $submission['status'] === 'approved' || $submission['status'] === 'published' ? 'success' : 'danger' ?>">
                                <?= nl2br(esc($submission['review_notes'])) ?>
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
        <?php if (in_array($submission['status'], ['submitted', 'under_review'])): ?>
        <div class="modal fade" id="approveModal<?= $submission['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-check me-2"></i>Approve News Content
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                        <div class="modal-body">
                            <p>Are you sure you want to approve this news content?</p>
                            <p><strong><?= esc($submission['title']) ?></strong></p>
                            <p class="text-muted">Choose your approval action:</p>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" value="approve" id="approve<?= $submission['id'] ?>" checked>
                                    <label class="form-check-label" for="approve<?= $submission['id'] ?>">
                                        <strong>Approve Only</strong> - Content will be approved but not yet published
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" value="publish" id="publish<?= $submission['id'] ?>">
                                    <label class="form-check-label" for="publish<?= $submission['id'] ?>">
                                        <strong>Approve & Publish</strong> - Content will be immediately published to the website
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="review_notes_<?= $submission['id'] ?>" class="form-label">Review Notes (Optional)</label>
                                <textarea class="form-control" id="review_notes_<?= $submission['id'] ?>" name="review_notes" rows="3" 
                                          placeholder="Add any feedback or notes..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Approve
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
                            <i class="fas fa-times me-2"></i>Reject News Content
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                        <div class="modal-body">
                            <p>Are you sure you want to reject this news content?</p>
                            <p><strong><?= esc($submission['title']) ?></strong></p>
                            <div class="mb-3">
                                <label for="reject_notes_<?= $submission['id'] ?>" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
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
        <?php endif; ?>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>