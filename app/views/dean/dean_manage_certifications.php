<?php
// app/views/dean/dean_manage_certifications.php - Dean Certifications Management
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../lib/Auth.php';

// Check authentication using Auth class
if (!Auth::check() || !Auth::is('dean')) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

require_once __DIR__ . '/../../models/FacultyCertification.php';
require_once __DIR__ . '/../../models/DeanLogs.php';
require_once __DIR__ . '/../../models/FacultySubmissions.php';

if (!function_exists('esc')) {
    function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = Auth::user();
$username = $user['username'] ?? 'Dean';
$notice = '';

try {
    $facultyCertification = new FacultyCertification();
} catch (Exception $e) {
    $notice = 'Error initializing models: ' . $e->getMessage();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle faculty submission approval/rejection
        if (!empty($_POST['faculty_action']) && !empty($_POST['submission_id'])) {
            $submissionId = (int)$_POST['submission_id'];
            $action = $_POST['faculty_action'];
            $reviewNotes = trim($_POST['review_notes'] ?? '');
            
            if ($action === 'approve') {
                FacultySubmissions::updateStatus($submissionId, 'approved', $user['id'] ?? null, $reviewNotes);
                $notice = 'Faculty certification submission approved successfully!';
                DeanLogs::logApprove('faculty_submissions', $submissionId, $user['id'] ?? null, "Approved certification submission: " . substr($reviewNotes, 0, 100));
            } elseif ($action === 'reject') {
                FacultySubmissions::updateStatus($submissionId, 'rejected', $user['id'] ?? null, $reviewNotes);
                $notice = 'Faculty certification submission rejected.';
                DeanLogs::logReject('faculty_submissions', $submissionId, $user['id'] ?? null, "Rejected certification submission: " . substr($reviewNotes, 0, 100));
            }
        }

        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add_certification':
                    $certId = $facultyCertification->create($_POST);
                    if ($certId) {
                        DeanLogs::logCreate('faculty_certifications', $certId, $user['id'] ?? null, "Added certification award");
                    }
                    $notice = 'Faculty certification award added successfully!';
                    break;
                
                case 'update_certification':
                    $facultyCertification->update($_POST['id'], $_POST);
                    DeanLogs::logUpdate('faculty_certifications', $_POST['id'], $user['id'] ?? null, "Updated certification award");
                    $notice = 'Faculty certification award updated successfully!';
                    break;
                
                case 'archive_certification':
                    $facultyCertification->archive($_POST['id']);
                    DeanLogs::logUpdate('faculty_certifications', $_POST['id'], $user['id'] ?? null, "Archived certification award");
                    $notice = 'Certification archived successfully!';
                    break;
                
                case 'unarchive_certification':
                    $facultyCertification->unarchive($_POST['id']);
                    DeanLogs::logUpdate('faculty_certifications', $_POST['id'], $user['id'] ?? null, "Unarchived certification award");
                    $notice = 'Certification unarchived successfully!';
                    break;
                
                case 'delete_certification':
                    $result = $facultyCertification->delete($_POST['id']);
                    if ($result) {
                        DeanLogs::logDelete($user['id'] ?? null, 'faculty_certifications', $_POST['id'], "Deleted certification award");
                        $notice = 'Faculty certification award deleted successfully!';
                    } else {
                        $notice = 'Error: Failed to delete certification award.';
                    }
                    break;
            }
        }
    } catch (Exception $e) {
        $notice = 'Error: ' . $e->getMessage();
    }
}

// Get current data
try {
    $status = $_GET['status'] ?? 'all';
    
    // Enhanced database debugging
    $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit;charset=utf8mb4", "root", "");
    
    // Check all relevant tables
    $tables = ['faculty_certification_award', 'certification', 'certifications', 'users'];
    foreach ($tables as $table) {
        $tableCheck = $pdo->query("SHOW TABLES LIKE '{$table}'");
        if ($tableCheck->rowCount() == 0) {
            error_log("Table {$table} not found!");
        } else {
            $countCheck = $pdo->query("SELECT COUNT(*) as count FROM {$table}");
            $count = $countCheck->fetch();
            error_log("Table {$table} has {$count['count']} records");
        }
    }
    
    // Check table structure for faculty_certification_award
    $structureCheck = $pdo->query("DESCRIBE faculty_certification_award");
    $columns = $structureCheck->fetchAll();
    error_log("faculty_certification_award structure: " . print_r(array_column($columns, 'Field'), true));
    
    $certifications = $facultyCertification->getAll($status);
    $counts = $facultyCertification->statusCounts();
    $availableCertifications = FacultyCertification::getAllCertifications();
    $facultyList = FacultyCertification::getAllFaculty();
    
    // Enhanced debug information
    error_log("=== Dean Certifications Debug ===");
    error_log("Status filter: " . $status);
    error_log("Certifications count: " . count($certifications));
    error_log("Available certifications count: " . count($availableCertifications));
    error_log("Faculty list count: " . count($facultyList));
    error_log("Counts array: " . print_r($counts, true));
    
    if (count($certifications) > 0) {
        error_log("First certification: " . print_r($certifications[0], true));
    } else {
        error_log("No certifications returned from getAll()");
        
        // Direct database check with sample data
        $directCheck = $pdo->query("SELECT COUNT(*) as count FROM faculty_certification_award");
        $directCount = $directCheck->fetch();
        error_log("Direct database count: " . print_r($directCount, true));
        
        if ($directCount['count'] > 0) {
            $sampleData = $pdo->query("SELECT * FROM faculty_certification_award LIMIT 3");
            $samples = $sampleData->fetchAll();
            error_log("Sample data from database: " . print_r($samples, true));
            
            // Check if related tables have data
            try {
                $userSample = $pdo->query("SELECT id, first_name, last_name FROM users WHERE role = 'faculty' LIMIT 3");
                $users = $userSample->fetchAll();
                error_log("Sample users: " . print_r($users, true));
            } catch (Exception $e) {
                error_log("Users table error: " . $e->getMessage());
            }
            
            try {
                $certSample = $pdo->query("SELECT id, cert_title, issuer FROM certification LIMIT 3");
                $certs = $certSample->fetchAll();
                error_log("Sample certifications (certification table): " . print_r($certs, true));
            } catch (Exception $e) {
                error_log("certification table error: " . $e->getMessage());
                try {
                    $certSample = $pdo->query("SELECT id, cert_title, issuer FROM certifications LIMIT 3");
                    $certs = $certSample->fetchAll();
                    error_log("Sample certifications (certifications table): " . print_r($certs, true));
                } catch (Exception $e2) {
                    error_log("certifications table error: " . $e2->getMessage());
                }
            }
        }
    }
    
    // Get pending faculty certification submissions
    $pendingCertificationSubmissions = [];
} catch (Exception $e) {
    $notice = 'Error loading data: ' . $e->getMessage();
    error_log("Certification loading error: " . $e->getMessage());
    error_log("Error trace: " . $e->getTraceAsString());
    $certifications = [];
    $counts = ['all' => 0, 'active' => 0, 'expired' => 0, 'revoked' => 0, 'archived' => 0];
    $availableCertifications = [];
    $facultyList = [];
    $pendingCertificationSubmissions = [];
}
?>

<?php
// Collect modal markup and print once at end of <body> so modals are not trapped by table/layout stacking
$__certCollectedModals = '';

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
    <title>Manage Certifications | CCIT Dean</title>
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
                <span class="admin-topbar__title">CCIT Certifications Management</span>
                <span class="admin-topbar__spacer"></span>
            </header>
            <section class="admin-section">

                <!-- Enhanced message handling -->
                <?php if (!empty($notice)): ?>
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
                                    <a class="nav-link <?= ($status ?? 'all') === 'all' ? 'active' : '' ?>" href="?page=dean_manage_certifications&status=all">
                                        All Certifications (<?= (int)($counts['all'] ?? 0) ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= ($status ?? '') === 'active' ? 'active' : '' ?>" href="?page=dean_manage_certifications&status=active">
                                        Active (<?= (int)($counts['active'] ?? 0) ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= ($status ?? '') === 'expired' ? 'active' : '' ?>" href="?page=dean_manage_certifications&status=expired">
                                        Expired (<?= (int)($counts['expired'] ?? 0) ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= ($status ?? '') === 'revoked' ? 'active' : '' ?>" href="?page=dean_manage_certifications&status=revoked">
                                        Revoked (<?= (int)($counts['revoked'] ?? 0) ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= ($status ?? '') === 'archived' ? 'active' : '' ?>" href="?page=dean_manage_certifications&status=archived">
                                        Archived (<?= (int)($counts['archived'] ?? 0) ?>)
                                    </a>
                                </li>
                            </ul>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCertificationModal">
                                <i class="fas fa-plus me-2"></i>Add Certification
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Faculty Certification Submissions for Approval -->
                <?php if (!empty($pendingCertificationSubmissions)): ?>
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock text-warning me-2"></i>
                            Pending Faculty Certification Submissions (<?= count($pendingCertificationSubmissions) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
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
                                    <?php foreach ($pendingCertificationSubmissions as $submission): ?>
                                        <?php
                                            $sid   = (int)$submission['id'];
                                            $sfac  = esc($submission['faculty_name'] ?? 'Unknown Faculty');
                                            $sdept = esc($submission['department_name'] ?? '');
                                            $scert = esc($submission['certification_name'] ?? '');
                                            $sissuer = esc($submission['issuer'] ?? 'Unknown');
                                            $ssub  = !empty($submission['submitted_at']) ? date('M j, Y g:i A', strtotime($submission['submitted_at'])) : '';
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?= $sfac ?></strong>
                                                <br><small class="text-muted"><?= $sdept ?></small>
                                            </td>
                                            <td><strong><?= $scert ?></strong></td>
                                            <td><span class="badge badge--category"><?= $sissuer ?></span></td>
                                            <td><small><?= esc($ssub) ?></small></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-success btn-sm" 
                                                            data-bs-toggle="modal" data-bs-target="#approveCertificationModal<?= $sid ?>">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            data-bs-toggle="modal" data-bs-target="#rejectCertificationModal<?= $sid ?>">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php ob_start(); ?>
                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveCertificationModal<?= $sid ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Approve Certification Submission</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="faculty_action" value="approve">
                                                            <input type="hidden" name="submission_id" value="<?= $sid ?>">
                                                            <p>Approve "<strong><?= $scert ?></strong>" by <?= $sfac ?>?</p>
                                                            <p class="text-muted">This will add the certification to the faculty member's profile.</p>
                                                            <div class="mb-3">
                                                                <label class="form-label">Review Notes (Optional)</label>
                                                                <textarea class="form-control" name="review_notes" rows="3" placeholder="Add any feedback or notes..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success">Approve &amp; Add</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectCertificationModal<?= $sid ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Certification Submission</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="faculty_action" value="reject">
                                                            <input type="hidden" name="submission_id" value="<?= $sid ?>">
                                                            <p>Reject "<strong><?= $scert ?></strong>" by <?= $sfac ?>?</p>
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
                                        <?php $__certCollectedModals .= ob_get_clean(); ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Certifications List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-certificate me-2"></i>Faculty Certification Awards (<?= count($certifications ?? []) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($certifications)): ?>
                            <div class="empty-state-card">
                                <i class="fas fa-certificate fa-3x"></i>
                                <h6>No Certification Awards Found</h6>
                                <div class="text-muted">Start by adding a certification award using the <strong>Add Certification</strong> button above.</div>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Faculty</th>
                                            <th>Certification</th>
                                            <th>Issuer</th>
                                            <th>Year Earned</th>
                                            <th>Year Expiry</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($certifications as $cert): ?>
                                            <?php
                                                $cid      = (int)$cert['id'];
                                                $cfac     = esc($cert['faculty_name'] ?? 'Unknown');
                                                $ctitle   = esc($cert['cert_title'] ?? 'Unknown');
                                                $cissuer  = esc($cert['issuer'] ?? 'Unknown');
                                                $cyEarned = esc($cert['year_earned'] ?? '');
                                                $cyExpiry = esc($cert['year_expiry'] ?? 'N/A');
                                                $cstatus  = esc($cert['status'] ?? 'Active');
                                                $cstatusLower = strtolower($cert['status'] ?? 'active');
                                                $isArchived = (int)($cert['is_archived'] ?? 0);
                                                $facultyIdCurrent = (int)($cert['faculty_id'] ?? 0);
                                                $certIdCurrent    = (int)($cert['certification_id'] ?? 0);
                                            ?>
                                            <tr>
                                                <td><strong><?= $cfac ?></strong></td>
                                                <td><strong><?= $ctitle ?></strong></td>
                                                <td><span class="badge badge--category"><?= $cissuer ?></span></td>
                                                <td><?= $cyEarned ?></td>
                                                <td><?= $cyExpiry ?></td>
                                                <td>
                                                    <span class="badge badge--status badge--<?= $cstatusLower ?>">
                                                        <?= $cstatus ?>
                                                        <?php if (($cert['status'] ?? '') === 'Expired'): ?>
                                                            <i class="fas fa-exclamation-triangle ms-1" title="This certification has expired"></i>
                                                        <?php elseif (($cert['status'] ?? '') === 'Revoked'): ?>
                                                            <i class="fas fa-ban ms-1" title="This certification has been revoked"></i>
                                                        <?php endif; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-warning" 
                                                                data-bs-toggle="modal" data-bs-target="#editCertificationModal<?= $cid ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <?php if ($isArchived): ?>
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="action" value="unarchive_certification">
                                                                <input type="hidden" name="id" value="<?= $cid ?>">
                                                                <button type="submit" class="btn btn-sm btn-info" title="Unarchive certification">
                                                                    <i class="fas fa-box-open"></i>
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <form method="POST" style="display: inline;" 
                                                                  onsubmit="return confirm('Archive this certification? It will be moved to the archived section.');">
                                                                <input type="hidden" name="action" value="archive_certification">
                                                                <input type="hidden" name="id" value="<?= $cid ?>">
                                                                <button type="submit" class="btn btn-sm btn-secondary" title="Archive certification">
                                                                    <i class="fas fa-archive"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>

                                                        <form method="POST" style="display: inline;" 
                                                              onsubmit="return confirm('Are you sure you want to permanently delete this certification award? This cannot be undone and will remove it from all records.');">
                                                            <input type="hidden" name="action" value="delete_certification">
                                                            <input type="hidden" name="id" value="<?= $cid ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Permanently Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php ob_start(); ?>
                                            <!-- Edit Certification Modal -->
                                            <div class="modal fade" id="editCertificationModal<?= $cid ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Certification Award</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST">
                                                            <input type="hidden" name="action" value="update_certification">
                                                            <input type="hidden" name="id" value="<?= $cid ?>">
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Faculty Member</label>
                                                                            <select class="form-control" name="faculty_id" required>
                                                                                <option value="">Select Faculty</option>
                                                                                <?php foreach ($facultyList as $faculty): ?>
                                                                                    <option value="<?= $faculty['id'] ?>" <?= (int)$faculty['id'] === $facultyIdCurrent ? 'selected' : '' ?>>
                                                                                        <?= esc($faculty['name']) ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Certification</label>
                                                                            <select class="form-control" name="certification_id" required>
                                                                                <option value="">Select Certification</option>
                                                                                <?php foreach ($availableCertifications as $availCert): ?>
                                                                                    <option value="<?= $availCert['id'] ?>" <?= (int)$availCert['id'] === $certIdCurrent ? 'selected' : '' ?>>
                                                                                        <?= esc($availCert['cert_title']) ?> (<?= esc($availCert['issuer']) ?>)
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Year Earned</label>
                                                                            <input type="text" class="form-control" name="year_earned" value="<?= $cyEarned ?>" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Year Expiry</label>
                                                                            <input type="text" class="form-control" name="year_expiry" value="<?= ($cert['year_expiry'] ?? '') ? esc($cert['year_expiry']) : '' ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Status</label>
                                                                            <select class="form-control" name="status" required>
                                                                                <option value="Active"  <?= ($cert['status'] ?? '') === 'Active'  ? 'selected' : '' ?>>Active</option>
                                                                                <option value="Expired" <?= ($cert['status'] ?? '') === 'Expired' ? 'selected' : '' ?>>Expired</option>
                                                                                <option value="Revoked" <?= ($cert['status'] ?? '') === 'Revoked' ? 'selected' : '' ?>>Revoked</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Update Certification</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php $__certCollectedModals .= ob_get_clean(); ?>
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

    <!-- Add Certification Modal (moved here intact so it stacks above content) -->
    <div class="modal fade" id="addCertificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add_certification">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Certification Award</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Faculty Member</label>
                                    <select class="form-control" name="faculty_id" required>
                                        <option value="">Select Faculty</option>
                                        <?php foreach ($facultyList as $faculty): ?>
                                            <option value="<?= $faculty['id'] ?>">
                                                <?= esc($faculty['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Certification</label>
                                    <select class="form-control" name="certification_id" required>
                                        <option value="">Select Certification</option>
                                        <?php foreach ($availableCertifications as $cert): ?>
                                            <option value="<?= $cert['id'] ?>">
                                                <?= esc($cert['cert_title']) ?> (<?= esc($cert['issuer']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Year Earned</label>
                                    <input type="text" class="form-control" name="year_earned" placeholder="2024" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Year Expiry</label>
                                    <input type="text" class="form-control" name="year_expiry" placeholder="2027">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Expired">Expired</option>
                                        <option value="Revoked">Revoked</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Certification Award
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Collected Approve/Reject/Edit modals for submissions and list rows -->
    <?= $__certCollectedModals ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
