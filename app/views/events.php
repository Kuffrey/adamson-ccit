<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/EventsPageSettings.php';

if (!function_exists('e')) {
    function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('url_with')) {
    function url_with(array $params): string {
      $base = '/adamson-ccit/public/index.php';
      $q = array_merge(['page' => 'events'], $params);
      return $base . '?' . http_build_query($q);
    }
}

$cat   = isset($_GET['cat'])  ? strtolower(trim((string)$_GET['cat'])) : 'all';
$year  = isset($_GET['year']) ? trim((string)$_GET['year'])            : 'all';
$q     = isset($_GET['q'])    ? trim((string)$_GET['q'])               : '';
$page  = max(1, (int)($_GET['p'] ?? 1));
$per   = 9;

$items = []; $total = 0; $metaYears = [];
try {
  $res = Event::searchPublished(
    [
      'cat'  => $cat === 'all' ? null : $cat,
      'year' => $year === 'all' ? null : (int)$year,
      'q'    => $q !== '' ? $q : null,
    ],
    ['page'=>$page, 'perPage'=>$per]
  );
  $items = $res['items'] ?? [];
  $total = (int)($res['total'] ?? 0);
  $metaYears = $res['years'] ?? [];
} catch (\Throwable $e) { $items=[]; $total=0; $metaYears=[]; }

$pages = max(1, (int)ceil($total / $per));
$page  = min($page, $pages);

$cats = [
  'all'=>'All','career'=>'Career','forum'=>'Forum','workshop'=>'Workshop','competition'=>'Competition','community'=>'Community'
];

$settings = (new EventsPageSettings())->get();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Events | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
</head>
<body>
<main>

  <style>
    /* Consistent container padding */
    .content > .container { padding: 16px 20px clamp(24px,5vw,48px); }
    .egrid { padding: 16px 0 clamp(32px,6vw,56px); }
  </style>

  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior" />
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">CCIT Updates</p>
      <h1 class="subhero__title">Events</h1>
        <p class="subhero__lead"><?= htmlspecialchars($settings['subhero_lead'] ?? 'Career fairs, forums, workshops, and student showcases happening at CCIT.') ?></p>
    </div>
  </section>

  <nav class="subnav" aria-label="CCIT sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li><a href="/adamson-ccit/public/index.php?page=news">News</a></li>
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=events" aria-current="page">Events</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=announcements">Announcements</a></li>
      </ul>
    </div>
  </nav>

  <!-- FILTER BAR -->
  <section class="nbar">
    <div class="container nbar__inner">
      <div class="nbar__left">
        <div class="ncats" role="tablist" aria-label="Filter events by category">
          <?php foreach ($cats as $val=>$label):
            $active = ($cat === $val) ? 'is-active' : '';
            $href = url_with(['cat'=>$val,'year'=>$year,'q'=>$q?:null,'p'=>1]);
          ?>
            <a class="pill <?= $active ?>" role="tab" aria-selected="<?= $active? 'true':'false' ?>" href="<?= e($href) ?>"><?= e($label) ?></a>
          <?php endforeach; ?>
        </div>
        <form class="nyear" method="get" action="/adamson-ccit/public/index.php">
          <input type="hidden" name="page" value="events">
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
      <form class="nsearch" role="search" aria-label="Search events" method="get" action="/adamson-ccit/public/index.php">
        <input type="hidden" name="page" value="events">
        <input type="hidden" name="cat" value="<?= e($cat) ?>">
        <input type="hidden" name="year" value="<?= e($year) ?>">
        <input id="eQuery" name="q" type="search" value="<?= e($q) ?>" placeholder="Search events…" aria-label="Search events" />
        <button class="btn btn--solid" type="submit"></button>
      </form>
    </div>
  </section>

  <!-- EVENTS GRID -->
  <section class="nlist">
    <div class="container">
      <?php
        $catLabel = $cats[$cat] ?? 'All';
        $yrLabel  = ($year==='all') ? 'all years' : $year;
        $countTxt = $total === 0 ? 'No events found' : "Showing {$total} result" . ($total!==1?'s':'');
        $chipClass = [
          'career' =>'chip',
          'forum'  =>'chip chip--blue',
          'workshop'=>'chip chip--gray',
          'competition'=>'chip chip--green',
          'community'=>'chip'
        ];
      ?>
      <div id="eCount" class="ncount"><?= e($countTxt) ?> • <?= e($catLabel) ?> • <?= e($yrLabel) ?></div>

      <div id="eGrid" class="cards">
        <?php if (!$items): ?>
          <div class="nempty" role="status">No events match your filters.</div>
        <?php else: foreach ($items as $row):
          $id    = (int)($row['id'] ?? 0);
          $title = (string)($row['title'] ?? 'Untitled');
          $desc  = (string)($row['description'] ?? '');
          $loc   = (string)($row['location'] ?? '');
          $catKey= strtolower((string)($row['category'] ?? 'career'));
          $img   = (string)($row['image_url'] ?? '/adamson-ccit/public/assets/images/news/sample5.jpg');
          $start = (string)($row['start_at'] ?? '');
          $end   = (string)($row['end_at']   ?? '');
          $chip  = $chipClass[$catKey] ?? 'chip';
          $y     = $start ? date('Y', strtotime($start)) : '';
          $editUrl= '/adamson-ccit/public/index.php?page=admin_manage_events&action=edit&id='.$id;
        ?>
        <article class="n" data-cat="<?= e($catKey) ?>" data-year="<?= e($y) ?>" data-date="<?= e(substr($start,0,10)) ?>">
          <div class="n__media" onclick="openEventModal('<?= e((string)$id) ?>')" style="cursor: pointer;">
            <img src="<?= e($img) ?>" alt="<?= e($title) ?>" />
            <span class="<?= e($chip) ?>"><?= e(ucfirst($catKey)) ?></span>
          </div>
          <div class="n__body">
            <h3 class="n__title"><a href="#" onclick="openEventModal('<?= e((string)$id) ?>')"><?= e($title) ?></a></h3>
            <?php if ($loc): ?><p class="n__meta"><?= e($loc) ?></p><?php endif; ?>
            <?php if ($desc): ?><p class="n__excerpt"><?= e(mb_substr(strip_tags($desc), 0, 140).'…') ?></p><?php endif; ?>
            <div class="n__meta">
              <?php if ($start): ?>
                <time datetime="<?= e($start) ?>"><?= e(date('M j, Y', strtotime($start))) ?></time>
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
      <nav class="pager" aria-label="Events pagination">
        <a class="pg" href="<?= e($prevUrl) ?>" <?= $page<=1?'disabled':'' ?>>« Prev</a>
        <span class="pg__status">Page <?= (int)$page ?> of <?= (int)$pages ?></span>
        <a class="pg" href="<?= e($nextUrl) ?>" <?= $page>=$pages?'disabled':'' ?>>Next »</a>
      </nav>
    </div>
  </section>
</main>

<!-- Event Modals -->
<?php foreach ($items as $row): 
  $id = (int)($row['id'] ?? 0);
  $title = (string)($row['title'] ?? 'Untitled');
  $content = (string)($row['description'] ?? '');
  $catKey = strtolower((string)($row['category'] ?? 'career'));
  $img = (string)($row['image_url'] ?? '/adamson-ccit/public/assets/images/news/sample5.jpg');
  $start = (string)($row['start_at'] ?? '');
  $loc = (string)($row['location'] ?? '');
  $formattedDate = $start ? date('F j, Y', strtotime($start)) : '';
?>
<div id="event-modal-<?= e((string)$id) ?>" class="event-modal" style="display: none;">
  <div class="event-modal-backdrop" onclick="closeEventModal()"></div>
  <div class="event-modal-content">
    <div class="event-modal-header">
      <div class="event-header-info">
        <span class="event-category"><?= e(ucfirst($catKey)) ?></span>
        <?php if ($formattedDate): ?>
          <span class="event-date"><?= e($formattedDate) ?></span>
        <?php endif; ?>
      </div>
      <button class="event-modal-close" onclick="closeEventModal()" aria-label="Close">&times;</button>
    </div>
    <div class="event-modal-body">
      <h3><?= e($title) ?></h3>
      <?php if ($img): ?>
        <img src="<?= e($img) ?>" alt="<?= e($title) ?>" class="event-image">
      <?php endif; ?>
      <?php if ($loc): ?>
        <p class="event-location"><strong>📍 Location:</strong> <?= e($loc) ?></p>
      <?php endif; ?>
      <div class="event-content">
        <?= $content ? nl2br(e($content)) : '<p>More details about this event will be available soon.</p>' ?>
      </div>
    </div>
    <div class="event-modal-footer">
      <button class="btn btn--solid" onclick="closeEventModal()">Close</button>
    </div>
  </div>
</div>
<?php endforeach; ?>

<!-- Event Modal Styles -->
<style>
.event-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 9999;
  animation: fadeIn 0.3s ease;
}

.event-modal-backdrop {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
}

.event-modal-content {
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

.event-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  background: #f8fafc;
}

.event-header-info {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.event-category {
  background: #1e40af;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.event-date {
  color: #6b7280;
  font-size: 0.9rem;
}

.event-modal-close {
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

.event-modal-close:hover {
  background: #f3f4f6;
}

.event-modal-body {
  padding: 1.5rem;
  max-height: 60vh;
  overflow-y: auto;
}

.event-modal-body h3 {
  margin: 0 0 1rem 0;
  font-size: 1.5rem;
  color: #1e40af;
  line-height: 1.3;
}

.event-image {
  width: 100%;
  max-height: 200px;
  object-fit: cover;
  border-radius: 8px;
  margin: 1rem 0;
}

.event-location {
  background: #f0f9ff;
  padding: 0.75rem;
  border-radius: 8px;
  border-left: 4px solid #0ea5e9;
  margin: 1rem 0;
  color: #0c4a6e;
}

.event-content {
  line-height: 1.6;
  color: #374151;
}

.event-content p {
  margin-bottom: 1rem;
}

.event-modal-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid #e5e7eb;
  text-align: right;
  background: #f8fafc;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideIn {
  from { 
    opacity: 0;
    transform: translateY(-50px) scale(0.95);
  }
  to { 
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@media (max-width: 768px) {
  .event-modal-content {
    margin: 5% auto;
    width: 95%;
    max-height: 95vh;
  }
  
  .event-header-info {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
}
</style>

<script>
// Event Modal Functions
function openEventModal(eventId) {
  const modal = document.getElementById('event-modal-' + eventId);
  if (modal) {
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
  }
}

function closeEventModal() {
  const modals = document.querySelectorAll('.event-modal');
  modals.forEach(modal => {
    modal.style.display = 'none';
  });
  document.body.style.overflow = '';
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeEventModal();
  }
});
</script>

</body>
</html>
