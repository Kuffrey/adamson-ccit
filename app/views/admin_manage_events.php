<?php
// admin_manage_events.php — CMS for managing Events
declare(strict_types=1);
require_once __DIR__ . '/../models/Event.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login_admin'); exit;
}

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function dtfix(?string $v): ?string {
  $v = trim((string)$v);
  if ($v === '') return null;                 // allow NULL
  $v = str_replace('T', ' ', $v);             // from datetime-local
  if (strlen($v) === 16) $v .= ':00';         // add seconds if missing
  return $v;                                   // "YYYY-MM-DD HH:MM:SS"
}

$username = $_SESSION['user']['username'] ?? 'Admin';
$allowedStatuses = ['all','draft','published','archived'];
$status = $_GET['status'] ?? 'all';
$status = in_array($status, $allowedStatuses, true) ? $status : 'all';

// ------- actions (create / status updates / delete) -------
// Use PRG so we never re-post if user refreshes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // CREATE
  if (!empty($_POST['add_event'])) {
    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $cat   = $_POST['category'] ?? 'career';
    $loc   = trim($_POST['location'] ?? '');
    $reg   = trim($_POST['registration_url'] ?? '');
    $start = dtfix($_POST['start_at'] ?? null);
    $end   = dtfix($_POST['end_at']   ?? null);
    $st    = $_POST['status']   ?? 'draft';
    $st    = in_array($st, ['draft','published','archived'], true) ? $st : 'draft';
    $imageUrl = null;

    // ensure end >= start
    if ($start && $end && strtotime($end) < strtotime($start)) $end = $start;

    // image upload (optional)
    if (!empty($_FILES['image']['name']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
      $tmp  = $_FILES['image']['tmp_name'];
      $okTypes = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
      $type = @mime_content_type($tmp) ?: '';
      if (isset($okTypes[$type])) {
        $ext      = $okTypes[$type];
        $safeBase = preg_replace('~[^a-zA-Z0-9_-]+~', '-', strtolower(pathinfo($_FILES['image']['name'], PATHINFO_FILENAME)));
        $name     = date('Ymd_His') . '_' . ($safeBase ?: 'event') . '.' . $ext;
        $dir      = __DIR__ . '/../../public/uploads/events/';
        if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
        $dest     = $dir . $name;
        if (move_uploaded_file($tmp, $dest)) {
          $imageUrl = '/adamson-ccit/public/uploads/events/' . $name;
        }
      }
    }

    // Most models prefer an associative array
    $ok = false;
    if (method_exists('Event','create')) {
      $ok = Event::create([
        'title'            => $title,
        'description'      => $desc,
        'location'         => $loc,
        'category'         => $cat,
        'image_url'        => $imageUrl,
        'start_at'         => $start,
        'end_at'           => $end,
        'registration_url' => $reg ?: null,
        'status'           => $st,
      ]);
    }

    $goto = '?page=admin_manage_events&status=' . urlencode($st) . '&ok=' . ($ok ? '1' : '0') . '&act=add';
    header('Location: ' . $goto); exit;
  }

  // STATUS CHANGE
  if (!empty($_POST['update_status']) && !empty($_POST['id'])) {
    $id   = (int)$_POST['id'];
    $to   = $_POST['update_status'];
    if (!in_array($to, ['draft','published','archived'], true)) $to = 'draft';
    $ok   = method_exists('Event','updateStatus') ? Event::updateStatus($id, $to) : false;
    $goto = '?page=admin_manage_events&status=' . urlencode($to) . '&ok=' . ($ok ? '1' : '0') . '&act=status';
    header('Location: ' . $goto); exit;
  }
}

// DELETE (soft/hard per your model)
if (!empty($_GET['delete'])) {
  $id  = (int)$_GET['delete'];
  $ok  = method_exists('Event','delete') ? Event::delete($id) : false;
  $goto = '?page=admin_manage_events&status=' . urlencode($status) . '&ok=' . ($ok ? '1' : '0') . '&act=del';
  header('Location: ' . $goto); exit;
}

// ------- data listing -------
$events = method_exists('Event','list') ? Event::list($status) : [];
$counts = method_exists('Event','statusCounts') ? Event::statusCounts() : ['all'=>0,'draft'=>0,'published'=>0,'archived'=>0];

$tabs = [
  'all'       => 'All ('.(int)($counts['all'] ?? 0).')',
  'draft'     => 'Drafts ('.(int)($counts['draft'] ?? 0).')',
  'published' => 'Published ('.(int)($counts['published'] ?? 0).')',
  'archived'  => 'Archived ('.(int)($counts['archived'] ?? 0).')',
];

// categories used on public Events
$cats = [
  'career' => 'Career',
  'forum'  => 'Forum',
  'workshop' => 'Workshop',
  'competition' => 'Competition',
  'community' => 'Community',
];

// Friendly notice text
$notice = '';
if (isset($_GET['ok'], $_GET['act'])) {
  $ok = $_GET['ok'] === '1';
  $act = $_GET['act'];
  $notice = match ($act) {
    'add'    => $ok ? 'Event added successfully.' : 'Failed to add event.',
    'status' => $ok ? 'Status updated.' : 'Failed to update status.',
    'del'    => $ok ? 'Event deleted.' : 'Failed to delete event.',
    default  => ''
  };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Events | CCIT CMS</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/admin/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Manage Events</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= e(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= e($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <?php if ($notice): ?><p class="notice success"><?= e($notice) ?></p><?php endif; ?>

      <!-- Tabs -->
      <nav class="tbar" aria-label="Events filters">
        <div class="tbar__inner">
          <?php foreach ($tabs as $key => $label): ?>
            <a class="pill<?= $status === $key ? ' is-active' : '' ?>"
               href="?page=admin_manage_events&status=<?= e($key) ?>"><?= e($label) ?></a>
          <?php endforeach; ?>
        </div>
      </nav>

      <!-- Add Event -->
      <div class="cms-card">
        <h2 class="cms-card-legend">Add Event</h2>
        <form class="admin-cms-form" method="post" enctype="multipart/form-data">
          <input type="hidden" name="add_event" value="1">
          <div class="form-section">
            <div class="field">
              <label>Title</label>
              <input type="text" name="title" required>
            </div>
            <div class="field">
              <label>Description</label>
              <textarea name="description" rows="5"></textarea>
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
              <label>Location</label>
              <input type="text" name="location" placeholder="e.g. Ozanam Bldg, Rm 405">
            </div>
            <div class="field">
              <label>Registration URL (optional)</label>
              <input type="url" name="registration_url" placeholder="https://…">
            </div>
            <div class="field">
              <label>Start Date & Time</label>
              <input type="datetime-local" name="start_at">
            </div>
            <div class="field">
              <label>End Date & Time</label>
              <input type="datetime-local" name="end_at">
            </div>
            <div class="field">
              <label>Image</label>
              <input type="file" name="image" accept="image/*">
            </div>
            <div class="form-actions">
              <button class="btn btn--primary" type="submit">Add Event</button>
            </div>
          </div>
        </form>
      </div>

      <!-- Events List -->
      <div class="cms-card">
        <h2 class="cms-card-legend">Events List</h2>
        <?php if (empty($events)): ?>
          <p>No events in this tab.</p>
        <?php else: foreach ($events as $ev):
          $s = strtolower($ev['status'] ?? 'draft');
          $statusClass = [
            'draft'=>'status--draft', 'published'=>'status--published', 'archived'=>'status--archived'
          ][$s] ?? 'status--draft';
          $dateTxt = '';
          if (!empty($ev['start_at'])) $dateTxt = date('M j, Y g:ia', strtotime($ev['start_at']));
          if (!empty($ev['end_at']))   $dateTxt .= ' – ' . date('M j, Y g:ia', strtotime($ev['end_at']));
        ?>
        <div class="cms-card-item">
          <div class="cms-card-item__thumb">
            <?php if (!empty($ev['image_url'])): ?>
              <img src="<?= e($ev['image_url']) ?>" alt="">
            <?php else: ?>
              <div class="cms-card-item__placeholder" aria-hidden="true">📅</div>
            <?php endif; ?>
          </div>
          <div class="cms-card-item__body">
            <h3><?= e($ev['title'] ?? 'Untitled') ?></h3>
            <p class="meta">
              <span class="status-badge <?= e($statusClass) ?>"><?= e(ucfirst($s)) ?></span>
              <?php if (!empty($ev['category'])): ?> • <?= e(ucfirst($ev['category'])) ?><?php endif; ?>
              <?php if ($dateTxt): ?> • <small><?= e($dateTxt) ?></small><?php endif; ?>
              <?php if (!empty($ev['location'])): ?> • <small><?= e($ev['location']) ?></small><?php endif; ?>
            </p>
            <?php if (!empty($ev['description'])): ?>
              <p class="excerpt">
                <?= e(mb_substr(strip_tags((string)$ev['description']),0,160)) ?><?= strlen((string)$ev['description'])>160?'…':'' ?>
              </p>
            <?php endif; ?>
            <?php if (!empty($ev['registration_url'])): ?>
              <p class="meta"><small>Reg: <a href="<?= e($ev['registration_url']) ?>" target="_blank" rel="noopener">link</a></small></p>
            <?php endif; ?>
          </div>
          <div class="cms-card-item__actions">
            <?php if ($s !== 'draft'): ?>
              <form method="post">
                <input type="hidden" name="id" value="<?= (int)$ev['id'] ?>">
                <button class="btn btn--ghost" name="update_status" value="draft" type="submit" title="Move to Draft">→ Draft</button>
              </form>
            <?php endif; ?>
            <?php if ($s !== 'published'): ?>
              <form method="post">
                <input type="hidden" name="id" value="<?= (int)$ev['id'] ?>">
                <button class="btn btn--success" name="update_status" value="published" type="submit" title="Publish">✓ Publish</button>
              </form>
            <?php endif; ?>
            <?php if ($s !== 'archived'): ?>
              <form method="post">
                <input type="hidden" name="id" value="<?= (int)$ev['id'] ?>">
                <button class="btn btn--outline" name="update_status" value="archived" type="submit" title="Archive">⤺ Archive</button>
              </form>
            <?php endif; ?>
            <a class="btn btn--danger"
               href="?page=admin_manage_events&delete=<?= (int)$ev['id'] ?>&status=<?= e($status) ?>"
               onclick="return confirm('Delete this event?')">Delete</a>
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
