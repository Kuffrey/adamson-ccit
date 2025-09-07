<?php
// app/views/admin/about_vision_mission_form.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../models/AboutVisionMission.php';
function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Prefer username from Auth if available (keeps header consistent)
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? ($_SESSION['user']['username'] ?? 'Admin');

$model = new AboutVisionMission();
$about = $model->get();
$success = false; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $model->update($_POST);
    $about = $model->get();
    $success = true;
  } catch (Exception $e) {
    $error = 'Error saving changes: ' . $e->getMessage();
  }
}
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/../admin/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Edit About: Vision &amp; Mission</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Vision &amp; Mission Editor</h1>
      <p class="intro">Update the college-wide mission/vision and each department’s text. Changes apply site-wide.</p>

      <!-- Sticky local subnav -->
      <nav class="cms-subnav" aria-label="About sections">
        <a href="#sec-main">Main</a>
        <a href="#sec-dept1">IT &amp; IS</a>
        <a href="#sec-dept2">Computer Science</a>
      </nav>

      <?php if ($success): ?>
        <p class="notice success">Changes saved successfully.</p>
      <?php elseif ($error): ?>
        <p class="notice error"><?= esc($error) ?></p>
      <?php endif; ?>

      <!-- Preview -->
      <div class="cms-card">
        <h2 class="cms-card-legend">Current Data</h2>
        <table class="admin-data-table">
          <thead><tr><th>Field</th><th>Value</th></tr></thead>
          <tbody>
            <tr><td>Intro</td><td><?= esc($about['main_intro'] ?? '—') ?></td></tr>
            <tr><td>Main Vision</td><td><?= esc($about['main_vision'] ?? '—') ?></td></tr>
            <tr><td>Main Mission</td><td><?= esc($about['main_mission'] ?? '—') ?></td></tr>
            <tr><td>Dept1 Title</td><td><?= esc($about['dept1_title'] ?? '—') ?></td></tr>
            <tr><td>Dept1 Vision</td><td><?= esc($about['dept1_vision'] ?? '—') ?></td></tr>
            <tr><td>Dept1 Mission</td><td><?= esc($about['dept1_mission'] ?? '—') ?></td></tr>
            <tr><td>Dept1 Objectives</td><td><?= esc($about['dept1_objectives'] ?? '—') ?></td></tr>
            <tr><td>Dept2 Title</td><td><?= esc($about['dept2_title'] ?? '—') ?></td></tr>
            <tr><td>Dept2 Vision</td><td><?= esc($about['dept2_vision'] ?? '—') ?></td></tr>
            <tr><td>Dept2 Mission</td><td><?= esc($about['dept2_mission'] ?? '—') ?></td></tr>
            <tr><td>Dept2 Objectives</td><td><?= esc($about['dept2_objectives'] ?? '—') ?></td></tr>
          </tbody>
        </table>
      </div>

      <!-- Editor -->
      <form id="vmForm" method="post" action="?page=admin_about_vision_mission" class="admin-cms-form">

        <!-- Main -->
        <div class="cms-card">
          <fieldset id="sec-main">
            <legend class="cms-card-legend">Main (College-wide)</legend>
            <div class="form-section">
              <div class="field">
                <label>Intro / Lead</label>
                <input type="text" name="main_intro" value="<?= esc($about['main_intro'] ?? '') ?>">
              </div>
            </div>
            <div class="form-row">
              <div class="field">
                <label>Vision</label>
                <textarea name="main_vision" rows="3"><?= esc($about['main_vision'] ?? '') ?></textarea>
              </div>
              <div class="field">
                <label>Mission</label>
                <textarea name="main_mission" rows="3"><?= esc($about['main_mission'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <!-- Dept 1 -->
        <div class="cms-card">
          <fieldset id="sec-dept1">
            <legend class="cms-card-legend">Department 1 — IT &amp; IS</legend>
            <div class="form-section">
              <div class="field">
                <label>Title</label>
                <input type="text" name="dept1_title" value="<?= esc($about['dept1_title'] ?? '') ?>">
              </div>
              <div class="field">
                <label>Objectives</label>
                <textarea name="dept1_objectives" rows="3"><?= esc($about['dept1_objectives'] ?? '') ?></textarea>
              </div>
            </div>
            <div class="form-row">
              <div class="field">
                <label>Vision</label>
                <textarea name="dept1_vision" rows="3"><?= esc($about['dept1_vision'] ?? '') ?></textarea>
              </div>
              <div class="field">
                <label>Mission</label>
                <textarea name="dept1_mission" rows="3"><?= esc($about['dept1_mission'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <!-- Dept 2 -->
        <div class="cms-card">
          <fieldset id="sec-dept2">
            <legend class="cms-card-legend">Department 2 — Computer Science</legend>
            <div class="form-section">
              <div class="field">
                <label>Title</label>
                <input type="text" name="dept2_title" value="<?= esc($about['dept2_title'] ?? '') ?>">
              </div>
              <div class="field">
                <label>Objectives</label>
                <textarea name="dept2_objectives" rows="3"><?= esc($about['dept2_objectives'] ?? '') ?></textarea>
              </div>
            </div>
            <div class="form-row">
              <div class="field">
                <label>Vision</label>
                <textarea name="dept2_vision" rows="3"><?= esc($about['dept2_vision'] ?? '') ?></textarea>
              </div>
              <div class="field">
                <label>Mission</label>
                <textarea name="dept2_mission" rows="3"><?= esc($about['dept2_mission'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <!-- No inline button; use only sticky Save Bar -->
      </form>

      <!-- Spacer to ensure last anchor scrolls nicely under sticky bars -->
      <div class="page-end-spacer" style="height:160px" aria-hidden="true"></div>

      <!-- Sticky Save Bar -->
      <div class="savebar">
        <div class="savebar__inner">
          <span class="savebar__status" id="saveStatus">All changes saved</span>
          <div class="savebar__actions">
            <button type="button" class="btn" id="discardBtn">Discard</button>
            <button type="submit" form="vmForm" class="btn btn--primary">Save Changes</button>
          </div>
        </div>
      </div>

    </section>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('vmForm');

  // measure sticky bits and expose as CSS vars
  function setStickyVars(){
    const topbarH = document.querySelector('.admin-topbar')?.offsetHeight || 64;
    const subnavH = document.querySelector('.cms-subnav')?.offsetHeight || 44;
    document.documentElement.style.setProperty('--topbar-h', topbarH + 'px');
    document.documentElement.style.setProperty('--subnav-h', subnavH + 'px');
  }
  setStickyVars();
  window.addEventListener('resize', setStickyVars);

  // smooth scroll with offset + bottom clamp
  function scrollToSection(id){
    const target = document.getElementById(id);
    if(!target) return;

    const card = target.closest('.cms-card');
    if(card?.classList.contains('is-collapsed')) card.classList.remove('is-collapsed');

    const cs = getComputedStyle(document.documentElement);
    const offset = (parseInt(cs.getPropertyValue('--topbar-h')) || 64)
                 + (parseInt(cs.getPropertyValue('--subnav-h')) || 44) + 16;

    const doc = document.documentElement;
    const targetTop = target.getBoundingClientRect().top + window.pageYOffset;
    const maxScroll = doc.scrollHeight - window.innerHeight;
    const y = Math.min(targetTop - offset, maxScroll);

    window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
    history.replaceState(null, '', '#' + id);
  }

  // subnav interactions
  document.querySelector('.cms-subnav')?.addEventListener('click', e => {
    const a = e.target.closest('a[href^="#"]');
    if(!a) return;
    e.preventDefault();
    scrollToSection(a.getAttribute('href').slice(1));
  });

  // active link on scroll
  const links = Array.from(document.querySelectorAll('.cms-subnav a'));
  const sections = Array.from(document.querySelectorAll('.cms-card fieldset[id]'));
  const setActive = () => {
    const cs = getComputedStyle(document.documentElement);
    const y = window.scrollY
      + (parseInt(cs.getPropertyValue('--topbar-h')) || 64)
      + (parseInt(cs.getPropertyValue('--subnav-h')) || 44)
      + 20;
    let current = sections[0]?.id;
    for(const s of sections){
      const top = s.getBoundingClientRect().top + window.scrollY;
      if(top <= y) current = s.id;
    }
    links.forEach(a => a.classList.toggle('is-active', a.getAttribute('href') === '#' + current));
  };
  window.addEventListener('scroll', setActive, {passive:true});
  setActive();

  // load with hash
  if(location.hash && document.getElementById(location.hash.slice(1))){
    setTimeout(() => scrollToSection(location.hash.slice(1)), 50);
  }
  window.addEventListener('hashchange', () => {
    const id = location.hash.slice(1);
    if(id) scrollToSection(id);
  });

  // collapsible cards + persist
  document.querySelectorAll('.cms-card legend').forEach((lg, i) => {
    lg.style.cursor = 'pointer';
    lg.addEventListener('click', () => {
      const card = lg.closest('.cms-card');
      card.classList.toggle('is-collapsed');
      localStorage.setItem('vm_card_' + i, card.classList.contains('is-collapsed') ? '1' : '0');
    });
    const card = lg.closest('.cms-card');
    if(localStorage.getItem('vm_card_' + i) === '1') card.classList.add('is-collapsed');
  });

  // unsaved changes + save feedback
  let dirty = false;
  const saveStatus = document.getElementById('saveStatus');
  form?.addEventListener('input', () => {
    if(!dirty){ dirty = true; saveStatus.textContent = 'Unsaved changes…'; }
  });
  form?.addEventListener('submit', () => {
    saveStatus.textContent = 'Saving…';
    setTimeout(() => { dirty = false; saveStatus.textContent = 'All changes saved'; }, 900);
  });
  window.addEventListener('beforeunload', (e) => { if(dirty){ e.preventDefault(); e.returnValue = ''; } });

  // discard
  document.getElementById('discardBtn')?.addEventListener('click', () => {
    if(!dirty || confirm('Discard all unsaved changes?')) location.reload();
  });

  // shortcuts
  window.addEventListener('keydown', (e) => {
    if((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's'){
      e.preventDefault();
      document.querySelector('.savebar [type="submit"]')?.click();
    }
    if(e.key === 'Escape'){
      const active = document.querySelector('.cms-subnav a.is-active');
      const sec = active && document.querySelector(active.getAttribute('href'));
      sec?.closest('.cms-card')?.classList.toggle('is-collapsed');
    }
  });

  // auto-hide notices
  const notices = document.querySelectorAll('.notice');
  if (notices.length) setTimeout(() => { notices.forEach(n => n.style.display='none'); }, 4000);
});
</script>
