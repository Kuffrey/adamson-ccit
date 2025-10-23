
<?php
require_once __DIR__ . '/../models/StudentResearchPageSettings.php';
require_once __DIR__ . '/../models/StudentResearch.php';

// ---------- Helpers ----------
if (!function_exists('e')) {
    function e(string $s): string {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('url_with')) {
    function url_with(array $params): string {
        $base = '/adamson-ccit/public/index.php';
        $q = array_merge(['page' => 'student_research'], $params);
        return $base . '?' . http_build_query($q);
    }
}

$settings = StudentResearchPageSettings::getSettings();

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
    // Get all research and filter
    $seed = StudentResearch::getAll();
    $items = array_values(array_filter($seed, function($r) use ($cat, $year, $q){
        $catOk  = ($cat === 'all') || (strtolower((string)($r['category'] ?? '')) === $cat);
        $yrOk   = ($year === 'all') || (substr((string)($r['year'] ?? ''), 0, 4) === $year);
        $qText  = strtolower(($r['title'] ?? '') . ' ' . ($r['meta'] ?? '') . ' ' . ($r['authors'] ?? ''));
        $qOk    = ($q === '') || (strpos($qText, strtolower($q)) !== false);
        return $catOk && $yrOk && $qOk;
    }));
    $total = count($items);
    // Paginate locally
    $offset = ($page - 1) * $per;
    $items = array_slice($items, $offset, $per);
    // Build year list from seed
    foreach ($seed as $r) {
        $y = substr((string)($r['year'] ?? ''), 0, 4);
        if ($y) $metaYears[$y] = true;
    }
    $metaYears = array_keys($metaYears);
    rsort($metaYears);
} catch (Throwable $e) {
    // Fail-soft: empty list but render the page
    $items = [];
    $total = 0;
    $metaYears = [];
}

// ---------- Paging calc ----------
$pages = max(1, (int)ceil($total / $per));
$page  = min($page, $pages);

// ---------- Category → chip color ----------
$chipClass = [
    'publication' => 'chip chip--blue',
    'project'     => 'chip chip--green',
    'award'       => 'chip chip--gray',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Research | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
  
  <style>
    /* Consistent container padding */
    .content > .container { 
      padding: 16px 20px clamp(24px,5vw,48px); 
    }
    
    /* Professional grid layout */
    .prog__grid {
      display: grid;
      gap: 20px;
      grid-template-columns: repeat(3,1fr);
      padding: 20px 0 clamp(32px,6vw,56px);
    }
    
    @media (max-width:960px) {
      .prog__grid {
        grid-template-columns: 1fr 1fr;
        gap: 18px;
      }
    }
    
    @media (max-width:580px) {
      .prog__grid {
        grid-template-columns: 1fr;
        gap: 16px;
      }
    }
    
    /* Enhanced card styling */
    .prog__card {
      border: 1px solid var(--edgec);
      border-radius: 16px;
      background: #fff;
      overflow: hidden;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .prog__card:hover {
      box-shadow: 0 12px 32px rgba(0,0,0,0.12);
      transform: translateY(-4px);
      border-color: #d1d5db;
    }
    
    /* Card header */
    .prog__head {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 24px 24px 16px;
      border-bottom: 1px solid #f3f4f6;
    }
    
    .prog__title {
      margin: 0;
      font-size: 18px;
      font-weight: 900;
      color: #0b234c;
      line-height: 1.3;
    }
    
    /* Research type badges */
    .res__type {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 12px;
      border-radius: 20px;
      font: 700 11px/1 "Inter",system-ui;
      letter-spacing: .05em;
      text-transform: uppercase;
      border: 1px solid var(--edgec);
      background: #f8fafc;
      color: #0b234c;
      white-space: nowrap;
    }
    
    .res__type--thesis {
      background: #dcfce7;
      border-color: #bbf7d0;
      color: #15803d;
    }
    
    .res__type--project {
      background: #fef3c7;
      border-color: #fcd34d;
      color: #92400e;
    }
    
    /* Card content */
    .prog__summary {
      padding: 0 24px 16px;
      margin: 0;
      color: #374151;
      line-height: 1.6;
    }
    
    .pillbox {
      margin: 0 24px 16px;
      padding: 16px;
      background: #f9fafb;
      border-radius: 12px;
      border: 1px solid #e5e7eb;
    }
    
    .pillbox__title {
      margin: 0 0 8px;
      font-size: 14px;
      font-weight: 700;
      color: #374151;
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }
    
    /* Card footer */
    .prog__footer {
      padding: 16px 24px 24px;
      border-top: 1px solid #f3f4f6;
      background: #fafbfc;
    }
    
    /* External link styling */
    .ext::after {
      content: "↗";
      font-weight: 900;
      margin-left: .5em;
      opacity: .7;
      transition: opacity 0.2s ease;
    }
    
    .ext:hover::after {
      opacity: 1;
    }
  </style>
</head>
<body>

<main>

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="<?= e($settings['subhero_image_url'] ?? '/adamson-ccit/public/assets/images/hero-research.jpg') ?>" alt="Students presenting research posters">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>

    <div class="container hero__inner">
      <div class="hero__copy">
      <span class="hero__eyebrow">Students</span>
      <h1 class="subhero__title">Research</h1>
      <p class="hero__lead"><?= e($settings['subhero_lead'] ?? 'Publications and conference papers by our students and faculty mentors.') ?></p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV ============ -->
  <nav class="subnav" aria-label="Student sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li>
          <a href="/adamson-ccit/public/index.php?page=student_organizations">Organizations</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=student_scholarships">Scholarships</a>
        </li>
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=student_research" aria-current="page">Research</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=student_certifications">Certifications</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=student_testimonials">Testimonials</a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- ============ FILTER BAR (server-side; no session usage) ============ -->
  <section class="nbar">
    <div class="container nbar__inner">
      <div class="nbar__left">
        <div class="ncats" role="tablist" aria-label="Filter research by category">
          <?php
            $cats = ['all'=>'All','publication'=>'Publications','project'=>'Projects','award'=>'Awards'];
            foreach ($cats as $val => $label):
              $active = ($cat === $val) ? 'is-active' : '';
              $href = url_with(['cat'=>$val,'year'=>$year,'q'=>$q?:null,'p'=>1]);
          ?>
            <a class="pill <?= $active ?>" role="tab" aria-selected="<?= $active? 'true':'false' ?>" href="<?= e($href) ?>"><?= e($label) ?></a>
          <?php endforeach; ?>
        </div>

        <form class="nyear" method="get" action="/adamson-ccit/public/index.php">
          <input type="hidden" name="page" value="student_research">
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

      <form class="nsearch" role="search" aria-label="Search research" method="get" action="/adamson-ccit/public/index.php">
        <input type="hidden" name="page" value="student_research">
        <input type="hidden" name="cat" value="<?= e($cat) ?>">
        <input type="hidden" name="year" value="<?= e($year) ?>">
        <input id="nQuery" name="q" type="search" value="<?= e($q) ?>" placeholder="Search…" aria-label="Search research" />
        <button class="btn btn--solid" type="submit"></button>
      </form>
    </div>
  </section>

  <!-- ============ RESEARCH GRID ============ -->
  <section class="rlist">
    <div class="container">
      <div id="rCount" class="rcount">Loading...</div>

      <div id="rGrid" class="cards">
        <?php if (empty($items)): ?>

        <?php else: ?>
          <!-- Dynamic content from database -->
          <?php foreach ($items as $item): ?>
            <article class="r" data-cat="<?= e($item['category'] ?? 'general') ?>" data-year="<?= e($item['year'] ?? date('Y')) ?>">
              <a class="r__media" href="<?= e($item['link_url'] ?? '#') ?>" target="_blank" rel="noopener">
                <img src="<?= e($item['image_url'] ?? '/adamson-ccit/public/assets/images/research-placeholder.jpg') ?>" 
                     alt="Poster: <?= e($item['title'] ?? 'Research') ?>">
                <span class="<?= $chipClass[$item['category'] ?? 'publication'] ?? 'chip chip--blue' ?>">
                  <?= e(ucfirst($item['category'] ?? 'Publication')) ?>
                </span>
              </a>
              <div class="r__body">
                <h3 class="r__title">
                  <a href="<?= e($item['link_url'] ?? '#') ?>" target="_blank" rel="noopener">
                    <?= e($item['title'] ?? 'Untitled Research') ?>
                  </a>
                </h3>
                <?php if (!empty($item['meta']) || !empty($item['authors'])): ?>
                  <p class="r__meta">
                    <?= e($item['authors'] ?? '') ?>
                    <?= (!empty($item['authors']) && !empty($item['meta'])) ? ' • ' : '' ?>
                    <?= e($item['meta'] ?? '') ?>
                  </p>
                <?php endif; ?>
                <div class="r__actions">
                  <?php if (!empty($item['link_url']) && !empty($item['link_label'])): ?>
                    <a class="btn btn--outline-blue" href="<?= e($item['link_url']) ?>" target="_blank" rel="noopener">
                      <?= e($item['link_label']) ?>
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div id="rEmpty" class="nempty" hidden>No research matches your filters.</div>
    </div>
  </section>

</main>

<script>
  // Research filter/search (client-side)
  (function(){
    'use strict';
    
    const pills = Array.from(document.querySelectorAll('.ncats .pill'));
    const yearSel = document.querySelector('.nyear select[name="year"]');
    const qInput = document.querySelector('#nQuery');
    const grid = document.getElementById('rGrid');
    const cards = Array.from(grid.querySelectorAll('.r'));
    const count = document.getElementById('rCount');
    const empty = document.getElementById('rEmpty');

    // Set initial active category based on URL
    const urlParams = new URLSearchParams(window.location.search);
    let activeCat = urlParams.get('cat') || 'all';
    
    // Update active pill
    pills.forEach(p => {
      const href = new URL(p.href, window.location.origin);
      const pillCat = href.searchParams.get('cat') || 'all';
      if (pillCat === activeCat) {
        p.classList.add('is-active');
      } else {
        p.classList.remove('is-active');
      }
    });

    function apply(){
      const q = (qInput.value || '').trim().toLowerCase();
      const y = yearSel.value;
      let visible = 0;

      cards.forEach(c => {
        const cat = (c.getAttribute('data-cat') || '').toLowerCase();
        const year = (c.getAttribute('data-year') || '');
        const text = c.innerText.toLowerCase();

        let ok = true;
        if (activeCat !== 'all' && cat !== activeCat) ok = false;
        if (ok && y !== 'all' && year !== y) ok = false;
        if (ok && q && !text.includes(q)) ok = false;

        c.style.display = ok ? '' : 'none';
        if (ok) visible++;
      });

      const catLabel = activeCat === 'all' ? 'All' :
        activeCat.charAt(0).toUpperCase() + activeCat.slice(1) + (activeCat.endsWith('s') ? '' : 's');
      const yearLabel = (y === 'all') ? 'all years' : y;
      const searchLabel = q ? ` matching "${q}"` : '';

      count.textContent = `Showing ${visible} item${visible!==1?'s':''} • ${catLabel} • ${yearLabel}${searchLabel}`;
      empty.hidden = visible !== 0;
    }

    // Handle category filter clicks (prevent default, update active state)
    pills.forEach(p => p.addEventListener('click', (e) => {
      e.preventDefault();
      pills.forEach(x => x.classList.remove('is-active'));
      p.classList.add('is-active');
      
      const href = new URL(p.href, window.location.origin);
      activeCat = href.searchParams.get('cat') || 'all';
      apply();
    }));

    // Handle year change
    if (yearSel) {
      yearSel.addEventListener('change', apply);
    }

    // Handle search
    if (qInput) {
      const searchForm = qInput.closest('form');
      if (searchForm) {
        searchForm.addEventListener('submit', (e) => { 
          e.preventDefault(); 
          apply(); 
        });
      }
    }

    // Initial filter application
    apply();
  })();
</script>

</body>
</html>
