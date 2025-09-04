<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/EventsPageSettings.php';

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function url_with(array $params): string {
  $base = '/adamson-ccit/public/index.php';
  $q = array_merge(['page' => 'events'], $params);
  return $base . '?' . http_build_query($q);
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

  <nav class="subnav" aria-label="News sub-navigation">
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
        <button class="btn btn--solid" type="submit">Search</button>
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
          <a class="n__media" href="#">
            <img src="<?= e($img) ?>" alt="<?= e($title) ?>" />
            <span class="<?= e($chip) ?>"><?= e(ucfirst($catKey)) ?></span>
          </a>
          <div class="n__body">
            <h3 class="n__title"><a href="#"><?= e($title) ?></a></h3>
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
</body>
</html>
