<?php
// app/views/admin/admin_programs_undergraduate.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login_admin'); exit;
}

// ✅ robust include
require_once dirname(__DIR__, 2) . '/models/ProgramsUndergraduateSettings.php';

function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$user     = $_SESSION['user'] ?? [];
$username = $user['username'] ?? 'Admin';

$notice = '';
$ugs = ProgramsUndergraduateSettings::getSettings();

/**
 * Handle actions:
 * - settings + optional add_card (insert one new card)
 * - update existing card (one row)
 * - delete existing card (one row)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $action = $_POST['action'] ?? 'save_settings';

    if ($action === 'update_card') {
      $id   = (int)($_POST['card_id'] ?? 0);
      $data = $_POST['card'] ?? [];
      ProgramsUndergraduateSettings::updateCardById($id, $data);
      $notice = 'Card updated.';
    } elseif ($action === 'delete_card') {
      $id = (int)($_POST['card_id'] ?? 0);
      ProgramsUndergraduateSettings::deleteCardById($id);
      $notice = 'Card deleted.';
    } else {
      // default: save settings + maybe add a new card
      ProgramsUndergraduateSettings::updateSettings($_POST['ugs'] ?? [], $_POST['add_card'] ?? null);
      $notice = 'Undergraduate settings saved.';
    }

    // refresh snapshot after any action
    $ugs = ProgramsUndergraduateSettings::getSettings();
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

// 🔽 Fetch dynamic cards (raw rows for editing)
$cards = ProgramsUndergraduateSettings::getAllCards();
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
      <p class="intro">Manage the sub-hero and <strong>dynamic program cards</strong>. Legacy HTML grid remains as a fallback.</p>

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
            <tr><td>Dynamic Cards</td><td><?= count($cards) ?> total</td></tr>
          </tbody>
        </table>
      </div>

      <!-- === Settings + Add-one-card === -->
      <form id="ugForm" method="post" class="admin-cms-form" autocomplete="off">
        <input type="hidden" name="action" value="save_settings">

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

        <div class="cms-card">
          <fieldset id="sec-add-card">
            <legend class="cms-card-legend">Add Program Card</legend>
            <p class="intro">Fill this and click <em>Save Changes</em>. I’ll add it as a new card (positioned last).</p>

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
                <input type="text" name="add_card[title_muted]" placeholder="(Software &amp; Data)">
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

        <!-- Legacy HTML fallback -->
        <div class="cms-card">
          <fieldset id="sec-legacy">
            <legend class="cms-card-legend">Legacy Programs Grid (HTML) — Optional</legend>
            <div class="form-section" style="border:1px dashed var(--line);border-radius:12px;padding:12px;">
              <div class="field">
                <label>HTML (used only if no dynamic cards)</label>
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

        <div class="savebar">
          <div class="savebar__inner">
            <span class="savebar__status" id="saveStatus">All changes saved</span>
            <div class="savebar__actions">
              <button type="submit" class="btn btn--primary">Save Changes</button>
            </div>
          </div>
        </div>
      </form>

      <!-- === Dynamic Cards (editable table) === -->
      <div class="cms-card">
        <fieldset id="sec-cards">
          <legend class="cms-card-legend">Program Cards (Dynamic)</legend>

          <?php if ($cards): ?>
            <table class="admin-data-table">
              <thead>
                <tr>
                  <th style="width:60px;">ID</th>
                  <th style="width:72px;">Pos</th>
                  <th style="width:80px;">Active</th>
                  <th>Badge</th>
                  <th>Title</th>
                  <th>Muted</th>
                  <th>Slug</th>
                  <th>Links</th>
                  <th style="width:180px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($cards as $c): ?>
                  <tr>
                    <form method="post" autocomplete="off">
                      <input type="hidden" name="action" value="update_card">
                      <input type="hidden" name="card_id" value="<?= (int)$c['id'] ?>">

                      <td><?= (int)$c['id'] ?></td>

                      <td><input type="number" name="card[position]" value="<?= esc($c['position']) ?>" style="width:72px"></td>

                      <td>
                        <input type="hidden" name="card[is_active]" value="0">
                        <label style="display:flex;align-items:center;gap:6px;">
                          <input type="checkbox" name="card[is_active]" value="1" <?= !empty($c['is_active']) ? 'checked' : '' ?>> Show
                        </label>
                      </td>

                      <td><input type="text" name="card[badge]" value="<?= esc($c['badge']) ?>" placeholder="BSCS" style="min-width:90px"></td>
                      <td><input type="text" name="card[title]" value="<?= esc($c['title']) ?>" placeholder="Full title"></td>
                      <td><input type="text" name="card[title_muted]" value="<?= esc($c['title_muted']) ?>" placeholder="(optional)"></td>
                      <td><input type="text" name="card[slug]" value="<?= esc($c['slug']) ?>" placeholder="slug-id"></td>

                      <td style="font-size:12px; line-height:1.2;">
                        <div>Learn: <input type="text" name="card[learn_more_url]" value="<?= esc($c['learn_more_url']) ?>" style="width:260px"></div>
                        <label style="display:inline-flex;align-items:center;gap:6px;margin-right:8px;">
                          <input type="hidden" name="card[learn_more_external]" value="0">
                          <input type="checkbox" name="card[learn_more_external]" value="1" <?= !empty($c['learn_more_external'])?'checked':''; ?>> ext
                        </label>
                        <div>Curr: <input type="text" name="card[curriculum_url]" value="<?= esc($c['curriculum_url']) ?>" style="width:260px"></div>
                        <label style="display:inline-flex;align-items:center;gap:6px;">
                          <input type="hidden" name="card[curriculum_external]" value="0">
                          <input type="checkbox" name="card[curriculum_external]" value="1" <?= !empty($c['curriculum_external'])?'checked':''; ?>> ext
                        </label>
                        <div>Apply: <input type="text" name="card[apply_url]" value="<?= esc($c['apply_url']) ?>" style="width:260px"></div>
                      </td>

                      <td>
                        <button type="submit" class="btn">Update</button>
                        <button
                          type="submit"
                          class="btn danger"
                          name="action"
                          value="delete_card"
                          onclick="return confirm('Delete this card?');"
                        >Delete</button>
                      </td>
                    </form>
                  </tr>

                  <!-- Optional expandable details (summary/pills) -->
                  <tr>
                    <td></td>
                    <td colspan="8">
                      <form method="post" autocomplete="off" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <input type="hidden" name="action" value="update_card">
                        <input type="hidden" name="card_id" value="<?= (int)$c['id'] ?>">

                        <div class="field">
                          <label>Summary</label>
                          <textarea name="card[summary]" rows="2"><?= esc($c['summary']) ?></textarea>
                        </div>

                        <div class="field">
                          <label>Pillbox Title</label>
                          <input type="text" name="card[pillbox_title]" value="<?= esc($c['pillbox_title']) ?>">
                          <label style="margin-top:8px;">Pills (one per line)</label>
                          <textarea name="card[pills]" rows="2"><?= esc($c['pills']) ?></textarea>
                        </div>

                        <div>
                          <button type="submit" class="btn">Update Details</button>
                        </div>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <p class="help">Reorder by changing “Pos”. Toggle visibility with “Active”. Use the second row to edit summary/pills.</p>
          <?php else: ?>
            <p class="intro">No cards yet. Use “Add Program Card” above and click <em>Save Changes</em>.</p>
          <?php endif; ?>
        </fieldset>
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
