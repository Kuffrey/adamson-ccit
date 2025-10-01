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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News & Content Approvals | CCIT Dean</title>
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
                <span class="admin-topbar__title">News & Content Approvals</span>
                <div class="admin-topbar__spacer"></div>
                <div class="admin-topbar__user">
                    <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'D')) ?></span>
                    <span class="admin-topbar__name"><?= esc($username) ?></span>
                </div>
            </header>

            <section class="admin-cms-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="admin-cms-section__title mb-1">News & Content Approvals</h1>
                        <p class="text-muted mb-0">Review and approve faculty news articles, announcements, and content</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="badge bg-warning">
                            <i class="fas fa-clock me-1"></i>
                            <?= count($pendingNews) ?> Pending
                        </div>
                        <div class="badge bg-success">
                            <i class="fas fa-check me-1"></i>
                            <?= count($approvedNews) ?> Approved
                        </div>
                        <div class="badge bg-primary">
                            <i class="fas fa-globe me-1"></i>
                            <?= count($publishedNews) ?> Published
                        </div>
                        <div class="badge bg-danger">
                            <i class="fas fa-times me-1"></i>
                            <?= count($rejectedNews) ?> Rejected
                        </div>
                    </div>
                </div>

                <?php if ($notice): ?>
                    <div class="alert <?= str_starts_with($notice, 'Error') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show" role="alert">
                        <i class="fas <?= str_starts_with($notice, 'Error') ? 'fa-exclamation-triangle' : 'fa-check-circle' ?> me-2"></i>
                        <?= esc($notice) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" id="newsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                            <i class="fas fa-clock me-1"></i>
                            Pending Review (<?= count($pendingNews) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                            <i class="fas fa-check me-1"></i>
                            Approved (<?= count($approvedNews) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="published-tab" data-bs-toggle="tab" data-bs-target="#published" type="button" role="tab">
                            <i class="fas fa-globe me-1"></i>
                            Published (<?= count($publishedNews) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                            <i class="fas fa-times me-1"></i>
                            Rejected (<?= count($rejectedNews) ?>)
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="newsTabContent">
                    <!-- Pending News -->
                    <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                        <?php if (empty($pendingNews)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No news content pending approval at this time.
                            </div>
                        <?php else: ?>
                            <?= renderNewsTable($pendingNews, 'pending') ?>
                        <?php endif; ?>
                    </div>

                    <!-- Approved News -->
                    <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                        <?php if (empty($approvedNews)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No approved news content found.
                            </div>
                        <?php else: ?>
                            <?= renderNewsTable($approvedNews, 'approved') ?>
                        <?php endif; ?>
                    </div>

                    <!-- Published News -->
                    <div class="tab-pane fade" id="published" role="tabpanel" aria-labelledby="published-tab">
                        <?php if (empty($publishedNews)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No published news content found.
                            </div>
                        <?php else: ?>
                            <?= renderNewsTable($publishedNews, 'published') ?>
                        <?php endif; ?>
                    </div>

                    <!-- Rejected News -->
                    <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                        <?php if (empty($rejectedNews)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No rejected news content found.
                            </div>
                        <?php else: ?>
                            <?= renderNewsTable($rejectedNews, 'rejected') ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Review Modals -->
    <?php foreach ($newsSubmissions as $submission): ?>
        <div class="modal fade" id="reviewModal<?= $submission['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-newspaper me-2"></i>
                            Review News Content
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6><strong>Title:</strong></h6>
                                <p><?= esc($submission['title']) ?></p>
                                
                                <h6><strong>Author:</strong></h6>
                                <p><?= esc($submission['faculty_name'] ?? $submission['faculty_username']) ?> 
                                   <?php if (!empty($submission['dept'])): ?>
                                       <br><small class="text-muted"><?= esc($submission['dept']) ?></small>
                                   <?php endif; ?>
                                </p>
                                
                                <h6><strong>Type:</strong></h6>
                                <p><span class="badge bg-info">
                                    <i class="fas fa-newspaper"></i>
                                    News & Content
                                </span></p>
                                
                                <?php if (!empty($submission['category'])): ?>
                                    <h6><strong>Category:</strong></h6>
                                    <p><span class="badge bg-secondary"><?= esc($submission['category']) ?></span></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Status:</strong></h6>
                                <p><span class="badge bg-<?= 
                                    $submission['status'] === 'approved' ? 'success' : 
                                    ($submission['status'] === 'published' ? 'primary' :
                                    ($submission['status'] === 'rejected' ? 'danger' : 'warning'))
                                ?>"><?= esc(ucfirst($submission['status'])) ?></span></p>
                                
                                <h6><strong>Submitted:</strong></h6>
                                <p><?= $submission['submitted_at'] ? date('M j, Y g:i A', strtotime($submission['submitted_at'])) : 'Not submitted' ?></p>
                                
                                <?php if ($submission['reviewed_at']): ?>
                                    <h6><strong>Reviewed:</strong></h6>
                                    <p><?= date('M j, Y g:i A', strtotime($submission['reviewed_at'])) ?></p>
                                    <?php if (!empty($submission['reviewer_name'])): ?>
                                        <p><strong>Reviewer:</strong> <?= esc($submission['reviewer_name']) ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($submission['description']): ?>
                            <h6><strong>Description/Summary:</strong></h6>
                            <div class="border p-3 rounded mb-3"><?= nl2br(esc($submission['description'])) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($submission['content']): ?>
                            <h6><strong>Full Content:</strong></h6>
                            <div class="border p-3 rounded mb-3" style="max-height: 400px; overflow-y: auto;"><?= nl2br(esc($submission['content'])) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($submission['review_notes']): ?>
                            <h6><strong>Review Notes:</strong></h6>
                            <div class="alert alert-<?= $submission['status'] === 'approved' || $submission['status'] === 'published' ? 'success' : 'danger' ?>">
                                <?= nl2br(esc($submission['review_notes'])) ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Review Form (only for pending submissions) -->
                        <?php if (in_array($submission['status'], ['submitted', 'under_review'])): ?>
                            <form method="post" action="?page=dean_news_approvals">
                                <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                                
                                <div class="mb-3">
                                    <label for="review_notes_<?= $submission['id'] ?>" class="form-label">Review Notes:</label>
                                    <textarea name="review_notes" id="review_notes_<?= $submission['id'] ?>" 
                                              class="form-control" rows="3" 
                                              placeholder="Add your review comments (optional)..."></textarea>
                                </div>
                                
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="submit" name="action" value="reject" class="btn btn-danger">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                    <button type="submit" name="action" value="approve" class="btn btn-success">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button type="submit" name="action" value="publish" class="btn btn-primary">
                                        <i class="fas fa-globe"></i> Approve & Publish
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
function renderNewsTable($submissions, $type) {
    if (empty($submissions)) {
        return '<div class="alert alert-info">No submissions found.</div>';
    }
    
    ob_start();
    ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Author</th>
                            <th>Title & Category</th>
                            <th>Summary</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <?php if ($type === 'pending'): ?>
                                <th>Actions</th>
                            <?php else: ?>
                                <th>Reviewed</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): ?>
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
                                        <br><span class="badge bg-secondary"><?= esc($submission['category']) ?></span>
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
                                    <?= $submission['submitted_at'] ? date('M j, Y', strtotime($submission['submitted_at'])) : '' ?>
                                    <?php if ($submission['submitted_at']): ?>
                                        <br><small class="text-muted"><?= date('g:i A', strtotime($submission['submitted_at'])) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $submission['status'] === 'approved' ? 'success' : 
                                        ($submission['status'] === 'published' ? 'primary' :
                                        ($submission['status'] === 'rejected' ? 'danger' : 'warning'))
                                    ?>">
                                        <i class="fas fa-<?= 
                                            $submission['status'] === 'approved' ? 'check' : 
                                            ($submission['status'] === 'published' ? 'globe' :
                                            ($submission['status'] === 'rejected' ? 'times' : 'clock'))
                                        ?>"></i>
                                        <?= esc(ucfirst($submission['status'])) ?>
                                    </span>
                                </td>
                                <?php if ($type === 'pending'): ?>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                data-bs-toggle="modal" data-bs-target="#reviewModal<?= $submission['id'] ?>" 
                                                title="Review & Decide">
                                            <i class="fas fa-eye"></i> Review
                                        </button>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <?= $submission['reviewed_at'] ? date('M j, Y', strtotime($submission['reviewed_at'])) : '' ?>
                                        <?php if ($submission['reviewed_at']): ?>
                                            <br><small class="text-muted"><?= date('g:i A', strtotime($submission['reviewed_at'])) ?></small>
                                        <?php endif; ?>
                                        <?php if (!empty($submission['reviewer_name'])): ?>
                                            <br><small class="text-muted">by <?= esc($submission['reviewer_name']) ?></small>
                                        <?php endif; ?>
                                        <br><button type="button" class="btn btn-sm btn-outline-secondary mt-1" 
                                                data-bs-toggle="modal" data-bs-target="#reviewModal<?= $submission['id'] ?>" 
                                                title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>