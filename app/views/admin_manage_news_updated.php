<?php
// admin_manage_news.php — CMS for managing News with integrated settings
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login_admin'); exit;
}

require_once __DIR__ . '/../models/News.php';
require_once __DIR__ . '/../models/NewsPageSettings.php';

function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$user = $_SESSION['user'] ?? [];
$username = $user['username'] ?? 'Admin';
$notice = '';

// Get current data
try {
  $status = $_GET['status'] ?? 'all';
  $news = News::list($status);
  $counts = News::statusCounts();
  $settings = class_exists('NewsPageSettings') ? NewsPageSettings::getSettings() : [];
} catch (Exception $e) {
  $notice = 'Error loading data: ' . $e->getMessage();
  $news = [];
  $counts = ['all' => 0, 'draft' => 0, 'published' => 0, 'archived' => 0];
  $settings = [];
}

/* ---------- Handle POST (CRUD) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // Update news settings
    if (!empty($_POST['settings']) && class_exists('NewsPageSettings')) {
      NewsPageSettings::saveSettings($_POST['settings']);
      $notice = 'Settings updated successfully!';
    }

    // Add new news
    if (!empty($_POST['add_news']) && !empty($_POST['title'])) {
      $title = trim($_POST['title']);
      $content = trim($_POST['content'] ?? '');
      $category = $_POST['category'] ?? 'news';
      $nstatus = $_POST['status'] ?? 'draft';
      $imageUrl = null;

      // Handle image upload
      if (!empty($_FILES['image']['tmp_name'])) {
        $imgTmp = $_FILES['image']['tmp_name'];
        $imgName = time() . '-' . basename($_FILES['image']['name']);
        $destDir = __DIR__ . '/../../public/uploads/news/';
        if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
        $dest = $destDir . $imgName;
        if (move_uploaded_file($imgTmp, $dest)) {
          $imageUrl = '/adamson-ccit/public/uploads/news/' . $imgName;
        }
      }

      $nid = News::create($title, $content, $nstatus, $category, $imageUrl);
      $notice = $nid ? 'News added successfully!' : 'Failed to add news.';
    }

    // Update news status
    if (!empty($_POST['update_status']) && !empty($_POST['id'])) {
      News::updateStatus((int)$_POST['id'], $_POST['update_status']);
      $notice = 'Status updated successfully!';
    }

    // Delete news
    if (!empty($_POST['delete_news'])) {
      News::delete((int)$_POST['delete_news']);
      $notice = 'News deleted successfully!';
    }

    // Edit news
    if (!empty($_POST['edit_news']) && !empty($_POST['id'])) {
      $data = [
        'title' => trim($_POST['title'] ?? ''),
        'content' => trim($_POST['content'] ?? ''),
        'category' => $_POST['category'] ?? 'news',
        'status' => $_POST['edit_status'] ?? 'draft'
      ];
      News::update((int)$_POST['id'], $data);
      $notice = 'News updated successfully!';
    }

    // Redirect to prevent double submission
    if ($notice) {
      header('Location: ?page=admin_manage_news&status=' . urlencode($status) . '&success=' . urlencode($notice));
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
  $news = News::list($status);
  $counts = News::statusCounts();
  $settings = class_exists('NewsPageSettings') ? NewsPageSettings::getSettings() : [];
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
      <span class="admin-topbar__title">News → Management</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">News Management</h1>
      
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
              Manage <strong>news articles and page settings</strong>. Control content visibility and page layout.
            </div>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr><th>Field</th><th>Value</th></tr>
                </thead>
                <tbody>
                  <tr><td>Total News</td><td><?= $counts['all'] ?></td></tr>
                  <tr><td>Published</td><td><?= $counts['published'] ?></td></tr>
                  <tr><td>Drafts</td><td><?= $counts['draft'] ?></td></tr>
                  <tr><td>Archived</td><td><?= $counts['archived'] ?></td></tr>
                  <tr><td>Page Title</td><td><?= esc($settings['page_title'] ?? '—') ?></td></tr>
                  <tr><td>Hero Lead</td><td><?= esc($settings['hero_lead'] ?? '—') ?></td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Management Form -->
      <form id="newsForm" method="post" autocomplete="off">

        <!-- News Page Settings -->
        <div class="card mb-4">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0"><i class="fas fa-cog me-2"></i>News Page Settings</h5>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                      data-bs-target="#settingsCollapse" aria-expanded="false" aria-controls="settingsCollapse">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </div>
          
          <div class="collapse" id="settingsCollapse">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Page Title</label>
                  <input type="text" class="form-control" name="settings[page_title]" value="<?= esc($settings['page_title'] ?? '') ?>" placeholder="News & Updates">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Hero Lead Text</label>
                  <input type="text" class="form-control" name="settings[hero_lead]" value="<?= esc($settings['hero_lead'] ?? '') ?>" placeholder="Stay updated with the latest news">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Hero Background Image URL</label>
                  <input type="text" class="form-control" name="settings[hero_bg_image]" value="<?= esc($settings['hero_bg_image'] ?? '') ?>" placeholder="/path/to/image.jpg">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Items Per Page</label>
                  <input type="number" class="form-control" name="settings[items_per_page]" value="<?= esc($settings['items_per_page'] ?? '12') ?>" min="1" max="50">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Add New News -->
        <div class="card mb-4">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0"><i class="fas fa-plus me-2"></i>Add New News</h5>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                      data-bs-target="#addNewsCollapse" aria-expanded="false" aria-controls="addNewsCollapse">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </div>
          
          <div class="collapse" id="addNewsCollapse">
            <div class="card-body">
              <input type="hidden" name="add_news" value="1">
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label class="form-label">Title</label>
                  <input type="text" name="title" class="form-control" placeholder="News title" required>
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
                <div class="col-md-6 mb-3">
                  <label class="form-label">Category</label>
                  <select name="category" class="form-select">
                    <option value="news">News</option>
                    <option value="announcement">Announcement</option>
                    <option value="event">Event</option>
                    <option value="update">Update</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Featured Image</label>
                  <input type="file" name="image" class="form-control" accept="image/*">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Content</label>
                <textarea name="content" class="form-control" rows="4" placeholder="News content"></textarea>
              </div>
            </div>
          </div>
        </div>
      </form>

      <!-- News List with Status Filter -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-table me-2"></i>News Articles</h5>
            <div class="d-flex align-items-center">
              <div class="btn-group me-2" role="group">
                <a href="?page=admin_manage_news&status=all" class="btn btn-sm <?= $status === 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">All (<?= $counts['all'] ?>)</a>
                <a href="?page=admin_manage_news&status=published" class="btn btn-sm <?= $status === 'published' ? 'btn-primary' : 'btn-outline-primary' ?>">Published (<?= $counts['published'] ?>)</a>
                <a href="?page=admin_manage_news&status=draft" class="btn btn-sm <?= $status === 'draft' ? 'btn-primary' : 'btn-outline-primary' ?>">Drafts (<?= $counts['draft'] ?>)</a>
                <a href="?page=admin_manage_news&status=archived" class="btn btn-sm <?= $status === 'archived' ? 'btn-primary' : 'btn-outline-primary' ?>">Archived (<?= $counts['archived'] ?>)</a>
              </div>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                      data-bs-target="#newsTableCollapse" aria-expanded="true" aria-controls="newsTableCollapse">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </div>
        </div>
        
        <div class="collapse show" id="newsTableCollapse">
          <div class="card-body">
            <?php if (empty($news)): ?>
              <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>No news found for status: <?= esc($status) ?>
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
                    <?php foreach ($news as $n): ?>
                      <tr>
                        <td><?= esc($n['id']) ?></td>
                        <td>
                          <span class="badge bg-<?= $n['status'] === 'published' ? 'success' : ($n['status'] === 'draft' ? 'warning' : 'secondary') ?>">
                            <?= esc(ucfirst($n['status'])) ?>
                          </span>
                        </td>
                        <td>
                          <strong><?= esc($n['title']) ?></strong>
                          <?php if (!empty($n['content'])): ?>
                            <br><small class="text-muted"><?= esc(substr(strip_tags($n['content']), 0, 60)) ?>...</small>
                          <?php endif; ?>
                        </td>
                        <td><span class="badge bg-info"><?= esc($n['category'] ?? 'news') ?></span></td>
                        <td><small><?= date('M j, Y', strtotime($n['created_at'] ?? 'now')) ?></small></td>
                        <td>
                          <button class="btn btn-sm btn-warning me-1 edit-news-btn" 
                                  data-news-id="<?= $n['id'] ?>" 
                                  title="Edit News">
                            <i class="fas fa-edit"></i>
                          </button>
                          <div class="btn-group me-1">
                            <button class="btn btn-sm btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                              <i class="fas fa-exchange-alt"></i>
                            </button>
                            <ul class="dropdown-menu">
                              <?php foreach (['draft', 'published', 'archived'] as $st): ?>
                                <?php if ($st !== $n['status']): ?>
                                  <li>
                                    <form method="post" class="d-inline">
                                      <input type="hidden" name="update_status" value="<?= $st ?>">
                                      <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                      <button type="submit" class="dropdown-item"><?= ucfirst($st) ?></button>
                                    </form>
                                  </li>
                                <?php endif; ?>
                              <?php endforeach; ?>
                            </ul>
                          </div>
                          <form method="post" class="d-inline">
                            <button type="submit" name="delete_news" value="<?= (int)$n['id'] ?>" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Really delete this news?')" title="Delete News">
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
            <button type="submit" form="newsForm" class="btn btn--primary">Save Changes</button>
            <a class="btn btn-outline-secondary" href="?page=admin_manage_events">Events</a>
            <a class="btn btn-outline-secondary" href="?page=admin_manage_announcements">Announcements</a>
          </div>
        </div>
      </div>

    </section>
  </main>
</div>

<!-- Edit News Modals -->
<?php foreach ($news as $n): ?>
<div class="modal fade" id="editNewsModal<?= $n['id'] ?>" tabindex="-1" aria-labelledby="editNewsModalLabel<?= $n['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="edit_news" value="1">
        <input type="hidden" name="id" value="<?= $n['id'] ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="editNewsModalLabel<?= $n['id'] ?>">Edit News: <?= esc($n['title']) ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8 mb-3">
              <label class="form-label">Title</label>
              <input type="text" name="title" class="form-control" value="<?= esc($n['title']) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Status</label>
              <select name="edit_status" class="form-select">
                <option value="draft" <?= $n['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= $n['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="archived" <?= $n['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
              <option value="news" <?= ($n['category'] ?? '') === 'news' ? 'selected' : '' ?>>News</option>
              <option value="announcement" <?= ($n['category'] ?? '') === 'announcement' ? 'selected' : '' ?>>Announcement</option>
              <option value="event" <?= ($n['category'] ?? '') === 'event' ? 'selected' : '' ?>>Event</option>
              <option value="update" <?= ($n['category'] ?? '') === 'update' ? 'selected' : '' ?>>Update</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="5"><?= esc($n['content'] ?? '') ?></textarea>
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
  const form = document.getElementById('newsForm');
  const saveStatus = document.getElementById('saveStatus');
  let dirty = false;

  // Manual modal handling for edit buttons
  document.querySelectorAll('.edit-news-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const newsId = btn.getAttribute('data-news-id');
      const modalId = `editNewsModal${newsId}`;
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