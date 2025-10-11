<?php
// app/views/dean/dean_manage_announcements.php - Dean Announcements Management
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../lib/Auth.php';

// Check authentication using Auth class
if (!Auth::check() || !Auth::is('dean')) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

require_once __DIR__ . '/../../models/Announcement.php';
require_once __DIR__ . '/../../models/DeanLogs.php';
require_once __DIR__ . '/../../models/FacultySubmissions.php';

// Helper function for escaping
if (!function_exists('esc')) {
    function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = Auth::user();
$username = $user['username'] ?? 'Dean';
$notice = '';

// Get current data
try {
    $status = $_GET['status'] ?? 'all';
    $announcements = Announcement::list($status);
    $counts = Announcement::statusCounts();
    
    // Get pending faculty announcement submissions
    $pendingAnnouncementSubmissions = FacultySubmissions::getPendingByType('announcement');
} catch (Exception $e) {
    $notice = 'Error loading data: ' . $e->getMessage();
    $announcements = [];
    $counts = ['all' => 0, 'draft' => 0, 'published' => 0, 'archived' => 0];
    $pendingAnnouncementSubmissions = [];
}

/* ---------- Handle POST (CRUD) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle faculty submission approval/rejection
        if (!empty($_POST['faculty_action']) && !empty($_POST['submission_id'])) {
            $submissionId = (int)$_POST['submission_id'];
            $action = $_POST['faculty_action'];
            $reviewNotes = trim($_POST['review_notes'] ?? '');
            
            if ($action === 'approve') {
                FacultySubmissions::updateStatus($submissionId, 'approved', $user['id'] ?? null, $reviewNotes);
                $notice = 'Faculty announcement submission approved and published successfully!';
                DeanLogs::logApprove('faculty_submissions', $submissionId, $user['id'] ?? null, "Approved announcement submission: " . substr($reviewNotes, 0, 100));
            } elseif ($action === 'reject') {
                FacultySubmissions::updateStatus($submissionId, 'rejected', $user['id'] ?? null, $reviewNotes);
                $notice = 'Faculty announcement submission rejected.';
                DeanLogs::logReject('faculty_submissions', $submissionId, $user['id'] ?? null, "Rejected announcement submission: " . substr($reviewNotes, 0, 100));
            }
        }

        // Add new announcement
        if (!empty($_POST['add_announcement']) && !empty($_POST['title'])) {
            $title = trim($_POST['title']);
            $content = trim($_POST['content'] ?? '');
            $category = $_POST['category'] ?? 'general';
            $nstatus = $_POST['status'] ?? 'draft';
            $date = $_POST['date'] ?? date('Y-m-d');
            $imageUrl = null;

            // Handle image upload
            if (!empty($_FILES['image']['tmp_name'])) {
                $imgTmp = $_FILES['image']['tmp_name'];
                $imgName = $_FILES['image']['name'];
                $imgExt = pathinfo($imgName, PATHINFO_EXTENSION);
                $newName = 'announcement_' . time() . '_' . rand(1000, 9999) . '.' . $imgExt;
                $uploadPath = __DIR__ . '/../../../public/assets/images/announcements/' . $newName;

                // Create directory if it doesn't exist
                $uploadDir = dirname($uploadPath);
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($imgTmp, $uploadPath)) {
                    $imageUrl = '/adamson-ccit/public/assets/images/announcements/' . $newName;
                }
            }

            $aid = Announcement::create($title, $content, $nstatus, $category, $date, $imageUrl);
            
            // Log announcement creation
            if ($aid) {
                DeanLogs::logCreate('announcements', $aid, $user['id'] ?? null, "Created announcement: {$title}");
            }
            $notice = $aid ? 'Announcement added successfully!' : 'Failed to add announcement.';
        }

        // Edit announcement
        if (!empty($_POST['edit_announcement']) && !empty($_POST['id'])) {
            $id = (int)$_POST['id'];
            $title = trim($_POST['title']);
            $content = trim($_POST['content'] ?? '');
            $category = $_POST['category'] ?? 'general';
            $date = $_POST['date'] ?? date('Y-m-d');
            $nstatus = $_POST['status'] ?? 'draft';

            $updateData = [
                'title' => $title,
                'content' => $content,
                'category' => $category,
                'date' => $date,
                'status' => $nstatus
            ];

            // Handle image upload for edit
            if (!empty($_FILES['image']['tmp_name'])) {
                $imgTmp = $_FILES['image']['tmp_name'];
                $imgName = $_FILES['image']['name'];
                $imgExt = pathinfo($imgName, PATHINFO_EXTENSION);
                $newName = 'announcement_' . time() . '_' . rand(1000, 9999) . '.' . $imgExt;
                $uploadPath = __DIR__ . '/../../../public/assets/images/announcements/' . $newName;

                // Create directory if it doesn't exist
                $uploadDir = dirname($uploadPath);
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($imgTmp, $uploadPath)) {
                    $updateData['image_url'] = '/adamson-ccit/public/assets/images/announcements/' . $newName;
                }
            }

            if (Announcement::update($id, $updateData)) {
                DeanLogs::logUpdate('announcements', $id, $user['id'] ?? null, "Updated announcement: {$title}");
                $notice = 'Announcement updated successfully!';
            } else {
                $notice = 'Failed to update announcement.';
            }
        }

        // Delete announcement
        if (!empty($_POST['delete_announcement']) && !empty($_POST['id'])) {
            $id = (int)$_POST['id'];
            
            // Get announcement title before deletion for logging
            $announcementToDelete = Announcement::get($id);
            $announcementTitle = $announcementToDelete['title'] ?? "Announcement ID {$id}";
            
            if (Announcement::delete($id)) {
                DeanLogs::logDelete($user['id'] ?? null, 'announcements', $id, "Deleted announcement: {$announcementTitle}");
                $notice = 'Announcement deleted successfully!';
            } else {
                $notice = 'Failed to delete announcement.';
            }
        }

        // Update status
        if (!empty($_POST['update_status']) && !empty($_POST['id'])) {
            $id = (int)$_POST['id'];
            $newStatus = $_POST['update_status'];
            
            // Get announcement title for logging
            $announcement = Announcement::get($id);
            $announcementTitle = $announcement['title'] ?? "Announcement ID {$id}";
            
            if (Announcement::updateStatus($id, $newStatus)) {
                DeanLogs::logUpdate('announcements', $id, $user['id'] ?? null, "Updated status of '{$announcementTitle}' to {$newStatus}");
                $notice = 'Status updated successfully!';
            } else {
                $notice = 'Failed to update status.';
            }
        }

        // Refresh data after operations
        $announcements = Announcement::list($status);
        $counts = Announcement::statusCounts();

    } catch (Exception $e) {
        $notice = 'Error: ' . $e->getMessage();
    }
}
?>

<?php
// Buffer for collecting modal markup (printed once at end of <body>)
$__annCollectedModals = '';

// Ensure esc() exists
if (!function_exists('esc')) {
    function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements | CCIT Dean</title>
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
                <span class="admin-topbar__title">CCIT Announcements Management</span>
                <span class="admin-topbar__spacer"></span>
            </header>
            <section class="admin-section">

                <!-- Enhanced message handling -->
                <?php if (!empty($notice)): ?>
                    <div class="alert <?= (str_starts_with($notice, 'Error') || str_starts_with($notice, 'Failed')) ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show modern-alert" role="alert">
                        <i class="fas <?= (str_starts_with($notice, 'Error') || str_starts_with($notice, 'Failed')) ? 'fa-exclamation-circle' : 'fa-check-circle' ?>"></i>
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
                                    <a class="nav-link <?= ($status ?? 'all') === 'all' ? 'active' : '' ?>" href="?page=dean_manage_announcements&status=all">
                                        All Announcements (<?= (int)($counts['all'] ?? 0) ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= ($status ?? '') === 'published' ? 'active' : '' ?>" href="?page=dean_manage_announcements&status=published">
                                        Published (<?= (int)($counts['published'] ?? 0) ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= ($status ?? '') === 'draft' ? 'active' : '' ?>" href="?page=dean_manage_announcements&status=draft">
                                        Draft (<?= (int)($counts['draft'] ?? 0) ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= ($status ?? '') === 'archived' ? 'active' : '' ?>" href="?page=dean_manage_announcements&status=archived">
                                        Archived (<?= (int)($counts['archived'] ?? 0) ?>)
                                    </a>
                                </li>
                            </ul>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                                <i class="fas fa-plus me-2"></i>Add Announcement
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Faculty Announcement Submissions for Approval -->
                <?php if (!empty($pendingAnnouncementSubmissions)): ?>
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock text-warning me-2"></i>
                            Pending Faculty Announcement Submissions (<?= count($pendingAnnouncementSubmissions) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Faculty</th>
                                        <th>Announcement Title</th>
                                        <th>Category</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingAnnouncementSubmissions as $submission): ?>
                                        <?php
                                            $sid   = (int)$submission['id'];
                                            $sfac  = esc($submission['faculty_name'] ?? 'Unknown Faculty');
                                            $sdept = esc($submission['department_name'] ?? '');
                                            $stitle= esc($submission['title'] ?? '');
                                            $scat  = esc($submission['category'] ?? 'Announcement');
                                            $ssub  = !empty($submission['submitted_at']) ? date('M j, Y g:i A', strtotime($submission['submitted_at'])) : '';
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?= $sfac ?></strong>
                                                <br><small class="text-muted"><?= $sdept ?></small>
                                            </td>
                                            <td><strong><?= $stitle ?></strong></td>
                                            <td><span class="badge badge--category"><?= $scat ?></span></td>
                                            <td><small><?= esc($ssub) ?></small></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-success btn-sm" 
                                                            data-bs-toggle="modal" data-bs-target="#approveAnnouncementModal<?= $sid ?>">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            data-bs-toggle="modal" data-bs-target="#rejectAnnouncementModal<?= $sid ?>">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                            // Collect approve/reject modals for this submission (printed at end of body)
                                            ob_start();
                                        ?>
                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveAnnouncementModal<?= $sid ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Approve Announcement Submission</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="faculty_action" value="approve">
                                                            <input type="hidden" name="submission_id" value="<?= $sid ?>">
                                                            <p>Approve "<strong><?= $stitle ?></strong>" by <?= $sfac ?>?</p>
                                                            <p class="text-muted">This will publish the announcement immediately.</p>
                                                            <div class="mb-3">
                                                                <label class="form-label">Review Notes (Optional)</label>
                                                                <textarea class="form-control" name="review_notes" rows="3" placeholder="Add any feedback or notes..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success">Approve &amp; Publish</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectAnnouncementModal<?= $sid ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Announcement Submission</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="faculty_action" value="reject">
                                                            <input type="hidden" name="submission_id" value="<?= $sid ?>">
                                                            <p>Reject "<strong><?= $stitle ?></strong>" by <?= $sfac ?>?</p>
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
                                        <?php
                                            $__annCollectedModals .= ob_get_clean();
                                        ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Announcements List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bullhorn me-2"></i>Announcements (<?= count($announcements ?? []) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($announcements)): ?>
                            <div class="empty-state-card">
                                <i class="fas fa-bullhorn fa-3x"></i>
                                <h6>No Announcements Found</h6>
                                <div class="text-muted">Start by adding an announcement using the <strong>Add Announcement</strong> button above.</div>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Announcement</th>
                                            <th>Category</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($announcements as $announcement): ?>
                                            <?php
                                                $aid     = (int)$announcement['id'];
                                                $atitle  = esc($announcement['title'] ?? '');
                                                $acat    = esc($announcement['category'] ?? 'General');
                                                $adate   = $announcement['date'] ?? '';
                                                $adisp   = $adate ? date('M j, Y', strtotime($adate)) : '';
                                                $astatus = esc($announcement['status'] ?? 'draft');
                                                $abody   = esc($announcement['body'] ?? '');
                                                $hasImg  = !empty($announcement['image_url'] ?? '');
                                            ?>
                                            <tr>
                                                <td><strong><?= $atitle ?></strong></td>
                                                <td><span class="badge badge--category"><?= $acat ?></span></td>
                                                <td><?= esc($adisp) ?></td>
                                                <td>
                                                    <span class="badge badge--status badge--<?= $astatus ?>">
                                                        <?= $astatus ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-warning" 
                                                                data-bs-toggle="modal" data-bs-target="#editAnnouncementModal<?= $aid ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form method="POST" style="display: inline;" 
                                                              onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                                            <input type="hidden" name="delete_announcement" value="1">
                                                            <input type="hidden" name="id" value="<?= $aid ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                                // Collect edit modal for this announcement (printed at end of body)
                                                ob_start();
                                            ?>
                                            <!-- Edit Announcement Modal -->
                                            <div class="modal fade" id="editAnnouncementModal<?= $aid ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Announcement</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" enctype="multipart/form-data">
                                                            <input type="hidden" name="edit_announcement" value="1">
                                                            <input type="hidden" name="id" value="<?= $aid ?>">
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Title</label>
                                                                            <input type="text" class="form-control" name="title" 
                                                                                value="<?= $atitle ?>" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Content</label>
                                                                            <textarea class="form-control" name="content" rows="6"><?= $abody ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Category</label>
                                                                            <select class="form-control" name="category">
                                                                                <option value="general"      <?= (($announcement['category'] ?? '') === 'general') ? 'selected' : '' ?>>General</option>
                                                                                <option value="academic"     <?= (($announcement['category'] ?? '') === 'academic') ? 'selected' : '' ?>>Academic</option>
                                                                                <option value="registration" <?= (($announcement['category'] ?? '') === 'registration') ? 'selected' : '' ?>>Registration</option>
                                                                                <option value="deadline"     <?= (($announcement['category'] ?? '') === 'deadline') ? 'selected' : '' ?>>Deadline</option>
                                                                                <option value="holiday"      <?= (($announcement['category'] ?? '') === 'holiday') ? 'selected' : '' ?>>Holiday</option>
                                                                                <option value="emergency"    <?= (($announcement['category'] ?? '') === 'emergency') ? 'selected' : '' ?>>Emergency</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Date</label>
                                                                            <input type="date" class="form-control" name="date" value="<?= esc($announcement['date'] ?? '') ?>">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Status</label>
                                                                            <select class="form-control" name="status">
                                                                                <option value="draft"     <?= (($announcement['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Draft</option>
                                                                                <option value="published" <?= (($announcement['status'] ?? '') === 'published') ? 'selected' : '' ?>>Published</option>
                                                                                <option value="archived"  <?= (($announcement['status'] ?? '') === 'archived') ? 'selected' : '' ?>>Archived</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Update Image</label>
                                                                            <input type="file" class="form-control" name="image" accept="image/*">
                                                                            <?php if ($hasImg): ?>
                                                                                <small class="text-muted">Current image will be replaced if new one is uploaded</small>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Update Announcement</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                                $__annCollectedModals .= ob_get_clean();
                                            ?>
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

    <!-- Add Announcement Modal (moved here, unchanged) -->
    <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="add_announcement" value="1">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Announcement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Announcement Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="content" class="form-label">Content</label>
                                    <textarea class="form-control" id="content" name="content" rows="6"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="general">General</option>
                                        <option value="academic">Academic</option>
                                        <option value="registration">Registration</option>
                                        <option value="deadline">Deadline</option>
                                        <option value="holiday">Holiday</option>
                                        <option value="emergency">Emergency</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date" name="date" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label">Announcement Image</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Collected approve/reject/edit modals -->
    <?= $__annCollectedModals ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
