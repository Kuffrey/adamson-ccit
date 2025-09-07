<?php
// admin_manage_about.php — CMS CRUD for About Page Content
require_once __DIR__ . '/../models/AboutHistory.php';
function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Prefer username from Auth if available (keeps header consistent)
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? ($_SESSION['user']['username'] ?? 'Admin');

// Load current settings
$model  = new AboutHistory();
$about  = $model->get();
$success = false;
$error   = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $model->update($_POST);
        $about   = $model->get(); // reload after save
        $success = true;
    } catch (Exception $e) {
        $error = "Error saving changes: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit About Page | CCIT CMS</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/admin/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Edit About Page Content</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username ?? 'Admin'); ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">About Page Editor</h1>
      <p class="intro">Edit About page sections: Subhero, Intro, Facts, Origins, Leaders, Identity & Values, and CTA.</p>

      <!-- Sticky Section Subnav -->
      <nav class="cms-subnav" aria-label="About sections">
        <a href="#sec-subhero">Subhero</a>
        <a href="#sec-intro">Intro</a>
        <a href="#sec-facts">Facts</a>
        <a href="#sec-origins">Origins</a>
        <a href="#sec-leaders">Leaders</a>
        <a href="#sec-identity">Identity</a>
        <a href="#sec-cta">CTA</a>
      </nav>

      <?php if ($success): ?>
        <p class="notice success">Changes saved successfully.</p>
      <?php elseif ($error): ?>
        <p class="notice error"><?= esc($error) ?></p>
      <?php endif; ?>

      <!-- Current Data Preview -->
      <div class="cms-card">
        <h2 class="cms-card-legend">Current About Page Data</h2>
        <table class="admin-data-table">
          <thead>
            <tr><th>Field</th><th>Value</th></tr>
          </thead>
          <tbody>
            <tr><td>Subhero Lead</td><td><?= esc($about['subhero_lead'] ?? '—') ?></td></tr>
            <tr><td>Intro Title</td><td><?= esc($about['intro_title'] ?? '—') ?></td></tr>
            <tr><td>Intro Lead</td><td><?= esc($about['intro_lead'] ?? '—') ?></td></tr>
            <tr><td>Fact Title</td><td><?= esc($about['fact_title'] ?? '—') ?></td></tr>
            <tr><td>Fact 2</td><td><?= esc($about['fact_2'] ?? '—') ?></td></tr>
            <tr><td>Fact 3</td><td><?= esc($about['fact_3'] ?? '—') ?></td></tr>
            <tr><td>Origins Title</td><td><?= esc($about['origins_title'] ?? '—') ?></td></tr>
            <tr><td>Origins Body</td><td><?= esc($about['origins_body'] ?? '—') ?></td></tr>
            <tr><td>Leaders Title</td><td><?= esc($about['leaders_title'] ?? '—') ?></td></tr>
            <tr><td>Leaders List</td><td><?= esc(strip_tags($about['leaders_list'] ?? '—')) ?></td></tr>
            <tr><td>Academic Leads Title</td><td><?= esc($about['academic_leads_title'] ?? '—') ?></td></tr>
            <tr><td>Academic Leads List</td><td><?= esc(strip_tags($about['academic_leads_list'] ?? '—')) ?></td></tr>
            <tr><td>Identity Title</td><td><?= esc($about['identity_title'] ?? '—') ?></td></tr>
            <tr><td>Identity Items</td><td><?= esc(strip_tags($about['identity_items'] ?? '—')) ?></td></tr>
            <tr><td>CTA Title</td><td><?= esc($about['cta_title'] ?? '—') ?></td></tr>
            <tr><td>CTA Body</td><td><?= esc($about['cta_body'] ?? '—') ?></td></tr>
            <tr><td>CTA Button Label</td><td><?= esc($about['cta_btn_label'] ?? '—') ?></td></tr>
            <tr><td>CTA Button URL</td><td><?= esc($about['cta_btn_url'] ?? '#') ?></td></tr>
          </tbody>
        </table>
      </div>

      <!-- Edit Form -->
      <form id="aboutForm" method="post" action="?page=admin_manage_about" class="admin-cms-form">

        <!-- Subhero -->
        <div class="cms-card">
          <fieldset id="sec-subhero">
            <legend class="cms-card-legend">Subhero Section</legend>
            <div class="form-section">
              <div class="field">
                <label>Subhero Lead</label>
                <input type="text" name="subhero_lead" value="<?= esc($about['subhero_lead'] ?? '') ?>">
              </div>
            </div>
          </fieldset>
        </div>

        <!-- Intro -->
        <div class="cms-card">
          <fieldset id="sec-intro">
            <legend class="cms-card-legend">Intro Section</legend>
            <div class="form-section">
              <div class="field">
                <label>Intro Title</label>
                <input type="text" name="intro_title" value="<?= esc($about['intro_title'] ?? '') ?>">
              </div>
              <div class="field">
                <label>Intro Lead</label>
                <textarea name="intro_lead" rows="2"><?= esc($about['intro_lead'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <!-- Facts -->
        <div class="cms-card">
          <fieldset id="sec-facts">
            <legend class="cms-card-legend">Facts</legend>
            <div class="form-section">
              <div class="field">
                <label>Fact Title</label>
                <input type="text" name="fact_title" value="<?= esc($about['fact_title'] ?? '') ?>">
              </div>
              <div class="field">
                <label>Fact 2</label>
                <input type="text" name="fact_2" value="<?= esc($about['fact_2'] ?? '') ?>">
              </div>
              <div class="field">
                <label>Fact 3</label>
                <input type="text" name="fact_3" value="<?= esc($about['fact_3'] ?? '') ?>">
              </div>
            </div>
          </fieldset>
        </div>

        <!-- Origins -->
        <div class="cms-card">
          <fieldset id="sec-origins">
            <legend class="cms-card-legend">Origins</legend>
            <div class="form-section">
              <div class="field">
                <label>Origins Title</label>
                <input type="text" name="origins_title" value="<?= esc($about['origins_title'] ?? '') ?>">
              </div>
              <div class="field">
                <label>Origins Body</label>
                <textarea name="origins_body" rows="3"><?= esc($about['origins_body'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <!-- Leaders -->
        <div class="cms-card">
          <fieldset id="sec-leaders">
            <legend class="cms-card-legend">Leaders</legend>
            <div class="form-section">
              <div class="field">
                <label>Leaders Title</label>
                <input type="text" name="leaders_title" value="<?= esc($about['leaders_title'] ?? '') ?>">
              </div>
              <div class="field">
                <label>Leaders List</label>
                <textarea name="leaders_list" rows="2"><?= esc($about['leaders_list'] ?? '') ?></textarea>
              </div>
              <div class="field">
                <label>Academic Leads Title</label>
                <input type="text" name="academic_leads_title" value="<?= esc($about['academic_leads_title'] ?? '') ?>">
              </div>
              <div class="field">
                <label>Academic Leads List</label>
                <textarea name="academic_leads_list" rows="2"><?= esc($about['academic_leads_list'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <!-- Identity & Values -->
        <div class="cms-card">
          <fieldset id="sec-identity">
            <legend class="cms-card-legend">Identity &amp; Values</legend>
            <div class="form-section">
              <div class="field">
                <label>Identity Title</label>
                <input type="text" name="identity_title" value="<?= esc($about['identity_title'] ?? '') ?>">
              </div>
              <div class="field">
                <label>Identity Items</label>
                <textarea name="identity_items" rows="2"><?= esc($about['identity_items'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <!-- CTA -->
        <div class="cms-card">
          <fieldset id="sec-cta">
            <legend class="cms-card-legend">CTA Section</legend>
            <div class="form-section">
              <div class="field">
                <label>CTA Title</label>
                <input type="text" name="cta_title" value="<?= esc($about['cta_title'] ?? '') ?>">
              </div>
              <div class="field">
                <label>CTA Body</label>
                <textarea name="cta_body" rows="2"><?= esc($about['cta_body'] ?? '') ?></textarea>
              </div>
              <div class="field">
                <label>CTA Button Label</label>
                <input type="text" name="cta_btn_label" value="<?= esc($about['cta_btn_label'] ?? '') ?>">
              </div>
              <div class="field">
                <label>CTA Button URL</label>
                <input type="text" name="cta_btn_url" value="<?= esc($about['cta_btn_url'] ?? '') ?>">
              </div>
            </div>
          </fieldset>
        </div>

        <!-- No inline Save button (use sticky Save Bar) -->
      </form>

      <!-- Spacer so last anchor (CTA) can sit under sticky bars -->
      <div class="page-end-spacer" style="height:160px" aria-hidden="true"></div>

      <!-- Sticky Save Bar -->
      <div class="savebar">
        <div class="savebar__inner">
          <span class="savebar__status" id="saveStatus">All changes saved</span>
          <div class="savebar__actions">
            <button type="button" class="btn" id="discardBtn">Discard</button>
            <button type="submit" form="aboutForm" class="btn btn--primary">Save Changes</button>
          </div>
        </div>
      </div>

    </section>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('aboutForm');

  // -- measure sticky bits and store as CSS vars
  function setStickyVars() {
    const topbarH = document.querySelector('.admin-topbar')?.offsetHeight || 64;
    const subnavH = document.querySelector('.cms-subnav')?.offsetHeight || 44;
    document.documentElement.style.setProperty('--topbar-h', topbarH + 'px');
    document.documentElement.style.setProperty('--subnav-h', subnavH + 'px');
  }
  setStickyVars();
  window.addEventListener('resize', setStickyVars);

  // -- precise scroll with offset & bottom clamp
  function scrollToSection(id) {
    const target = document.getElementById(id);
    if (!target) return;

    // expand if collapsed
    const card = target.closest('.cms-card');
    if (card?.classList.contains('is-collapsed')) card.classList.remove('is-collapsed');

    const styles = getComputedStyle(document.documentElement);
    const topbar = parseInt(styles.getPropertyValue('--topbar-h')) || 64;
    const subnav = parseInt(styles.getPropertyValue('--subnav-h')) || 44;
    const offset = topbar + subnav + 16;

    const doc = document.documentElement;
    const targetTop = target.getBoundingClientRect().top + window.pageYOffset;
    const maxScroll = doc.scrollHeight - window.innerHeight;
    const y = Math.min(targetTop - offset, maxScroll);

    window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
    history.replaceState(null, '', '#' + id);
  }

  // -- subnav clicks
  document.querySelector('.cms-subnav')?.addEventListener('click', (e) => {
    const a = e.target.closest('a[href^="#"]');
    if (!a) return;
    e.preventDefault();
    scrollToSection(a.getAttribute('href').slice(1));
  });

  // -- active link while scrolling
  const links = Array.from(document.querySelectorAll('.cms-subnav a'));
  const sections = Array.from(document.querySelectorAll('.cms-card fieldset[id]'));
  const setActive = () => {
    const styles = getComputedStyle(document.documentElement);
    const topbar = parseInt(styles.getPropertyValue('--topbar-h')) || 64;
    const subnav = parseInt(styles.getPropertyValue('--subnav-h')) || 44;
    const y = window.scrollY + topbar + subnav + 20;

    let current = sections[0]?.id;
    for (const s of sections) {
      const top = s.getBoundingClientRect().top + window.scrollY;
      if (top <= y) current = s.id;
    }
    links.forEach(a => a.classList.toggle('is-active', a.getAttribute('href') === '#' + current));
  };
  window.addEventListener('scroll', setActive, { passive: true });
  setActive();

  // -- load hash (direct link) & react to changes
  if (location.hash && document.getElementById(location.hash.slice(1))) {
    setTimeout(() => scrollToSection(location.hash.slice(1)), 50);
  }
  window.addEventListener('hashchange', () => {
    const id = location.hash.slice(1);
    if (id) scrollToSection(id);
  });

  // -- collapsible cards + persist state
  document.querySelectorAll('.cms-card legend').forEach((lg, i) => {
    lg.style.cursor = 'pointer';
    lg.addEventListener('click', () => {
      const card = lg.closest('.cms-card');
      card.classList.toggle('is-collapsed');
      localStorage.setItem('about_card_' + i, card.classList.contains('is-collapsed') ? '1' : '0');
    });
    const card = lg.closest('.cms-card');
    if (localStorage.getItem('about_card_' + i) === '1') card.classList.add('is-collapsed');
  });

  // -- unsaved changes tracking + save feedback
  let dirty = false;
  const saveStatus = document.getElementById('saveStatus');
  form?.addEventListener('input', () => {
    if (!dirty) { dirty = true; saveStatus.textContent = 'Unsaved changes…'; }
  });
  form?.addEventListener('submit', () => {
    saveStatus.textContent = 'Saving…';
    setTimeout(() => { dirty = false; saveStatus.textContent = 'All changes saved'; }, 900);
  });
  window.addEventListener('beforeunload', (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });

  // -- discard
  document.getElementById('discardBtn')?.addEventListener('click', () => {
    if (!dirty || confirm('Discard all unsaved changes?')) location.reload();
  });

  // -- shortcuts
  window.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
      e.preventDefault();
      document.querySelector('.savebar [type="submit"]')?.click();
    }
    if (e.key === 'Escape') {
      const active = document.querySelector('.cms-subnav a.is-active');
      const sec = active && document.querySelector(active.getAttribute('href'));
      sec?.closest('.cms-card')?.classList.toggle('is-collapsed');
    }
  });

  // -- auto-hide notices
  const notices = document.querySelectorAll('.notice');
  if (notices.length) setTimeout(() => { notices.forEach(n => n.style.display = 'none'); }, 4000);
});
</script>
</body>
</html>
