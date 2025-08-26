<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') { header('Location: ?page=admin_login'); exit; }
/** @var array $announcements */
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
      <div class="cms__head">
        <h1 class="cms__title">Manage Announcements</h1>
        <p class="cms__kicker">Post advisories, deadlines, and notices</p>
      </div>

      <nav class="tbar" style="background:#fff;border-bottom:1px solid #e6e9ef;margin-bottom:12px;">
        <div class="tbar__inner" style="display:flex;gap:10px;flex-wrap:wrap;">
          <?php foreach ($tabs as $key=>$label): ?>
            <a class="pill <?= $status===$key ? 'is-active':'' ?>" href="?page=admin_manage_announcements&status=<?= e($key) ?>"><?= e($label) ?></a>
          <?php endforeach; ?>
        </div>
      </nav>

      <div class="cms-box" style="margin-bottom:14px;">
        <form method="post" action="?page=admin_manage_announcements&status=<?= e($status) ?>" enctype="multipart/form-data">
          <input type="hidden" name="add_announcement" value="1" />
          <div style="display:grid;gap:10px;">
            <input type="text" name="title" placeholder="Announcement Title" required
                   style="padding:10px;border:1px solid #e6e9ef;border-radius:10px;">
            <textarea name="content" placeholder="Announcement Content" required rows="6"
                      style="padding:10px;border:1px solid #e6e9ef;border-radius:10px;"></textarea>

            <div style="display:flex;gap:10px;flex-wrap:wrap;">
              <label>
                <span style="display:block;font-weight:700;margin-bottom:4px;">Category</span>
                <select name="category" style="padding:8px 10px;border:1px solid #e6e9ef;border-radius:10px;">
                  <option value="general">General</option>
                  <option value="advisory">Advisory</option>
                  <option value="deadline">Deadline</option>
                  <option value="policy">Policy</option>
                  <option value="alert">Alert</option>
                </select>
              </label>
              <label>
                <span style="display:block;font-weight:700;margin-bottom:4px;">Status</span>
                <select name="status" style="padding:8px 10px;border:1px solid #e6e9ef;border-radius:10px;">
                  <option value="draft">Draft</option>
                  <option value="published">Published</option>
                  <option value="archived">Archived</option>
                </select>
              </label>
              <label>
                <span style="display:block;font-weight:700;margin-bottom:4px;">Image (optional)</span>
                <input type="file" name="image" accept="image/*">
              </label>
            </div>

            <div><button class="btn btn--solid" type="submit">Add Announcement</button></div>
          </div>
        </form>
      </div>

      <div class="fgrid" style="display:grid;gap:12px;grid-template-columns:1fr;">
        <?php if (empty($announcements)): ?>
          <div class="fempty">No items in this tab.</div>
        <?php else: foreach ($announcements as $a): ?>
          <article class="f" style="display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;border:1px solid #e6e9ef;border-radius:14px;padding:12px;background:#fff;">
            <div>
              <?php if (!empty($a['image_url'])): ?>
                <img src="<?= e($a['image_url']) ?>" alt="" style="width:72px;height:72px;object-fit:cover;border-radius:10px;border:1px solid #e6e9ef;">
              <?php else: ?>
                <div style="width:72px;height:72px;border-radius:10px;background:#f8fafc;border:1px solid #e6e9ef;display:grid;place-items:center;font-weight:900;color:#0b234c;">📣</div>
              <?php endif; ?>
            </div>
            <div style="min-width:0;">
              <h3 class="f__name" style="margin:0 0 4px;overflow-wrap:anywhere;"><?= e($a['title'] ?? 'Untitled') ?></h3>
              <div class="f__title" style="color:#475569;margin-bottom:6px;">
                <?= e(ucfirst($a['category'] ?? 'general')) ?> • <?= e(ucfirst($a['status'] ?? 'draft')) ?>
                <?php if (!empty($a['created_at'])): ?>
                  • <small><?= e(date('M j, Y', strtotime($a['created_at']))) ?></small>
                <?php endif; ?>
              </div>
              <?php if (!empty($a['excerpt'])): ?>
                <div style="color:#374151;"><?= e($a['excerpt']) ?></div>
              <?php endif; ?>
            </div>
            <div style="display:flex;gap:6px;flex-direction:column;align-items:flex-end;">
              <form method="post" action="?page=admin_manage_announcements&status=<?= e($status) ?>">
                <input type="hidden" name="id" value="<?= (int)($a['id'] ?? 0) ?>">
                <button class="btn" name="update_status" value="draft" type="submit">→ Draft</button>
              </form>
              <form method="post" action="?page=admin_manage_announcements&status=<?= e($status) ?>">
                <input type="hidden" name="id" value="<?= (int)($a['id'] ?? 0) ?>">
                <button class="btn blue" name="update_status" value="published" type="submit">✓ Publish</button>
              </form>
              <form method="post" action="?page=admin_manage_announcements&status=<?= e($status) ?>">
                <input type="hidden" name="id" value="<?= (int)($a['id'] ?? 0) ?>">
                <button class="btn outline" name="update_status" value="archived" type="submit">⤺ Archive</button>
              </form>
              <a class="btn btn--danger" href="?page=admin_manage_announcements&delete=<?= (int)($a['id'] ?? 0) ?>&status=<?= e($status) ?>" onclick="return confirm('Delete this announcement?')">Delete</a>
            </div>
          </article>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>
</main>
