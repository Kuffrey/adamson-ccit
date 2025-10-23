<?php
require_once __DIR__ . '/../models/Announcement.php';

if (!function_exists('e')) { function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); } }
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

  <!-- SUB-HERO -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>

    <div class="container hero__inner">
      <div class="hero__copy">
        <span class="hero__eyebrow">CCIT Updates</span>
        <h1 class="subhero__title">Announcements</h1>
        <p class="hero__lead">Official CCIT advisories, deadlines, and important updates.</p>
      </div>
    </div>
  </section>

  <!-- SUBNAV -->
  <nav class="subnav" aria-label="News sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li><a href="/adamson-ccit/public/index.php?page=news">News</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=events">Events</a></li>
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=announcements" aria-current="page">Announcements</a></li>
      </ul>
    </div>
  </nav>

  <!-- FILTER BAR -->
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
        <input id="aQuery" name="q" type="search" value="<?= e($q) ?>" placeholder="Search…" aria-label="Search announcements" />
        <button class="btn btn--solid" type="submit"></button>
      </form>
    </div>
  </section>

  <!-- ANNOUNCEMENTS GRID -->
  <section class="nlist">
    <div class="container">
      <div id="aCount" class="rcount">Loading...</div>

      <div id="aGrid" class="cards">
        <?php foreach ($items as $row):
          $id     = (int)($row['id'] ?? 0);
          $title  = (string)($row['title'] ?? 'Untitled');
          $excerpt= (string)($row['excerpt'] ?? '');
          $catKey = strtolower((string)($row['category'] ?? 'general'));
          $img    = (string)($row['image_url'] ?? '/adamson-ccit/public/assets/images/news/sample3.jpg');
          $date   = (string)($row['date'] ?? $row['published_at'] ?? '');
        ?>
        <article class="n" data-cat="<?= e($catKey) ?>" data-year="<?= e(substr($date,0,4) ?: '') ?>">
          <a class="n__media" href="#" onclick="openModal('announcement-modal-<?= e($id) ?>'); return false;">
            <img src="<?= e($img) ?>" alt="<?= e($title) ?>">
            <span class="chip chip--gray"><?= e(ucfirst($catKey)) ?></span>
          </a>
          <div class="n__body">
            <h3 class="n__title"><a href="#" onclick="openModal('announcement-modal-<?= e($id) ?>'); return false;"><?= e($title) ?></a></h3>
            <?php if ($excerpt): ?><p class="n__excerpt"><?= e($excerpt) ?></p><?php endif; ?>
            <div class="n__meta">
              <?php if ($date): ?><time datetime="<?= e($date) ?>"><?= e(date('M j, Y', strtotime($date))) ?></time><?php endif; ?>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <div id="aEmpty" class="nempty" hidden>No announcements match your filters.</div>

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

<!-- Modals (use unified modal CSS from style.css) -->
<?php foreach ($items as $row):
  $id = (int)($row['id'] ?? 0);
  $title = (string)($row['title'] ?? 'Untitled');
  $content = (string)($row['body'] ?? ($row['content'] ?? ''));
  $excerpt = (string)($row['excerpt'] ?? '');
  $catKey = strtolower((string)($row['category'] ?? 'general'));
  $img = (string)($row['image_url'] ?? '/adamson-ccit/public/assets/images/news/sample3.jpg');
  $date = (string)($row['date'] ?? $row['published_at'] ?? '');
  $formattedDate = $date ? date('F j, Y', strtotime($date)) : '';
?>
<div id="announcement-modal-<?= e($id) ?>" class="modal announcement-modal" role="dialog" aria-modal="true" style="display:none;">
  <div class="modal-backdrop announcement-modal-backdrop"></div>
  <div class="modal-content announcement-modal-content">
    <div class="modal-header announcement-modal-header">
      <div class="modal-header-info announcement-header-info">
        <span class="modal-category announcement-category"><?= e(ucfirst($catKey)) ?></span>
        <?php if ($formattedDate): ?><span class="modal-date announcement-date"><?= e($formattedDate) ?></span><?php endif; ?>
      </div>
      <button class="modal-close announcement-modal-close" aria-label="Close" data-modal-focus>&times;</button>
    </div>
    <div class="modal-body announcement-modal-body">
      <h3><?= e($title) ?></h3>
      <?php if ($img): ?><img src="<?= e($img) ?>" alt="<?= e($title) ?>" class="announcement-image"><?php endif; ?>
      <?php if ($excerpt): ?><p class="announcement-excerpt"><?= e($excerpt) ?></p><?php endif; ?>
      <div class="announcement-content">
        <?= $content ? nl2br(e($content)) : '<p>No additional content available.</p>' ?>
      </div>
    </div>
    <div class="modal-footer announcement-modal-footer">
      <button class="btn btn--solid" onclick="closeModal()">Close</button>
    </div>
  </div>
</div>
<?php endforeach; ?>

<!-- Reuse the same generic modal JS -->
<script>
(function(){
  window.openModal = function(id){
    var m = document.getElementById(id);
    if (!m) return;
    m.classList.add('modal--open');
    m.style.display = 'block';
    document.body.dataset.modalScrollLock = document.body.style.overflow || '';
    document.body.style.overflow = 'hidden';
    var f = m.querySelector('[data-modal-focus], a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])');
    if (f) { try { f.focus(); } catch(_) {} }
  };
  window.closeModal = function(){
    document.querySelectorAll('.modal, .event-modal, .announcement-modal').forEach(function(m){
      m.classList.remove('modal--open');
      m.style.display = 'none';
    });
    document.body.style.overflow = document.body.dataset.modalScrollLock || '';
  };
  document.addEventListener('click', function(e){
    if (e.target.matches('.modal-backdrop, .event-modal-backdrop, .announcement-modal-backdrop')) closeModal();
    if (e.target.matches('.modal-close, .event-modal-close, .announcement-modal-close')) { e.preventDefault(); closeModal(); }
  });
  document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });
})();
</script>

<script>
/* Announcements filter/search (client-side) — unified rcount behavior */
(function(){
  'use strict';
  var pills   = Array.prototype.slice.call(document.querySelectorAll('.ncats .pill'));
  var yearSel = document.querySelector('.nyear select[name="year"]');
  var qInput  = document.getElementById('aQuery');
  var grid    = document.getElementById('aGrid');
  var countEl = document.getElementById('aCount');
  var emptyEl = document.getElementById('aEmpty');
  if (!countEl) return;
  var cards = grid ? Array.prototype.slice.call(grid.querySelectorAll('.n')) : [];

  var urlParams = new URLSearchParams(window.location.search);
  var activeCat = (urlParams.get('cat') || 'all').toLowerCase();

  pills.forEach(function(p){
    try{
      var href=new URL(p.href, window.location.origin);
      var pillCat=(href.searchParams.get('cat')||'all').toLowerCase();
      p.classList.toggle('is-active', pillCat===activeCat);
    }catch(_){}
  });

  function labelizeCat(cat){ if (cat==='all') return 'All';
    var cap=cat.charAt(0).toUpperCase()+cat.slice(1); return cap+(cap.slice(-1)==='s'?'':'s'); }

  function apply(){
    var q=(qInput && qInput.value ? qInput.value : '').trim().toLowerCase();
    var y=(yearSel && yearSel.value) ? yearSel.value : 'all';
    var visible=0;

    cards.forEach(function(card){
      var cat=(card.getAttribute('data-cat')||'').toLowerCase();
      var year=(card.getAttribute('data-year')||'');
      var text=(card.innerText||'').toLowerCase();
      var ok=true;
      if(activeCat!=='all' && cat!==activeCat) ok=false;
      if(ok && y!=='all' && year!==y) ok=false;
      if(ok && q && text.indexOf(q)===-1) ok=false;
      card.style.display= ok ? '' : 'none';
      if(ok) visible++;
    });

    var catLabel=labelizeCat(activeCat);
    var yearLabel=(y==='all') ? 'all years' : y;
    var searchLabel=q ? (' matching "'+q+'"') : '';
    countEl.textContent='Showing '+visible+' item'+(visible!==1?'s':'')+' • '+catLabel+' • '+yearLabel+searchLabel;
    if (emptyEl) emptyEl.hidden = (visible !== 0);
  }

  pills.forEach(function(p){
    p.addEventListener('click', function(e){
      e.preventDefault();
      pills.forEach(function(x){ x.classList.remove('is-active'); });
      p.classList.add('is-active');
      try{
        var href=new URL(p.href, window.location.origin);
        activeCat=(href.searchParams.get('cat')||'all').toLowerCase();
      }catch(_){ activeCat='all'; }
      apply();
    });
  });

  if (yearSel) yearSel.addEventListener('change', apply);
  if (qInput){
    var form=qInput.closest('form');
    if (form){ form.addEventListener('submit', function(e){ e.preventDefault(); apply(); }); }
  }
  apply();
})();
</script>

</body>
</html>
