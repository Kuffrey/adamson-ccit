<?php
// admin_manage_about.php — CMS CRUD for About Page Content
require_once __DIR__ . '/../models/AboutHistory.php';
function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Prefer username from Auth if available (keeps header consistent)
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? ($_SESSION['user']['username'] ?? 'Admin');

// Load current settings
$model   = new AboutHistory();
$about   = $model->get();
$success = false;
$error   = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // Parse milestones JSON (safe decode)
    $milestonesJSON = $_POST['milestones_json'] ?? '';
    $milestones = [];
    if (trim($milestonesJSON) !== '') {
      $decoded = json_decode($milestonesJSON, true);
      if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $milestones = $decoded;
      } else {
        throw new RuntimeException('Milestones JSON is invalid.');
      }
    }

    // Build payload only with supported columns
    $payload = [
      'subhero_lead'        => $_POST['subhero_lead']        ?? null,
      'intro_lead'          => $_POST['intro_lead']          ?? null,
      'fact_title'          => $_POST['fact_title']          ?? null,
      'fact_1'              => $_POST['fact_1']              ?? null,
      'fact_2'              => $_POST['fact_2']              ?? null,
      'fact_3'              => $_POST['fact_3']              ?? null,
      'origins_body'        => $_POST['origins_body']        ?? null,
      'leaders_list'        => $_POST['leaders_list']        ?? null,
      'academic_leads_list' => $_POST['academic_leads_list'] ?? null,
      'identity_title'      => $_POST['identity_title']      ?? null,
      'identity_items'      => $_POST['identity_items']      ?? null,
      'photo_url'           => $_POST['photo_url']           ?? null,
      'photo_caption'       => $_POST['photo_caption']       ?? null,
      'milestones'          => $milestones, // array; model encodes
    ];

    $model->update($payload);
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
              Manage <strong>About page content and sections</strong>. Control page narrative, timeline, leadership, facts, identity, and media.
            </div>
            <div class="table-responsive">
              <table class="table table-striped align-middle">
                <thead>
                  <tr><th style="width: 220px;">Field</th><th>Value</th></tr>
                </thead>
                <tbody>
                  <tr><td>Subhero Lead</td><td><?= esc($about['subhero_lead'] ?? '—') ?></td></tr>
                  <tr><td>Intro Lead</td><td><?= esc(substr($about['intro_lead'] ?? '—', 0, 200)) ?><?= isset($about['intro_lead']) && strlen($about['intro_lead'])>200?'…':'' ?></td></tr>

                  <tr><td>Fact Title</td><td><?= esc($about['fact_title'] ?? '—') ?></td></tr>
                  <tr><td>Fact 1</td><td><?= esc($about['fact_1'] ?? '—') ?></td></tr>
                  <tr><td>Fact 2</td><td><?= esc($about['fact_2'] ?? '—') ?></td></tr>
                  <tr><td>Fact 3</td><td><?= esc($about['fact_3'] ?? '—') ?></td></tr>

                  <tr><td>Origins Body</td><td><?= esc(substr($about['origins_body'] ?? '—', 0, 200)) ?><?= isset($about['origins_body']) && strlen($about['origins_body'])>200?'…':'' ?></td></tr>

                  <tr><td>Leaders List</td><td><?= esc(substr(strip_tags($about['leaders_list'] ?? '—'), 0, 200)) ?><?= isset($about['leaders_list']) && strlen(strip_tags($about['leaders_list']))>200?'…':'' ?></td></tr>
                  <tr><td>Academic Leads List</td><td><?= esc(substr(strip_tags($about['academic_leads_list'] ?? '—'), 0, 200)) ?><?= isset($about['academic_leads_list']) && strlen(strip_tags($about['academic_leads_list']))>200?'…':'' ?></td></tr>

                  <tr><td>Identity Title</td><td><?= esc($about['identity_title'] ?? '—') ?></td></tr>
                  <tr><td>Identity Items</td><td><?= esc(substr(strip_tags($about['identity_items'] ?? '—'), 0, 200)) ?><?= isset($about['identity_items']) && strlen(strip_tags($about['identity_items']))>200?'…':'' ?></td></tr>

                  <tr><td>Photo URL</td><td><?= esc($about['photo_url'] ?? '—') ?></td></tr>
                  <tr><td>Photo Caption</td><td><?= esc($about['photo_caption'] ?? '—') ?></td></tr>

                  <tr>
                    <td>Milestones</td>
                    <td>
                      <?php if (!empty($about['milestones'])): ?>
                        <code>[<?= count($about['milestones']) ?> items]</code>
                        <details class="mt-2">
                          <summary>Preview JSON</summary>
                          <pre class="mb-0" style="white-space:pre-wrap;"><?= esc(json_encode($about['milestones'], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)) ?></pre>
                        </details>
                      <?php else: ?>
                        —
                      <?php endif; ?>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Section Navigation -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Quick Navigation</h5>
        </div>
        <div class="card-body cms-subnav">
          <nav class="d-flex flex-wrap gap-2" aria-label="About sections">
            <a href="#sec-subhero" class="btn btn-sm btn-outline-primary">Subhero</a>
            <a href="#sec-intro" class="btn btn-sm btn-outline-primary">Intro</a>
            <a href="#sec-facts" class="btn btn-sm btn-outline-primary">Facts</a>
            <a href="#sec-origins" class="btn btn-sm btn-outline-primary">Origins</a>
            <a href="#sec-leaders" class="btn btn-sm btn-outline-primary">Leaders</a>
            <a href="#sec-identity" class="btn btn-sm btn-outline-primary">Identity</a>
            <a href="#sec-media" class="btn btn-sm btn-outline-primary">Media</a>
            <a href="#sec-timeline" class="btn btn-sm btn-outline-primary">Timeline</a>
          </nav>
        </div>
      </div>

      <!-- Edit Form -->
      <form id="aboutForm" method="post" action="?page=admin_manage_about" class="mb-4">

        <!-- Subhero -->
        <div class="card mb-4 cms-card">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-subhero"><i class="fas fa-star me-2"></i>Subhero</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="subhero_lead" class="form-label">Subhero Lead</label>
              <input type="text" class="form-control" id="subhero_lead" name="subhero_lead"
                     value="<?= esc($about['subhero_lead'] ?? '') ?>"
                     placeholder="Our journey, our growth, and the milestones that shaped CCIT.">
            </div>
          </div>
        </div>

        <!-- Intro -->
        <div class="card mb-4 cms-card">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-intro"><i class="fas fa-info-circle me-2"></i>Intro</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="intro_lead" class="form-label">Intro Lead</label>
              <textarea class="form-control" id="intro_lead" name="intro_lead" rows="4"
                        placeholder="Introductory paragraph for the About page"><?= esc($about['intro_lead'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Facts -->
        <div class="card mb-4 cms-card">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-facts"><i class="fas fa-chart-bar me-2"></i>Facts</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="fact_title" class="form-label">Fact Title</label>
                <input type="text" class="form-control" id="fact_title" name="fact_title"
                       value="<?= esc($about['fact_title'] ?? '') ?>" placeholder="At a Glance">
              </div>
              <div class="col-md-4 mb-3">
                <label for="fact_1" class="form-label">Fact 1</label>
                <input type="text" class="form-control" id="fact_1" name="fact_1"
                       value="<?= esc($about['fact_1'] ?? '') ?>" placeholder="Primary Colors: CCIT Green, Adamson Blue">
              </div>
              <div class="col-md-4 mb-3">
                <label for="fact_2" class="form-label">Fact 2</label>
                <input type="text" class="form-control" id="fact_2" name="fact_2"
                       value="<?= esc($about['fact_2'] ?? '') ?>" placeholder="Strengths: ...">
              </div>
            </div>
            <div class="mb-3">
              <label for="fact_3" class="form-label">Fact 3</label>
              <input type="text" class="form-control" id="fact_3" name="fact_3"
                     value="<?= esc($about['fact_3'] ?? '') ?>" placeholder="Student Support: ...">
            </div>
          </div>
        </div>

        <!-- Origins -->
        <div class="card mb-4 cms-card">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-origins"><i class="fas fa-history me-2"></i>Origins</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="origins_body" class="form-label">Origins Body</label>
              <textarea class="form-control" id="origins_body" name="origins_body" rows="5"
                        placeholder="Describe CCIT's origins"><?= esc($about['origins_body'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Leaders -->
        <div class="card mb-4 cms-card">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-leaders"><i class="fas fa-users me-2"></i>Leaders</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="leaders_list" class="form-label">College Leadership (HTML UL allowed)</label>
              <textarea class="form-control" id="leaders_list" name="leaders_list" rows="4"
                        placeholder="<ul><li>…</li></ul>"><?= esc($about['leaders_list'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
              <label for="academic_leads_list" class="form-label">Academic Leadership (HTML UL allowed)</label>
              <textarea class="form-control" id="academic_leads_list" name="academic_leads_list" rows="4"
                        placeholder="<ul><li>…</li></ul>"><?= esc($about['academic_leads_list'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Identity -->
        <div class="card mb-4 cms-card">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-identity"><i class="fas fa-heart me-2"></i>Identity & Values</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="identity_title" class="form-label">Identity Title</label>
              <input type="text" class="form-control" id="identity_title" name="identity_title"
                     value="<?= esc($about['identity_title'] ?? '') ?>" placeholder="Identity & Values">
            </div>
            <div class="mb-3">
              <label for="identity_items" class="form-label">Identity Items (HTML UL allowed)</label>
              <textarea class="form-control" id="identity_items" name="identity_items" rows="5"
                        placeholder="<ul><li>…</li></ul>"><?= esc($about['identity_items'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Media -->
        <div class="card mb-4 cms-card">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-media"><i class="fas fa-image me-2"></i>Media</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="photo_url" class="form-label">Photo URL</label>
              <input type="text" class="form-control" id="photo_url" name="photo_url"
                     value="<?= esc($about['photo_url'] ?? '/adamson-ccit/public/assets/images/hero-campus.jpg') ?>">
            </div>
            <div class="mb-3">
              <label for="photo_caption" class="form-label">Photo Caption</label>
              <input type="text" class="form-control" id="photo_caption" name="photo_caption"
                     value="<?= esc($about['photo_caption'] ?? 'CCIT continues to grow and evolve.') ?>">
            </div>
          </div>
        </div>

        <!-- Timeline (Milestones JSON) -->
        <div class="card mb-4 cms-card">
          <div class="card-header">
            <h5 class="card-title mb-0" id="sec-timeline"><i class="fas fa-stream me-2"></i>Timeline</h5>
          </div>
          <div class="card-body">
            <div class="alert alert-secondary small">
              Provide milestones as valid JSON array of objects with <code>date</code>, <code>label</code>, and <code>desc</code>.
              Example:
              <pre class="mb-0" style="white-space:pre-wrap;">[
  {"date":"2024-01-01","label":"Thanksgiving Mass","desc":"A community celebration..."},
  {"date":"2024-02-01","label":"Official Launch of CCIT","desc":"Formally introduced to the community"}
]</pre>
            </div>
            <textarea class="form-control" id="milestones_json" name="milestones_json" rows="8"
              placeholder='[{"date":"YYYY-MM-DD","label":"…","desc":"…"}]'><?= esc(
                json_encode($about['milestones'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)
              ) ?></textarea>
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

  // -- unsaved changes tracking + save feedback
  let dirty = false;
  const saveStatus = document.getElementById('saveStatus');
  form?.addEventListener('input', () => {
    if (!dirty) { dirty = true; saveStatus.textContent = 'Unsaved changes…'; }
  });
  form?.addEventListener('submit', () => {
    dirty = false;
    saveStatus.textContent = 'Saving…';
    setTimeout(() => { dirty = false; saveStatus.textContent = 'All changes saved'; }, 900);
  });
  window.addEventListener('beforeunload', (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });

  // -- discard
  document.getElementById('discardBtn')?.addEventListener('click', () => {
    if (!dirty || confirm('Discard all unsaved changes?')) location.reload();
  });

  // -- keyboard shortcuts
  window.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
      e.preventDefault();
      document.querySelector('.savebar [type="submit"]')?.click();
    }
  });
});
</script>
