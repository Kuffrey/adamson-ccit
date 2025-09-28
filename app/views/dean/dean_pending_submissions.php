<?php
// app/views/dean/dean_pending_submissions.php - Dean Submission Queue
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

// Get all submissions with faculty details and filter by type if specified
$filterType = $_GET['type'] ?? 'all';

try {
    // Get all submissions for dean oversight
    $allSubmissions = FacultySubmissions::getAllSubmissionsWithFacultyDetails();
    
    // Filter to only research and news submissions (dean oversight)
    $submissions = array_filter($allSubmissions, function($submission) use ($filterType) {
        $validTypes = ['research', 'news'];
        if (!in_array($submission['submission_type'], $validTypes)) {
            return false;
        }
        
        if ($filterType !== 'all' && $submission['submission_type'] !== $filterType) {
            return false;
        }
        
        return true;
    });
    
    // Get counts for tabs
    $counts = [
        'all' => 0,
        'research' => 0,
        'news' => 0,
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0
    ];
    
    foreach ($submissions as $submission) {
        $counts['all']++;
        $counts[$submission['submission_type']]++;
        if (in_array($submission['status'], ['submitted', 'under_review'])) {
            $counts['pending']++;
        } elseif ($submission['status'] === 'approved') {
            $counts['approved']++;
        } elseif ($submission['status'] === 'rejected') {
            $counts['rejected']++;
        }
    }
    
    // Sort by most recent first
    usort($submissions, function($a, $b) {
        return strtotime($b['submitted_at'] ?? '0') - strtotime($a['submitted_at'] ?? '0');
    });
    
} catch (Exception $e) {
    $submissions = [];
    $counts = ['all' => 0, 'research' => 0, 'news' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
    $notice = 'Error loading submissions: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Queue | CCIT Dean</title>
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
                <span class="admin-topbar__title">Submission Queue</span>
                <div class="admin-topbar__spacer"></div>
                <div class="admin-topbar__user">
                    <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'D')) ?></span>
                    <span class="admin-topbar__name"><?= esc($username) ?></span>
                </div>
            </header>

            <section class="admin-cms-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="admin-cms-section__title mb-1">Submission Queue</h1>
                        <p class="text-muted mb-0">View all faculty submissions under dean oversight</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="badge bg-warning">Pending: <?= $counts['pending'] ?></div>
                        <div class="badge bg-success">Approved: <?= $counts['approved'] ?></div>
                        <div class="badge bg-danger">Rejected: <?= $counts['rejected'] ?></div>
                    </div>
                </div>

                <?php if ($notice): ?>
                    <div class="alert <?= str_starts_with($notice, 'Error') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show" role="alert">
                        <i class="fas <?= str_starts_with($notice, 'Error') ? 'fa-exclamation-triangle' : 'fa-check-circle' ?> me-2"></i>
                        <?= esc($notice) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filter Tabs -->
                <div class="card mb-4">
                    <div class="card-body">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link <?= $filterType === 'all' ? 'active' : '' ?>" 
                                   href="?page=dean_pending_submissions&type=all">
                                    All Submissions (<?= $counts['all'] ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $filterType === 'research' ? 'active' : '' ?>" 
                                   href="?page=dean_pending_submissions&type=research">
                                    Research (<?= $counts['research'] ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $filterType === 'news' ? 'active' : '' ?>" 
                                   href="?page=dean_pending_submissions&type=news">
                                    News (<?= $counts['news'] ?>)
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Submissions List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-inbox me-2"></i>
                            <?= ucfirst($filterType) ?> Submissions (<?= count($submissions) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($submissions)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No submissions found for this filter.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Faculty</th>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Category</th>
                                            <th>Submitted</th>
                                            <th>Status</th>
                                            <th>Reviewed</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($submissions as $submission): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($submission['faculty_name']) ?></strong>
                                                    <br><small class="text-muted"><?= esc($submission['dept']) ?></small>
                                                </td>
                                                <td>
                                                    <strong><?= esc($submission['title']) ?></strong>
                                                    <?php if (!empty($submission['description'])): ?>
                                                        <br><small class="text-muted"><?= esc(substr($submission['description'], 0, 60)) ?>...</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $submission['submission_type'] === 'research' ? 'warning' : 'info' ?>">
                                                        <i class="fas fa-<?= $submission['submission_type'] === 'research' ? 'microscope' : 'newspaper' ?>"></i>
                                                        <?= esc($submission['submission_type']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($submission['category']): ?>
                                                        <span class="badge bg-secondary"><?= esc($submission['category']) ?></span>
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
                                                        ($submission['status'] === 'rejected' ? 'danger' : 
                                                        (in_array($submission['status'], ['submitted', 'under_review']) ? 'warning' : 'secondary'))
                                                    ?>">
                                                        <?= esc($submission['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($submission['reviewed_at']): ?>
                                                        <?= date('M j, Y', strtotime($submission['reviewed_at'])) ?>
                                                        <?php if (!empty($submission['reviewer_name'])): ?>
                                                            <br><small class="text-muted">by <?= esc($submission['reviewer_name']) ?></small>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                                data-bs-toggle="modal" data-bs-target="#viewModal<?= $submission['id'] ?>" 
                                                                title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <?php if (in_array($submission['status'], ['submitted', 'under_review'])): ?>
                                                            <a href="?page=dean_approvals" class="btn btn-sm btn-primary" title="Go to Approvals">
                                                                <i class="fas fa-tasks"></i>
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
                </div>
            </section>
        </main>
    </div>

    <!-- View Details Modals -->
    <?php foreach ($submissions as $submission): ?>
        <div class="modal fade" id="viewModal<?= $submission['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Submission Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><strong>Title:</strong></h6>
                                <p><?= esc($submission['title']) ?></p>
                                
                                <h6><strong>Faculty:</strong></h6>
                                <p><?= esc($submission['faculty_name']) ?> (<?= esc($submission['dept']) ?>)</p>
                                
                                <h6><strong>Type:</strong></h6>
                                <p><span class="badge bg-<?= $submission['submission_type'] === 'research' ? 'warning' : 'info' ?>"><?= esc($submission['submission_type']) ?></span></p>
                                
                                <?php if ($submission['category']): ?>
                                    <h6><strong>Category:</strong></h6>
                                    <p><?= esc($submission['category']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Status:</strong></h6>
                                <p>
                                    <span class="badge bg-<?= 
                                        $submission['status'] === 'approved' ? 'success' : 
                                        ($submission['status'] === 'rejected' ? 'danger' : 'warning')
                                    ?>">
                                        <?= esc($submission['status']) ?>
                                    </span>
                                </p>
                                
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
                            <h6><strong>Description:</strong></h6>
                            <div class="border p-3 rounded mb-3"><?= nl2br(esc($submission['description'])) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($submission['content']): ?>
                            <h6><strong>Content:</strong></h6>
                            <div class="border p-3 rounded" style="max-height: 300px; overflow-y: auto;"><?= nl2br(esc($submission['content'])) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($submission['review_notes']): ?>
                            <h6><strong>Review Notes:</strong></h6>
                            <div class="alert alert-<?= $submission['status'] === 'approved' ? 'success' : 'danger' ?>">
                                <?= nl2br(esc($submission['review_notes'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <?php if (in_array($submission['status'], ['submitted', 'under_review'])): ?>
                            <a href="?page=dean_approvals" class="btn btn-primary">
                                <i class="fas fa-tasks"></i> Process in Approvals
                            </a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>