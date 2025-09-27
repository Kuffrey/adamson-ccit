<?php
require_once __DIR__ . '/../models/Announcement.php';
if (!function_exists('e')) {
    function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('url_with')) {
    function url_with(array $params): string {
        $base = '/adamson-ccit/public/index.php';
        $q = array_merge(['page' => 'announcements'], $params);
        return $base . '?' . http_build_query($q);
    }
}

$cat   = isset($_GET['cat'])  ? strtolower(trim((string)$_GET['cat']))   : 'all';
$year  = isset($_GET['year']) ? trim((string)$_GET['year'])               : 'all';
$q     = isset($_GET['q'])    ? trim((string)$_GET['q'])                  : '';
$page  = max(1, (int)($_GET['p'] ?? 1));
$per   = 9;

$items = [];
$total = 0;
$metaYears = [];
try {
    $result = Announcement::searchPublished(
        [
            'cat'  => $cat === 'all' ? null : $cat,
            'year' => $year === 'all' ? null : (int)$year,
            'q'    => $q !== '' ? $q : null,
        ],
        ['page' => $page, 'perPage' => $per]
    );
    $items = $result['items'] ?? [];
    $total = (int)($result['total'] ?? count($items));
    $metaYears = $result['years'] ?? [];
} catch (Throwable $e) {
    $items = [];
    $total = 0;
    $metaYears = [];
}

$pages = max(1, (int)ceil($total / $per));
$page  = min($page, $pages);

$cats = [
  'all'=>'All','general'=>'General','advisory'=>'Advisory','deadline'=>'Deadline','policy'=>'Policy','alert'=>'Alert'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Announcements | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
</head>
<body>
<main>

  <style>
    /* Consistent container padding */
    .content > .container { padding: 16px 20px clamp(24px,5vw,48px); }
    .agrid { padding: 16px 0 clamp(32px,6vw,56px); }
  </style>

  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">CCIT Updates</p>
      <h1 class="subhero__title">Announcements</h1>
      <p class="subhero__lead">Official CCIT advisories, deadlines, and important updates.</p>
    </div>
  </section>

  <nav class="subnav" aria-label="News sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li><a href="/adamson-ccit/public/index.php?page=news">News</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=events">Events</a></li>
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=announcements" aria-current="page">Announcements</a></li>
      </ul>
    </div>
  </nav>

  <section class="nbar">
    <div class="container nbar__inner">
      <div class="nbar__left">
        <div class="ncats" role="tablist" aria-label="Filter announcements by category">
          <?php foreach ($cats as $val=>$label):
            $active = ($cat === $val) ? 'is-active' : '';
            $href = url_with(['cat'=>$val,'year'=>$year,'q'=>$q?:null,'p'=>1]);
          ?>
            <a class="pill <?= $active ?>" role="tab" aria-selected="<?= $active? 'true':'false' ?>" href="<?= e($href) ?>"><?= e($label) ?></a>
          <?php endforeach; ?>
        </div>
        <form class="nyear" method="get" action="/adamson-ccit/public/index.php">
          <input type="hidden" name="page" value="announcements">
          <input type="hidden" name="cat" value="<?= e($cat) ?>">
          <input type="hidden" name="q" value="<?= e($q) ?>">
          <select name="year" onchange="this.form.submit()">
            <option value="all" <?= $year==='all'?'selected':''; ?>>All Years</option>
            <?php
              if (!$metaYears) {
                  $yNow = (int)date('Y');
                  for ($y=$yNow; $y>=$yNow-6; $y--) echo '<option>'.(int)$y.'</option>';
              } else {
                  foreach ($metaYears as $y) {
                      $sel = ($year == (string)$y) ? 'selected' : '';
                      echo '<option value="'.e((string)$y).'" '.$sel.'>'.e((string)$y).'</option>';
                  }
              }
            ?>
          </select>
        </form>
      </div>
      <form class="nsearch" role="search" aria-label="Search announcements" method="get" action="/adamson-ccit/public/index.php">
        <input type="hidden" name="page" value="announcements">
        <input type="hidden" name="cat" value="<?= e($cat) ?>">
        <input type="hidden" name="year" value="<?= e($year) ?>">
        <input id="aQuery" name="q" type="search" value="<?= e($q) ?>" placeholder="Search announcements…" aria-label="Search announcements" />
        <button class="btn btn--solid" type="submit"></button>
      </form>
    </div>
  </section>

  <section>
    <div class="container">
      <?php
        $catLabel = $cats[$cat] ?? 'All';
        $yrLabel  = ($year==='all') ? 'all years' : $year;
        $countTxt = $total === 0 ? 'No announcements found' : "Showing {$total} result" . ($total!==1?'s':'');
      ?>
      <div id="aCount" class="ncount"><?= e($countTxt) ?> • <?= e($catLabel) ?> • <?= e($yrLabel) ?></div>

      <div id="aGrid" class="cards">
        <?php if (!$items): ?>
          <div class="nempty" role="status">No announcements match your filters.</div>
        <?php else: foreach ($items as $row):
          $id    = (int)($row['id'] ?? 0);
          $title = (string)($row['title'] ?? 'Untitled');
          $excerpt= (string)($row['excerpt'] ?? '');
          $catKey= strtolower((string)($row['category'] ?? 'general'));
          $img   = (string)($row['image_url'] ?? '/adamson-ccit/public/assets/images/news/sample3.jpg');
          $date  = (string)($row['date'] ?? $row['published_at'] ?? '');
          $viewUrl= '/adamson-ccit/public/index.php?page=announcement_view&id='.$id;
        ?>
        <article class="n" data-cat="<?= e($catKey) ?>" data-year="<?= e(substr($date,0,4) ?: '') ?>">
          <div class="n__media" onclick="openAnnouncementModal('<?= e($id) ?>')" style="cursor: pointer;">
            <img src="<?= e($img) ?>" alt="<?= e($title) ?>">
            <span class="chip chip--gray"><?= e(ucfirst($catKey)) ?></span>
          </div>
          <div class="n__body">
            <h3 class="n__title"><a href="#" onclick="openAnnouncementModal('<?= e($id) ?>')"><?= e($title) ?></a></h3>
            <?php if ($excerpt): ?><p class="n__excerpt"><?= e($excerpt) ?></p><?php endif; ?>
            <div class="n__meta">
              <?php if ($date): ?>
                <time datetime="<?= e($date) ?>"><?= e(date('M j, Y', strtotime($date))) ?></time>
              <?php endif; ?>
            </div>
          </div>
        </article>
        <?php endforeach; endif; ?>
      </div>

      <!-- Pagination -->
      <?php
        $prevUrl = url_with(['cat'=>$cat,'year'=>$year,'q'=>$q?:null,'p'=>max(1,$page-1)]);
        $nextUrl = url_with(['cat'=>$cat,'year'=>$year,'q'=>$q?:null,'p'=>min($pages,$page+1)]);
      ?>
      <nav class="pager" aria-label="Announcements pagination">
        <a class="pg" href="<?= e($prevUrl) ?>" <?= $page<=1?'disabled':'' ?>>« Prev</a>
        <span class="pg__status">Page <?= (int)$page ?> of <?= (int)$pages ?></span>
        <a class="pg" href="<?= e($nextUrl) ?>" <?= $page>=$pages?'disabled':'' ?>>Next »</a>
      </nav>
    </div>
  </section>
</main>

<!-- Announcement Modals -->
<?php foreach ($items as $row): 
  $id = (int)($row['id'] ?? 0);
  $title = (string)($row['title'] ?? 'Untitled');
  $content = (string)($row['body'] ?? $row['content'] ?? '');
  $excerpt = (string)($row['excerpt'] ?? '');
  $catKey = strtolower((string)($row['category'] ?? 'general'));
  $img = (string)($row['image_url'] ?? '/adamson-ccit/public/assets/images/news/sample3.jpg');
  $date = (string)($row['date'] ?? $row['published_at'] ?? '');
  $formattedDate = $date ? date('F j, Y', strtotime($date)) : '';
?>
<div id="announcement-modal-<?= e($id) ?>" class="announcement-modal" style="display: none;">
  <div class="announcement-modal-backdrop" onclick="closeAnnouncementModal()"></div>
  <div class="announcement-modal-content">
    <div class="announcement-modal-header">
      <div class="announcement-header-info">
        <span class="announcement-category"><?= e(ucfirst($catKey)) ?></span>
        <?php if ($formattedDate): ?>
          <span class="announcement-date"><?= e($formattedDate) ?></span>
        <?php endif; ?>
      </div>
      <button class="announcement-modal-close" onclick="closeAnnouncementModal()" aria-label="Close">&times;</button>
    </div>
    <div class="announcement-modal-body">
      <h3><?= e($title) ?></h3>
      <?php if ($img): ?>
        <img src="<?= e($img) ?>" alt="<?= e($title) ?>" class="announcement-image">
      <?php endif; ?>
      <?php if ($excerpt): ?>
        <p class="announcement-excerpt"><?= e($excerpt) ?></p>
      <?php endif; ?>
      <div class="announcement-content">
        <?= $content ? nl2br(e($content)) : '<p>No additional content available.</p>' ?>
      </div>
    </div>
    <div class="announcement-modal-footer">
      <button class="btn btn--solid" onclick="closeAnnouncementModal()">Close</button>
    </div>
  </div>
</div>
<?php endforeach; ?>

<!-- Announcement Modal Styles -->
<style>
.announcement-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 9999;
  animation: fadeIn 0.3s ease;
}

.announcement-modal-backdrop {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
}

.announcement-modal-content {
  position: relative;
  background: white;
  margin: 2% auto;
  padding: 0;
  width: 90%;
  max-width: 700px;
  max-height: 90vh;
  border-radius: 12px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
  animation: slideIn 0.3s ease;
  overflow: hidden;
  z-index: 10000;
}

.announcement-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  background: #f8fafc;
}

.announcement-header-info {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.announcement-category {
  background: #1e40af;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.announcement-date {
  color: #6b7280;
  font-size: 0.9rem;
}

.announcement-modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6b7280;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.2s;
}

.announcement-modal-close:hover {
  background: #f3f4f6;
}

.announcement-modal-body {
  padding: 1.5rem;
  max-height: 60vh;
  overflow-y: auto;
}

.announcement-modal-body h3 {
  margin: 0 0 1rem 0;
  font-size: 1.5rem;
  color: #1e40af;
  line-height: 1.3;
}

.announcement-image {
  width: 100%;
  max-height: 200px;
  object-fit: cover;
  border-radius: 8px;
  margin: 1rem 0;
}

.announcement-excerpt {
  background: #f8fafc;
  padding: 1rem;
  border-radius: 8px;
  border-left: 4px solid #1e40af;
  margin: 1rem 0;
  font-style: italic;
}

.announcement-content {
  line-height: 1.6;
  color: #374151;
}

.announcement-content p {
  margin-bottom: 1rem;
}

.announcement-modal-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid #e5e7eb;
  text-align: right;
  background: #f8fafc;
}

@media (max-width: 768px) {
  .announcement-modal-content {
    margin: 5% auto;
    width: 95%;
    max-height: 95vh;
  }
  
  .announcement-header-info {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
}
</style>

<script>
// Announcement Modal Functions
function openAnnouncementModal(announcementId) {
  const modal = document.getElementById('announcement-modal-' + announcementId);
  if (modal) {
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
  }
}

function closeAnnouncementModal() {
  const modals = document.querySelectorAll('.announcement-modal');
  modals.forEach(modal => {
    modal.style.display = 'none';
  });
  document.body.style.overflow = '';
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeAnnouncementModal();
  }
});
</script>

</body>
</html>
