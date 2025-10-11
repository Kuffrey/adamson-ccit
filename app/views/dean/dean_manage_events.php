<?php
// app/views/dean/dean_manage_events.php - Dean Events Management
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../lib/Auth.php';

// Check authentication using Auth class
if (!Auth::check() || !Auth::is('dean')) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

require_once __DIR__ . '/../../models/Event.php';
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
    $events = Event::list($status);
    $counts = Event::statusCounts();
    
    // Get pending faculty event submissions
    $pendingEventSubmissions = FacultySubmissions::getPendingByType('event');
} catch (Exception $e) {
    $notice = 'Error loading data: ' . $e->getMessage();
    $events = [];
    $counts = ['all' => 0, 'draft' => 0, 'published' => 0, 'archived' => 0];
    $pendingEventSubmissions = [];
}

// Helper function for dates
function dtfix(?string $v): ?string {
    $v = trim((string)$v);
    if ($v === '') return null;
    $v = str_replace('T', ' ', $v);
    if (strlen($v) === 16) $v .= ':00';
    return $v;
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
                $notice = 'Faculty event submission approved and published successfully!';
                DeanLogs::logApprove('faculty_submissions', $submissionId, $user['id'] ?? null, "Approved event submission: " . substr($reviewNotes, 0, 100));
            } elseif ($action === 'reject') {
                FacultySubmissions::updateStatus($submissionId, 'rejected', $user['id'] ?? null, $reviewNotes);
                $notice = 'Faculty event submission rejected.';
                DeanLogs::logReject('faculty_submissions', $submissionId, $user['id'] ?? null, "Rejected event submission: " . substr($reviewNotes, 0, 100));
            }
        }

        // Add new event
        if (!empty($_POST['add_event']) && !empty($_POST['title'])) {
            $title = trim($_POST['title']);
            $description = trim($_POST['description'] ?? '');
            $category = $_POST['category'] ?? 'career';
            $location = trim($_POST['location'] ?? '');
            $registration = trim($_POST['registration_url'] ?? '');
            $start = dtfix($_POST['start_at'] ?? null);
            $end = dtfix($_POST['end_at'] ?? null);
            $nstatus = $_POST['status'] ?? 'draft';
            $imageUrl = null;

            // Handle image upload
            if (!empty($_FILES['image']['tmp_name'])) {
                $imgTmp = $_FILES['image']['tmp_name'];
                $imgName = $_FILES['image']['name'];
                $imgExt = pathinfo($imgName, PATHINFO_EXTENSION);
                $newName = 'event_' . time() . '_' . rand(1000, 9999) . '.' . $imgExt;
                $uploadPath = __DIR__ . '/../../../public/assets/images/events/' . $newName;

                // Create directory if it doesn't exist
                $uploadDir = dirname($uploadPath);
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($imgTmp, $uploadPath)) {
                    $imageUrl = '/adamson-ccit/public/assets/images/events/' . $newName;
                }
            }

            Event::create([
                'title' => $title,
                'description' => $description,
                'category' => $category,
                'location' => $location,
                'registration_url' => $registration,
                'start_at' => $start,
                'end_at' => $end,
                'status' => $nstatus,
                'image_url' => $imageUrl,
                'created_by' => $username
            ]);
            
            // Log event creation
            DeanLogs::logCreate('events', null, $user['id'] ?? null, "Created event: {$title}");
            $notice = 'Event added successfully!';
        }

        // Edit existing event
        if (!empty($_POST['edit_event']) && !empty($_POST['id']) && !empty($_POST['title'])) {
            $id = (int)$_POST['id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description'] ?? '');
            $category = $_POST['category'] ?? 'career';
            $location = trim($_POST['location'] ?? '');
            $registration = trim($_POST['registration_url'] ?? '');
            $start = dtfix($_POST['start_at'] ?? null);
            $end = dtfix($_POST['end_at'] ?? null);
            $nstatus = $_POST['status'] ?? 'draft';

            $updateData = [
                'title' => $title,
                'description' => $description,
                'category' => $category,
                'location' => $location,
                'registration_url' => $registration,
                'start_at' => $start,
                'end_at' => $end,
                'status' => $nstatus
            ];

            // Handle image upload for edit
            if (!empty($_FILES['image']['tmp_name'])) {
                $imgTmp = $_FILES['image']['tmp_name'];
                $imgName = $_FILES['image']['name'];
                $imgExt = pathinfo($imgName, PATHINFO_EXTENSION);
                $newName = 'event_' . time() . '_' . rand(1000, 9999) . '.' . $imgExt;
                $uploadPath = __DIR__ . '/../../../public/assets/images/events/' . $newName;

                // Create directory if it doesn't exist
                $uploadDir = dirname($uploadPath);
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($imgTmp, $uploadPath)) {
                    $updateData['image_url'] = '/adamson-ccit/public/assets/images/events/' . $newName;
                }
            }

            Event::update($id, $updateData);
            
            // Log event update
            DeanLogs::logUpdate('events', $id, $user['id'] ?? null, "Updated event: {$title}");
            $notice = 'Event updated successfully!';
        }

        // Delete event
        if (!empty($_POST['delete_event']) && !empty($_POST['id'])) {
            $id = (int)$_POST['id'];
            
            // Get event title before deletion for logging
            $eventToDelete = Event::get($id);
            $eventTitle = $eventToDelete['title'] ?? "Event ID {$id}";
            
            Event::delete($id);
            
            // Log event deletion
            DeanLogs::logDelete('events', $id, $user['id'] ?? null, "Deleted event: {$eventTitle}");
            $notice = 'Event deleted successfully!';
        }

        // Refresh data after operations
        $events = Event::list($status);
        $counts = Event::statusCounts();

    } catch (Exception $e) {
        $notice = 'Error: ' . $e->getMessage();
    }
}
?>

<?php
// We only collect modal markup here and print it at the end of <body>.
$__eventsCollectedModals = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events | CCIT Dean</title>
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
                <span class="admin-topbar__title">CCIT Events Management</span>
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
                                    <a class="nav-link <?= $status === 'all' ? 'active' : '' ?>" href="?page=dean_manage_events&status=all">
                                        All Events (<?= $counts['all'] ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $status === 'published' ? 'active' : '' ?>" href="?page=dean_manage_events&status=published">
                                        Published (<?= $counts['published'] ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $status === 'draft' ? 'active' : '' ?>" href="?page=dean_manage_events&status=draft">
                                        Draft (<?= $counts['draft'] ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $status === 'archived' ? 'active' : '' ?>" href="?page=dean_manage_events&status=archived">
                                        Archived (<?= $counts['archived'] ?>)
                                    </a>
                                </li>
                            </ul>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                                <i class="fas fa-plus me-2"></i>Add Event
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Faculty Event Submissions for Approval -->
                <?php if (!empty($pendingEventSubmissions)): ?>
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock text-warning me-2"></i>
                            Pending Faculty Event Submissions (<?= count($pendingEventSubmissions) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Faculty</th>
                                        <th>Event Title</th>
                                        <th>Category</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingEventSubmissions as $submission): ?>
                                        <?php
                                            $sid = (int)$submission['id'];
                                            $sfac = esc($submission['faculty_name'] ?? 'Unknown Faculty');
                                            $sdept = esc($submission['department_name'] ?? '');
                                            $stitle = esc($submission['title']);
                                            $scat = esc($submission['category'] ?? 'Event');
                                            $ssub = date('M j, Y g:i A', strtotime($submission['submitted_at']));
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?= $sfac ?></strong>
                                                <br><small class="text-muted"><?= $sdept ?></small>
                                            </td>
                                            <td>
                                                <strong><?= $stitle ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge badge--category"><?= $scat ?></span>
                                            </td>
                                            <td>
                                                <small><?= $ssub ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-success btn-sm" 
                                                            data-bs-toggle="modal" data-bs-target="#approveEventModal<?= $sid ?>">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            data-bs-toggle="modal" data-bs-target="#rejectEventModal<?= $sid ?>">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                            // Collect the approve/reject modals into the buffer (printed at end of body)
                                            ob_start();
                                        ?>
                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveEventModal<?= $sid ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Approve Event Submission</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="faculty_action" value="approve">
                                                            <input type="hidden" name="submission_id" value="<?= $sid ?>">
                                                            <p>Approve "<strong><?= $stitle ?></strong>" by <?= $sfac ?>?</p>
                                                            <p class="text-muted">This will publish the event immediately.</p>
                                                            <div class="mb-3">
                                                                <label class="form-label">Review Notes (Optional)</label>
                                                                <textarea class="form-control" name="review_notes" rows="3" placeholder="Add any feedback or notes..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success">Approve & Publish</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectEventModal<?= $sid ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Event Submission</h5>
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
                                            $__eventsCollectedModals .= ob_get_clean();
                                        ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Events List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Events (<?= count($events) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($events)): ?>
                            <div class="empty-state-card">
                                <i class="fas fa-calendar-alt fa-3x"></i>
                                <h6>No Events Found</h6>
                                <div class="text-muted">Start by adding an event using the <strong>Add Event</strong> button above.</div>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Event</th>
                                            <th>Category</th>
                                            <th>Date & Time</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($events as $event): ?>
                                            <?php
                                                $eid = (int)$event['id'];
                                                $etitle = esc($event['title']);
                                                $ecat = esc($event['category'] ?? 'Uncategorized');
                                                $estatus = esc($event['status'] ?? 'draft');
                                                $eloc = esc($event['location'] ?: 'TBA');
                                                $start_at = $event['start_at'] ?? null;
                                                $end_at = $event['end_at'] ?? null;
                                                $startDateDisp = $start_at ? date('M j, Y', strtotime($start_at)) : '';
                                                $startTimeDisp = $start_at ? date('g:i A', strtotime($start_at)) : '';
                                                $startValue = $start_at ? date('Y-m-d\TH:i', strtotime($start_at)) : '';
                                                $endValue = $end_at ? date('Y-m-d\TH:i', strtotime($end_at)) : '';
                                                $ereg = esc($event['registration_url'] ?? '');
                                                $edesc = esc($event['description'] ?? '');
                                                $hasImg = !empty($event['image_url']);
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?= $etitle ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge badge--category"><?= $ecat ?></span>
                                                </td>
                                                <td>
                                                    <?php if ($start_at): ?>
                                                        <strong><?= $startDateDisp ?></strong>
                                                        <br><small><?= $startTimeDisp ?></small>
                                                    <?php else: ?>
                                                        <small class="text-muted">No date set</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $eloc ?></td>
                                                <td>
                                                    <span class="badge badge--status badge--<?= $estatus ?>">
                                                        <?= $estatus ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-warning" 
                                                                data-bs-toggle="modal" data-bs-target="#editEventModal<?= $eid ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form method="POST" style="display: inline;" 
                                                              onsubmit="return confirm('Are you sure you want to delete this event?');">
                                                            <input type="hidden" name="delete_event" value="1">
                                                            <input type="hidden" name="id" value="<?= $eid ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                                // Collect the edit modal into the buffer (printed at end of body)
                                                ob_start();
                                            ?>
                                            <!-- Edit Event Modal -->
                                            <div class="modal fade" id="editEventModal<?= $eid ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Event</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" enctype="multipart/form-data">
                                                            <input type="hidden" name="edit_event" value="1">
                                                            <input type="hidden" name="id" value="<?= $eid ?>">
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Title</label>
                                                                            <input type="text" class="form-control" name="title" 
                                                                                value="<?= $etitle ?>" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Description</label>
                                                                            <textarea class="form-control" name="description" rows="4"><?= $edesc ?></textarea>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="mb-3">
                                                                                    <label class="form-label">Start Date & Time</label>
                                                                                    <input type="datetime-local" class="form-control" name="start_at" 
                                                                                        value="<?= $startValue ?>">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="mb-3">
                                                                                    <label class="form-label">End Date & Time</label>
                                                                                    <input type="datetime-local" class="form-control" name="end_at" 
                                                                                        value="<?= $endValue ?>">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Location</label>
                                                                            <input type="text" class="form-control" name="location" 
                                                                                value="<?= esc($event['location'] ?? '') ?>">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Registration URL</label>
                                                                            <input type="url" class="form-control" name="registration_url" 
                                                                                value="<?= $ereg ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Category</label>
                                                                            <select class="form-control" name="category">
                                                                                <option value="career" <?= ($event['category'] === 'career') ? 'selected' : '' ?>>Career</option>
                                                                                <option value="academic" <?= ($event['category'] === 'academic') ? 'selected' : '' ?>>Academic</option>
                                                                                <option value="social" <?= ($event['category'] === 'social') ? 'selected' : '' ?>>Social</option>
                                                                                <option value="workshop" <?= ($event['category'] === 'workshop') ? 'selected' : '' ?>>Workshop</option>
                                                                                <option value="seminar" <?= ($event['category'] === 'seminar') ? 'selected' : '' ?>>Seminar</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Status</label>
                                                                            <select class="form-control" name="status">
                                                                                <option value="draft" <?= ($event['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
                                                                                <option value="published" <?= ($event['status'] === 'published') ? 'selected' : '' ?>>Published</option>
                                                                                <option value="archived" <?= ($event['status'] === 'archived') ? 'selected' : '' ?>>Archived</option>
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
                                                                <button type="submit" class="btn btn-primary">Update Event</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                                $__eventsCollectedModals .= ob_get_clean();
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

    <!-- Add Event Modal (moved here, unchanged) -->
    <div class="modal fade" id="addEventModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="add_event" value="1">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Event Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_at" class="form-label">Start Date & Time</label>
                                            <input type="datetime-local" class="form-control" id="start_at" name="start_at">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_at" class="form-label">End Date & Time</label>
                                            <input type="datetime-local" class="form-control" id="end_at" name="end_at">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location">
                                </div>
                                <div class="mb-3">
                                    <label for="registration_url" class="form-label">Registration URL</label>
                                    <input type="url" class="form-control" id="registration_url" name="registration_url">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="career">Career</option>
                                        <option value="academic">Academic</option>
                                        <option value="social">Social</option>
                                        <option value="workshop">Workshop</option>
                                        <option value="seminar">Seminar</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label">Event Image</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Collected approve/reject/edit modals -->
    <?= $__eventsCollectedModals ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
