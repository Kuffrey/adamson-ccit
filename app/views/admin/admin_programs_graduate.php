<?php
// app/views/admin/admin_programs_graduate.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login_admin'); exit;
}
require_once __DIR__ . '/../../models/ProgramsGraduateSettings.php';

function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$user     = $_SESSION['user'] ?? [];
$username = $user['username'] ?? 'Admin';

$notice = '';
$gs = ProgramsGraduateSettings::getSettings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    ProgramsGraduateSettings::updateSettings($_POST['gs'] ?? []);
    $gs = ProgramsGraduateSettings::getSettings();
    $notice = 'Graduate settings saved.';
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Programs → Graduate Studies</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Graduate Page Settings</h1>
      <p class="intro">Plain text fields only. The public page renders the HTML layout automatically.</p>

      <?php if ($notice): ?>
        <p class="notice <?= str_starts_with($notice, 'Error:') ? 'error' : 'success' ?>"><?= esc($notice) ?></p>
      <?php endif; ?>

      <form id="gradForm" method="post" class="admin-cms-form" autocomplete="off">
        <div class="cms-card">
          <fieldset>
            <legend class="cms-card-legend">Subhero</legend>
            <div class="form-row">
              <div class="field">
                <label>Image URL</label>
                <input type="text" name="gs[subhero_image_url]" value="<?= esc($gs['subhero_image_url'] ?? '') ?>">
              </div>
              <div class="field">
                <label>Lead</label>
                <input type="text" name="gs[subhero_lead]" value="<?= esc($gs['subhero_lead'] ?? '') ?>">
              </div>
            </div>
          </fieldset>
        </div>

        <div class="cms-card">
          <fieldset id="sec-cards">
            <legend class="cms-card-legend">Program Cards (1–4)</legend>
            <p class="intro">“Pills” accepts one item per line.</p>

            <?php for ($i=1; $i<=4; $i++): $p="card{$i}_"; ?>
              <div class="form-section" style="border:1px solid var(--line); border-radius:12px; padding:12px; margin-bottom:10px;">
                <div class="form-row">
                  <div class="field">
                    <label>Active</label>
                    <input type="hidden" name="gs[<?= $p ?>active]" value="0">
                    <label style="display:flex;align-items:center;gap:8px;font-weight:400;">
                      <input type="checkbox" name="gs[<?= $p ?>active]" value="1" <?= !empty($gs[$p.'active'])?'checked':''; ?>> Show
                    </label>
                  </div>
                  <div class="field">
                    <label>Position</label>
                    <input type="number" name="gs[<?= $p ?>position]" value="<?= esc($gs[$p.'position'] ?? $i) ?>">
                  </div>
                  <div class="field">
                    <label>Slug / ID</label>
                    <input type="text" name="gs[<?= $p ?>slug]" value="<?= esc($gs[$p.'slug'] ?? '') ?>">
                  </div>
                </div>

                <div class="form-row">
                  <div class="field">
                    <label>Badge</label>
                    <input type="text" name="gs[<?= $p ?>badge]" value="<?= esc($gs[$p.'badge'] ?? '') ?>">
                  </div>
                  <div class="field">
                    <label>Title</label>
                    <input type="text" name="gs[<?= $p ?>title]" value="<?= esc($gs[$p.'title'] ?? '') ?>">
                  </div>
                  <div class="field">
                    <label>Title (Muted Suffix)</label>
                    <input type="text" name="gs[<?= $p ?>title_muted]" value="<?= esc($gs[$p.'title_muted'] ?? '') ?>">
                  </div>
                </div>

                <div class="form-section">
                  <div class="field">
                    <label>Summary</label>
                    <textarea rows="2" name="gs[<?= $p ?>summary]"><?= esc($gs[$p.'summary'] ?? '') ?></textarea>
                  </div>
                </div>

                <div class="form-row">
                  <div class="field">
                    <label>Pillbox Title</label>
                    <input type="text" name="gs[<?= $p ?>pillbox_title]" value="<?= esc($gs[$p.'pillbox_title'] ?? '') ?>">
                  </div>
                  <div class="field">
                    <label>Pills (one per line)</label>
                    <textarea rows="3" name="gs[<?= $p ?>pills]"><?= esc($gs[$p.'pills'] ?? '') ?></textarea>
                  </div>
                </div>

                <div class="form-row">
                  <div class="field">
                    <label>Learn More URL</label>
                    <input type="text" name="gs[<?= $p ?>learn_more_url]" value="<?= esc($gs[$p.'learn_more_url'] ?? '') ?>">
                  </div>
                  <div class="field">
                    <label>Learn More is External</label>
                    <input type="hidden" name="gs[<?= $p ?>learn_more_external]" value="0">
                    <label style="display:flex;align-items:center;gap:8px;font-weight:400;">
                      <input type="checkbox" name="gs[<?= $p ?>learn_more_external]" value="1" <?= !empty($gs[$p.'learn_more_external'])?'checked':''; ?>> Open in new tab
                    </label>
                  </div>
                </div>

                <div class="form-row">
                  <div class="field">
                    <label>Curriculum URL</label>
                    <input type="text" name="gs[<?= $p ?>curriculum_url]" value="<?= esc($gs[$p.'curriculum_url'] ?? '') ?>">
                  </div>
                  <div class="field">
                    <label>Curriculum is External</label>
                    <input type="hidden" name="gs[<?= $p ?>curriculum_external]" value="0">
                    <label style="display:flex;align-items:center;gap:8px;font-weight:400;">
                      <input type="checkbox" name="gs[<?= $p ?>curriculum_external]" value="1" <?= !empty($gs[$p.'curriculum_external'])?'checked':''; ?>> Open in new tab
                    </label>
                  </div>
                </div>

                <div class="form-row">
                  <div class="field">
                    <label>Apply URL</label>
                    <input type="text" name="gs[<?= $p ?>apply_url]" value="<?= esc($gs[$p.'apply_url'] ?? '/adamson-ccit/public/index.php?page=admission_graduate_school') ?>">
                  </div>
                </div>
              </div>
            <?php endfor; ?>
          </fieldset>
        </div>

        <div class="cms-card">
          <fieldset>
            <legend class="cms-card-legend">Call to Action</legend>
            <div class="form-row">
              <div class="field"><label>Title</label><input type="text" name="gs[cta_title]" value="<?= esc($gs['cta_title'] ?? '') ?>"></div>
              <div class="field"><label>Description</label><input type="text" name="gs[cta_description]" value="<?= esc($gs['cta_description'] ?? '') ?>"></div>
            </div>
            <div class="form-row">
              <div class="field"><label>Action URL</label><input type="text" name="gs[cta_action_url]" value="<?= esc($gs['cta_action_url'] ?? '') ?>"></div>
              <div class="field"><label>Action Label</label><input type="text" name="gs[cta_action_label]" value="<?= esc($gs['cta_action_label'] ?? '') ?>"></div>
            </div>
          </fieldset>
        </div>
      </form>

      <div class="savebar">
        <div class="savebar__inner">
          <span class="savebar__status" id="saveStatus">All changes saved</span>
          <div class="savebar__actions">
            <button type="submit" form="gradForm" class="btn btn--primary">Save Changes</button>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('gradForm');
  const saveStatus = document.getElementById('saveStatus');
  let dirty = false, isSubmitting = false;

  form?.addEventListener('input', () => { if(!dirty){ dirty=true; saveStatus.textContent='Unsaved changes…'; } });
  form?.addEventListener('submit', () => { isSubmitting = true; dirty=false; saveStatus.textContent='Saving…'; });
  window.addEventListener('beforeunload', e => { if(dirty && !isSubmitting){ e.preventDefault(); e.returnValue=''; }});
});
</script>
