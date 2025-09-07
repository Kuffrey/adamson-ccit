<?php
// admin_manage_announcements.php — CMS for managing Announcements
declare(strict_types=1);
require_once __DIR__ . '/../models/Announcement.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login_admin'); exit;
}

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$username = $_SESSION['user']['username'] ?? 'Admin';
$status   = $_GET['status'] ?? 'all';

// ------- actions -------
$notice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // create
  if (!empty($_POST['add_announcement'])) {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $cat     = $_POST['category'] ?? 'general';
    $st      = $_POST['status']   ?? 'draft';
    $date    = $_POST['date']     ?? date('Y-m-d');
    $imageUrl = null;

    if (!empty($_FILES['image']['tmp_name'])) {
      $tmp  = $_FILES['image']['tmp_name'];
      $name = time() . '-' . preg_replace('/[^\w.\-]+/','-', basename($_FILES['image']['name']));
      $dir  = __DIR__ . '/../../public/uploads/announcements/';
      if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
      $dest = $dir . $name;
      if (move_uploaded_file($tmp, $dest)) {
        $imageUrl = '/adamson-ccit/public/uploads/announcements/' . $name;
      }
    }

    // You should have Announcement::create(...)
    $ok = Announcement::create($title, $content, $st, $cat, $date, $imageUrl);
    $notice = $ok ? 'Announcement added successfully.' : 'Failed to add announcement.';
  }

  // status update
  if (!empty($_POST['update_status']) && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
    $to = $_POST['update_status'];
    $ok = Announcement::updateStatus($id, $to);
    $notice = $ok ? 'Status updated.' : 'Failed to update status.';
  }
}

// delete
if (!empty($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $ok = Announcement::delete($id);
  $notice = $ok ? 'Announcement deleted.' : 'Failed to delete announcement.';
}

// ------- data -------
$rows   = Announcement::list($status);      // implement like News::list
$counts = Announcement::statusCounts();     // implement like News::statusCounts
$tabs = [
  'all'       => 'All ('.($counts['all'] ?? 0).')',
  'draft'     => 'Drafts ('.($counts['draft'] ?? 0).')',
  'published' => 'Published ('.($counts['published'] ?? 0).')',
  'archived'  => 'Archived ('.($counts['archived'] ?? 0).')',
];

$cats = [
  'general'=>'General', 'advisory'=>'Advisory', 'deadline'=>'Deadline', 'policy'=>'Policy', 'alert'=>'Alert'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Announcements | CCIT CMS</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/admin/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Manage Announcements</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= e(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= e($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <?php if ($notice): ?><p class="notice success"><?= e($notice) ?></p><?php endif; ?>

      <!-- Tabs -->
      <nav class="tbar" aria-label="Announcements filters">
        <div class="tbar__inner">
          <?php foreach ($tabs as $key => $label): ?>
            <a class="pill<?= $status === $key ? ' is-active' : '' ?>"
               href="?page=admin_manage_announcements&status=<?= e($key) ?>"><?= e($label) ?></a>
          <?php endforeach; ?>
        </div>
      </nav>

      <!-- Add Announcement -->
      <div class="cms-card">
        <h2 class="cms-card-legend">Add Announcement</h2>
        <form class="admin-cms-form" method="post" enctype="multipart/form-data">
          <input type="hidden" name="add_announcement" value="1">
          <div class="form-section">
            <div class="field">
              <label>Title</label>
              <input type="text" name="title" required>
            </div>
            <div class="field">
              <label>Body</label>
              <textarea name="content" rows="5" required></textarea>
            </div>
            <div class="field">
              <label>Category</label>
              <select name="category">
                <?php foreach ($cats as $k=>$v): ?>
                  <option value="<?= e($k) ?>"><?= e($v) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="field">
              <label>Status</label>
              <select name="status">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
              </select>
            </div>
            <div class="field">
              <label>Date</label>
              <input type="date" name="date" value="<?= e(date('Y-m-d')) ?>">
            </div>
            <div class="field">
              <label>Image</label>
              <input type="file" name="image" accept="image/*">
            </div>
            <div class="form-actions">
              <button class="btn btn--primary" type="submit">Add Announcement</button>
            </div>
          </div>
        </form>
      </div>

      <!-- Announcements List -->
      <div class="cms-card">
        <h2 class="cms-card-legend">Announcements List</h2>
        <?php if (empty($rows)): ?>
          <p>No announcements in this tab.</p>
        <?php else: foreach ($rows as $a):
          $s = strtolower($a['status'] ?? 'draft');
          $statusClass = [
            'draft'=>'status--draft', 'published'=>'status--published', 'archived'=>'status--archived'
          ][$s] ?? 'status--draft';
        ?>
        <div class="cms-card-item">
          <div class="cms-card-item__thumb">
            <?php if (!empty($a['image_url'])): ?>
              <img src="<?= e($a['image_url']) ?>" alt="">
            <?php else: ?>
              <div class="cms-card-item__placeholder" aria-hidden="true">📢</div>
            <?php endif; ?>
          </div>
          <div class="cms-card-item__body">
            <h3><?= e($a['title'] ?? 'Untitled') ?></h3>
            <p class="meta">
              <span class="status-badge <?= e($statusClass) ?>"><?= e(ucfirst($s)) ?></span>
              <?php if (!empty($a['category'])): ?> • <?= e(ucfirst($a['category'])) ?><?php endif; ?>
              <?php if (!empty($a['date'])): ?> • <small><?= e(date('M j, Y', strtotime($a['date']))) ?></small><?php endif; ?>
            </p>
            <?php if (!empty($a['content'])): ?>
              <p class="excerpt"><?= e(mb_substr(strip_tags($a['content']), 0, 160)) ?><?= strlen($a['content'])>160?'…':'' ?></p>
            <?php endif; ?>
          </div>
          <div class="cms-card-item__actions">
            <?php if ($s !== 'draft'): ?>
              <form method="post">
                <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                <button class="btn btn--ghost" name="update_status" value="draft" type="submit" title="Move to Draft">→ Draft</button>
              </form>
            <?php endif; ?>
            <?php if ($s !== 'published'): ?>
              <form method="post">
                <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                <button class="btn btn--success" name="update_status" value="published" type="submit" title="Publish">✓ Publish</button>
              </form>
            <?php endif; ?>
            <?php if ($s !== 'archived'): ?>
              <form method="post">
                <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                <button class="btn btn--outline" name="update_status" value="archived" type="submit" title="Archive">⤺ Archive</button>
              </form>
            <?php endif; ?>
            <a class="btn btn--danger"
               href="?page=admin_manage_announcements&delete=<?= (int)$a['id'] ?>&status=<?= e($status) ?>"
               onclick="return confirm('Delete this announcement?')">Delete</a>
          </div>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </section>
  </main>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const notices = document.querySelectorAll(".notice");
  if (notices.length) setTimeout(() => notices.forEach(n => n.style.display = "none"), 4000);
});
</script>
</body>
</html>
