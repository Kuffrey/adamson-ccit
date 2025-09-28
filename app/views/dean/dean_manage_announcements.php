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
} catch (Exception $e) {
    $notice = 'Error loading data: ' . $e->getMessage();
    $announcements = [];
    $counts = ['all' => 0, 'draft' => 0, 'published' => 0, 'archived' => 0];
}

/* ---------- Handle POST (CRUD) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
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

            // Note: The Announcement model doesn't have a general update method
            // This functionality might need to be implemented in the model
            // Announcement::update($id, $updateData);
            $notice = 'Announcement editing functionality needs to be implemented in the model.';
        }

        // Delete announcement
        if (!empty($_POST['delete_announcement']) && !empty($_POST['id'])) {
            $id = (int)$_POST['id'];
            
            // Get announcement title before deletion for logging
            $announcementToDelete = Announcement::get($id);
            $announcementTitle = $announcementToDelete['title'] ?? "Announcement ID {$id}";
            
            if (Announcement::delete($id)) {
                DeanLogs::logDelete('announcements', $id, $user['id'] ?? null, "Deleted announcement: {$announcementTitle}");
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements | CCIT Dean</title>
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
                <span class="admin-topbar__title">Announcements Management</span>
                <div class="admin-topbar__spacer"></div>
                <div class="admin-topbar__user">
                    <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'D')) ?></span>
                    <span class="admin-topbar__name"><?= esc($username) ?></span>
                </div>
            </header>

            <section class="admin-cms-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="admin-cms-section__title mb-1">Announcements Management</h1>
                        <p class="text-muted mb-0">Create and manage department announcements</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="badge bg-primary">Total: <?= $counts['all'] ?></div>
                        <div class="badge bg-success">Published: <?= $counts['published'] ?></div>
                        <div class="badge bg-warning">Draft: <?= $counts['draft'] ?></div>
                    </div>
                </div>

                <?php if ($notice): ?>
                    <div class="alert <?= str_starts_with($notice, 'Error') || str_starts_with($notice, 'Failed') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show" role="alert">
                        <i class="fas <?= str_starts_with($notice, 'Error') || str_starts_with($notice, 'Failed') ? 'fa-exclamation-triangle' : 'fa-check-circle' ?> me-2"></i>
                        <?= esc($notice) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filter Tabs -->
                <div class="card mb-4">
                    <div class="card-body">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link <?= $status === 'all' ? 'active' : '' ?>" href="?page=dean_manage_announcements&status=all">
                                    All Announcements (<?= $counts['all'] ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $status === 'published' ? 'active' : '' ?>" href="?page=dean_manage_announcements&status=published">
                                    Published (<?= $counts['published'] ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $status === 'draft' ? 'active' : '' ?>" href="?page=dean_manage_announcements&status=draft">
                                    Draft (<?= $counts['draft'] ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $status === 'archived' ? 'active' : '' ?>" href="?page=dean_manage_announcements&status=archived">
                                    Archived (<?= $counts['archived'] ?>)
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Add New Announcement Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="fas fa-plus me-2"></i>Add New Announcement</h5>
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#addAnnouncementCollapse" aria-expanded="false">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="collapse" id="addAnnouncementCollapse">
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="add_announcement" value="1">
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
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Announcement
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Announcements List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-bullhorn me-2"></i>Announcements (<?= count($announcements) ?>)</h5>
                    </div>
                    
                    <div class="card-body">
                        <?php if (empty($announcements)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No announcements found.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
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
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-start">
                                                        <?php if (!empty($announcement['image_url'])): ?>
                                                            <img src="<?= esc($announcement['image_url']) ?>" alt="Announcement Image" 
                                                                 class="rounded me-3" width="50" height="50" style="object-fit: cover;">
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong><?= esc($announcement['title']) ?></strong>
                                                            <?php if (!empty($announcement['content'])): ?>
                                                                <br><small class="text-muted"><?= esc(substr($announcement['content'], 0, 100)) ?>...</small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= 
                                                        $announcement['category'] === 'emergency' ? 'danger' : 
                                                        ($announcement['category'] === 'deadline' ? 'warning' : 
                                                        ($announcement['category'] === 'academic' ? 'info' : 'secondary')) 
                                                    ?>">
                                                        <?= esc($announcement['category']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= $announcement['date'] ? date('M j, Y', strtotime($announcement['date'])) : '' ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $announcement['status'] === 'published' ? 'success' : ($announcement['status'] === 'draft' ? 'warning' : 'secondary') ?>">
                                                        <?= esc($announcement['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <?php if ($announcement['status'] === 'draft'): ?>
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="update_status" value="published">
                                                                <input type="hidden" name="id" value="<?= $announcement['id'] ?>">
                                                                <button type="submit" class="btn btn-sm btn-success" title="Publish">
                                                                    <i class="fas fa-play"></i>
                                                                </button>
                                                            </form>
                                                        <?php elseif ($announcement['status'] === 'published'): ?>
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="update_status" value="archived">
                                                                <input type="hidden" name="id" value="<?= $announcement['id'] ?>">
                                                                <button type="submit" class="btn btn-sm btn-secondary" title="Archive">
                                                                    <i class="fas fa-archive"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                        
                                                        <button type="button" class="btn btn-sm btn-warning" 
                                                                data-bs-toggle="modal" data-bs-target="#editAnnouncementModal<?= $announcement['id'] ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form method="POST" style="display: inline;" 
                                                              onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                                            <input type="hidden" name="delete_announcement" value="1">
                                                            <input type="hidden" name="id" value="<?= $announcement['id'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Edit Announcement Modal -->
                                            <div class="modal fade" id="editAnnouncementModal<?= $announcement['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Announcement</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" enctype="multipart/form-data">
                                                            <input type="hidden" name="edit_announcement" value="1">
                                                            <input type="hidden" name="id" value="<?= $announcement['id'] ?>">
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Title</label>
                                                                            <input type="text" class="form-control" name="title" 
                                                                                   value="<?= esc($announcement['title']) ?>" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Content</label>
                                                                            <textarea class="form-control" name="content" rows="6"><?= esc($announcement['content']) ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Category</label>
                                                                            <select class="form-control" name="category">
                                                                                <option value="general" <?= $announcement['category'] === 'general' ? 'selected' : '' ?>>General</option>
                                                                                <option value="academic" <?= $announcement['category'] === 'academic' ? 'selected' : '' ?>>Academic</option>
                                                                                <option value="registration" <?= $announcement['category'] === 'registration' ? 'selected' : '' ?>>Registration</option>
                                                                                <option value="deadline" <?= $announcement['category'] === 'deadline' ? 'selected' : '' ?>>Deadline</option>
                                                                                <option value="holiday" <?= $announcement['category'] === 'holiday' ? 'selected' : '' ?>>Holiday</option>
                                                                                <option value="emergency" <?= $announcement['category'] === 'emergency' ? 'selected' : '' ?>>Emergency</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Date</label>
                                                                            <input type="date" class="form-control" name="date" 
                                                                                   value="<?= $announcement['date'] ?>">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Status</label>
                                                                            <select class="form-control" name="status">
                                                                                <option value="draft" <?= $announcement['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                                                                <option value="published" <?= $announcement['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                                                                <option value="archived" <?= $announcement['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Update Image</label>
                                                                            <input type="file" class="form-control" name="image" accept="image/*">
                                                                            <?php if (!empty($announcement['image_url'])): ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>