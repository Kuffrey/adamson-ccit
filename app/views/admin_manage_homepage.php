<?php
// view-only: controller provides $homepage, $success, $error
if (!function_exists('esc')) {
  function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? ($_SESSION['user']['username'] ?? 'Admin');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Homepage | CCIT CMS</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/admin/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Edit Homepage Content</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username); ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Homepage Editor</h1>
      <p class="intro">Update hero, spotlight, program blurbs, and the CTA area. Changes apply site-wide.</p>

      <!-- Sticky Section Subnav -->
      <nav class="cms-subnav" aria-label="Homepage sections">
        <a href="#sec-hero">Hero</a>
        <a href="#sec-why">Why CCIT</a>
        <a href="#sec-spotlight">Spotlight</a>
        <a href="#sec-programs">Programs</a>
        <a href="#sec-cta">CTA</a>
        <a href="?page=admin_manage_quick_actions" style="background: #007bff; color: white; border-radius: 4px;">Manage Quick Actions →</a>
      </nav>

      <?php if ($success): ?>
        <p class="notice success">Changes saved successfully.</p>
      <?php elseif ($error): ?>
        <p class="notice error"><?= esc($error) ?></p>
      <?php endif; ?>

      <!-- Current Homepage Data Preview -->
      <div class="cms-card">
        <h2 class="cms-card-legend">Current Homepage Data</h2>
        <table class="admin-data-table">
          <thead>
            <tr>
              <th>Field</th>
              <th>Value</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Hero Eyebrow</td><td><?= esc($homepage['hero_eyebrow'] ?? '') ?></td></tr>
            <tr><td>Hero Title</td><td><?= esc($homepage['hero_title'] ?? '') ?></td></tr>
            <tr><td>Hero Subtitle</td><td><?= esc($homepage['hero_subtitle'] ?? '') ?></td></tr>
            <tr><td>Why Title</td><td><?= esc($homepage['why_title'] ?? '') ?></td></tr>
            <tr><td>Why Subtitle</td><td><?= esc($homepage['why_subtitle'] ?? '') ?></td></tr>
            <tr><td>Faculty</td><td><?= esc($homepage['why_faculty_text'] ?? '') ?> — <?= esc($homepage['why_faculty_desc'] ?? '') ?></td></tr>
            <tr><td>Faculty Image</td><td><?= esc($homepage['why_faculty_image'] ?? '—') ?></td></tr>
            <tr><td>Faculty Link</td><td><?= esc($homepage['why_faculty_link_label'] ?? '') ?> → <?= esc($homepage['why_faculty_link'] ?? '') ?></td></tr>
            <tr><td>Facilities</td><td><?= esc($homepage['why_facilities_text'] ?? '') ?> — <?= esc($homepage['why_facilities_desc'] ?? '') ?></td></tr>
            <tr><td>Facilities Image</td><td><?= esc($homepage['why_facilities_image'] ?? '—') ?></td></tr>
            <tr><td>Facilities Link</td><td><?= esc($homepage['why_facilities_link_label'] ?? '') ?> → <?= esc($homepage['why_facilities_link'] ?? '') ?></td></tr>
            <tr><td>Career</td><td><?= esc($homepage['why_career_text'] ?? '') ?> — <?= esc($homepage['why_career_desc'] ?? '') ?></td></tr>
            <tr><td>Career Image</td><td><?= esc($homepage['why_career_image'] ?? '—') ?></td></tr>
            <tr><td>Career Link</td><td><?= esc($homepage['why_career_link_label'] ?? '') ?> → <?= esc($homepage['why_career_link'] ?? '') ?></td></tr>
            <tr><td>Spotlight Title</td><td><?= esc($homepage['spotlight_title'] ?? '') ?></td></tr>
            <tr><td>CTA Title</td><td><?= esc($homepage['cta_title'] ?? '') ?></td></tr>
            <tr><td>CTA Description</td><td><?= esc($homepage['cta_description'] ?? '') ?></td></tr>
          </tbody>
        </table>
      </div>

      <form id="homepageForm" method="post" action="?page=admin_manage_homepage" class="admin-cms-form">

        <!-- HERO SECTION -->
        <div class="cms-card">
          <fieldset id="sec-hero">
            <legend class="cms-card-legend">Hero Section</legend>
            <div class="form-section">
              <div class="field">
                <label>Eyebrow <small class="help">Short label above the title (e.g., “College of CCIT”).</small></label>
                <input type="text" name="hero_eyebrow" value="<?= esc($homepage['hero_eyebrow'] ?? '') ?>">
              </div>
              <div class="field">
                <label>Title <small class="help">Use Shift+Enter for a new line.</small></label>
                <textarea name="hero_title" rows="2" required><?= esc($homepage['hero_title'] ?? '') ?></textarea>
              </div>
              <div class="field">
                <label>Subtitle <small class="help">One brief sentence.</small></label>
                <input type="text" name="hero_subtitle" value="<?= esc($homepage['hero_subtitle'] ?? '') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="field"><label>Background Image</label><input type="text" name="hero_bg" value="<?= esc($homepage['hero_bg'] ?? '') ?>"></div>
              <div class="field"><label>Primary Button Text</label><input type="text" name="btn_primary_text" value="<?= esc($homepage['btn_primary_text'] ?? '') ?>"></div>
              <div class="field"><label>Primary Button URL</label><input type="text" name="btn_primary_url" value="<?= esc($homepage['btn_primary_url'] ?? '') ?>"></div>
              <div class="field"><label>Secondary Button Text</label><input type="text" name="btn_secondary_text" value="<?= esc($homepage['btn_secondary_text'] ?? '') ?>"></div>
              <div class="field"><label>Secondary Button URL</label><input type="text" name="btn_secondary_url" value="<?= esc($homepage['btn_secondary_url'] ?? '') ?>"></div>
            </div>
          </fieldset>
        </div>

        <!-- WHY SECTION -->
        <div class="cms-card">
          <fieldset id="sec-why">
            <legend class="cms-card-legend">Why Choose CCIT</legend>
            <div class="form-section">
              <div class="field"><label>Section Title</label><input type="text" name="why_title" value="<?= esc($homepage['why_title'] ?? '') ?>"></div>
              <div class="field"><label>Section Subtitle</label><input type="text" name="why_subtitle" value="<?= esc($homepage['why_subtitle'] ?? '') ?>"></div>
            </div>
            <div class="form-row">
              <div class="field"><label>Faculty Title</label><input type="text" name="why_faculty_text" value="<?= esc($homepage['why_faculty_text'] ?? '') ?>"></div>
              <div class="field"><label>Faculty Description</label><textarea name="why_faculty_desc" rows="2"><?= esc($homepage['why_faculty_desc'] ?? '') ?></textarea></div>
              <div class="field"><label>Faculty Image</label><input type="text" name="why_faculty_image" value="<?= esc($homepage['why_faculty_image'] ?? '') ?>" placeholder="/path/to/faculty-image.jpg"></div>
            </div>
            <div class="form-row">
              <div class="field"><label>Faculty Link Label</label><input type="text" name="why_faculty_link_label" value="<?= esc($homepage['why_faculty_link_label'] ?? '') ?>" placeholder="e.g., Meet Our Faculty"></div>
              <div class="field"><label>Faculty Link URL</label><input type="text" name="why_faculty_link" value="<?= esc($homepage['why_faculty_link'] ?? '') ?>" placeholder="/faculty"></div>
            </div>
            <div class="form-row">
              <div class="field"><label>Facilities Title</label><input type="text" name="why_facilities_text" value="<?= esc($homepage['why_facilities_text'] ?? '') ?>"></div>
              <div class="field"><label>Facilities Description</label><textarea name="why_facilities_desc" rows="2"><?= esc($homepage['why_facilities_desc'] ?? '') ?></textarea></div>
              <div class="field"><label>Facilities Image</label><input type="text" name="why_facilities_image" value="<?= esc($homepage['why_facilities_image'] ?? '') ?>" placeholder="/path/to/facilities-image.jpg"></div>
            </div>
            <div class="form-row">
              <div class="field"><label>Facilities Link Label</label><input type="text" name="why_facilities_link_label" value="<?= esc($homepage['why_facilities_link_label'] ?? '') ?>" placeholder="e.g., Tour Our Campus"></div>
              <div class="field"><label>Facilities Link URL</label><input type="text" name="why_facilities_link" value="<?= esc($homepage['why_facilities_link'] ?? '') ?>" placeholder="/facilities"></div>
            </div>
            <div class="form-row">
              <div class="field"><label>Career Title</label><input type="text" name="why_career_text" value="<?= esc($homepage['why_career_text'] ?? '') ?>"></div>
              <div class="field"><label>Career Description</label><textarea name="why_career_desc" rows="2"><?= esc($homepage['why_career_desc'] ?? '') ?></textarea></div>
              <div class="field"><label>Career Image</label><input type="text" name="why_career_image" value="<?= esc($homepage['why_career_image'] ?? '') ?>" placeholder="/path/to/career-image.jpg"></div>
            </div>
            <div class="form-row">
              <div class="field"><label>Career Link Label</label><input type="text" name="why_career_link_label" value="<?= esc($homepage['why_career_link_label'] ?? '') ?>" placeholder="e.g., Explore Careers"></div>
              <div class="field"><label>Career Link URL</label><input type="text" name="why_career_link" value="<?= esc($homepage['why_career_link'] ?? '') ?>" placeholder="/careers"></div>
            </div>
          </fieldset>
        </div>

        <!-- SPOTLIGHT -->
        <div class="cms-card">
          <fieldset id="sec-spotlight">
            <legend class="cms-card-legend">Spotlight Section</legend>
            <div class="form-section">
              <div class="field"><label>Eyebrow</label><input type="text" name="spotlight_eyebrow" value="<?= esc($homepage['spotlight_eyebrow'] ?? '') ?>"></div>
              <div class="field"><label>Title</label><input type="text" name="spotlight_title" value="<?= esc($homepage['spotlight_title'] ?? '') ?>"></div>
              <div class="field"><label>Video URL</label><input type="text" name="spotlight_video_url" value="<?= esc($homepage['spotlight_video_url'] ?? '') ?>"></div>
            </div>
            <div class="form-row">
              <div class="field" style="grid-column: 1 / -1;">
                <label>Blurb</label>
                <textarea name="spotlight_blurb" rows="3"><?= esc($homepage['spotlight_blurb'] ?? '') ?></textarea>
              </div>
            </div>
            <div class="form-row">
              <div class="field"><label>CTA Text</label><input type="text" name="spotlight_cta_text" value="<?= esc($homepage['spotlight_cta_text'] ?? '') ?>"></div>
              <div class="field"><label>CTA URL</label><input type="text" name="spotlight_cta_url" value="<?= esc($homepage['spotlight_cta_url'] ?? '') ?>"></div>
              <div class="field"><label>CTA2 Text</label><input type="text" name="spotlight_cta2_text" value="<?= esc($homepage['spotlight_cta2_text'] ?? '') ?>"></div>
              <div class="field"><label>CTA2 URL</label><input type="text" name="spotlight_cta2_url" value="<?= esc($homepage['spotlight_cta2_url'] ?? '') ?>"></div>
            </div>
          </fieldset>
        </div>

        <!-- CTA -->
        <div class="cms-card">
          <fieldset id="sec-cta">
            <legend class="cms-card-legend">Call To Action</legend>
            <div class="form-section">
              <div class="field"><label>CTA Title</label><input type="text" name="cta_title" value="<?= esc($homepage['cta_title'] ?? '') ?>"></div>
              <div class="field"><label>CTA Description</label><textarea name="cta_description" rows="2"><?= esc($homepage['cta_description'] ?? '') ?></textarea></div>
              <div class="field"><label>CTA Action Label</label><input type="text" name="cta_action_label" value="<?= esc($homepage['cta_action_label'] ?? '') ?>"></div>
              <div class="field"><label>CTA Action URL</label><input type="text" name="cta_action_url" value="<?= esc($homepage['cta_action_url'] ?? '') ?>"></div>
            </div>
          </fieldset>
        </div>

        <!-- Inline Save removed on purpose (sticky Save Bar only) -->
      </form>

      <!-- Spacer so the last anchor (CTA) can fully settle under sticky bars -->
      <div class="page-end-spacer" aria-hidden="true"></div>

      <!-- Sticky Save Bar -->
      <div class="savebar">
        <div class="savebar__inner">
          <span class="savebar__status" id="saveStatus">All changes saved</span>
          <div class="savebar__actions">
            <button type="button" class="btn" id="discardBtn">Discard</button>
            <button type="submit" form="homepageForm" class="btn btn--primary">Save Changes</button>
          </div>
        </div>
      </div>

    </section>
  </main>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById('homepageForm');

  // -------- measure sticky pieces & expose as CSS vars --------
  function setStickyVars() {
    const topbarH = document.querySelector('.admin-topbar')?.offsetHeight || 64;
    const subnavH = document.querySelector('.cms-subnav')?.offsetHeight || 44;
    document.documentElement.style.setProperty('--topbar-h', topbarH + 'px');
    document.documentElement.style.setProperty('--subnav-h', subnavH + 'px');
  }
  setStickyVars();
  window.addEventListener('resize', setStickyVars);

  // -------- precise scroll with offset & bottom clamp --------
  function scrollToSection(id) {
    const target = document.getElementById(id);
    if (!target) return;

    // expand if the section card is collapsed
    const card = target.closest('.cms-card');
    if (card?.classList.contains('is-collapsed')) {
      card.classList.remove('is-collapsed');
    }

    const styles = getComputedStyle(document.documentElement);
    const topbar = parseInt(styles.getPropertyValue('--topbar-h')) || 64;
    const subnav = parseInt(styles.getPropertyValue('--subnav-h')) || 44;
    const offset = topbar + subnav + 16; // breathing room

    const doc = document.documentElement;
    const targetTop = target.getBoundingClientRect().top + window.pageYOffset;
    const maxScroll = doc.scrollHeight - window.innerHeight;
    const y = Math.min(targetTop - offset, maxScroll);

    window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
    history.replaceState(null, '', '#' + id);
  }

  // -------- subnav click handling --------
  document.querySelector('.cms-subnav')?.addEventListener('click', (e) => {
    const a = e.target.closest('a[href^="#"]');
    if (!a) return;
    e.preventDefault();
    scrollToSection(a.getAttribute('href').slice(1));
  });

  // -------- active link while scrolling --------
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
    links.forEach(a => a.classList.toggle('is-active', a.getAttribute('href') === `#${current}`));
  };
  window.addEventListener('scroll', setActive, { passive: true });
  setActive();

  // -------- handle hash on load / change --------
  if (location.hash && document.getElementById(location.hash.slice(1))) {
    setTimeout(() => scrollToSection(location.hash.slice(1)), 50);
  }
  window.addEventListener('hashchange', () => {
    const id = location.hash.slice(1);
    if (id) scrollToSection(id);
  });

  // -------- collapsible cards + persist --------
  document.querySelectorAll('.cms-card legend').forEach((lg, i) => {
    lg.style.cursor = 'pointer';
    lg.addEventListener('click', () => {
      const card = lg.closest('.cms-card');
      card.classList.toggle('is-collapsed');
      localStorage.setItem('hp_card_' + i, card.classList.contains('is-collapsed') ? '1' : '0');
    });
    const card = lg.closest('.cms-card');
    if (localStorage.getItem('hp_card_' + i) === '1') card.classList.add('is-collapsed');
  });

  // -------- unsaved changes + save feedback --------
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

  // -------- discard --------
  document.getElementById('discardBtn')?.addEventListener('click', () => {
    if (!dirty || confirm('Discard all unsaved changes?')) location.reload();
  });

  // -------- shortcuts --------
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

  // -------- auto-hide notices --------
  const notices = document.querySelectorAll(".notice");
  if (notices.length) setTimeout(() => { notices.forEach(n => n.style.display = "none"); }, 4000);
});
</script>
</body>
</html>
