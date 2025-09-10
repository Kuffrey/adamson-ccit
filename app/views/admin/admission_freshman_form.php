<?php
// app/views/admin/admission_freshman_form.php
if (session_status() === PHP_SESSION_NONE) session_start();

function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Prefer username from Auth if available (keeps header consistent)
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? ($_SESSION['user']['username'] ?? 'Admin');

/** @var array $settings Settings array injected by controller */
/** @var string|null $msg Optional success message from controller */
$success = !empty($msg);
$error = '';
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Edit Admissions: Freshman</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Freshman Admission Editor</h1>
      <p class="intro">Update content for the Freshman Admissions page. HTML is allowed where noted.</p>

      <!-- Sticky local subnav -->
      <nav class="cms-subnav" aria-label="Admission sections">
        <a href="#sec-subhero">Subhero</a>
        <a href="#sec-apply">How to Apply</a>
        <a href="#sec-uploads">Initial Uploads</a>
        <a href="#sec-req-shs">Reqs: SHS</a>
        <a href="#sec-req-als">Reqs: ALS/PEPT</a>
        <a href="#sec-req-abroad">Reqs: Abroad</a>
        <a href="#sec-enrollment">Enrollment</a>
        <a href="#sec-sidebar">Sidebar</a>
        <a href="#sec-cta">CTA</a>
      </nav>

      <?php if ($success): ?>
        <p class="notice success"><?= esc($msg) ?></p>
      <?php elseif ($error): ?>
        <p class="notice error"><?= esc($error) ?></p>
      <?php endif; ?>

      <!-- Preview -->
      <div class="cms-card">
        <h2 class="cms-card-legend">Current Data</h2>
        <table class="admin-data-table">
          <thead><tr><th>Field</th><th>Value</th></tr></thead>
          <tbody>
            <tr><td>Subhero Lead</td><td><?= esc($settings['subhero_lead'] ?? '—') ?></td></tr>
            <tr><td>How to Apply (HTML)</td><td><?= esc($settings['how_to_apply'] ?? '—') ?></td></tr>
            <tr><td>Initial Uploads (HTML)</td><td><?= esc($settings['initial_uploads'] ?? '—') ?></td></tr>
            <tr><td>Requirements (SHS) (HTML)</td><td><?= esc($settings['requirements_shs'] ?? '—') ?></td></tr>
            <tr><td>Requirements (ALS/PEPT) (HTML)</td><td><?= esc($settings['requirements_als'] ?? '—') ?></td></tr>
            <tr><td>Requirements (Abroad) (HTML)</td><td><?= esc($settings['requirements_abroad'] ?? '—') ?></td></tr>
            <tr><td>Enrollment Procedure (HTML)</td><td><?= esc($settings['enrollment_procedure'] ?? '—') ?></td></tr>
            <tr><td>Sidebar Office (HTML)</td><td><?= esc($settings['sidebar_office'] ?? '—') ?></td></tr>
            <tr><td>Sidebar Links (HTML)</td><td><?= esc($settings['sidebar_links'] ?? '—') ?></td></tr>
            <tr><td>Sidebar Image URL</td><td><?= esc($settings['sidebar_image_url'] ?? '—') ?></td></tr>
            <tr><td>Sidebar Image Caption</td><td><?= esc($settings['sidebar_image_caption'] ?? '—') ?></td></tr>
            <tr><td>CTA Title</td><td><?= esc($settings['cta_title'] ?? '—') ?></td></tr>
            <tr><td>CTA Description</td><td><?= esc($settings['cta_description'] ?? '—') ?></td></tr>
            <tr><td>CTA Action Label</td><td><?= esc($settings['cta_action_label'] ?? '—') ?></td></tr>
            <tr><td>CTA Action URL</td><td><?= esc($settings['cta_action_url'] ?? '—') ?></td></tr>
          </tbody>
        </table>
      </div>

      <!-- Editor -->
      <form id="admFreshForm" method="post" action="?page=admin_admission_freshman" class="admin-cms-form" autocomplete="off">

        <!-- Subhero -->
        <div class="cms-card">
          <fieldset id="sec-subhero">
            <legend class="cms-card-legend">Subhero</legend>
            <div class="form-section">
              <div class="field">
                <label>Subhero Lead</label>
                <input type="text" name="subhero_lead" value="<?= esc($settings['subhero_lead'] ?? '') ?>" maxlength="255" required>
              </div>
            </div>
          </fieldset>
        </div>

        <!-- How to Apply -->
        <div class="cms-card">
          <fieldset id="sec-apply">
            <legend class="cms-card-legend">How to Apply (HTML allowed)</legend>
            <div class="form-section">
              <div class="field">
                <label>Body (HTML)</label>
                <textarea name="how_to_apply" rows="4"><?= esc($settings['how_to_apply'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <!-- Initial Uploads -->
        <div class="cms-card">
          <fieldset id="sec-uploads">
            <legend class="cms-card-legend">Initial Uploads (HTML allowed)</legend>
            <div class="form-section">
              <div class="field">
                <label>List / Body (HTML)</label>
                <textarea name="initial_uploads" rows="4"><?= esc($settings['initial_uploads'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <!-- Requirements -->
        <div class="cms-card">
          <fieldset id="sec-req-shs">
            <legend class="cms-card-legend">Requirements — Senior High School (HTML allowed)</legend>
            <div class="form-section">
              <div class="field">
                <label>SHS Requirements (HTML)</label>
                <textarea name="requirements_shs" rows="4"><?= esc($settings['requirements_shs'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <div class="cms-card">
          <fieldset id="sec-req-als">
            <legend class="cms-card-legend">Requirements — ALS/PEPT (HTML allowed)</legend>
            <div class="form-section">
              <div class="field">
                <label>ALS/PEPT Requirements (HTML)</label>
                <textarea name="requirements_als" rows="4"><?= esc($settings['requirements_als'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <div class="cms-card">
          <fieldset id="sec-req-abroad">
            <legend class="cms-card-legend">Requirements — Graduates from Abroad (HTML allowed)</legend>
            <div class="form-section">
              <div class="field">
                <label>Abroad Requirements (HTML)</label>
                <textarea name="requirements_abroad" rows="3"><?= esc($settings['requirements_abroad'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <!-- Enrollment -->
        <div class="cms-card">
          <fieldset id="sec-enrollment">
            <legend class="cms-card-legend">Enrollment Procedure (HTML allowed)</legend>
            <div class="form-section">
              <div class="field">
                <label>Procedure (HTML)</label>
                <textarea name="enrollment_procedure" rows="4"><?= esc($settings['enrollment_procedure'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <!-- Sidebar -->
        <div class="cms-card">
          <fieldset id="sec-sidebar">
            <legend class="cms-card-legend">Sidebar</legend>
            <div class="form-section">
              <div class="field">
                <label>Office Info (HTML)</label>
                <textarea name="sidebar_office" rows="3"><?= esc($settings['sidebar_office'] ?? '') ?></textarea>
              </div>
              <div class="field">
                <label>Quick Links (HTML)</label>
                <textarea name="sidebar_links" rows="3"><?= esc($settings['sidebar_links'] ?? '') ?></textarea>
              </div>
            </div>
            <div class="form-row">
              <div class="field">
                <label>Image URL</label>
                <input type="text" name="sidebar_image_url" value="<?= esc($settings['sidebar_image_url'] ?? '') ?>">
                <span class="help">Absolute or site-relative URL</span>
              </div>
              <div class="field">
                <label>Image Caption</label>
                <input type="text" name="sidebar_image_caption" value="<?= esc($settings['sidebar_image_caption'] ?? '') ?>">
              </div>
            </div>
          </fieldset>
        </div>

        <!-- CTA -->
        <div class="cms-card">
          <fieldset id="sec-cta">
            <legend class="cms-card-legend">Call to Action</legend>
            <div class="form-section">
              <div class="field">
                <label>CTA Title</label>
                <input type="text" name="cta_title" value="<?= esc($settings['cta_title'] ?? '') ?>">
              </div>
              <div class="field">
                <label>CTA Description</label>
                <input type="text" name="cta_description" value="<?= esc($settings['cta_description'] ?? '') ?>">
              </div>
            </div>
            <div class="form-row">
              <div class="field">
                <label>Action Label</label>
                <input type="text" name="cta_action_label" value="<?= esc($settings['cta_action_label'] ?? '') ?>">
              </div>
              <div class="field">
                <label>Action URL</label>
                <input type="text" name="cta_action_url" value="<?= esc($settings['cta_action_url'] ?? '') ?>">
              </div>
            </div>
          </fieldset>
        </div>

        <!-- Only sticky Save Bar is used -->
      </form>

      <!-- Spacer to ensure last anchor scrolls nicely under sticky bars -->
      <div class="page-end-spacer" style="height:160px" aria-hidden="true"></div>

      <!-- Sticky Save Bar -->
      <div class="savebar">
        <div class="savebar__inner">
          <span class="savebar__status" id="saveStatus">All changes saved</span>
          <div class="savebar__actions">
            <button type="button" class="btn" id="discardBtn">Discard</button>
            <button type="submit" form="admFreshForm" class="btn btn--primary">Save Changes</button>
          </div>
        </div>
      </div>

    </section>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('admFreshForm');

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
      localStorage.setItem('adm_fresh_card_' + i, card.classList.contains('is-collapsed') ? '1' : '0');
    });
    const card = lg.closest('.cms-card');
    if(localStorage.getItem('adm_fresh_card_' + i) === '1') card.classList.add('is-collapsed');
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

  // keyboard shortcuts
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
