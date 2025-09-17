<?php
/* header.php — dynamic main nav & utility links via DB; compact user menu stays the same */

if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;

if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}

/* Build PDO directly from config (same pattern as footer) */
$config = require __DIR__ . '/../../config/database.php';
try {
  $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
  $pdo = new PDO($dsn, $config['user'], $config['pass'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (PDOException $e) {
  // If DB fails, we'll render with fallbacks below
  $pdo = null;
}

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
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">

<!-- Small scoped styles for the user icon menu -->
<style>
    .site-header{
    border-bottom: 1px solid var(--edgec, #e6e9ef);
  }
  .user-menu{ position:relative; }
  .user-menu summary{
    list-style:none; cursor:pointer; border:1px solid #d1d5db; background:#fff; color:#0b234c;
    width:40px; height:40px; border-radius:999px; display:grid; place-items:center; padding:0;
  }
  .user-menu summary::-webkit-details-marker{ display:none }
  .user-menu summary:focus-visible{ outline:3px solid #9ad1ff; outline-offset:2px; border-radius:999px; }
  .user-menu .panel{
    position:absolute; right:0; top:calc(100% + 10px);
    background:#fff; border:1px solid var(--edgec, #e6e9ef); border-radius:12px; box-shadow:var(--shadow, 0 6px 20px rgba(17,24,39,.08));
    padding:8px; display:grid; gap:4px; min-width:220px; z-index:4000;
    opacity:0; transform:translateY(6px); transition:opacity .15s ease, transform .15s ease;
  }
  .user-menu[open] .panel{ opacity:1; transform:translateY(0) }
  .user-menu .panel a{
    text-decoration:none; color:#111827; padding:10px 12px; border-radius:8px; font-weight:600; display:flex; align-items:center; gap:8px;
  }
  .user-menu .panel a:hover{ background:#f3f4f6 }
  .user-menu .umeta{ padding:8px 10px; border-bottom:1px solid var(--edgec, #e6e9ef); color:#475569; font-weight:600 }
  .user-menu .role{ text-transform:uppercase; font-size:12px; letter-spacing:.05em; color:#0b234c; font-weight:900 }
  .main-nav-right{ display:flex; gap:10px; align-items:center; position:relative; z-index:1200 }
  .icon{ width:20px; height:20px; display:inline-block }
</style>

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
        <!-- Not logged in -->
        <a href="/adamson-ccit/public/index.php?page=login_guest_student" class="btn outline">Login</a>
      <?php else: ?>
        <!-- Logged in: compact user icon + dropdown -->
        <details class="user-menu" aria-label="User menu">
          <summary aria-label="Open user menu" title="Account">
            <!-- User icon (SVG) -->
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
              <a role="menuitem" href="/adamson-ccit/public/index.php?page=faculty_manage_research">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20l-6-6 6-6"/><path d="M6 8v8"/></svg>
                Manage Research
              </a>
              <a role="menuitem" href="/adamson-ccit/public/index.php?page=faculty_manage_certifications">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><rect x="3" y="3" width="18" height="14" rx="2"/></svg>
                Manage Certifications
              </a>
            <?php elseif ($user['role'] === 'admin'): ?>
              <a role="menuitem" href="/adamson-ccit/public/index.php?page=admin_dashboard">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Admin Dashboard
              </a>
              <a role="menuitem" href="/adamson-ccit/public/index.php?page=admin_manage_news">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10M7 12h10M7 16h6"/></svg>
                Manage News
              </a>
              <a role="menuitem" href="/adamson-ccit/public/index.php?page=admin_manage_programs">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/></svg>
                Manage Programs
              </a>
              <a role="menuitem" href="/adamson-ccit/public/index.php?page=admin_manage_faculty">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="7" r="4"/><path d="M17 11v8m-4-4h8M3 21v-2a4 4 0 0 1 4-4h4"/></svg>
                Manage Faculty
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
</header>

<script>
  // Existing mega menu sizing (About/Programs/Student/Faculty)
  (function () {
    const items = Array.from(document.querySelectorAll('.site-header .main-nav > li > details'));
    const ctas  = document.querySelector('.site-header .main-nav-right');
    const media = window.matchMedia('(pointer:fine) and (min-width:1024px)');

    function closeOthers(except) {
      items.forEach(d => { if (d !== except) d.removeAttribute('open'); });
      document.querySelectorAll('.site-header summary[aria-expanded="true"]').forEach(s=>s.setAttribute('aria-expanded','false'));
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

    document.addEventListener('click', (e) => {
      if (!e.target.closest('.site-header')) {
        items.forEach(d => d.removeAttribute('open'));
        document.querySelectorAll('.site-header summary').forEach(s=>s.setAttribute('aria-expanded','false'));
      }
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        items.forEach(d => d.removeAttribute('open'));
        document.querySelectorAll('.site-header summary').forEach(s=>s.setAttribute('aria-expanded','false'));
      }
    });

    items.forEach(d => {
      const summary = d.querySelector('summary');
      const panel   = d.querySelector('.panel');
      let closeTimer = null;

      const open = () => { d.setAttribute('open',''); summary?.setAttribute('aria-expanded','true'); alignPanel(d); };
      const close = () => { d.removeAttribute('open'); summary?.setAttribute('aria-expanded','false'); };
      const scheduleClose = () => { closeTimer = setTimeout(close, 140); };
      const cancelClose = () => { if (closeTimer){ clearTimeout(closeTimer); closeTimer = null; } };

      summary?.addEventListener('click', (e) => {
        e.preventDefault();
        if (d.open) { close(); } else { closeOthers(d); open(); }
      });

      if (media.matches) {
        summary?.addEventListener('pointerenter', () => { cancelClose(); closeOthers(d); open(); });
        d.addEventListener('pointerleave', scheduleClose);
        panel?.addEventListener('pointerenter', cancelClose);
        panel?.addEventListener('pointerleave', scheduleClose);
      }

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
</script>
