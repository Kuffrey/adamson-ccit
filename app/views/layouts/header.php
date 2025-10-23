<?php
/* header.php — dynamic main nav & utility links via DB; compact user menu stays the same */

if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;

if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}

/* Build PDO directly from config (same pattern as footer) */
require_once __DIR__ . '/../../config/database.php';
try {
  $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
  $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (PDOException $e) { $pdo = null; }

require_once __DIR__ . '/../../models/HeaderRepository.php';

$settings     = $pdo ? HeaderRepository::getSettings($pdo) : [
  'logo_url'  => '/adamson-ccit/public/assets/images/adamson-ccit-logo.png',
  'cta_label' => 'Career Pathway Generator',
  'cta_url'   => '/adamson-ccit/public/index.php?page=career_pathway_generator',
];
$utilityLinks = $pdo ? HeaderRepository::getUtilityLinks($pdo) : [
  ['label'=>'AdU Website','url'=>'https://www.adamson.edu.ph/2018/','is_external'=>1],
  ['label'=>'AdU Live','url'=>'https://live.adamson.edu.ph/login','is_external'=>1],
];
$menus        = $pdo ? HeaderRepository::getMenu($pdo) : [];

/* Fallback full menu if DB empty */
if (!$menus) {
  $menus = [
    ['label'=>'Home','url'=>'/adamson-ccit/public/index.php','type'=>'link'],
    ['label'=>'About','type'=>'dropdown','items'=>[
      ['label'=>'History','url'=>'/adamson-ccit/public/index.php?page=about_history'],
      ['label'=>'Mission & Vision','url'=>'/adamson-ccit/public/index.php?page=about_vision_mission'],
      ['label'=>'Dean\'s Corner','url'=>'/adamson-ccit/public/index.php?page=deans_corner'],
    ]],
    ['label'=>'News','url'=>'/adamson-ccit/public/index.php?page=news','type'=>'link'],
    ['label'=>'Admission','type'=>'dropdown','items'=>[
      ['label'=>'Freshman','url'=>'/adamson-ccit/public/index.php?page=admission_freshman'],
      ['label'=>'Transferee','url'=>'/adamson-ccit/public/index.php?page=admission_transferee'],
      ['label'=>'Graduate School and Juris Doctor','url'=>'/adamson-ccit/public/index.php?page=admission_graduate_school'],
    ]],
    ['label'=>'Programs','type'=>'dropdown','items'=>[
      ['label'=>'Undergraduate','url'=>'/adamson-ccit/public/index.php?page=programs_undergraduate'],
      ['label'=>'Graduate Studies','url'=>'/adamson-ccit/public/index.php?page=programs_graduate_studies'],
    ]],
    ['label'=>'Student','type'=>'dropdown','items'=>[
      ['label'=>'Student Organizations','url'=>'/adamson-ccit/public/index.php?page=student_organizations'],
      ['label'=>'Student Scholarships','url'=>'/adamson-ccit/public/index.php?page=student_scholarships'],
      ['label'=>'Student Research','url'=>'/adamson-ccit/public/index.php?page=student_research'],
      ['label'=>'Student Certifications','url'=>'/adamson-ccit/public/index.php?page=student_certifications'],
      ['label'=>'Student Testimonials','url'=>'/adamson-ccit/public/index.php?page=student_testimonials'],
    ]],
    ['label'=>'Faculty','type'=>'dropdown','items'=>[
      ['label'=>'Faculty Profile','url'=>'/adamson-ccit/public/index.php?page=faculty_profile'],
      ['label'=>'Faculty Research','url'=>'/adamson-ccit/public/index.php?page=faculty_research'],
      ['label'=>'Faculty Certifications','url'=>'/adamson-ccit/public/index.php?page=faculty_certifications'],
    ]],
  ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
</head>
<body>

<!-- Utility strip -->
<div class="util" role="banner">
  <div class="container">
    <div>Adamson University | College of Computing and Information Technology</div>
    <nav class="util-links" aria-label="Quick links">
      <?php foreach ($utilityLinks as $ul): ?>
        <a href="<?= e($ul['url']); ?>" <?= !empty($ul['is_external']) ? 'target="_blank" rel="noopener"' : '' ?>>
          <?= e($ul['label']); ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </div>
</div>

<!-- Header -->
<header class="site-header">
  <div class="container header-inner">
    <div class="brand">
      <a class="logo" href="/adamson-ccit/public/index.php" aria-label="Adamson CCIT Home">
        <img src="<?= e($settings['logo_url']); ?>" alt="Adamson CCIT Logo">
      </a>
    </div>

    <!-- Hamburger (mobile only via CSS) -->
    <button class="nav-toggle" type="button" aria-label="Open menu" aria-controls="mobileNav" aria-expanded="false">
       <span class="nav-toggle__bar" aria-hidden="true"></span>
    </button>

    <nav aria-label="Main">
      <ul class="main-nav">
        <?php foreach ($menus as $m): ?>
          <?php if (($m['type'] ?? 'link') === 'link'): ?>
            <li><a href="<?= e($m['url']); ?>"><?= e($m['label']); ?></a></li>
          <?php else: ?>
            <li>
              <details <?= strtolower($m['label'])==='faculty' ? 'class="no-cap"' : '' ?>>
                <summary aria-haspopup="true" aria-expanded="false"><?= e($m['label']); ?></summary>
                <div class="panel" role="menu">
                  <?php foreach ($m['items'] ?? [] as $it): ?>
                    <a role="menuitem" href="<?= e($it['url']); ?>"><?= e($it['label']); ?></a>
                  <?php endforeach; ?>
                </div>
              </details>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    </nav>

    <div class="main-nav-right">
      <a href="<?= e($settings['cta_url']); ?>" class="btn blue"><?= e($settings['cta_label']); ?></a>

      <?php if (!$user): ?>
        <a href="/adamson-ccit/public/index.php?page=login" class="btn outline">Login</a>
      <?php else: ?>
        <details class="user-menu" aria-label="User menu">
          <summary aria-label="Open user menu" title="Account">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          </summary>
          <div class="panel" role="menu">
            <div class="umeta">
              <div><strong><?= e($user['username']); ?></strong></div>
              <div class="role"><?= e($user['role']); ?></div>
            </div>

            <?php if ($user['role'] === 'student'): ?>
              <a role="menuitem" href="/adamson-ccit/public/index.php?page=student_profile">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7h-5l-2-2H9L7 7H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0  0 0-2-2z"/></svg>
                My Portfolio
              </a>
              <a role="menuitem" href="/adamson-ccit/public/index.php?page=career_pathway_generator">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16 8 14 14 8 16 10 10 16 8"/></svg>
                Career Pathway
              </a>
            <?php elseif ($user['role'] === 'faculty'): ?>
              <a role="menuitem" href="/adamson-ccit/public/index.php?page=faculty_dashboard">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Faculty Dashboard
              </a>
              <a role="menuitem" href="/adamson-ccit/public/index.php?page=faculty_manage_news">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20l-6-6 6-6"/><path d="M6 8v8"/></svg>
                Submissions
              </a>
              <a role="menuitem" href="/adamson-ccit/public/index.php?page=faculty_manage_certifications">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><rect x="3" y="3" width="18" height="14" rx="2"/></svg>
                My Portfolio
              </a>
            <?php elseif ($user['role'] === 'admin'): ?>
              <a role="menuitem" href="/adamson-ccit/public/index.php?page=admin_dashboard">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Admin Dashboard
              </a>
            <?php elseif ($user['role'] === 'dean'): ?>
              <a role="menuitem" href="/adamson-ccit/public/index.php?page=dean_dashboard">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dean Dashboard
              </a>
            <?php endif; ?>
            

            <a role="menuitem" href="/adamson-ccit/public/index.php?page=logout">
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Logout
            </a>
          </div>
        </details>
      <?php endif; ?>
    </div>
  </div>

  <!-- Mobile drawer (reuses same $menus) -->
  <div id="mobileNav" class="mobile-nav" hidden>
    <div class="mobile-nav__backdrop" data-close-nav></div>
    <aside class="mobile-nav__panel" role="dialog" aria-modal="true" aria-label="Main menu" tabindex="-1">
      <div class="mobile-nav__header">
        <span>Menu</span>
        <button class="mobile-nav__close" type="button" aria-label="Close menu" data-close-nav>✕</button>
      </div>

      <nav class="mobile-nav__body">
        <ul class="mobile-nav__list">
          <?php foreach ($menus as $m): ?>
            <?php if (($m['type'] ?? 'link') === 'link'): ?>
              <li><a href="<?= e($m['url']); ?>"><?= e($m['label']); ?></a></li>
            <?php else: ?>
              <li class="m-collapsible" aria-expanded="false">
                <button class="m-collapsible__btn" type="button" aria-expanded="false">
                  <?= e($m['label']); ?><span class="chev" aria-hidden="true">▾</span>
                </button>
                <ul class="m-collapsible__panel">
                  <?php foreach ($m['items'] ?? [] as $it): ?>
                    <li><a href="<?= e($it['url']); ?>"><?= e($it['label']); ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>

        <div class="mobile-nav__footer">
          <a class="btn blue" href="<?= e($settings['cta_url']); ?>"><?= e($settings['cta_label']); ?></a>
          <?php if (!$user): ?>
            <a class="btn outline" href="/adamson-ccit/public/index.php?page=login">Login</a>
          <?php else: ?>
            <a class="btn outline" href="/adamson-ccit/public/index.php?page=logout">Logout</a>
          <?php endif; ?>
        </div>
      </nav>
    </aside>
  </div>
</header>

<script>
  // Desktop dropdowns: click + hover (hover only when >=961px and pointer is fine)
  (function () {
    const items = Array.from(document.querySelectorAll('.site-header .main-nav > li > details'));
    const ctas  = document.querySelector('.site-header .main-nav-right');

    // Treat >=961px (matches CSS) and fine pointer as "desktop hover"
    const isDesktopHover = () =>
      matchMedia('(min-width: 961px)').matches &&
      matchMedia('(pointer: fine)').matches;

    function closeOthers(except) {
      items.forEach(d => { if (d !== except) d.removeAttribute('open'); });
      document.querySelectorAll('.site-header summary[aria-expanded="true"]')
        .forEach(s => s.setAttribute('aria-expanded','false'));
    }

    function alignPanel(d) {
      const summary = d.querySelector('summary');
      const panel   = d.querySelector('.panel');
      if (!summary || !panel) return;

      d.classList.remove('align-right');
      panel.style.removeProperty('--panel-left');
      panel.style.removeProperty('--panel-right');
      panel.style.removeProperty('--panel-max');

      const liRect  = d.closest('li').getBoundingClientRect();
      const sRect   = summary.getBoundingClientRect();
      const vw      = innerWidth || document.documentElement.clientWidth;
      const pad     = 12;

      const leftOffset = Math.round(sRect.left - liRect.left);
      panel.style.setProperty('--panel-left', leftOffset + 'px');

      let rightLimit = vw - pad;
      if (ctas) {
        const cRect = ctas.getBoundingClientRect();
        if (cRect.left > sRect.left) rightLimit = Math.min(rightLimit, cRect.left - pad);
      }
      const availRight = Math.max(0, rightLimit - sRect.left);
      const isNoCap = d.classList.contains('no-cap');

      if (availRight > 24) {
        panel.style.setProperty('--panel-max', isNoCap ? (vw - pad*2) + 'px' : availRight + 'px');
      } else {
        d.classList.add('align-right');
        const rightOffset = Math.round(liRect.right - sRect.right);
        panel.style.setProperty('--panel-right', rightOffset + 'px');
        const availLeft = Math.max(0, sRect.right - pad);
        panel.style.setProperty('--panel-max', isNoCap ? (vw - pad*2) + 'px' : availLeft + 'px');
      }
    }

    // Close on outside click / ESC
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.site-header')) {
        items.forEach(d => d.removeAttribute('open'));
        document.querySelectorAll('.site-header summary')
          .forEach(s => s.setAttribute('aria-expanded','false'));
      }
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        items.forEach(d => d.removeAttribute('open'));
        document.querySelectorAll('.site-header summary')
          .forEach(s => s.setAttribute('aria-expanded','false'));
      }
    });

    // Bind once; gate behavior with isDesktopHover() so it works after resize too
    items.forEach(d => {
      const summary = d.querySelector('summary');
      const panel   = d.querySelector('.panel');
      let closeTimer = null;

      const open  = () => { d.setAttribute('open',''); summary?.setAttribute('aria-expanded','true'); alignPanel(d); };
      const close = () => { d.removeAttribute('open'); summary?.setAttribute('aria-expanded','false'); };
      const scheduleClose = () => { closeTimer = setTimeout(close, 140); };
      const cancelClose   = () => { if (closeTimer){ clearTimeout(closeTimer); closeTimer = null; } };

      // Click toggles (works on all sizes)
      summary?.addEventListener('click', (e) => {
        e.preventDefault();
        if (d.open) { close(); } else { closeOthers(d); open(); }
      });

      // Hover behavior, but only when desktop-hover capable right now
      summary?.addEventListener('pointerenter', () => {
        if (!isDesktopHover()) return;
        cancelClose(); closeOthers(d); open();
      });
      d.addEventListener('pointerleave', () => {
        if (!isDesktopHover()) return;
        scheduleClose();
      });
      panel?.addEventListener('pointerenter', () => {
        if (!isDesktopHover()) return;
        cancelClose();
      });
      panel?.addEventListener('pointerleave', () => {
        if (!isDesktopHover()) return;
        scheduleClose();
      });

      addEventListener('resize', () => { if (d.open) alignPanel(d); });
    });
  })();

  // Close the user menu when clicking outside / ESC
  (function(){
    const um = document.querySelector('.user-menu');
    if (!um) return;
    document.addEventListener('click', (e)=>{ if (!e.target.closest('.user-menu')) um.removeAttribute('open'); });
    document.addEventListener('keydown', (e)=>{ if (e.key === 'Escape') um.removeAttribute('open'); });
  })();

  // Mobile drawer / hamburger
  (function(){
    const btn = document.querySelector('.nav-toggle');
    const sheet = document.getElementById('mobileNav');
    const panel = sheet?.querySelector('.mobile-nav__panel');
    const closers = sheet?.querySelectorAll('[data-close-nav]');
    let lastFocus = null;

    function openNav(){
      if (!sheet) return;
      lastFocus = document.activeElement;
      sheet.hidden = false;
      requestAnimationFrame(()=> sheet.classList.add('is-open'));
      btn?.setAttribute('aria-expanded','true');
      btn?.classList.add('is-active');
      document.documentElement.style.overflow = 'hidden';
      panel?.focus?.();
    }
    function closeNav(){
      if (!sheet) return;
      sheet.classList.remove('is-open');
      btn?.setAttribute('aria-expanded','false');
      btn?.classList.remove('is-active');
      document.documentElement.style.overflow = '';
      setTimeout(()=>{ sheet.hidden = true; lastFocus?.focus?.(); }, 220);
    }

    btn?.addEventListener('click', ()=> sheet.classList.contains('is-open') ? closeNav() : openNav());
    closers?.forEach(el => el.addEventListener('click', closeNav));
    sheet?.addEventListener('click', e => { if (e.target.matches('.mobile-nav__backdrop')) closeNav(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && sheet?.classList.contains('is-open')) closeNav(); });
  })();
</script>

<script>
// Mobile collapsibles: toggle aria-expanded on click/Enter/Space
(function () {
  const sheet = document.getElementById('mobileNav');
  if (!sheet) return;

  // Close other sections when opening one (accordion behavior)
  function closeOthers(except) {
    sheet.querySelectorAll('.m-collapsible[aria-expanded="true"]').forEach(li => {
      if (li !== except) {
        li.setAttribute('aria-expanded','false');
        li.querySelector('.m-collapsible__btn')?.setAttribute('aria-expanded','false');
      }
    });
  }

  sheet.addEventListener('click', (e) => {
    const btn = e.target.closest('.m-collapsible__btn');
    if (!btn) return;

    const li = btn.closest('.m-collapsible');
    const isOpen = li.getAttribute('aria-expanded') === 'true';

    closeOthers(li);
    li.setAttribute('aria-expanded', String(!isOpen));
    btn.setAttribute('aria-expanded', String(!isOpen));
  });

  // Keyboard support for Enter/Space
  sheet.addEventListener('keydown', (e) => {
    const btn = e.target.closest('.m-collapsible__btn');
    if (!btn) return;
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      btn.click();
    }
  });
})();
</script>

<script>
(function(){
  const header = document.querySelector('.site-header');
  const headerInner = header?.querySelector('.header-inner');
  const navEl = header?.querySelector('nav[aria-label="Main"]');
  const list = header?.querySelector('.main-nav');
  const right = header?.querySelector('.main-nav-right');

  if (!header || !headerInner || !navEl || !list || !right) return;

  // Space buffer so they never "kiss" visually before collapsing
  const GAP = 8;

  function isWrapped(){
    // true wrap: first and last items sit on different rows
    const items = list.children;
    if (!items.length) return false;
    const firstTop = items[0].offsetTop;
    const lastTop  = items[items.length - 1].offsetTop;
    return lastTop > firstTop + 1; // small tolerance
  }

  function isColliding(){
    // If either cluster is hidden (e.g., due to media query), don't force collapse
    if (!list.offsetParent || !right.offsetParent) return false;

    const navRect   = list.getBoundingClientRect();
    const rightRect = right.getBoundingClientRect();

    if (!navRect.width || !rightRect.width) return false;

    // Positive means space between clusters; collapse only when < GAP
    const available = rightRect.left - navRect.right;
    return available < GAP;
  }

  function applyCollapse(){
    // IMPORTANT: only collapse when there's true wrap OR true collision.
    const collapse = isWrapped() || isColliding();
    header.classList.toggle('nav-collapsed', collapse);
  }

  // Re-check on size changes of key containers
  const ro = new ResizeObserver(()=> {
    // double RAF to let flex & fonts settle before measuring
    requestAnimationFrame(()=> requestAnimationFrame(applyCollapse));
  });
  ro.observe(headerInner);
  ro.observe(list);
  ro.observe(right);

  // Fonts can change widths after load; then measure
  const fontsReady = (document.fonts && document.fonts.ready) ? document.fonts.ready : Promise.resolve();
  fontsReady.finally(()=> {
    // initial measurement after layout paints
    requestAnimationFrame(()=> requestAnimationFrame(applyCollapse));
  });

  // Also handle window resize
  window.addEventListener('resize', ()=> {
    requestAnimationFrame(()=> requestAnimationFrame(applyCollapse));
  }, { passive:true });
})();
</script>

