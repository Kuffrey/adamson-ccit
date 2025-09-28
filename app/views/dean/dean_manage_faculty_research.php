<?php
// app/views/dean/dean_manage_faculty_research.php - Complete Faculty Research Management
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../lib/Auth.php';

// Check authentication using Auth class
if (!Auth::check() || !Auth::is('dean')) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

require_once __DIR__ . '/../../models/FacultyResearch.php';
require_once __DIR__ . '/../../models/FacultyCertification.php';
require_once __DIR__ . '/../../models/FacultySubmissions.php';
require_once __DIR__ . '/../../models/DeanLogs.php';

// Helper function for escaping
if (!function_exists('esc')) {
    function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = Auth::user();
$username = $user['username'] ?? 'Dean';
$notice = '';

// Handle approval/rejection actions for submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action']) && !empty($_POST['submission_id'])) {
    try {
        $submissionId = (int)$_POST['submission_id'];
        $action = $_POST['action'];
        $reviewNotes = trim($_POST['review_notes'] ?? '');
        
        if ($action === 'approve') {
            FacultySubmissions::updateStatus($submissionId, 'approved', $user['id'] ?? null, $reviewNotes);
            DeanLogs::logApprove('faculty_submissions', $submissionId, $user['id'] ?? null);
            $notice = 'Research submission approved successfully!';
        } elseif ($action === 'reject') {
            FacultySubmissions::updateStatus($submissionId, 'rejected', $user['id'] ?? null, $reviewNotes);
            DeanLogs::logReject('faculty_submissions', $submissionId, $user['id'] ?? null);
            $notice = 'Research submission rejected successfully!';
        }
    } catch (Exception $e) {
        $notice = 'Error processing action: ' . $e->getMessage();
    }
}

// Get comprehensive research data
try {
    // Get all research records
    $allResearch = FacultyResearch::getAll();
    
    // Get all certifications (view-only for dean)
    $facultyCertificationModel = new FacultyCertification();
    $allCertifications = $facultyCertificationModel->getAll();
    
    // Get pending research submissions for approval
    $pendingResearchSubmissions = FacultySubmissions::getPendingByType('research');
    
    // Get research statistics
    $researchCounts = [
        'total' => count($allResearch),
        'published' => count(array_filter($allResearch, function($r) { return ($r['status'] ?? '') === 'published'; })),
        'pending_approval' => count($pendingResearchSubmissions)
    ];
    
    $certificationCounts = [
        'total' => count($allCertifications),
        'active' => count(array_filter($allCertifications, function($c) { return ($c['status'] ?? 'active') === 'active'; }))
    ];
    
} catch (Exception $e) {
    $allResearch = [];
    $allCertifications = [];
    $pendingResearchSubmissions = [];
    $researchCounts = ['total' => 0, 'published' => 0, 'pending_approval' => 0];
    $certificationCounts = ['total' => 0, 'active' => 0];
    $notice = 'Error loading research data: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Research Management | CCIT Dean</title>
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
                <span class="admin-topbar__title">Faculty Research Management</span>
                <div class="admin-topbar__spacer"></div>
                <div class="admin-topbar__user">
                    <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'D')) ?></span>
                    <span class="admin-topbar__name"><?= esc($username) ?></span>
                </div>
            </header>

            <section class="admin-cms-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="admin-cms-section__title mb-1">Faculty Research Management</h1>
                        <p class="text-muted mb-0">Comprehensive management of faculty research and certifications</p>
                    </div>
                </div>

                <?php if ($notice): ?>
                    <div class="alert <?= str_starts_with($notice, 'Error') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show" role="alert">
                        <i class="fas <?= str_starts_with($notice, 'Error') ? 'fa-exclamation-triangle' : 'fa-check-circle' ?> me-2"></i>
                        <?= esc($notice) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-0">Total Research</h5>
                                        <h2 class="mb-0"><?= $researchCounts['total'] ?></h2>
                                    </div>
                                    <div class="ms-3">
                                        <i class="fas fa-microscope fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-0">Published</h5>
                                        <h2 class="mb-0"><?= $researchCounts['published'] ?></h2>
                                    </div>
                                    <div class="ms-3">
                                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-0">Pending Approval</h5>
                                        <h2 class="mb-0"><?= $researchCounts['pending_approval'] ?></h2>
                                    </div>
                                    <div class="ms-3">
                                        <i class="fas fa-clock fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-0">Certifications</h5>
                                        <h2 class="mb-0"><?= $certificationCounts['total'] ?></h2>
                                    </div>
                                    <div class="ms-3">
                                        <i class="fas fa-certificate fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Research Submissions for Approval -->
                <?php if (!empty($pendingResearchSubmissions)): ?>
                <div class="card mb-4 shadow-sm border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock me-2"></i>Pending Research Submissions 
                            <span class="badge bg-dark ms-2"><?= count($pendingResearchSubmissions) ?> awaiting approval</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Faculty</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingResearchSubmissions as $submission): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($submission['name']) ?></strong>
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
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            data-bs-toggle="modal" data-bs-target="#approveSubmissionModal<?= $submission['id'] ?>">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            data-bs-toggle="modal" data-bs-target="#rejectSubmissionModal<?= $submission['id'] ?>">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Published Research -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-microscope text-primary me-2"></i>Faculty Research Database
                            <span class="badge bg-primary ms-2"><?= count($allResearch) ?> records</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($allResearch)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No research records found.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Title</th>
                                            <th>Faculty</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allResearch as $research): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($research['title']) ?></strong>
                                                    <?php if (!empty($research['description'])): ?>
                                                        <br><small class="text-muted"><?= esc(substr($research['description'], 0, 100)) ?>...</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?= esc($research['faculty_name'] ?? 'Unknown') ?></strong>
                                                    <br><small class="text-muted"><?= esc($research['dept'] ?? '') ?></small>
                                                </td>
                                                <td>
                                                    <?php if (!empty($research['category'])): ?>
                                                        <span class="badge bg-secondary"><?= esc($research['category']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= ($research['status'] ?? 'active') === 'published' ? 'success' : 'secondary' ?>">
                                                        <?= esc($research['status'] ?? 'active') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= !empty($research['published_at']) ? date('M j, Y', strtotime($research['published_at'])) : 
                                                        (!empty($research['created_at']) ? date('M j, Y', strtotime($research['created_at'])) : '') ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Faculty Certifications (View Only) -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-certificate text-info me-2"></i>Faculty Certifications 
                            <span class="badge bg-info ms-2"><?= count($allCertifications) ?> records</span>
                            <small class="text-muted ms-2">(View Only)</small>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($allCertifications)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No certification records found.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Certification</th>
                                            <th>Faculty</th>
                                            <th>Issuer</th>
                                            <th>Date Obtained</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allCertifications as $cert): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($cert['cert_title'] ?? 'Untitled Certification') ?></strong>
                                                    <?php if (!empty($cert['description'])): ?>
                                                        <br><small class="text-muted"><?= esc(substr($cert['description'], 0, 80)) ?>...</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?= esc($cert['faculty_name'] ?? 'Unknown') ?></strong>
                                                    <br><small class="text-muted"><?= esc($cert['dept'] ?? '') ?></small>
                                                </td>
                                                <td>
                                                    <?= esc($cert['issuer'] ?? 'N/A') ?>
                                                </td>
                                                <td>
                                                    <?= !empty($cert['year_earned']) ? $cert['year_earned'] : 'N/A' ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= ($cert['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                                        <?= esc($cert['status'] ?? 'active') ?>
                                                    </span>
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

    <!-- Approval/Rejection Modals for Research Submissions -->
    <?php foreach ($pendingResearchSubmissions as $submission): ?>
        <!-- Approve Modal -->
        <div class="modal fade" id="approveSubmissionModal<?= $submission['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Approve Research Submission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="approve">
                        <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                        <div class="modal-body">
                            <p>Are you sure you want to approve this research submission?</p>
                            <p><strong><?= esc($submission['title']) ?></strong></p>
                            <div class="mb-3">
                                <label class="form-label">Approval Notes (Optional)</label>
                                <textarea class="form-control" name="review_notes" rows="3" 
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
        <div class="modal fade" id="rejectSubmissionModal<?= $submission['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Research Submission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                        <div class="modal-body">
                            <p>Are you sure you want to reject this research submission?</p>
                            <p><strong><?= esc($submission['title']) ?></strong></p>
                            <div class="mb-3">
                                <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="review_notes" rows="3" 
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