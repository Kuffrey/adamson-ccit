<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: ?page=admin_login'); exit;
}
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
/** @var array $events */
/** @var string $status */
/** @var array $counts */
$tab = $status ?? 'all';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin • Manage Events</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
  <style>
    .cms{background:#fff;border:1px solid #e6e9ef;border-radius:14px;padding:16px}
    .tabs{display:flex;gap:8px;flex-wrap:wrap;margin:0 0 12px}
    .pill{border:1px solid #e6e9ef;border-radius:999px;padding:8px 12px;font-weight:800;text-decoration:none;color:#0b234c}
    .pill.is-active{border-color:#0080c9;box-shadow:0 0 0 2px rgba(0,128,201,.15)}
    .count{opacity:.7;margin-left:6px}
    .cms-form .row{margin-bottom:12px}
    .cms-form input[type=text], .cms-form textarea, .cms-form select, .cms-form input[type=datetime-local]{width:100%;padding:10px;border:1px solid #e6e9ef;border-radius:10px;font:inherit}
    .cms-form input[type=file]{width:100%}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;border:2px solid #003169;background:#003169;color:#fff;font-weight:800;text-decoration:none}
    .btn--ghost{background:#fff;color:#003169}
    .list{margin-top:16px;display:grid;gap:12px}
    .card{border:1px solid #e6e9ef;border-radius:12px;padding:12px;display:grid;grid-template-columns:120px 1fr;gap:12px;align-items:start}
    .card h4{margin:0 0 6px}
    .muted{color:#6b7280}
    .thumb{width:120px;height:80px;border-radius:8px;border:1px solid #e6e9ef;object-fit:cover;background:#f8fafc}
    .tags{display:flex;gap:8px;flex-wrap:wrap;margin-top:6px}
    .tag{display:inline-block;padding:4px 8px;border-radius:999px;border:1px solid #e6e9ef;font-weight:800}
    .actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px}
    .grid2{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
    @media (max-width:800px){ .grid2{grid-template-columns:1fr} }
    @media (max-width:640px){ .card{grid-template-columns:1fr} .thumb{width:100%;height:180px} }
  </style>
</head>
<body>
<main class="container" style="padding:16px 20px">
  <section class="cms">
    <h2 style="margin:0 0 10px">Manage Events</h2>

    <!-- Status tabs with counts -->
    <nav class="tabs" aria-label="Filter by status">
      <?php
      $labels = ['all'=>'All','draft'=>'Drafts','published'=>'Published','archived'=>'Archived'];
      foreach ($labels as $key=>$label):
      ?>
        <a class="pill <?= $tab===$key?'is-active':'' ?>" href="?page=admin_manage_events&status=<?= e($key) ?>">
          <?= e($label) ?> <span class="count">(<?= (int)($counts[$key] ?? 0) ?>)</span>
        </a>
      <?php endforeach; ?>
    </nav>

    <!-- Create -->
    <form method="post" action="?page=admin_manage_events&status=<?= e($tab) ?>" class="cms-form" enctype="multipart/form-data" novalidate>
      <div class="row">
        <label>
          <div style="font-weight:700;margin-bottom:6px">Title</div>
          <input type="text" name="title" placeholder="Event Title" required>
        </label>
      </div>

      <div class="row grid2">
        <label>
          <div style="font-weight:700;margin-bottom:6px">Start</div>
          <input type="datetime-local" name="start_at" required>
        </label>
        <label>
          <div style="font-weight:700;margin-bottom:6px">End (optional)</div>
          <input type="datetime-local" name="end_at">
        </label>
      </div>

      <div class="row grid2">
        <label>
          <div style="font-weight:700;margin-bottom:6px">Location</div>
          <input type="text" name="location" placeholder="e.g., Auditorium • 2:00 PM" required>
        </label>
        <label>
          <div style="font-weight:700;margin-bottom:6px">Category</div>
          <select name="category" required>
            <option value="career">Career</option>
            <option value="forum">Forum</option>
            <option value="workshop">Workshop</option>
            <option value="competition">Competition</option>
            <option value="community">Community</option>
          </select>
        </label>
      </div>

      <div class="row">
        <label>
          <div style="font-weight:700;margin-bottom:6px">Description</div>
          <textarea name="description" rows="5" placeholder="Event details…" required></textarea>
        </label>
      </div>

      <div class="row grid2">
        <label>
          <div style="font-weight:700;margin-bottom:6px">Status</div>
          <select name="status">
            <option value="draft" <?= $tab==='draft'?'selected':''; ?>>Draft</option>
            <option value="published" <?= $tab==='published'?'selected':''; ?>>Published</option>
            <option value="archived" <?= $tab==='archived'?'selected':''; ?>>Archived</option>
          </select>
        </label>
        <label>
          <div style="font-weight:700;margin-bottom:6px">Image (optional)</div>
          <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
          <div class="muted" style="margin-top:6px">Max ~3–5MB; JPG/PNG/WebP/GIF</div>
        </label>
      </div>

      <div style="display:flex;justify-content:flex-end">
        <button class="btn" type="submit" name="add_event">Add Event</button>
      </div>
    </form>

    <hr style="margin:16px 0;border:none;border-top:1px solid #e6e9ef">

    <h3 style="margin:0 0 10px">Items (<?= e(ucfirst($tab)) ?>)</h3>
    <div class="list">
      <?php if (!empty($events)): ?>
        <?php foreach ($events as $ev): ?>
          <article class="card">
            <?php if (!empty($ev['image_url'])): ?>
              <img class="thumb" src="<?= e($ev['image_url']) ?>" alt="">
            <?php else: ?>
              <div class="thumb" style="display:grid;place-items:center;color:#9aa3b2;font-weight:800">No Image</div>
            <?php endif; ?>

            <div>
              <h4><?= e($ev['title'] ?? 'Untitled') ?></h4>
              <div class="muted">
                <?php
                  $s = $ev['start_at'] ?? null; $e = $ev['end_at'] ?? null;
                  $sd = $s ? date('M j, Y • g:i A', strtotime($s)) : '';
                  $ed = $e ? date('M j, Y • g:i A', strtotime($e)) : '';
                  echo e($sd);
                  if ($ed) echo ' — '.e($ed);
                ?>
              </div>
              <div class="tags">
                <?php if (!empty($ev['location'])): ?><span class="tag"><?= e($ev['location']) ?></span><?php endif; ?>
                <?php if (!empty($ev['category'])): ?><span class="tag"><?= e($ev['category']) ?></span><?php endif; ?>
                <?php if (!empty($ev['status'])): ?><span class="tag"><?= e($ev['status']) ?></span><?php endif; ?>
              </div>
              <p style="margin:8px 0 10px"><?= nl2br(e($ev['description'] ?? '')) ?></p>

              <div class="actions">
                <form class="inline" method="post" action="?page=admin_manage_events&status=<?= e($tab) ?>">
                  <input type="hidden" name="id" value="<?= (int)$ev['id'] ?>">
                  <input type="hidden" name="update_status" value="published">
                  <button class="btn btn--ghost" type="submit">Publish</button>
                </form>

                <form class="inline" method="post" action="?page=admin_manage_events&status=<?= e($tab) ?>">
                  <input type="hidden" name="id" value="<?= (int)$ev['id'] ?>">
                  <input type="hidden" name="update_status" value="archived">
                  <button class="btn btn--ghost" type="submit">Archive</button>
                </form>

                <form class="inline" method="post" action="?page=admin_manage_events&status=<?= e($tab) ?>">
                  <input type="hidden" name="id" value="<?= (int)$ev['id'] ?>">
                  <input type="hidden" name="update_status" value="draft">
                  <button class="btn btn--ghost" type="submit">Move to Draft</button>
                </form>

                <a class="btn btn--ghost" href="?page=admin_manage_events&status=<?= e($tab) ?>&delete=<?= (int)$ev['id'] ?>"
                   onclick="return confirm('Delete this event?')">Delete</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="card muted" style="grid-template-columns:1fr">No items in this status.</div>
      <?php endif; ?>
    </div>
  </section>
</main>
</body>
</html>
