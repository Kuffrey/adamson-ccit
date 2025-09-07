<?php
// admin_manage_news.php — CMS for managing News
require_once __DIR__ . '/../models/News.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: ?page=admin_login'); exit;
}

$username = $_SESSION['user']['username'] ?? 'Admin';
$status   = $_GET['status'] ?? 'all';
$news     = News::list($status);
$counts   = News::statusCounts();

$tabs = [
    'all'       => 'All ('.$counts['all'].')',
    'draft'     => 'Drafts ('.$counts['draft'].')',
    'published' => 'Published ('.$counts['published'].')',
    'archived'  => 'Archived ('.$counts['archived'].')',
];

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Handle Add News form submission
$notice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['add_news'])) {
    $title    = trim($_POST['title'] ?? '');
    $content  = trim($_POST['content'] ?? '');
    $category = $_POST['category'] ?? 'news';
    $nstatus  = $_POST['status'] ?? 'draft';
    $imageUrl = null;

    if (!empty($_FILES['image']['tmp_name'])) {
        $imgTmp  = $_FILES['image']['tmp_name'];
        $imgName = basename($_FILES['image']['name']);
        $destDir = __DIR__ . '/../../public/uploads/news/';
        if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
        $dest    = $destDir . $imgName;
        if (move_uploaded_file($imgTmp, $dest)) {
            $imageUrl = '/adamson-ccit/public/uploads/news/' . $imgName;
        }
    }

    $nid = News::create($title, $content, $nstatus, $category, $imageUrl);
    $notice = $nid ? 'News added successfully.' : 'Failed to add news.';
    $news = News::list($status); // reload after add
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage News | CCIT CMS</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/admin/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <!-- Topbar -->
    <header class="admin-topbar">
      <span class="admin-topbar__title">Manage News</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= e(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= e($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <?php if ($notice): ?>
        <p class="notice success"><?= e($notice) ?></p>
      <?php endif; ?>

      <!-- Tabs -->
      <nav class="tbar" aria-label="News filters">
        <div class="tbar__inner">
          <?php foreach ($tabs as $key => $label): ?>
            <a class="pill<?= $status === $key ? ' is-active' : '' ?>"
               href="?page=admin_manage_news&status=<?= e($key) ?>"><?= e($label) ?></a>
          <?php endforeach; ?>
        </div>
      </nav>

      <!-- Add News Form -->
      <div class="cms-card">
        <h2 class="cms-card-legend">Add News</h2>
        <form class="admin-cms-form" method="post" enctype="multipart/form-data">
          <input type="hidden" name="add_news" value="1">
          <div class="form-section">
            <div class="field">
              <label>Title</label>
              <input type="text" name="title" required>
            </div>
            <div class="field">
              <label>Content</label>
              <textarea name="content" rows="5" required></textarea>
            </div>
            <div class="field">
              <label>Category</label>
              <select name="category">
                <option value="news">News</option>
                <option value="research">Research</option>
                <option value="achievement">Achievement</option>
                <option value="student">Student Life</option>
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
              <label>Image</label>
              <input type="file" name="image" accept="image/*">
            </div>
            <div class="form-actions">
              <button class="btn btn--primary" type="submit">Add News</button>
            </div>
          </div>
        </form>
      </div>

      <!-- News List -->
      <div class="cms-card">
        <h2 class="cms-card-legend">News List</h2>
        <?php if (empty($news)): ?>
          <p>No news in this tab.</p>
        <?php else: ?>
          <?php foreach ($news as $n): ?>
            <?php
              $s = strtolower($n['status'] ?? 'draft');
              $statusClass = [
                'draft'     => 'status--draft',
                'published' => 'status--published',
                'archived'  => 'status--archived',
              ][$s] ?? 'status--draft';
            ?>
            <div class="cms-card-item">
              <div class="cms-card-item__thumb">
                <?php if (!empty($n['image_url'])): ?>
                  <img src="<?= e($n['image_url']) ?>" alt="">
                <?php else: ?>
                  <div class="cms-card-item__placeholder" aria-hidden="true">📰</div>
                <?php endif; ?>
              </div>

              <div class="cms-card-item__body">
                <h3><?= e($n['title'] ?? 'Untitled') ?></h3>
                <p class="meta">
                  <span class="status-badge <?= e($statusClass) ?>"><?= e(ucfirst($s)) ?></span>
                  • <?= e(ucfirst($n['category'] ?? 'news')) ?>
                  <?php if (!empty($n['display_date'] ?? $n['created_at'])): ?>
                    • <small><?= e(date('M j, Y', strtotime($n['display_date'] ?? $n['created_at']))) ?></small>
                  <?php endif; ?>
                  <?php if (!empty($n['author'])): ?> • <small><?= e($n['author']) ?></small><?php endif; ?>
                </p>
                <?php if (!empty($n['excerpt'])): ?>
                  <p class="excerpt"><?= e($n['excerpt']) ?></p>
                <?php endif; ?>
              </div>

              <!-- Contextual Actions -->
              <div class="cms-card-item__actions">
                <?php if ($s !== 'draft'): ?>
                  <form method="post" style="display:inline">
                    <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                    <button class="btn btn--ghost" name="update_status" value="draft" type="submit" title="Move to Draft">→ Draft</button>
                  </form>
                <?php endif; ?>

                <?php if ($s !== 'published'): ?>
                  <form method="post" style="display:inline">
                    <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                    <button class="btn btn--success" name="update_status" value="published" type="submit" title="Publish this article">✓ Publish</button>
                  </form>
                <?php endif; ?>

                <?php if ($s !== 'archived'): ?>
                  <form method="post" style="display:inline">
                    <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                    <button class="btn btn--outline" name="update_status" value="archived" type="submit" title="Archive (hide from public)">⤺ Archive</button>
                  </form>
                <?php endif; ?>

                <a class="btn btn--danger"
                   href="?page=admin_manage_news&delete=<?= (int)$n['id'] ?>&status=<?= e($status) ?>"
                   onclick="return confirm('Delete this news?')"
                   title="Delete">Delete</a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </main>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const notices = document.querySelectorAll(".notice");
  if (notices.length) {
    setTimeout(() => { notices.forEach(n => n.style.display = "none"); }, 4000);
  }
});
</script>
</body>
</html>
