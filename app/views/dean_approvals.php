<?php
// app/views/dean_approvals.php - Dean Approval Interface
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../lib/Auth.php';

// Check authentication using Auth class
if (!Auth::check() || !Auth::is('dean')) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

require_once __DIR__ . '/../models/FacultySubmissions.php';

// Helper function for escaping
if (!function_exists('esc')) {
    function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

// Add helper to decode JSON if valid
function try_decode_json($str) {
    $data = json_decode($str, true);
    return (json_last_error() === JSON_ERROR_NONE && is_array($data)) ? $data : null;
}

// Add helper to format content for modal display
function format_submission_content($type, $jsonContent) {
    // Define which fields to show for each type
    $fields = [
        'research' => [
            'type' => 'Type',
            'year' => 'Year',
            'authors' => 'Authors',
            'venue' => 'Venue',
            'description' => 'Description'
        ],
        'certification' => [
            'cert_title' => 'Title',
            'issuer' => 'Issuer',
            'year_earned' => 'Year Earned',
            'year_expiry' => 'Year Expiry',
            'description' => 'Description'
        ],
        'news' => [
            'headline' => 'Headline',
            'date' => 'Date',
            'description' => 'Description'
        ]
    ];
    $output = '';
    if (isset($fields[$type])) {
        $output .= '<table class="table table-sm table-bordered mb-0"><tbody>';
        foreach ($fields[$type] as $key => $label) {
            if (!empty($jsonContent[$key])) {
                $output .= '<tr><th style="width:30%;">' . esc($label) . '</th><td>' . esc($jsonContent[$key]) . '</td></tr>';
            }
        }
        $output .= '</tbody></table>';
    } else {
        // fallback: show all fields
        $output .= '<table class="table table-sm table-bordered mb-0"><tbody>';
        foreach ($jsonContent as $key => $value) {
            $output .= '<tr><th style="width:30%;">' . esc(ucwords(str_replace('_', ' ', $key))) . '</th><td>' . esc(is_array($value) ? json_encode($value) : $value) . '</td></tr>';
        }
        $output .= '</tbody></table>';
    }
    return $output;
}

$user = Auth::user();
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
            
            $success = FacultySubmissions::updateStatus($submissionId, $status, $reviewerId, $reviewNotes);
            
            if ($success) {
                $notice = ucfirst($action) . 'd submission successfully!';
            } else {
                $notice = 'Error: Failed to ' . $action . ' submission.';
            }
        } catch (Exception $e) {
            $notice = 'Error: ' . $e->getMessage();
        }
    }
}

// Get pending submissions for dean review
try {
    $allSubmissions = FacultySubmissions::getAllSubmissionsWithFacultyDetails();
    
    // Filter to pending submissions (submitted or under_review) and include all approval types
    $pendingSubmissions = array_filter($allSubmissions, function($submission) {
        return in_array($submission['status'], ['submitted', 'under_review']) && 
               in_array($submission['submission_type'], ['research', 'certification', 'news']);
    });
    
    // Get counts by type for dashboard
    $counts = [
        'total' => count($pendingSubmissions),
        'research' => count(array_filter($pendingSubmissions, fn($s) => $s['submission_type'] === 'research')),
        'certification' => count(array_filter($pendingSubmissions, fn($s) => $s['submission_type'] === 'certification')),
        'news' => count(array_filter($pendingSubmissions, fn($s) => $s['submission_type'] === 'news'))
    ];
    
    // Sort by most recent first
    usort($pendingSubmissions, function($a, $b) {
        return strtotime($b['submitted_at'] ?? '0') - strtotime($a['submitted_at'] ?? '0');
    });
    
} catch (Exception $e) {
    $pendingSubmissions = [];
    $notice = 'Error loading submissions: ' . $e->getMessage();
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
        <?php include __DIR__ . '/dean/_dean_sidebar.php'; ?>
        
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
                        <p class="text-muted mb-0">Review and approve faculty submissions awaiting your decision</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="badge bg-warning">
                            <i class="fas fa-clock me-1"></i>
                            <?= $counts['total'] ?> Total Pending
                        </div>
                        <div class="badge bg-success">
                            <i class="fas fa-certificate me-1"></i>
                            <?= $counts['certification'] ?> Certifications
                        </div>
                        <div class="badge bg-info">
                            <i class="fas fa-microscope me-1"></i>
                            <?= $counts['research'] ?> Research
                        </div>
                        <div class="badge bg-primary">
                            <i class="fas fa-newspaper me-1"></i>
                            <?= $counts['news'] ?> News
                        </div>
                    </div>
                </div>

                <!-- Quick Action Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i class="fas fa-certificate fa-2x text-success mb-2"></i>
                                <h5 class="card-title">Certifications</h5>
                                <p class="card-text"><?= $counts['certification'] ?> pending review</p>
                                <a href="?page=dean_certifications" class="btn btn-success btn-sm">
                                    <i class="fas fa-arrow-right"></i> Review Certifications
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <i class="fas fa-microscope fa-2x text-warning mb-2"></i>
                                <h5 class="card-title">Research</h5>
                                <p class="card-text"><?= $counts['research'] ?> pending review</p>
                                <a href="?page=dean_research_approvals" class="btn btn-warning btn-sm">
                                    <i class="fas fa-arrow-right"></i> Review Research
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <i class="fas fa-newspaper fa-2x text-info mb-2"></i>
                                <h5 class="card-title">News & Content</h5>
                                <p class="card-text"><?= $counts['news'] ?> pending review</p>
                                <a href="?page=dean_news_approvals" class="btn btn-info btn-sm">
                                    <i class="fas fa-arrow-right"></i> Review News
                                </a>
                            </div>
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

                <!-- Pending Submissions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tasks me-2"></i>
                            Submissions Awaiting Review (<?= count($pendingSubmissions) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pendingSubmissions)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No submissions pending approval at this time.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Faculty</th>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Submitted</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingSubmissions as $submission): ?>
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
                                                    <span class="badge bg-<?php 
                                                        switch($submission['submission_type']) {
                                                            case 'research': echo 'warning'; break;
                                                            case 'certification': echo 'success'; break;
                                                            case 'news': echo 'info'; break;
                                                            default: echo 'secondary';
                                                        }
                                                    ?>">
                                                        <i class="fas fa-<?php 
                                                            switch($submission['submission_type']) {
                                                                case 'research': echo 'microscope'; break;
                                                                case 'certification': echo 'certificate'; break;
                                                                case 'news': echo 'newspaper'; break;
                                                                default: echo 'file';
                                                            }
                                                        ?>"></i>
                                                        <?= esc(ucfirst($submission['submission_type'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= $submission['submitted_at'] ? date('M j, Y', strtotime($submission['submitted_at'])) : '' ?>
                                                    <?php if ($submission['submitted_at']): ?>
                                                        <br><small class="text-muted"><?= date('g:i A', strtotime($submission['submitted_at'])) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock"></i>
                                                        <?= esc(ucfirst($submission['status'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                                data-bs-toggle="modal" data-bs-target="#reviewModal<?= $submission['id'] ?>" 
                                                                title="Review & Decide">
                                                            <i class="fas fa-eye"></i>
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

    <!-- Review Modals -->
    <?php foreach ($pendingSubmissions as $submission): ?>
        <div class="modal fade" id="reviewModal<?= $submission['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Review Submission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6><strong>Title:</strong></h6>
                                <p><?= esc($submission['title']) ?></p>
                                
                                <h6><strong>Faculty:</strong></h6>
                                <p><?= esc($submission['faculty_name']) ?> (<?= esc($submission['dept']) ?>)</p>
                                
                                <h6><strong>Type:</strong></h6>
                                <p><span class="badge bg-<?php 
                                    switch($submission['submission_type']) {
                                        case 'research': echo 'warning'; break;
                                        case 'certification': echo 'success'; break;
                                        case 'news': echo 'info'; break;
                                        default: echo 'secondary';
                                    }
                                ?>"><?= esc(ucfirst($submission['submission_type'])) ?></span></p>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Status:</strong></h6>
                                <p><span class="badge bg-warning"><?= esc(ucfirst($submission['status'])) ?></span></p>
                                
                                <h6><strong>Submitted:</strong></h6>
                                <p><?= $submission['submitted_at'] ? date('M j, Y g:i A', strtotime($submission['submitted_at'])) : 'Not submitted' ?></p>
                            </div>
                        </div>
                        
                        <?php if ($submission['description']): ?>
                            <h6><strong>Description:</strong></h6>
                            <div class="border p-3 rounded mb-3"><?= nl2br(esc($submission['description'])) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($submission['content']): ?>
                            <h6><strong>Content:</strong></h6>
                            <?php $jsonContent = try_decode_json($submission['content']); ?>
                            <?php if ($jsonContent): ?>
                                <div class="border p-3 rounded mb-3" style="max-height: 200px; overflow-y: auto;">
                                    <?= format_submission_content($submission['submission_type'], $jsonContent) ?>
                            </div>
                            <?php else: ?>
                                <div class="border p-3 rounded mb-3" style="max-height: 200px; overflow-y: auto;"><?= nl2br(esc($submission['content'])) ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <!-- Review Form -->
                        <form method="post" action="?page=dean_approvals">
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
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
