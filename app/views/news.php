<?php
declare(strict_types=1);

// Expect Router to have loaded Auth already. We only require the model here.

require_once __DIR__ . '/../models/News.php';
require_once __DIR__ . '/../models/NewsPageSettings.php';

// ---------- Helpers ----------
if (!function_exists('e')) {
    function e(string $s): string {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('url_with')) {
    function url_with(array $params): string {
        $base = '/adamson-ccit/public/index.php';
        $q = array_merge(['page' => 'news'], $params);
        return $base . '?' . http_build_query($q);
    }
}

$settings = NewsPageSettings::getSettings();

// ---------- Inputs ----------
$cat   = isset($_GET['cat'])  ? strtolower(trim((string)$_GET['cat']))   : 'all';
$year  = isset($_GET['year']) ? trim((string)$_GET['year'])               : 'all';
$q     = isset($_GET['q'])    ? trim((string)$_GET['q'])                  : '';
$page  = max(1, (int)($_GET['p'] ?? 1));
$per   = 9;

// ---------- Fetch ----------
$items = [];
$total = 0;
$metaYears = [];

try {
    if (method_exists(News::class, 'searchPublished')) {
        // Optional richer method you can add in your model:
        // News::searchPublished(['cat'=>?, 'year'=>?, 'q'=>?], ['page'=>?, 'perPage'=>?])
        $result = News::searchPublished(
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
    } else {
        // Fallback: use latest() and do light in-PHP filtering so the page still works now.
        $seed = News::latest(48);
        $items = array_values(array_filter($seed, function($r) use ($cat, $year, $q){
            $catOk  = ($cat === 'all') || (strtolower((string)($r['category'] ?? '')) === $cat);
            $yrOk   = ($year === 'all') || (substr((string)($r['date'] ?? ''), 0, 4) === $year);
            $qText  = strtolower(($r['title'] ?? '') . ' ' . ($r['excerpt'] ?? '') . ' ' . ($r['body'] ?? ''));
            $qOk    = ($q === '') || (strpos($qText, strtolower($q)) !== false);
            return $catOk && $yrOk && $qOk;
        }));
        $total = count($items);
        // Paginate locally
        $offset = ($page - 1) * $per;
        $items = array_slice($items, $offset, $per);
        // Build year list from seed
        foreach ($seed as $r) {
            $y = substr((string)($r['date'] ?? ''), 0, 4);
            if ($y) $metaYears[$y] = true;
        }
        $metaYears = array_keys($metaYears);
        rsort($metaYears);
    }
} catch (Throwable $e) {
    // Fail-soft: empty list but render the page
    $items = [];
    $total = 0;
    $metaYears = [];
}

// ---------- Paging calc ----------
$pages = max(1, (int)ceil($total / $per));
$page  = min($page, $pages);

// ---------- Role (for CMS actions) ----------
$role = class_exists('Auth') ? (Auth::role() ?? null) : null;
$isManager = in_array($role, ['admin','dean'], true);

// ---------- Category → chip color ----------
$chipClass = [
    'research'     => 'chip chip--blue',
    'achievement'  => 'chip',
    'announcement' => 'chip chip--gray',
    'student'      => 'chip chip--green',
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>News | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
</head>
<body>

<main>

  <style>
    /* Consistent container padding */
    .content > .container { padding: 16px 20px clamp(24px,5vw,48px); }
    .ngrid { padding: 16px 0 clamp(32px,6vw,56px); }
  </style>

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">CCIT Updates</p>
      <h1 class="subhero__title">News</h1>
      <p class="subhero__lead"><?= e($settings['subhero_lead'] ?? '') ?></p>
    </div>
  </section>

  <?php if (!empty($settings['announcement'])): ?>
  <section class="announcement">
    <div class="container">
      <div class="announcement__content">
        <?= nl2br(e($settings['announcement'])) ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ============ LOCAL SUBNAV ============ -->
<nav class="subnav" aria-label="News sub-navigation">
  <div class="container">
    <ul class="subnav__list" role="list">
      <li class="is-active">
        <a href="/adamson-ccit/public/index.php?page=news" aria-current="page">News</a>
      </li>
      <li>
        <a href="/adamson-ccit/public/index.php?page=events">Events</a>
      </li>
      <li>
        <a href="/adamson-ccit/public/index.php?page=announcements">Announcements</a>
      </li>
    </ul>
  </div>
</nav>

  <!-- ============ FILTER BAR (server-side; no session usage) ============ -->
  <section class="nbar">
    <div class="container nbar__inner">
      <div class="nbar__left">
        <div class="ncats" role="tablist" aria-label="Filter news by category">
          <?php
            $cats = ['all'=>'All','research'=>'Research','achievement'=>'Achievements','student'=>'Student Life'];
            foreach ($cats as $val => $label):
              $active = ($cat === $val) ? 'is-active' : '';
              $href = url_with(['cat'=>$val,'year'=>$year,'q'=>$q?:null,'p'=>1]);
          ?>
            <a class="pill <?= $active ?>" role="tab" aria-selected="<?= $active? 'true':'false' ?>" href="<?= e($href) ?>"><?= e($label) ?></a>
          <?php endforeach; ?>
        </div>

        <form class="nyear" method="get" action="/adamson-ccit/public/index.php">
          <input type="hidden" name="page" value="news">
          <input type="hidden" name="cat" value="<?= e($cat) ?>">
          <input type="hidden" name="q" value="<?= e($q) ?>">
          <select name="year" onchange="this.form.submit()">
            <option value="all" <?= $year==='all'?'selected':''; ?>>All Years</option>
            <?php
              // Prefer metaYears; if empty, seed with a small range
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

      <form class="nsearch" role="search" aria-label="Search news" method="get" action="/adamson-ccit/public/index.php">
        <input type="hidden" name="page" value="news">
        <input type="hidden" name="cat" value="<?= e($cat) ?>">
        <input type="hidden" name="year" value="<?= e($year) ?>">
        <input id="nQuery" name="q" type="search" value="<?= e($q) ?>" placeholder="Search news…" aria-label="Search news" />
        <button class="btn btn--solid" type="submit"></button>
      </form>
    </div>
  </section>

  <!-- ============ OPTIONAL CMS ACTIONS (admins/deans) ============ -->
  <?php if ($isManager): ?>
  <section class="content">
    <div class="container" style="display:flex;justify-content:flex-end;padding:10px 0;">
      <a class="btn btn--outline-blue" href="/adamson-ccit/public/index.php?page=admin_manage_news">➕ Add Article</a>
    </div>
  </section>
  <?php endif; ?>

  <!-- ============ NEWS GRID ============ -->
  <section class="nlist">
    <div class="container">
      <?php
        $catLabel = $cats[$cat] ?? 'All';
        $yrLabel  = ($year==='all') ? 'all years' : $year;
        $countTxt = $total === 0 ? 'No news found' : "Showing {$total} result" . ($total!==1?'s':'');
      ?>
      <div id="nCount" class="ncount"><?= e($countTxt) ?> • <?= e($catLabel) ?> • <?= e($yrLabel) ?></div>

      <div id="nGrid" class="cards">
        <?php if (!$items): ?>
          <div class="nempty" role="status">No news matches your filters.</div>
        <?php else: ?>
          <?php foreach ($items as $row):
            $id     = (int)($row['id'] ?? 0);
            $title  = (string)($row['title'] ?? 'Untitled');
            $excerpt= (string)($row['excerpt'] ?? ($row['summary'] ?? ''));
            if ($excerpt === '' && !empty($row['body'])) {
              $excerpt = mb_substr(strip_tags((string)$row['body']), 0, 140) . '…';
            }
            $catKey = strtolower((string)($row['category'] ?? ''));
            $catCls = $chipClass[$catKey] ?? 'chip';
            $catCap = $cats[$catKey] ?? ucfirst($catKey ?: 'News');
            $img    = (string)($row['image_url'] ?? '/adamson-ccit/public/assets/images/news/sample1.jpg');
            $date   = (string)($row['date'] ?? $row['published_at'] ?? '');
            $author = (string)($row['author'] ?? 'CCIT Communications');
            $viewUrl= '/adamson-ccit/public/index.php?page=news_article&id=' . $id;
            $editUrl= '/adamson-ccit/public/index.php?page=admin_manage_news&action=edit&id='.$id;
          ?>
          <article class="n" data-cat="<?= e($catKey ?: 'news') ?>" data-year="<?= e(substr($date,0,4) ?: '') ?>">
            <a class="n__media" href="<?= e($viewUrl) ?>">
              <img src="<?= e($img) ?>" alt="<?= e($title) ?>">
              <span class="<?= e($catCls) ?>"><?= e($catCap) ?></span>
            </a>
            <div class="n__body">
              <h3 class="n__title"><a href="<?= e($viewUrl) ?>"><?= e($title) ?></a></h3>
              <?php if ($excerpt): ?><p class="n__excerpt"><?= e($excerpt) ?></p><?php endif; ?>
              <div class="n__meta">
                <?php if ($date): ?>
                  <time datetime="<?= e($date) ?>"><?= e(date('M j, Y', strtotime($date))) ?></time>
                <?php endif; ?>
                <?= $author ? ' • '.e($author) : '' ?>
                <?php if ($isManager && $id): ?>
                  &nbsp;•&nbsp;<a href="<?= e($editUrl) ?>" class="link">Edit</a>
                <?php endif; ?>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Pagination -->
      <nav class="pager" aria-label="News pagination">
        <?php
          $prevDisabled = $page <= 1 ? 'disabled' : '';
          $nextDisabled = $page >= $pages ? 'disabled' : '';
          $prevUrl = url_with(['cat'=>$cat,'year'=>$year,'q'=>$q?:null,'p'=>max(1, $page-1)]);
          $nextUrl = url_with(['cat'=>$cat,'year'=>$year,'q'=>$q?:null,'p'=>min($pages, $page+1)]);
        ?>
        <a class="pg" href="<?= e($prevUrl) ?>" <?= $prevDisabled ?>>« Prev</a>
        <span class="pg__status">Page <?= (int)$page ?> of <?= (int)$pages ?></span>
        <a class="pg" href="<?= e($nextUrl) ?>" <?= $nextDisabled ?>>Next »</a>
      </nav>

    </div>
  </section>

</main>

</body>
</html>
