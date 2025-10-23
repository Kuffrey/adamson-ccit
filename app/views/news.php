<?php
declare(strict_types=1);

// Expect Router to have loaded Auth already.
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
        $result = News::searchPublished(
            [
                'cat'  => $cat === 'all' ? null : $cat,
                'year' => $year === 'all' ? null : (int)$year,
                'q'    => $q !== '' ? $q : null,
            ],
            ['page' => $page, 'perPage' => $per]
        );
        $items     = $result['items'] ?? [];
        $total     = (int)($result['total'] ?? count($items));
        $metaYears = $result['years'] ?? [];
    } else {
        // Fallback so the page still works now.
        $seed  = News::latest(48);
        $items = array_values(array_filter($seed, function($r) use ($cat, $year, $q){
            $catOk  = ($cat === 'all') || (strtolower((string)($r['category'] ?? '')) === $cat);
            $yrOk   = ($year === 'all') || (substr((string)($r['date'] ?? ''), 0, 4) === $year);
            $qText  = strtolower(($r['title'] ?? '') . ' ' . ($r['excerpt'] ?? '') . ' ' . ($r['body'] ?? ''));
            $qOk    = ($q === '') || (strpos($qText, strtolower($q)) !== false);
            return $catOk && $yrOk && $qOk;
        }));
        $total  = count($items);
        $offset = ($page - 1) * $per;
        $items  = array_slice($items, $offset, $per);
        foreach ($seed as $r) {
            $y = substr((string)($r['date'] ?? ''), 0, 4);
            if ($y) $metaYears[$y] = true;
        }
        $metaYears = array_keys($metaYears);
        rsort($metaYears);
    }
} catch (Throwable $e) {
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

// ---------- Category labels (mirrors student_research) ----------
$cats = ['all'=>'All','research'=>'Research','achievement'=>'Achievements','student'=>'Student Life'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>News | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
  <style>
    .content > .container { padding: 16px 20px clamp(24px,5vw,48px); }
    .nlist .container { padding: 16px 20px clamp(24px,5vw,48px); }
  </style>
</head>
<body>

<main>

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>

    <div class="container hero__inner">
      <div class="hero__copy">
        <span class="hero__eyebrow">CCIT Updates</span>
        <h1 class="subhero__title">News</h1>
        <p class="hero__lead"><?= e($settings['subhero_lead'] ?? '') ?></p>
      </div>
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

  <!-- ============ FILTER BAR ============ -->
  <section class="nbar">
    <div class="container nbar__inner">
      <div class="nbar__left">
        <div class="ncats" role="tablist" aria-label="Filter news by category">
          <?php foreach ($cats as $val => $label):
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
        <input id="nQuery" name="q" type="search" value="<?= e($q) ?>" placeholder="Search…" aria-label="Search news" />
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
      <!-- EXACTLY like student_research: JS fills this -->
      <div id="nCount" class="rcount">Loading...</div>

      <div id="nGrid" class="cards">
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
      </div>

      <div id="nEmpty" class="nempty" hidden>No news matches your filters.</div>

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

<script>
// News filter/search (client-side) — mirrors student_research rcount behavior
(function(){
  'use strict';

  var pills   = Array.prototype.slice.call(document.querySelectorAll('.ncats .pill'));
  var yearSel = document.querySelector('.nyear select[name="year"]');
  var qInput  = document.getElementById('nQuery');
  var grid    = document.getElementById('nGrid');
  var countEl = document.getElementById('nCount');
  var emptyEl = document.getElementById('nEmpty');

  if (!countEl) return;
  var cards = grid ? Array.prototype.slice.call(grid.querySelectorAll('.n')) : [];

  // Initial active category from URL
  var urlParams = new URLSearchParams(window.location.search);
  var activeCat = (urlParams.get('cat') || 'all').toLowerCase();

  // Update active pill state
  pills.forEach(function(p){
    try {
      var href = new URL(p.href, window.location.origin);
      var pillCat = (href.searchParams.get('cat') || 'all').toLowerCase();
      p.classList.toggle('is-active', pillCat === activeCat);
    } catch(_) {}
  });

  function labelizeCat(cat){
    if (cat === 'all') return 'All';
    var cap = cat.charAt(0).toUpperCase() + cat.slice(1);
    return cap + (cap.slice(-1) === 's' ? '' : 's');
  }

  function apply(){
    var q = (qInput && qInput.value ? qInput.value : '').trim().toLowerCase();
    var y = (yearSel && yearSel.value) ? yearSel.value : 'all';
    var visible = 0;

    cards.forEach(function(card){
      var cat  = (card.getAttribute('data-cat')  || '').toLowerCase();
      var year = (card.getAttribute('data-year') || '');
      var text = (card.innerText || '').toLowerCase();

      var ok = true;
      if (activeCat !== 'all' && cat !== activeCat) ok = false;
      if (ok && y !== 'all' && year !== y) ok = false;
      if (ok && q && text.indexOf(q) === -1) ok = false;

      card.style.display = ok ? '' : 'none';
      if (ok) visible++;
    });

    var catLabel    = labelizeCat(activeCat);
    var yearLabel   = (y === 'all') ? 'all years' : y;
    var searchLabel = q ? (' matching "' + q + '"') : '';

    countEl.textContent = 'Showing ' + visible + ' item' + (visible !== 1 ? 's' : '') +
                          ' • ' + catLabel + ' • ' + yearLabel + searchLabel;

    if (emptyEl) emptyEl.hidden = (visible !== 0);
  }

  // Prevent navigation for pills; re-apply filters live
  pills.forEach(function(p){
    p.addEventListener('click', function(e){
      e.preventDefault();
      pills.forEach(function(x){ x.classList.remove('is-active'); });
      p.classList.add('is-active');
      try {
        var href = new URL(p.href, window.location.origin);
        activeCat = (href.searchParams.get('cat') || 'all').toLowerCase();
      } catch(_) { activeCat = 'all'; }
      apply();
    });
  });

  if (yearSel) yearSel.addEventListener('change', apply);

  if (qInput) {
    var form = qInput.closest('form');
    if (form) {
      form.addEventListener('submit', function(e){
        e.preventDefault();
        apply();
      });
    }
  }

  // Initial compute
  apply();
})();
</script>

</body>
</html>
