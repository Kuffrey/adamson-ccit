<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: ?page=admin_login'); exit;
}
/** @var array $news */
/** @var string $status */
/** @var array $counts */
$counts = $counts ?? ['all'=>0,'draft'=>0,'published'=>0,'archived'=>0];
$tabs = [
    'all'       => 'All ('.$counts['all'].')',
    'draft'     => 'Drafts ('.$counts['draft'].')',
    'published' => 'Published ('.$counts['published'].')',
    'archived'  => 'Archived ('.$counts['archived'].')',
];
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<main class="cms-page">
  <div class="container">
    <div class="cms">
      <header class="cms__head">
        <h1 class="cms__title">Manage News</h1>
        <p class="cms__kicker">Create, publish, archive</p>
      </header>

      <!-- Tabs -->
      <nav class="tbar" aria-label="News filters">
        <div class="tbar__inner">
          <?php foreach ($tabs as $key=>$label): ?>
            <a class="pill<?= $status === $key ? ' is-active' : '' ?>"
               href="?page=admin_manage_news&status=<?= e($key) ?>"><?= e($label) ?></a>
          <?php endforeach; ?>
        </div>
      </nav>

      <!-- Create form -->
      <section class="cms-box">
        <form class="form" method="post" action="?page=admin_manage_news&status=<?= e($status) ?>" enctype="multipart/form-data">
          <input type="hidden" name="add_news" value="1" />
          <div class="form-grid">
            <input class="input" type="text" name="title" placeholder="News Title" required>
            <textarea class="textarea" name="content" placeholder="News Content" required rows="6"></textarea>

            <div class="form-row">
              <label class="field">
                <span class="field__label">Category</span>
                <select class="select" name="category">
                  <option value="news">News</option>
                  <option value="research">Research</option>
                  <option value="achievement">Achievement</option>
                  <option value="student">Student Life</option>
                </select>
              </label>

              <label class="field">
                <span class="field__label">Status</span>
                <select class="select" name="status">
                  <option value="draft">Draft</option>
                  <option value="published">Published</option>
                  <option value="archived">Archived</option>
                </select>
              </label>

              <label class="field">
                <span class="field__label">Image</span>
                <input class="file" type="file" name="image" accept="image/*">
              </label>
            </div>

            <div class="form-actions">
              <button class="btn btn--primary" type="submit">Add News</button>
            </div>
          </div>
        </form>
      </section>

      <!-- List -->
      <section class="fgrid">
        <?php if (empty($news)): ?>
          <div class="fempty">No items in this tab.</div>
        <?php else: ?>
          <?php foreach ($news as $n): ?>
            <article class="f">
              <div class="f__thumb">
                <?php if (!empty($n['image_url'])): ?>
                  <img src="<?= e($n['image_url']) ?>" alt="">
                <?php else: ?>
                  <div class="f__thumb--placeholder" aria-hidden="true">📰</div>
                <?php endif; ?>
              </div>

              <div class="f__body">
                <h3 class="f__name"><?= e($n['title'] ?? 'Untitled') ?></h3>
                <div class="f__meta">
                  <?= e(ucfirst($n['category'] ?? 'news')) ?> • <?= e(ucfirst($n['status'] ?? 'draft')) ?>
                  <?php if (!empty($n['created_at'])): ?>
                    • <small><?= e(date('M j, Y', strtotime($n['created_at']))) ?></small>
                  <?php endif; ?>
                </div>
                <?php if (!empty($n['excerpt'])): ?>
                  <div class="f__excerpt"><?= e($n['excerpt']) ?></div>
                <?php endif; ?>
              </div>

              <div class="f__actions">
                <form method="post" action="?page=admin_manage_news&status=<?= e($status) ?>">
                  <input type="hidden" name="id" value="<?= (int)($n['id'] ?? 0) ?>">
                  <button class="btn btn--ghost" name="update_status" value="draft" type="submit">→ Draft</button>
                </form>
                <form method="post" action="?page=admin_manage_news&status=<?= e($status) ?>">
                  <input type="hidden" name="id" value="<?= (int)($n['id'] ?? 0) ?>">
                  <button class="btn btn--success" name="update_status" value="published" type="submit">✓ Publish</button>
                </form>
                <form method="post" action="?page=admin_manage_news&status=<?= e($status) ?>">
                  <input type="hidden" name="id" value="<?= (int)($n['id'] ?? 0) ?>">
                  <button class="btn btn--outline" name="update_status" value="archived" type="submit">⤺ Archive</button>
                </form>
                <a class="btn btn--danger"
                   href="?page=admin_manage_news&delete=<?= (int)($n['id'] ?? 0) ?>&status=<?= e($status) ?>"
                   onclick="return confirm('Delete this news?')">Delete</a>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>
    </div>
  </div>
</main>
