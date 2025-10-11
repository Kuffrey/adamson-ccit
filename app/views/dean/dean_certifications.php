<?php
// app/views/dean/dean_certifications.php - Dean Certification Approval Interface
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['dean'], true)) {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../../models/FacultySubmissions.php';
require_once __DIR__ . '/../../models/FacultyCertification.php';

if (!function_exists('esc')) {
    function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = $_SESSION['user'] ?? [];
$username = $user['username'] ?? 'Dean';
$notice = '';

// Handle approval/rejection actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submissionId = $_POST['submission_id'] ?? null;
    $action = $_POST['action'] ?? null;
    $reviewNotes = $_POST['review_notes'] ?? '';
    
    if ($submissionId && in_array($action, ['approve', 'reject'])) {
        try {
            $status = ($action === 'approve') ? 'approved' : 'rejected';
            $reviewerId = $user['id'];
            
            // If approving, also add to FacultyCertification table
            if ($action === 'approve') {
                $submission = FacultySubmissions::getById($submissionId);
                if ($submission && $submission['submission_type'] === 'certification') {
                    // Decode the certification data from JSON content
                    $certData = json_decode($submission['content'] ?? '{}', true) ?: [];
                    
                    // First, check if we need to create the certification record
                    $certificationId = $certData['certification_id'] ?? null;
                    
                    // If certification_id is provided and exists, use it
                    if ($certificationId) {
                        $availableCerts = FacultyCertification::getAllCertifications();
                        $certExists = false;
                        foreach ($availableCerts as $cert) {
                            if ($cert['id'] == $certificationId) {
                                $certExists = true;
                                break;
                            }
                        }
                        if (!$certExists) {
                            $certificationId = null; // Reset if cert doesn't exist
                        }
                    }
                    
                    // If no valid certification_id, try to find or create by title/issuer
                    if (!$certificationId) {
                        $availableCerts = FacultyCertification::getAllCertifications();
                        
                        // Look for existing certification by title and issuer
                        foreach ($availableCerts as $cert) {
                            $submittedTitle = $certData['cert_title'] ?? $certData['title'] ?? '';
                            $submittedIssuer = $certData['issuer'] ?? '';
                            
                            if (strtolower($cert['cert_title']) === strtolower($submittedTitle) && 
                                strtolower($cert['issuer']) === strtolower($submittedIssuer)) {
                                $certificationId = $cert['id'];
                                break;
                            }
                        }
                        
                        // If certification doesn't exist, create it
                        if (!$certificationId) {
                            $db = FacultyCertification::db();
                            $sql = "INSERT INTO certification (cert_title, issuer, cert_url, verify_url) 
                                    VALUES (:title, :issuer, :cert_url, :verify_url)";
                            $stmt = $db->prepare($sql);
                            $stmt->execute([
                                ':title' => $certData['cert_title'] ?? $certData['title'] ?? 'Unknown Certification',
                                ':issuer' => $certData['issuer'] ?? 'Unknown Issuer',
                                ':cert_url' => $certData['verification_url'] ?? '',
                                ':verify_url' => $certData['verification_url'] ?? ''
                            ]);
                            $certificationId = $db->lastInsertId();
                        }
                    }
                    
                    // Create the certification award entry
                    $facultyCertification = new FacultyCertification();
                    $certificationData = [
                        'faculty_id' => $submission['faculty_id'],
                        'certification_id' => $certificationId,
                        'year_earned' => $certData['year_earned'] ?? (!empty($certData['issue_date']) ? date('Y', strtotime($certData['issue_date'])) : date('Y')),
                        'year_expiry' => $certData['year_expiry'] ?? (!empty($certData['expiry_date']) ? date('Y', strtotime($certData['expiry_date'])) : null),
                        'status' => 'Active'
                    ];
                    
                    $facultyCertification->create($certificationData);
                }
            }
            
            $success = FacultySubmissions::updateStatus($submissionId, $status, $reviewerId, $reviewNotes);
            
            if ($success) {
                $notice = $action === 'approve' ? 'Certification approved successfully!' : 'Certification rejected successfully!';
            } else {
                $notice = 'Error: Failed to ' . $action . ' certification.';
            }
        } catch (Exception $e) {
            $notice = 'Error: ' . $e->getMessage();
        }
    }
}

// Get certification submissions
try {
    $certificationSubmissions = FacultySubmissions::getSubmissionsByType('certification');
    
    // Sort by most recent first
    usort($certificationSubmissions, function($a, $b) {
        return strtotime($b['submitted_at'] ?? '0') - strtotime($a['submitted_at'] ?? '0');
    });
    
    // Filter submissions by status
    $pendingCertifications = array_filter($certificationSubmissions, function($sub) {
        return in_array($sub['status'], ['submitted', 'under_review']);
    });
    
    $approvedCertifications = array_filter($certificationSubmissions, function($sub) {
        return $sub['status'] === 'approved';
    });
    
    $rejectedCertifications = array_filter($certificationSubmissions, function($sub) {
        return $sub['status'] === 'rejected';
    });
    
} catch (Exception $e) {
    $certificationSubmissions = [];
    $pendingCertifications = [];
    $approvedCertifications = [];
    $rejectedCertifications = [];
    $notice = 'Error loading certification submissions: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certifications Approvals | CCIT Dean</title>
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/dean.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    .card { border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
    .card-header { background: #f8fafc; border-bottom: 1px solid #e5e7eb; padding: 20px 24px; font-size: 1.05rem; font-weight: 700; color: var(--navy);}
    .card-body { padding: 20px 24px; }
    .table th { font-weight: 600; font-size: .85rem; background: #f1f5f9; }
    .table td { font-size: .95rem; }
    .badge { font-size: .8rem; font-weight: 700; padding: 6px 12px; border-radius: 6px; }
    .modal-content { border-radius: 12px; }
    .alert { border-radius: 10px; font-size: .95rem; font-weight: 500; }
    .empty-state-card { text-align: center; padding: 32px 0; color: #6c757d; }
    </style>
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/_dean_sidebar.php'; ?>
    <main class="admin-main">
        <header class="admin-topbar">
            <button class="topbar__btn hide-desktop" type="button" aria-label="Open navigation menu" data-sb-open>
                <i class="fas fa-bars"></i>
            </button>
            <span class="admin-topbar__title">Certification Approvals</span>
            <span class="admin-topbar__spacer"></span>
        </header>
        <section class="admin-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <!-- Removed heading and description -->
                </div>
                <div class="d-flex gap-2">
                    <a href="?page=dean_manage_certifications" class="btn btn-outline-primary">
                        <i class="fas fa-certificate"></i> Manage Certifications
                    </a>
                </div>
            </div>
            <?php if ($notice): ?>
                <div class="alert alert-<?= str_starts_with($notice, 'Error:') ? 'danger' : 'success' ?> alert-dismissible fade show modern-alert" role="alert">
                    <?= esc($notice) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <ul class="nav nav-tabs mb-4" id="certificationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                        <i class="fas fa-clock me-1"></i>
                        Pending (<?= count($pendingCertifications) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                        <i class="fas fa-check me-1"></i>
                        Approved (<?= count($approvedCertifications) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                        <i class="fas fa-times me-1"></i>
                        Rejected (<?= count($rejectedCertifications) ?>)
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="certificationTabContent">
                <!-- Pending Certifications -->
                <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-clock me-2"></i>Pending Certifications (<?= count($pendingCertifications) ?>)
                        </div>
                        <div class="card-body">
                            <?php if (empty($pendingCertifications)): ?>
                                <div class="empty-state-card">
                                    <i class="fas fa-certificate fa-3x"></i>
                                    <h6>No certification submissions pending approval at this time.</h6>
                                    <div class="text-muted">All certification submissions have been processed.</div>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Faculty</th>
                                                <th>Certification</th>
                                                <th>Issuer</th>
                                                <th>Submitted</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pendingCertifications as $submission): ?>
                                                <?php $certData = json_decode($submission['content'] ?? '{}', true) ?: []; ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-secondary"><?= esc($submission['faculty_name'] ?? 'Unknown') ?></span>
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
                                                        <small><?= $submission['submitted_at'] ? date('M j, Y g:i A', strtotime($submission['submitted_at'])) : '-' ?></small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewModal<?= $submission['id'] ?>">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal<?= $submission['id'] ?>">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $submission['id'] ?>">
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
                </div>
                <!-- Approved Certifications -->
                <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-check me-2"></i>Approved Certifications (<?= count($approvedCertifications) ?>)
                        </div>
                        <div class="card-body">
                            <?php if (empty($approvedCertifications)): ?>
                                <div class="empty-state-card">
                                    <i class="fas fa-certificate fa-3x"></i>
                                    <h6>No approved certifications found.</h6>
                                    <div class="text-muted">No certifications have been approved yet.</div>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Faculty</th>
                                                <th>Certification</th>
                                                <th>Issuer</th>
                                                <th>Approved</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($approvedCertifications as $submission): ?>
                                                <?php $certData = json_decode($submission['content'] ?? '{}', true) ?: []; ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-secondary"><?= esc($submission['faculty_name'] ?? 'Unknown') ?></span>
                                                    </td>
                                                    <td>
                                                        <strong><?= esc($submission['title']) ?></strong>
                                                        <span class="badge bg-success ms-2">Approved</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge--category"><?= esc($certData['issuer'] ?? 'Unknown') ?></span>
                                                    </td>
                                                    <td>
                                                        <small><?= $submission['reviewed_at'] ? date('M j, Y g:i A', strtotime($submission['reviewed_at'])) : '-' ?></small>
                                                    </td>
                                                    <td>
                                                        <small><?= esc($submission['review_notes'] ?? 'No notes') ?></small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- Rejected Certifications -->
                <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-times me-2"></i>Rejected Certifications (<?= count($rejectedCertifications) ?>)
                        </div>
                        <div class="card-body">
                            <?php if (empty($rejectedCertifications)): ?>
                                <div class="empty-state-card">
                                    <i class="fas fa-certificate fa-3x"></i>
                                    <h6>No rejected certifications found.</h6>
                                    <div class="text-muted">No certifications have been rejected yet.</div>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Faculty</th>
                                                <th>Certification</th>
                                                <th>Issuer</th>
                                                <th>Rejected</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($rejectedCertifications as $submission): ?>
                                                <?php $certData = json_decode($submission['content'] ?? '{}', true) ?: []; ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-secondary"><?= esc($submission['faculty_name'] ?? 'Unknown') ?></span>
                                                    </td>
                                                    <td>
                                                        <strong><?= esc($submission['title']) ?></strong>
                                                        <span class="badge bg-danger ms-2">Rejected</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge--category"><?= esc($certData['issuer'] ?? 'Unknown') ?></span>
                                                    </td>
                                                    <td>
                                                        <small><?= $submission['reviewed_at'] ? date('M j, Y g:i A', strtotime($submission['reviewed_at'])) : '-' ?></small>
                                                    </td>
                                                    <td>
                                                        <small class="text-danger"><?= esc($submission['review_notes'] ?? 'No reason provided') ?></small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <?php foreach ($pendingCertifications as $submission): ?>
                <!-- View Modal -->
                <div class="modal fade" id="viewModal<?= $submission['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-eye me-2"></i>View Certification Submission
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
                                        <h6><strong>Faculty:</strong></h6>
                                        <p><?= esc($submission['faculty_name'] ?? 'Unknown') ?></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><strong>Issuer:</strong></h6>
                                        <p><?= esc($certData['issuer'] ?? 'Unknown') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><strong>Submitted:</strong></h6>
                                        <p><?= $submission['submitted_at'] ? date('M j, Y g:i A', strtotime($submission['submitted_at'])) : '-' ?></p>
                                    </div>
                                </div>
                                <?php if (!empty($submission['description'])): ?>
                                    <h6><strong>Description:</strong></h6>
                                    <div class="card">
                                        <div class="card-body">
                                            <?= nl2br(esc($submission['description'])) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($submission['content'])): ?>
                                    <h6><strong>Certification Details:</strong></h6>
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
                                <h5 class="modal-title">Approve Certification</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <p><strong>Title:</strong> <?= esc($submission['title']) ?></p>
                                    <p><strong>Faculty:</strong> <?= esc($submission['faculty_name'] ?? 'Unknown') ?></p>
                                    <?php
                                    $certData = json_decode($submission['content'] ?? '{}', true) ?: [];
                                    if (!empty($certData)): ?>
                                        <div class="border p-2 rounded bg-light mb-3">
                                            <h6>Certification Details:</h6>
                                            <?php if (!empty($certData['issuer'])): ?>
                                                <p><strong>Issuer:</strong> <?= esc($certData['issuer']) ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($certData['issue_date'])): ?>
                                                <p><strong>Issue Date:</strong> <?= esc($certData['issue_date']) ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($certData['expiry_date'])): ?>
                                                <p><strong>Expiry Date:</strong> <?= esc($certData['expiry_date']) ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($certData['credential_id'])): ?>
                                                <p><strong>Credential ID:</strong> <?= esc($certData['credential_id']) ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($certData['verification_url'])): ?>
                                                <p><strong>Verification:</strong> <a href="<?= esc($certData['verification_url']) ?>" target="_blank">View Certificate</a></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($submission['description'])): ?>
                                        <p><strong>Description:</strong></p>
                                        <div class="border p-2 rounded bg-light">
                                            <small><?= nl2br(esc($submission['description'])) ?></small>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mt-3">
                                        <label class="form-label">Approval Notes (Optional)</label>
                                        <textarea class="form-control" name="review_notes" rows="3" 
                                                  placeholder="Add any comments about this approval..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Approve Certification
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
                                <h5 class="modal-title">Reject Certification</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <p><strong>Title:</strong> <?= esc($submission['title']) ?></p>
                                    <p><strong>Faculty:</strong> <?= esc($submission['faculty_name'] ?? 'Unknown') ?></p>
                                    <div class="mb-3">
                                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="review_notes" rows="3" required
                                                  placeholder="Please provide a clear reason for rejection..."></textarea>
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
        </section>
    </main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>