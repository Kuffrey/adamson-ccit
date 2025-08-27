<?php
require_once __DIR__ . '/../models/Announcement.php';
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function url_with(array $params): string {
    $base = '/adamson-ccit/public/index.php';
    $q = array_merge(['page' => 'announcements'], $params);
    return $base . '?' . http_build_query($q);
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
        <button class="btn btn--solid" type="submit">Search</button>
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
          <a class="n__media" href="<?= e($viewUrl) ?>">
            <img src="<?= e($img) ?>" alt="<?= e($title) ?>">
            <span class="chip chip--gray"><?= e(ucfirst($catKey)) ?></span>
          </a>
          <div class="n__body">
            <h3 class="n__title"><a href="<?= e($viewUrl) ?>"><?= e($title) ?></a></h3>
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
</body>
</html>
