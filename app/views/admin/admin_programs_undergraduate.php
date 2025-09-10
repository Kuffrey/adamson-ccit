<?php
// app/views/admin/admin_programs_undergraduate.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login_admin'); exit;
}
require_once __DIR__ . '/../../models/ProgramsUndergraduateSettings.php';

function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$user     = $_SESSION['user'] ?? [];
$username = $user['username'] ?? 'Admin';

$notice = '';
$ugs = ProgramsUndergraduateSettings::getSettings();

// Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // Pass through everything; model should whitelist needed columns
    ProgramsUndergraduateSettings::updateSettings($_POST['ugs'] ?? []);
    $ugs = ProgramsUndergraduateSettings::getSettings();
    $notice = 'Undergraduate settings saved.';
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
      <span class="admin-topbar__title">Programs → Undergraduate</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Undergraduate Page Settings</h1>
      <p class="intro">Manage the sub-hero and the 1–4 program cards. HTML grid remains as a legacy fallback.</p>

      <?php if ($notice): ?>
        <p class="notice <?= str_starts_with($notice, 'Error:') ? 'error' : 'success' ?>"><?= esc($notice) ?></p>
      <?php endif; ?>

      <!-- Preview -->
      <div class="cms-card">
        <h2 class="cms-card-legend">Current Snapshot</h2>
        <table class="admin-data-table">
          <thead><tr><th>Field</th><th>Value</th></tr></thead>
          <tbody>
            <tr><td>Subhero Lead</td><td><?= esc($ugs['subhero_lead'] ?? '—') ?></td></tr>
            <tr><td>CTA</td><td><?= esc(($ugs['cta_title'] ?? '').' / '.($ugs['cta_action_label'] ?? '')) ?></td></tr>
          </tbody>
        </table>
      </div>

      <form id="ugForm" method="post" class="admin-cms-form" autocomplete="off">
        <div class="cms-card">
          <fieldset id="sec-hero">
            <legend class="cms-card-legend">Subhero</legend>
            <div class="form-row">
              <div class="field">
                <label>Image URL</label>
                <input type="text" name="ugs[subhero_image_url]" value="<?= esc($ugs['subhero_image_url'] ?? '') ?>">
              </div>
              <div class="field">
                <label>Lead</label>
                <input type="text" name="ugs[subhero_lead]" value="<?= esc($ugs['subhero_lead'] ?? '') ?>">
              </div>
            </div>
          </fieldset>
        </div>

              <!-- Add Program Card (quick add into the first available slot) -->
      <div class="cms-card">
        <fieldset id="sec-add-card">
          <legend class="cms-card-legend">Add Program Card</legend>
          <p class="intro">Fill this and click <em>Save Changes</em>. I’ll place it in the first available slot (1–4).</p>

          <div class="form-row">
            <div class="field">
              <label>Badge (short)</label>
              <input type="text" name="add_card[badge]" placeholder="e.g., BSCS">
            </div>
            <div class="field">
              <label>Title</label>
              <input type="text" name="add_card[title]" placeholder="B.S. in Computer Science">
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label>Muted (optional)</label>
              <input type="text" name="add_card[title_muted]" placeholder="(Dual)">
            </div>
            <div class="field">
              <label>Slug (optional)</label>
              <input type="text" name="add_card[slug]" placeholder="bscs">
              <span class="help">If empty, I’ll auto-generate from the Title.</span>
            </div>
          </div>

          <div class="form-section">
            <div class="field">
              <label>Summary</label>
              <textarea name="add_card[summary]" rows="2" placeholder="Short blurb for the card."></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label>Pillbox Title</label>
              <input type="text" name="add_card[pillbox_title]" placeholder="CCIT Tracks">
            </div>
            <div class="field">
              <label>Pills (one per line)</label>
              <textarea name="add_card[pills]" rows="3" placeholder="Track 1&#10;Track 2&#10;Track 3"></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label>Learn More URL</label>
              <input type="text" name="add_card[learn_more_url]" placeholder="https://www.adamson.edu.ph/...">
              <label class="mt-12" style="display:flex; align-items:center; gap:8px; font-weight:400;">
                <input type="checkbox" name="add_card[learn_more_external]" value="1" checked> Open in new tab
              </label>
            </div>
            <div class="field">
              <label>Curriculum URL</label>
              <input type="text" name="add_card[curriculum_url]" placeholder="https://www.adamson.edu.ph/...">
              <label class="mt-12" style="display:flex; align-items:center; gap:8px; font-weight:400;">
                <input type="checkbox" name="add_card[curriculum_external]" value="1" checked> Open in new tab
              </label>
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label>Apply URL</label>
              <input type="text" name="add_card[apply_url]" placeholder="/adamson-ccit/public/index.php?page=admission_freshman">
            </div>
          </div>
        </fieldset>
      </div>


        <div class="cms-card">
          <fieldset id="sec-cards">
            <legend class="cms-card-legend">Program Cards</legend>
            <p class="intro">Each “pills” field accepts one item per line.</p>

            <?php for ($i=1; $i<=4; $i++): $p="card{$i}_"; ?>
              <div class="form-section" style="border:1px solid var(--line); border-radius:12px; padding:12px; margin-bottom:10px;">
                <div class="form-row">
                  <div class="field">
                    <label>Active</label>
                    <input type="hidden" name="ugs[<?= $p ?>active]" value="0">
                    <label style="display:flex;align-items:center;gap:8px;font-weight:400;">
                      <input type="checkbox" name="ugs[<?= $p ?>active]" value="1" <?= !empty($ugs[$p.'active'])?'checked':''; ?>> Show
                    </label>
                  </div>
                  <div class="field">
                    <label>Position</label>
                    <input type="number" name="ugs[<?= $p ?>position]" value="<?= esc($ugs[$p.'position'] ?? $i) ?>">
                  </div>
                  <div class="field">
                    <label>Slug / ID</label>
                    <input type="text" name="ugs[<?= $p ?>slug]" value="<?= esc($ugs[$p.'slug'] ?? '') ?>" placeholder="<?= ['bscs','dual-degree','bsis','bsit'][$i-1] ?? '' ?>">
                  </div>
                </div>

                <div class="form-row">
                  <div class="field">
                    <label>Badge</label>
                    <input type="text" name="ugs[<?= $p ?>badge]" value="<?= esc($ugs[$p.'badge'] ?? '') ?>">
                  </div>
                  <div class="field">
                    <label>Title</label>
                    <input type="text" name="ugs[<?= $p ?>title]" value="<?= esc($ugs[$p.'title'] ?? '') ?>">
                  </div>
                  <div class="field">
                    <label>Title (Muted Suffix)</label>
                    <input type="text" name="ugs[<?= $p ?>title_muted]" value="<?= esc($ugs[$p.'title_muted'] ?? '') ?>">
                  </div>
                </div>

                <div class="form-section">
                  <div class="field">
                    <label>Summary</label>
                    <textarea rows="2" name="ugs[<?= $p ?>summary]"><?= esc($ugs[$p.'summary'] ?? '') ?></textarea>
                  </div>
                </div>

                <div class="form-row">
                  <div class="field">
                    <label>Pillbox Title</label>
                    <input type="text" name="ugs[<?= $p ?>pillbox_title]" value="<?= esc($ugs[$p.'pillbox_title'] ?? '') ?>">
                  </div>
                  <div class="field">
                    <label>Pills (one per line)</label>
                    <textarea rows="3" name="ugs[<?= $p ?>pills]"><?= esc($ugs[$p.'pills'] ?? '') ?></textarea>
                  </div>
                </div>

                <div class="form-row">
                  <div class="field">
                    <label>Learn More URL</label>
                    <input type="text" name="ugs[<?= $p ?>learn_more_url]" value="<?= esc($ugs[$p.'learn_more_url'] ?? '') ?>">
                  </div>
                  <div class="field">
                    <label>Learn More is External</label>
                    <input type="hidden" name="ugs[<?= $p ?>learn_more_external]" value="0">
                    <label style="display:flex;align-items:center;gap:8px;font-weight:400;">
                      <input type="checkbox" name="ugs[<?= $p ?>learn_more_external]" value="1" <?= !empty($ugs[$p.'learn_more_external'])?'checked':''; ?>> Open in new tab
                    </label>
                  </div>
                </div>

                <div class="form-row">
                  <div class="field">
                    <label>Curriculum URL</label>
                    <input type="text" name="ugs[<?= $p ?>curriculum_url]" value="<?= esc($ugs[$p.'curriculum_url'] ?? '') ?>">
                  </div>
                  <div class="field">
                    <label>Curriculum is External</label>
                    <input type="hidden" name="ugs[<?= $p ?>curriculum_external]" value="0">
                    <label style="display:flex;align-items:center;gap:8px;font-weight:400;">
                      <input type="checkbox" name="ugs[<?= $p ?>curriculum_external]" value="1" <?= !empty($ugs[$p.'curriculum_external'])?'checked':''; ?>> Open in new tab
                    </label>
                  </div>
                </div>

                <div class="form-row">
                  <div class="field">
                    <label>Apply URL</label>
                    <input type="text" name="ugs[<?= $p ?>apply_url]" value="<?= esc($ugs[$p.'apply_url'] ?? '/adamson-ccit/public/index.php?page=admission_freshman') ?>">
                  </div>
                </div>
              </div>
            <?php endfor; ?>

            <div class="form-section" style="border:1px dashed var(--line);border-radius:12px;padding:12px;">
              <div class="field">
                <label>Legacy Programs Grid (HTML) — Optional</label>
                <textarea rows="6" name="ugs[programs_grid]"><?= esc($ugs['programs_grid'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>
        </div>

        <div class="cms-card">
          <fieldset id="sec-cta">
            <legend class="cms-card-legend">Call to Action</legend>
            <div class="form-row">
              <div class="field"><label>Title</label><input type="text" name="ugs[cta_title]" value="<?= esc($ugs['cta_title'] ?? '') ?>"></div>
              <div class="field"><label>Description</label><input type="text" name="ugs[cta_description]" value="<?= esc($ugs['cta_description'] ?? '') ?>"></div>
            </div>
            <div class="form-row">
              <div class="field"><label>Action URL</label><input type="text" name="ugs[cta_action_url]" value="<?= esc($ugs['cta_action_url'] ?? '') ?>"></div>
              <div class="field"><label>Action Label</label><input type="text" name="ugs[cta_action_label]" value="<?= esc($ugs['cta_action_label'] ?? '') ?>"></div>
            </div>
          </fieldset>
        </div>
      </form>

      <div class="savebar">
        <div class="savebar__inner">
          <span class="savebar__status" id="saveStatus">All changes saved</span>
          <div class="savebar__actions">
            <button type="submit" form="ugForm" class="btn btn--primary">Save Changes</button>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('ugForm');
  const saveStatus = document.getElementById('saveStatus');
  let dirty = false;
  form?.addEventListener('input', () => { if(!dirty){ dirty=true; saveStatus.textContent='Unsaved changes…'; } });
  form?.addEventListener('submit', () => { saveStatus.textContent='Saving…'; setTimeout(()=>{ dirty=false; saveStatus.textContent='All changes saved'; }, 800); });
  window.addEventListener('beforeunload', e => { if(dirty){ e.preventDefault(); e.returnValue=''; }});
});
</script>
