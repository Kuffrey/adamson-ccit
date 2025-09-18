<?php
// app/views/admin_manage_programs.php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login_admin'); exit;
}
require_once __DIR__ . '/../models/Program.php';
require_once __DIR__ . '/../models/ProgramsUndergraduateSettings.php';
require_once __DIR__ . '/../models/ProgramsGraduateSettings.php';

function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$username = $_SESSION['user']['username'] ?? 'Admin';
$notice   = '';

/* ---------- Handle POST (CRUD) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // Update existing programs
    if (!empty($_POST['program']) && is_array($_POST['program'])) {
      foreach ($_POST['program'] as $id => $data) {
        $id = (int)$id;
        // Normalize checkbox
        $data['is_active'] = isset($data['is_active']) && (int)$data['is_active'] === 1 ? 1 : 0;
        Program::update($id, $data);
      }
      $notice = 'Programs updated.';
    }

    // Delete one
    if (!empty($_POST['delete_program'])) {
      Program::delete((int)$_POST['delete_program']);
      $notice = 'Program deleted.';
    }

    // Create new
    if (!empty($_POST['new_program']['name'])) {
      $np = $_POST['new_program'];
      $np['is_active'] = !empty($np['is_active']) ? 1 : 0;
      Program::createFull($np);
      $notice = 'Program added.';
    }
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

/* ---------- Fetch data ---------- */
$programs = Program::all();
$ugs      = ProgramsUndergraduateSettings::getSettings();
$gs       = ProgramsGraduateSettings::getSettings();
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/admin/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Manage Programs</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Programs &amp; Page Content</h1>
      <p class="intro">
        Edit the Programs list here. For page content, use
        <a class="pill" href="?page=admin_programs_undergraduate">Undergraduate</a>
        and
        <a class="pill" href="?page=admin_programs_graduate">Graduate Studies</a>.
      </p>

      <?php if ($notice): ?>
        <p class="notice <?= str_starts_with($notice,'Error:') ? 'error':'success' ?>"><?= esc($notice) ?></p>
      <?php endif; ?>

      <!-- Overview -->
      <div class="cms-card">
        <h2 class="cms-card-legend">Snapshot</h2>
        <table class="admin-data-table">
          <thead><tr><th>Field</th><th>Value</th></tr></thead>
          <tbody>
            <tr><td>Programs (count)</td><td><?= count($programs) ?></td></tr>
            <tr><td>UG Lead</td><td><?= esc($ugs['subhero_lead'] ?? '—') ?></td></tr>
            <tr><td>Grad Lead</td><td><?= esc($gs['subhero_lead'] ?? '—') ?></td></tr>
          </tbody>
        </table>
      </div>

      <form id="progForm" method="post" class="admin-cms-form" autocomplete="off">
        <!-- Programs List -->
        <div class="cms-card">
          <fieldset id="sec-programs">
            <legend class="cms-card-legend">Programs List</legend>

            <?php if (empty($programs)): ?>
              <p>No programs found.</p>
            <?php else: foreach ($programs as $p): ?>
              <div class="form-section" style="border:1px solid var(--line); border-radius:12px; padding:12px; margin-bottom:10px;">
                <div class="form-row">
                  <div class="field">
                    <label>Name</label>
                    <input type="text" name="program[<?= (int)$p['id'] ?>][name]" value="<?= esc($p['name']) ?>">
                  </div>
                  <div class="field">
                    <label>URL (optional)</label>
                    <input type="text" name="program[<?= (int)$p['id'] ?>][url]" value="<?= esc($p['url'] ?? '') ?>">
                  </div>
                  <div class="field">
                    <label>Status</label>
                    <input type="hidden" name="program[<?= (int)$p['id'] ?>][is_active]" value="0">
                    <label style="display:flex; align-items:center; gap:8px; font-weight:400;">
                      <input type="checkbox" name="program[<?= (int)$p['id'] ?>][is_active]" value="1" <?= !empty($p['is_active']) ? 'checked' : '' ?>>
                      Active
                    </label>
                  </div>
                </div>

                <div class="form-section">
                  <div class="field">
                    <label>Description</label>
                    <textarea rows="2" name="program[<?= (int)$p['id'] ?>][description]"><?= esc($p['description'] ?? '') ?></textarea>
                  </div>
                </div>

                <div class="form-row">
                  <div class="field">
                    <label>Image URL</label>
                    <input type="text" name="program[<?= (int)$p['id'] ?>][image]" value="<?= esc($p['image'] ?? '') ?>">
                    <span class="help">Absolute or site-relative URL</span>
                  </div>
                  <div class="field">
                    <label>Image Alt</label>
                    <input type="text" name="program[<?= (int)$p['id'] ?>][image_alt]" value="<?= esc($p['image_alt'] ?? '') ?>">
                  </div>
                  <div class="field" style="display:flex; align-items:flex-end;">
                    <button class="btn btn--danger" name="delete_program" value="<?= (int)$p['id'] ?>" type="submit" onclick="return confirm('Delete this program?')">Delete</button>
                  </div>
                </div>
              </div>
            <?php endforeach; endif; ?>

            <!-- Add New Program -->
            <div class="form-section" style="border:1px dashed var(--line); border-radius:12px; padding:12px; margin-top:12px;">
              <div class="form-row">
                <div class="field">
                  <label>New Program — Name</label>
                  <input type="text" name="new_program[name]" placeholder="e.g., Undergraduate">
                </div>
                <div class="field">
                  <label>URL (optional)</label>
                  <input type="text" name="new_program[url]" placeholder="/adamson-ccit/public/index.php?page=programs_undergraduate">
                </div>
                <div class="field">
                  <label>Status</label>
                  <input type="hidden" name="new_program[is_active]" value="0">
                  <label style="display:flex; align-items:center; gap:8px; font-weight:400;">
                    <input type="checkbox" name="new_program[is_active]" value="1" checked> Active
                  </label>
                </div>
              </div>
              <div class="form-section">
                <div class="field">
                  <label>Description</label>
                  <textarea rows="2" name="new_program[description]" placeholder="Short blurb shown on the Programs page."></textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="field">
                  <label>Image URL</label>
                  <input type="text" name="new_program[image]" placeholder="/adamson-ccit/public/assets/images/programs/undergrad.jpg">
                </div>
                <div class="field">
                  <label>Image Alt</label>
                  <input type="text" name="new_program[image_alt]" placeholder="Students collaborating with laptops">
                </div>
              </div>
            </div>
          </fieldset>
        </div>
      </form>

      <div class="savebar">
        <div class="savebar__inner">
          <span class="savebar__status" id="saveStatus">All changes saved</span>
          <div class="savebar__actions">
            <button type="submit" form="progForm" class="btn btn--primary">Save Changes</button>
            <a class="btn" href="?page=admin_programs_undergraduate">Undergraduate Page</a>
            <a class="btn" href="?page=admin_programs_graduate">Graduate Page</a>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('progForm');
  const saveStatus = document.getElementById('saveStatus');
  let dirty = false;
  form?.addEventListener('input', () => { if(!dirty){ dirty=true; saveStatus.textContent='Unsaved changes…'; }});
  form?.addEventListener('submit', () => { saveStatus.textContent='Saving…'; setTimeout(()=>{ dirty=false; saveStatus.textContent='All changes saved'; }, 800); });
  window.addEventListener('beforeunload', e => { if(dirty){ e.preventDefault(); e.returnValue=''; }});
});
</script>
