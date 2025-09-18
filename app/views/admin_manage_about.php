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
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/admin/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">About → Management</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username ?? 'Admin'); ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h1 class="admin-cms-section__title mb-1">About Page Management</h1>
          <p class="text-muted mb-0">Edit About page sections, content, and settings</p>
        </div>
        <div class="d-flex gap-2">
          <div class="badge bg-info">About Content Editor</div>
        </div>
      </div>

      <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          Changes saved successfully!
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php elseif ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= esc($error) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Current Data Preview Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-eye me-2"></i>Current Data Preview</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#currentDataCard" aria-expanded="false" aria-controls="currentDataCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="currentDataCard">
          <div class="card-body">
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i>
              Manage <strong>About page content and sections</strong>. Control page narrative, facts, leadership information, and call-to-action elements.
            </div>
            <div class="table-responsive">
              <table class="table table-striped">
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
                  <tr><td>Origins Body</td><td><?= esc(substr($about['origins_body'] ?? '—', 0, 60)) ?>...</td></tr>
                  <tr><td>Leaders Title</td><td><?= esc($about['leaders_title'] ?? '—') ?></td></tr>
                  <tr><td>Leaders List</td><td><?= esc(substr(strip_tags($about['leaders_list'] ?? '—'), 0, 60)) ?>...</td></tr>
                  <tr><td>Academic Leads Title</td><td><?= esc($about['academic_leads_title'] ?? '—') ?></td></tr>
                  <tr><td>Academic Leads List</td><td><?= esc(substr(strip_tags($about['academic_leads_list'] ?? '—'), 0, 60)) ?>...</td></tr>
                  <tr><td>Identity Title</td><td><?= esc($about['identity_title'] ?? '—') ?></td></tr>
                  <tr><td>Identity Items</td><td><?= esc(substr(strip_tags($about['identity_items'] ?? '—'), 0, 60)) ?>...</td></tr>
                  <tr><td>CTA Title</td><td><?= esc($about['cta_title'] ?? '—') ?></td></tr>
                  <tr><td>CTA Body</td><td><?= esc(substr($about['cta_body'] ?? '—', 0, 60)) ?>...</td></tr>
                  <tr><td>CTA Button Label</td><td><?= esc($about['cta_btn_label'] ?? '—') ?></td></tr>
                  <tr><td>CTA Button URL</td><td><?= esc($about['cta_btn_url'] ?? '#') ?></td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Sticky Section Navigation -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Quick Navigation</h5>
        </div>
        <div class="card-body">
          <nav class="d-flex flex-wrap gap-2" aria-label="About sections">
            <a href="#sec-subhero" class="btn btn-sm btn-outline-primary">Subhero</a>
            <a href="#sec-intro" class="btn btn-sm btn-outline-primary">Intro</a>
            <a href="#sec-facts" class="btn btn-sm btn-outline-primary">Facts</a>
            <a href="#sec-origins" class="btn btn-sm btn-outline-primary">Origins</a>
            <a href="#sec-leaders" class="btn btn-sm btn-outline-primary">Leaders</a>
            <a href="#sec-identity" class="btn btn-sm btn-outline-primary">Identity</a>
            <a href="#sec-cta" class="btn btn-sm btn-outline-primary">CTA</a>
          </nav>
        </div>
      </div>

      <!-- Edit Form -->
      <form id="aboutForm" method="post" action="?page=admin_manage_about" class="mb-4">

        <!-- Subhero Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-subhero"><i class="fas fa-star me-2"></i>Subhero Section</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="subhero_lead" class="form-label">Subhero Lead</label>
              <input type="text" class="form-control" id="subhero_lead" name="subhero_lead" 
                     value="<?= esc($about['subhero_lead'] ?? '') ?>" 
                     placeholder="Enter the main subhero text">
            </div>
          </div>
        </div>

        <!-- Intro Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-intro"><i class="fas fa-info-circle me-2"></i>Intro Section</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="intro_title" class="form-label">Intro Title</label>
              <input type="text" class="form-control" id="intro_title" name="intro_title" 
                     value="<?= esc($about['intro_title'] ?? '') ?>" 
                     placeholder="Enter intro section title">
            </div>
            <div class="mb-3">
              <label for="intro_lead" class="form-label">Intro Lead</label>
              <textarea class="form-control" id="intro_lead" name="intro_lead" rows="3" 
                        placeholder="Enter intro lead text"><?= esc($about['intro_lead'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Facts Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-facts"><i class="fas fa-chart-bar me-2"></i>Facts Section</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="fact_title" class="form-label">Fact Title</label>
                <input type="text" class="form-control" id="fact_title" name="fact_title" 
                       value="<?= esc($about['fact_title'] ?? '') ?>" 
                       placeholder="Main fact title">
              </div>
              <div class="col-md-4 mb-3">
                <label for="fact_2" class="form-label">Fact 2</label>
                <input type="text" class="form-control" id="fact_2" name="fact_2" 
                       value="<?= esc($about['fact_2'] ?? '') ?>" 
                       placeholder="Second fact">
              </div>
              <div class="col-md-4 mb-3">
                <label for="fact_3" class="form-label">Fact 3</label>
                <input type="text" class="form-control" id="fact_3" name="fact_3" 
                       value="<?= esc($about['fact_3'] ?? '') ?>" 
                       placeholder="Third fact">
              </div>
            </div>
          </div>
        </div>

        <!-- Origins Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-origins"><i class="fas fa-history me-2"></i>Origins Section</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="origins_title" class="form-label">Origins Title</label>
              <input type="text" class="form-control" id="origins_title" name="origins_title" 
                     value="<?= esc($about['origins_title'] ?? '') ?>" 
                     placeholder="Enter origins section title">
            </div>
            <div class="mb-3">
              <label for="origins_body" class="form-label">Origins Body</label>
              <textarea class="form-control" id="origins_body" name="origins_body" rows="4" 
                        placeholder="Enter the origins story content"><?= esc($about['origins_body'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Leaders Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-leaders"><i class="fas fa-users me-2"></i>Leaders Section</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="leaders_title" class="form-label">Leaders Title</label>
              <input type="text" class="form-control" id="leaders_title" name="leaders_title" 
                     value="<?= esc($about['leaders_title'] ?? '') ?>" 
                     placeholder="Enter leaders section title">
            </div>
            <div class="mb-3">
              <label for="leaders_list" class="form-label">Leaders List</label>
              <textarea class="form-control" id="leaders_list" name="leaders_list" rows="3" 
                        placeholder="Enter leaders information"><?= esc($about['leaders_list'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
              <label for="academic_leads_title" class="form-label">Academic Leads Title</label>
              <input type="text" class="form-control" id="academic_leads_title" name="academic_leads_title" 
                     value="<?= esc($about['academic_leads_title'] ?? '') ?>" 
                     placeholder="Enter academic leads section title">
            </div>
            <div class="mb-3">
              <label for="academic_leads_list" class="form-label">Academic Leads List</label>
              <textarea class="form-control" id="academic_leads_list" name="academic_leads_list" rows="3" 
                        placeholder="Enter academic leads information"><?= esc($about['academic_leads_list'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Identity & Values Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-identity"><i class="fas fa-heart me-2"></i>Identity & Values</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="identity_title" class="form-label">Identity Title</label>
              <input type="text" class="form-control" id="identity_title" name="identity_title" 
                     value="<?= esc($about['identity_title'] ?? '') ?>" 
                     placeholder="Enter identity section title">
            </div>
            <div class="mb-3">
              <label for="identity_items" class="form-label">Identity Items</label>
              <textarea class="form-control" id="identity_items" name="identity_items" rows="4" 
                        placeholder="Enter identity and values content"><?= esc($about['identity_items'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- CTA Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-cta"><i class="fas fa-bullhorn me-2"></i>Call-to-Action Section</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="cta_title" class="form-label">CTA Title</label>
                <input type="text" class="form-control" id="cta_title" name="cta_title" 
                       value="<?= esc($about['cta_title'] ?? '') ?>" 
                       placeholder="Enter CTA title">
              </div>
              <div class="col-md-6 mb-3">
                <label for="cta_btn_label" class="form-label">CTA Button Label</label>
                <input type="text" class="form-control" id="cta_btn_label" name="cta_btn_label" 
                       value="<?= esc($about['cta_btn_label'] ?? '') ?>" 
                       placeholder="Enter button text">
              </div>
            </div>
            <div class="mb-3">
              <label for="cta_body" class="form-label">CTA Body</label>
              <textarea class="form-control" id="cta_body" name="cta_body" rows="3" 
                        placeholder="Enter call-to-action content"><?= esc($about['cta_body'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
              <label for="cta_btn_url" class="form-label">CTA Button URL</label>
              <input type="url" class="form-control" id="cta_btn_url" name="cta_btn_url" 
                     value="<?= esc($about['cta_btn_url'] ?? '') ?>" 
                     placeholder="Enter button link URL">
            </div>
          </div>
        </div>

      </form>

      <div class="page-end-spacer" style="height:160px" aria-hidden="true"></div>

      <div class="savebar">
        <div class="savebar__inner">
          <span class="savebar__status" id="saveStatus">All changes saved</span>
          <div class="savebar__actions">
            <button type="button" class="btn" id="discardBtn">Discard</button>
            <button type="submit" form="aboutForm" class="btn btn--primary">Save Changes</button>
            <a class="btn btn-outline-secondary" href="?page=admin_manage_news">News</a>
            <a class="btn btn-outline-secondary" href="?page=admin_manage_events">Events</a>
            <a class="btn btn-outline-secondary" href="?page=admin_manage_announcements">Announcements</a>
          </div>
        </div>
      </div>

    </section>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
