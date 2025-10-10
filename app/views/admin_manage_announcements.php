<?php
// admin_manage_announcements.php — CMS for managing Announcements with consistent layout
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../models/Announcement.php';

function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$user = $_SESSION['user'] ?? [];
$username = $user['username'] ?? 'Admin';
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
      $excerpt = trim($_POST['excerpt'] ?? '');
      $category = $_POST['category'] ?? 'general';
      $nstatus = $_POST['status'] ?? 'draft';
      $date = $_POST['date'] ?? date('Y-m-d');
      $imageUrl = null;

      // Handle image upload
      if (!empty($_FILES['image']['tmp_name'])) {
        $imgTmp = $_FILES['image']['tmp_name'];
        $imgName = time() . '-' . basename($_FILES['image']['name']);
        $destDir = __DIR__ . '/../../public/uploads/announcements/';
        if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
        $dest = $destDir . $imgName;
        if (move_uploaded_file($imgTmp, $dest)) {
          $imageUrl = '/adamson-ccit/public/uploads/announcements/' . $imgName;
        }
      }

      $aid = Announcement::create($title, $content, $nstatus, $category, $date, $imageUrl, $excerpt);
      $notice = $aid ? 'Announcement added successfully!' : 'Failed to add announcement.';
    }

    // Update announcement status
    if (!empty($_POST['update_status']) && !empty($_POST['id'])) {
      Announcement::updateStatus((int)$_POST['id'], $_POST['update_status']);
      $notice = 'Status updated successfully!';
    }

    // Delete announcement
    if (!empty($_POST['delete_announcement'])) {
      Announcement::delete((int)$_POST['delete_announcement']);
      $notice = 'Announcement deleted successfully!';
    }

    // Edit announcement
    if (!empty($_POST['edit_announcement']) && !empty($_POST['id'])) {
      $announcementId = (int)$_POST['id'];
      $imageUrl = null;
      $currentImageUrl = trim($_POST['current_image_url'] ?? '');
      
      // Debug logging
      error_log('ADMIN ANNOUNCEMENT EDIT - ID: ' . $announcementId);
      error_log('ADMIN ANNOUNCEMENT EDIT - POST data: ' . json_encode($_POST));
      error_log('ADMIN ANNOUNCEMENT EDIT - Current image URL: ' . $currentImageUrl);
      
      // Handle image upload or removal
      if (!empty($_POST['remove_image'])) {
        // User wants to remove the current image
        $imageUrl = null;
        error_log('ADMIN ANNOUNCEMENT EDIT - Removing image');
        // Optionally delete the physical file
        if ($currentImageUrl && file_exists(__DIR__ . '/../../public' . str_replace('/adamson-ccit/public', '', $currentImageUrl))) {
          @unlink(__DIR__ . '/../../public' . str_replace('/adamson-ccit/public', '', $currentImageUrl));
        }
      } elseif (!empty($_FILES['image']['tmp_name'])) {
        // User uploaded a new image
        error_log('ADMIN ANNOUNCEMENT EDIT - New image uploaded');
        $imgTmp = $_FILES['image']['tmp_name'];
        $imgName = time() . '-' . basename($_FILES['image']['name']);
        $destDir = __DIR__ . '/../../public/uploads/announcements/';
        if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
        $dest = $destDir . $imgName;
        if (move_uploaded_file($imgTmp, $dest)) {
          $imageUrl = '/adamson-ccit/public/uploads/announcements/' . $imgName;
          // Remove old image file if it exists
          if ($currentImageUrl && file_exists(__DIR__ . '/../../public' . str_replace('/adamson-ccit/public', '', $currentImageUrl))) {
            @unlink(__DIR__ . '/../../public' . str_replace('/adamson-ccit/public', '', $currentImageUrl));
          }
        } else {
          $imageUrl = $currentImageUrl; // Keep current image if upload fails
        }
      } else {
        // No new image uploaded and not removing, keep current image
        $imageUrl = $currentImageUrl ?: null;
        error_log('ADMIN ANNOUNCEMENT EDIT - Keeping current image: ' . ($imageUrl ?? 'NULL'));
      }
      
      $data = [
        'title' => trim($_POST['title'] ?? ''),
        'excerpt' => trim($_POST['excerpt'] ?? ''),
        'content' => trim($_POST['content'] ?? ''),
        'category' => $_POST['category'] ?? 'general',
        'date' => $_POST['date'] ?? date('Y-m-d'),
        'status' => $_POST['edit_status'] ?? 'draft',
        'image_url' => $imageUrl
      ];
      
      error_log('ADMIN ANNOUNCEMENT EDIT - Update data: ' . json_encode($data));
      
      $result = Announcement::update($announcementId, $data);
      error_log('ADMIN ANNOUNCEMENT EDIT - Update result: ' . ($result ? 'SUCCESS' : 'FAILED'));
      
      $notice = $result ? 'Announcement updated successfully!' : 'Failed to update announcement!';
    }

    // Redirect to prevent double submission
    if ($notice) {
      header('Location: ?page=admin_manage_announcements&status=' . urlencode($status) . '&success=' . urlencode($notice));
      exit;
    }
  } catch (Exception $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

// Handle success message from redirect
if (!empty($_GET['success'])) {
  $notice = $_GET['success'];
}

// Refresh data after operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $announcements = Announcement::list($status);
  $counts = Announcement::statusCounts();
}
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/admin/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Announcements → Management</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h1 class="admin-cms-section__title mb-1">Announcement Management</h1>
          <p class="text-muted mb-0">Manage official announcements and notifications</p>
        </div>
        <div class="d-flex gap-2">
          <div class="badge bg-primary">Total: <?= $counts['all'] ?></div>
          <div class="badge bg-success">Published: <?= $counts['published'] ?></div>
          <div class="badge bg-warning text-dark">Drafts: <?= $counts['draft'] ?></div>
        </div>
      </div>
      
      <?php if ($notice): ?>
        <div class="alert <?= str_starts_with($notice, 'Error') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show" role="alert">
          <?= esc($notice) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Current Data Preview Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-eye me-2"></i>Current Data Preview</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#currentDataCard" aria-expanded="false" aria-controls="currentDataCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="currentDataCard">
          <div class="card-body">
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i>
              Manage <strong>announcements and notifications</strong>. Control public announcements and their visibility.
            </div>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr><th>Field</th><th>Value</th></tr>
                </thead>
                <tbody>
                  <tr><td>Total Announcements</td><td><?= $counts['all'] ?></td></tr>
                  <tr><td>Published</td><td><?= $counts['published'] ?></td></tr>
                  <tr><td>Drafts</td><td><?= $counts['draft'] ?></td></tr>
                  <tr><td>Archived</td><td><?= $counts['archived'] ?></td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Management Form -->
      <form id="announcementsForm" method="post" enctype="multipart/form-data" autocomplete="off">

        <!-- Add New Announcement -->
        <div class="card mb-4">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0"><i class="fas fa-plus me-2"></i>Add New Announcement</h5>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                      data-bs-target="#addAnnouncementCollapse" aria-expanded="false" aria-controls="addAnnouncementCollapse">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </div>
          
          <div class="collapse" id="addAnnouncementCollapse">
            <div class="card-body">
              <input type="hidden" name="add_announcement" value="1">
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label class="form-label">Title</label>
                  <input type="text" name="title" class="form-control" placeholder="Announcement title" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Status</label>
                  <select name="status" class="form-select">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Category</label>
                  <select name="category" class="form-select">
                    <option value="general">General</option>
                    <option value="advisory">Advisory</option>
                    <option value="deadline">Deadline</option>
                    <option value="policy">Policy</option>
                    <option value="alert">Alert</option>
                  </select>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Date</label>
                  <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Featured Image</label>
                  <input type="file" name="image" class="form-control" accept="image/*">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Excerpt</label>
                <textarea name="excerpt" class="form-control excerpt-field" rows="2" placeholder="Brief summary (optional)" maxlength="255"></textarea>
                <div class="form-text">Brief summary for announcement cards (<span class="char-count">0</span>/255 characters)</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Content</label>
                <textarea name="content" class="form-control" rows="4" placeholder="Announcement content" required></textarea>
              </div>
            </div>
          </div>
        </div>
      </form>

      <!-- Announcements List with Status Filter -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-table me-2"></i>Announcements</h5>
            <div class="d-flex align-items-center">
              <div class="btn-group me-3" role="group" aria-label="Status filter">
                <a href="?page=admin_manage_announcements&status=all" class="btn btn-sm <?= $status === 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">
                  <i class="fas fa-list me-1"></i>All <span class="badge bg-light text-dark ms-1"><?= $counts['all'] ?></span>
                </a>
                <a href="?page=admin_manage_announcements&status=published" class="btn btn-sm <?= $status === 'published' ? 'btn-success' : 'btn-outline-success' ?>">
                  <i class="fas fa-check-circle me-1"></i>Published <span class="badge bg-light text-dark ms-1"><?= $counts['published'] ?></span>
                </a>
                <a href="?page=admin_manage_announcements&status=draft" class="btn btn-sm <?= $status === 'draft' ? 'btn-warning' : 'btn-outline-warning' ?>">
                  <i class="fas fa-edit me-1"></i>Drafts <span class="badge bg-light text-dark ms-1"><?= $counts['draft'] ?></span>
                </a>
                <a href="?page=admin_manage_announcements&status=archived" class="btn btn-sm <?= $status === 'archived' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
                  <i class="fas fa-archive me-1"></i>Archived <span class="badge bg-light text-dark ms-1"><?= $counts['archived'] ?></span>
                </a>
              </div>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                      data-bs-target="#announcementsTableCollapse" aria-expanded="true" aria-controls="announcementsTableCollapse">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </div>
        </div>
        
        <div class="collapse show" id="announcementsTableCollapse">
          <div class="card-body">
            <?php if (empty($announcements)): ?>
              <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>No announcements found for status: <?= esc($status) ?>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Status</th>
                      <th>Title</th>
                      <th>Category</th>
                      <th>Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($announcements as $a): ?>
                      <tr>
                        <td><?= esc($a['id']) ?></td>
                        <td>
                          <span class="badge bg-<?= $a['status'] === 'published' ? 'success' : ($a['status'] === 'draft' ? 'warning' : 'secondary') ?>">
                            <?= esc(ucfirst($a['status'])) ?>
                          </span>
                        </td>
                        <td>
                          <strong><?= esc($a['title']) ?></strong>
                          <?php 
                          $excerpt = $a['excerpt'] ?? '';
                          $content = $a['body'] ?? $a['content'] ?? '';
                          if (!empty($excerpt)): ?>
                            <br><small class="text-muted"><strong>Excerpt:</strong> <?= esc(substr($excerpt, 0, 60)) ?>...</small>
                          <?php elseif (!empty($content)): ?>
                            <br><small class="text-muted"><?= esc(substr(strip_tags($content), 0, 60)) ?>...</small>
                          <?php endif; ?>
                        </td>
                        <td><span class="badge bg-info"><?= esc($a['category'] ?? 'general') ?></span></td>
                        <td><small><?= !empty($a['date']) ? date('M j, Y', strtotime($a['date'])) : '—' ?></small></td>
                        <td>
                          <button class="btn btn-sm btn-warning me-1 edit-announcement-btn" 
                                  data-announcement-id="<?= $a['id'] ?>" 
                                  title="Edit Announcement">
                            <i class="fas fa-edit"></i>
                          </button>
                          <div class="btn-group me-1">
                            <button class="btn btn-sm btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                              <i class="fas fa-exchange-alt"></i>
                            </button>
                            <ul class="dropdown-menu">
                              <?php foreach (['draft', 'published', 'archived'] as $st): ?>
                                <?php if ($st !== $a['status']): ?>
                                  <li>
                                    <form method="post" class="d-inline">
                                      <input type="hidden" name="update_status" value="<?= $st ?>">
                                      <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                      <button type="submit" class="dropdown-item"><?= ucfirst($st) ?></button>
                                    </form>
                                  </li>
                                <?php endif; ?>
                              <?php endforeach; ?>
                            </ul>
                          </div>
                          <form method="post" class="d-inline">
                            <button type="submit" name="delete_announcement" value="<?= (int)$a['id'] ?>" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Really delete this announcement?')" title="Delete Announcement">
                              <i class="fas fa-trash"></i>
                            </button>
                          </form>
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

      <div class="page-end-spacer" style="height:160px" aria-hidden="true"></div>

      <div class="savebar">
        <div class="savebar__inner">
          <span class="savebar__status" id="saveStatus">All changes saved</span>
          <div class="savebar__actions">
            <button type="button" class="btn" id="discardBtn">Discard</button>
            <button type="submit" form="announcementsForm" class="btn btn--primary">Save Changes</button>
            <a class="btn btn-outline-secondary" href="?page=admin_manage_news">News</a>
            <a class="btn btn-outline-secondary" href="?page=admin_manage_events">Events</a>
          </div>
        </div>
      </div>

    </section>
  </main>
</div>

<!-- Edit Announcement Modals -->
<?php foreach ($announcements as $a): ?>
<div class="modal fade" id="editAnnouncementModal<?= $a['id'] ?>" tabindex="-1" aria-labelledby="editAnnouncementModalLabel<?= $a['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="edit_announcement" value="1">
        <input type="hidden" name="id" value="<?= $a['id'] ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="editAnnouncementModalLabel<?= $a['id'] ?>">Edit Announcement: <?= esc($a['title']) ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8 mb-3">
              <label class="form-label">Title</label>
              <input type="text" name="title" class="form-control" value="<?= esc($a['title']) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Status</label>
              <select name="edit_status" class="form-select">
                <option value="draft" <?= $a['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= $a['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="archived" <?= $a['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Category</label>
              <select name="category" class="form-select">
                <option value="general" <?= ($a['category'] ?? '') === 'general' ? 'selected' : '' ?>>General</option>
                <option value="advisory" <?= ($a['category'] ?? '') === 'advisory' ? 'selected' : '' ?>>Advisory</option>
                <option value="deadline" <?= ($a['category'] ?? '') === 'deadline' ? 'selected' : '' ?>>Deadline</option>
                <option value="policy" <?= ($a['category'] ?? '') === 'policy' ? 'selected' : '' ?>>Policy</option>
                <option value="alert" <?= ($a['category'] ?? '') === 'alert' ? 'selected' : '' ?>>Alert</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Date</label>
              <input type="date" name="date" class="form-control" value="<?= esc($a['date'] ?? date('Y-m-d')) ?>">
            </div>
          </div>
          
          <!-- Current Image Display and New Image Upload -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Current Image</label>
              <?php if (!empty($a['image_url'])): ?>
                <div class="current-image-preview">
                  <img src="<?= esc($a['image_url']) ?>" alt="Current announcement image" 
                       style="max-width: 100%; max-height: 150px; border-radius: 8px; border: 1px solid #ddd;">
                  <input type="hidden" name="current_image_url" value="<?= esc($a['image_url']) ?>">
                </div>
              <?php else: ?>
                <p class="text-muted">No image uploaded</p>
              <?php endif; ?>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Upload New Image</label>
              <input type="file" name="image" class="form-control" accept="image/*">
              <div class="form-text">Leave empty to keep current image. Upload a new image to replace it.</div>
              
              <?php if (!empty($a['image_url'])): ?>
                <div class="form-check mt-2">
                  <input type="checkbox" name="remove_image" value="1" class="form-check-input" id="removeImage<?= $a['id'] ?>">
                  <label class="form-check-label text-danger" for="removeImage<?= $a['id'] ?>">
                    Remove current image
                  </label>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Excerpt</label>
            <textarea name="excerpt" class="form-control excerpt-field" rows="2" maxlength="255"><?= esc($a['excerpt'] ?? '') ?></textarea>
            <div class="form-text">Brief summary for announcement cards (<span class="char-count"><?= strlen($a['excerpt'] ?? '') ?></span>/255 characters)</div>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="5"><?= esc($a['body'] ?? $a['content'] ?? '') ?></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('announcementsForm');
  const saveStatus = document.getElementById('saveStatus');
  let dirty = false;

  // Manual modal handling for edit buttons
  document.querySelectorAll('.edit-announcement-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const announcementId = btn.getAttribute('data-announcement-id');
      const modalId = `editAnnouncementModal${announcementId}`;
      const modalElement = document.getElementById(modalId);
      
      if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
      } else {
        console.error('Modal not found:', modalId);
      }
    });
  });

  // Track changes
  form?.addEventListener('input', () => {
    dirty = true;
    saveStatus.textContent = 'Unsaved changes';
  });

  // Character counting for excerpt fields
  document.querySelectorAll('.excerpt-field').forEach(field => {
    const updateCharCount = () => {
      const charCount = field.value.length;
      const charCountSpan = field.parentElement.querySelector('.char-count');
      if (charCountSpan) {
        charCountSpan.textContent = charCount;
        charCountSpan.style.color = charCount > 255 ? '#dc3545' : '#6c757d';
      }
    };
    
    field.addEventListener('input', updateCharCount);
    updateCharCount(); // Initial count
  });

  form?.addEventListener('submit', () => {
    dirty = false;
    saveStatus.textContent = 'Saving...';
  });

  // Prevent accidental navigation
  window.addEventListener('beforeunload', (e) => { if(dirty){ e.preventDefault(); e.returnValue = ''; } });

  document.getElementById('discardBtn')?.addEventListener('click', () => {
    if(!dirty || confirm('Discard all unsaved changes?')) location.reload();
  });

  window.addEventListener('keydown', (e) => {
    if((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's'){
      e.preventDefault();
      document.querySelector('.savebar [type="submit"]')?.click();
    }
  });

  const notices = document.querySelectorAll('.alert');
  if (notices.length) setTimeout(() => { notices.forEach(n => n.style.display='none'); }, 4000);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
