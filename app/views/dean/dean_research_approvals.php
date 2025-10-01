<?php
// app/views/dean/dean_research_approvals.php - Dean Research Approval Interface
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['dean'], true)) {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../../models/FacultySubmissions.php';
require_once __DIR__ . '/../../models/FacultyResearch.php';

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
            
            // If approving, also publish to FacultyResearch table
            if ($action === 'approve') {
                $submission = FacultySubmissions::getById($submissionId);
                if ($submission && $submission['submission_type'] === 'research') {
                    // Decode the research data from JSON content
                    $researchData = json_decode($submission['content'] ?? '{}', true) ?: [];
                    
                    // Prepare data for FacultyResearch table
                    $researchForPublication = [
                        'title' => $submission['title'],
                        'dept' => $researchData['dept'] ?? '',
                        'type' => $researchData['type'] ?? 'journal',
                        'year' => $researchData['year'] ?? date('Y'),
                        'authors' => $researchData['authors'] ?? '',
                        'venue' => $researchData['venue'] ?? '',
                        'pdf_url' => $researchData['pdf_url'] ?? '',
                        'view_url' => $researchData['view_url'] ?? '',
                        'image_url' => $researchData['image_url'] ?? ''
                    ];
                    
                    // Create the published research entry
                    FacultyResearch::create($researchForPublication);
                }
            }
            
            $success = FacultySubmissions::updateStatus($submissionId, $status, $reviewerId, $reviewNotes);
            
            if ($success) {
                $notice = $action === 'approve' ? 'Research approved and published successfully!' : 'Research rejected successfully!';
            } else {
                $notice = 'Error: Failed to ' . $action . ' research.';
            }
        } catch (Exception $e) {
            $notice = 'Error: ' . $e->getMessage();
        }
    }
}

// Get research submissions
try {
    $researchSubmissions = FacultySubmissions::getSubmissionsByType('research');
    
    // Sort by most recent first
    usort($researchSubmissions, function($a, $b) {
        return strtotime($b['submitted_at'] ?? '0') - strtotime($a['submitted_at'] ?? '0');
    });
    
    // Filter submissions by status
    $pendingResearch = array_filter($researchSubmissions, function($sub) {
        return in_array($sub['status'], ['submitted', 'under_review']);
    });
    
    $approvedResearch = array_filter($researchSubmissions, function($sub) {
        return $sub['status'] === 'approved';
    });
    
    $rejectedResearch = array_filter($researchSubmissions, function($sub) {
        return $sub['status'] === 'rejected';
    });
    
} catch (Exception $e) {
    $researchSubmissions = [];
    $pendingResearch = [];
    $approvedResearch = [];
    $rejectedResearch = [];
    $notice = 'Error loading research submissions: ' . $e->getMessage();
}
?>

<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
.card {
  border-radius: 15px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
.table th {
  font-weight: 600;
  font-size: 0.875rem;
  letter-spacing: 0.5px;
}
.btn {
  border-radius: 8px;
  font-weight: 500;
}
.modal-content {
  border-radius: 15px;
}
.alert {
  border-radius: 10px;
}
.badge {
  font-size: 0.75rem;
  padding: 0.375rem 0.75rem;
}
</style>

<div class="admin-cms-layout">
    <?php include __DIR__ . '/_dean_sidebar.php'; ?>
    
    <main class="admin-main">
        <header class="admin-topbar">
            <span class="admin-topbar__title">Research Approvals</span>
            <div class="admin-topbar__spacer"></div>
            <div class="admin-topbar__user">
                <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'D')) ?></span>
                <span class="admin-topbar__name"><?= esc($username) ?></span>
            </div>
        </header>

        <section class="admin-cms-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="admin-cms-section__title mb-1">Research Approval Center</h1>
                    <p class="text-muted mb-0">Review and approve faculty research submissions</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="?page=dean_manage_research" class="btn btn-outline-primary">
                        <i class="fas fa-microscope"></i> Manage Research
                    </a>
                </div>
            </div>
            
            <?php if ($notice): ?>
                <div class="alert alert-<?= str_starts_with($notice, 'Error:') ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
                    <?= esc($notice) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tabs for Different Research States -->
            <ul class="nav nav-tabs mb-4" id="researchTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                        <i class="fas fa-clock me-1"></i>
                        Pending (<?= count($pendingResearch) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                        <i class="fas fa-check me-1"></i>
                        Approved (<?= count($approvedResearch) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                        <i class="fas fa-times me-1"></i>
                        Rejected (<?= count($rejectedResearch) ?>)
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="researchTabContent">
                <!-- Pending Research -->
                <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <?php if (empty($pendingResearch)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No research submissions pending approval at this time.
                        </div>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Research</th>
                                                <th>Faculty</th>
                                                <th>Submitted</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pendingResearch as $submission): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= esc($submission['title']) ?></strong>
                                                        <?php
                                                        $researchData = json_decode($submission['content'] ?? '{}', true) ?: [];
                                                        if (!empty($researchData['authors'])): ?>
                                                            <br><small class="text-muted">Authors: <?= esc($researchData['authors']) ?></small>
                                                        <?php endif; ?>
                                                        <?php if (!empty($submission['description'])): ?>
                                                            <br><small class="text-muted"><?= esc(substr($submission['description'], 0, 100)) ?>...</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary"><?= esc($submission['faculty_name'] ?? 'Unknown') ?></span>
                                                    </td>
                                                    <td>
                                                        <small><?= $submission['submitted_at'] ? date('M j, Y g:i A', strtotime($submission['submitted_at'])) : '-' ?></small>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-success me-1" data-bs-toggle="modal" data-bs-target="#approveModal<?= $submission['id'] ?>">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $submission['id'] ?>">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Approved Research -->
                <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                    <?php if (empty($approvedResearch)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No approved research found.
                        </div>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Research</th>
                                                <th>Faculty</th>
                                                <th>Approved</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($approvedResearch as $submission): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= esc($submission['title']) ?></strong>
                                                        <span class="badge bg-success ms-2">Published</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary"><?= esc($submission['faculty_name'] ?? 'Unknown') ?></span>
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
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Rejected Research -->
                <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                    <?php if (empty($rejectedResearch)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No rejected research found.
                        </div>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Research</th>
                                                <th>Faculty</th>
                                                <th>Rejected</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($rejectedResearch as $submission): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= esc($submission['title']) ?></strong>
                                                        <span class="badge bg-danger ms-2">Rejected</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary"><?= esc($submission['faculty_name'] ?? 'Unknown') ?></span>
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
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Approval/Rejection Modals -->
            <?php foreach ($pendingResearch as $submission): ?>
                <!-- Approve Modal -->
                <div class="modal fade" id="approveModal<?= $submission['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Approve Research</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    
                                    <p><strong>Title:</strong> <?= esc($submission['title']) ?></p>
                                    <p><strong>Faculty:</strong> <?= esc($submission['faculty_name'] ?? 'Unknown') ?></p>
                                    
                                    <?php if (!empty($submission['description'])): ?>
                                        <p><strong>Abstract:</strong></p>
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
                                <h5 class="modal-title">Reject Research</h5>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>