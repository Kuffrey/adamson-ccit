<?php
session_start();
if (!in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login'); exit;
}

require_once dirname(__DIR__, 2) . '/models/ProgramsGraduateSettings.php';

function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$user     = $_SESSION['user'] ?? [];
$username = $user['username'] ?? 'Admin';

$notice = '';

try {
  $gs = ProgramsGraduateSettings::getSettings();
  $cards = ProgramsGraduateSettings::getCards([], true); // true = admin mode
} catch (Exception $e) {
  $notice = 'Error: ' . $e->getMessage();
  $gs = [];
  $cards = [];
}

if ($_POST) {
  $action = $_POST['action'] ?? '';
  
  try {
    if ($action === 'save_settings') {
      if (!empty($_POST['gs'])) {
        ProgramsGraduateSettings::saveSettings($_POST['gs']);
      }
      
      if (!empty($_POST['add_card'])) {
        ProgramsGraduateSettings::createCard($_POST['add_card']);
      }
      
      $notice = 'Changes saved successfully!';
    } elseif ($action === 'delete_card') {
      ProgramsGraduateSettings::deleteCard((int)($_POST['card_id'] ?? 0));
      $notice = 'Card deleted successfully!';
    } elseif ($action === 'edit_card') {
      ProgramsGraduateSettings::updateCard((int)($_POST['card_id'] ?? 0), $_POST['card'] ?? []);
      $notice = 'Card updated successfully!';
    }
    
    // Refresh data
    $gs = ProgramsGraduateSettings::getSettings();
    $cards = ProgramsGraduateSettings::getCards([], true); // true = admin mode
    
  } catch (Exception $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

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
      
      <?php if ($notice): ?>
        <div class="alert <?= str_starts_with($notice, 'Error') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show" role="alert">
          <?= esc($notice) ?>
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
              Manage the sub-hero and <strong>dynamic program cards</strong>. Legacy HTML grid remains as a fallback.
            </div>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr><th>Field</th><th>Value</th></tr>
                </thead>
                <tbody>
                  <tr><td>Subhero Lead</td><td><?= esc($gs['subhero_lead'] ?? '—') ?></td></tr>
                  <tr><td>CTA</td><td><?= esc(($gs['cta_title'] ?? '').' / '.($gs['cta_action_label'] ?? '')) ?></td></tr>
                  <tr><td>Dynamic Cards</td><td><?= count($cards) ?> total</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Management Form -->
      <form id="gradForm" method="post" autocomplete="off">
        <input type="hidden" name="action" value="save_settings">

        <!-- Subhero Section -->
        <div class="card mb-4">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Subhero</h5>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                      data-bs-target="#subheroCollapse" aria-expanded="false" aria-controls="subheroCollapse">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </div>
          
          <div class="collapse" id="subheroCollapse">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Image URL</label>
                  <input type="text" class="form-control" name="gs[subhero_image_url]" value="<?= esc($gs['subhero_image_url'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Lead</label>
                  <input type="text" class="form-control" name="gs[subhero_lead]" value="<?= esc($gs['subhero_lead'] ?? '') ?>">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Add Program Card -->
        <div class="card mb-4">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Add Program Card</h5>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                      data-bs-target="#addCardCollapse" aria-expanded="false" aria-controls="addCardCollapse">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </div>
          
          <div class="collapse" id="addCardCollapse">
            <div class="card-body">
              <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Fill this and click <strong>Save Changes</strong>. Will add as a new card (positioned last).
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Badge (short) <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="add_card[badge]" placeholder="e.g., MIT, PhD" required>
                  <div class="form-text">Short identifier like MIT, PhD, MSCS, etc.</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Title <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="add_card[title]" placeholder="Master in Information Technology" required>
                  <div class="form-text">Full program title</div>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Muted (optional)</label>
                  <input type="text" class="form-control" name="add_card[title_muted]" placeholder="(with Research Track)">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Slug (optional)</label>
                  <input type="text" class="form-control" name="add_card[slug]" placeholder="mit">
                  <div class="form-text">If empty, auto-generated from title</div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Summary</label>
                <textarea class="form-control" name="add_card[summary]" rows="2" placeholder="Short blurb for the card"></textarea>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Pillbox Title</label>
                  <input type="text" class="form-control" name="add_card[pillbox_title]" placeholder="Program Emphases">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Pills (one per line)</label>
                  <textarea class="form-control" name="add_card[pills]" rows="3" placeholder="Advanced Computing&#10;IT Leadership&#10;Ethics & Responsibility"></textarea>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Learn More URL</label>
                  <input type="text" class="form-control" name="add_card[learn_more_url]" placeholder="https://...">
                  <div class="form-check mt-2">
                    <input type="checkbox" class="form-check-input" name="add_card[learn_more_external]" value="1" checked>
                    <label class="form-check-label">Open in new tab</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Curriculum URL</label>
                  <input type="text" class="form-control" name="add_card[curriculum_url]" placeholder="https://...">
                  <div class="form-check mt-2">
                    <input type="checkbox" class="form-check-input" name="add_card[curriculum_external]" value="1" checked>
                    <label class="form-check-label">Open in new tab</label>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Apply URL</label>
                <input type="text" class="form-control" name="add_card[apply_url]" placeholder="/adamson-ccit/public/index.php?page=admission_graduate_school">
              </div>
            </div>
          </div>
        </div>

        <!-- Call to Action -->
        <div class="card mb-4">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Call to Action</h5>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                      data-bs-target="#ctaCollapse" aria-expanded="false" aria-controls="ctaCollapse">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </div>
          
          <div class="collapse" id="ctaCollapse">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Title</label>
                  <input type="text" class="form-control" name="gs[cta_title]" value="<?= esc($gs['cta_title'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Description</label>
                  <input type="text" class="form-control" name="gs[cta_description]" value="<?= esc($gs['cta_description'] ?? '') ?>">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Action URL</label>
                  <input type="text" class="form-control" name="gs[cta_action_url]" value="<?= esc($gs['cta_action_url'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Action Label</label>
                  <input type="text" class="form-control" name="gs[cta_action_label]" value="<?= esc($gs['cta_action_label'] ?? '') ?>">
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>

      <!-- Dynamic Cards Management -->
      <?php if ($cards): ?>
        <div class="card mb-4">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Program Cards (Dynamic) (<?= count($cards) ?>)</h5>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                      data-bs-target="#cardsListCard" aria-expanded="true" aria-controls="cardsListCard">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </div>
          
          <div class="collapse show" id="cardsListCard">
            <div class="card-body">
              <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Current program cards. Edit details, reorder positions, or toggle visibility.
              </div>
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead class="table-dark">
                    <tr>
                      <th>ID</th>
                      <th>Position</th>
                      <th>Active</th>
                      <th>Badge</th>
                      <th>Title</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($cards as $card): ?>
                      <tr>
                        <td><?= esc($card['id']) ?></td>
                        <td><?= esc($card['position'] ?? '0') ?></td>
                        <td><?= !empty($card['is_active']) ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
                        <td>
                          <?php if (!empty($card['badge'])): ?>
                            <span class="badge bg-info"><?= esc($card['badge']) ?></span>
                          <?php else: ?>
                            <span class="text-muted">—</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <strong><?= esc($card['title'] ?? '') ?></strong>
                          <?php if (!empty($card['summary'])): ?>
                            <br><small class="text-muted"><?= esc(substr($card['summary'], 0, 50)) ?>...</small>
                          <?php endif; ?>
                        </td>
                        <td>
                          <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#editCardModal<?= $card['id'] ?>">
                            <i class="fas fa-edit"></i>
                          </button>
                          <form method="post" class="d-inline">
                            <input type="hidden" name="action" value="delete_card">
                            <input type="hidden" name="card_id" value="<?= esc($card['id']) ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this card?')">
                              <i class="fas fa-trash"></i>
                            </button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <div class="page-end-spacer" style="height:160px" aria-hidden="true"></div>

      <div class="savebar">
        <div class="savebar__inner">
          <span class="savebar__status" id="saveStatus">All changes saved</span>
          <div class="savebar__actions">
            <button type="button" class="btn" id="discardBtn">Discard</button>
            <button type="submit" form="gradForm" class="btn btn--primary">Save Changes</button>
          </div>
        </div>
      </div>

    </section>
  </main>

  <!-- Edit Card Modals -->
  <?php if ($cards): ?>
    <?php foreach ($cards as $card): ?>
      <div class="modal fade" id="editCardModal<?= $card['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit Program Card: <?= esc($card['badge'] ?? 'Program') ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
              <div class="modal-body">
                <input type="hidden" name="action" value="edit_card">
                <input type="hidden" name="card_id" value="<?= (int)$card['id'] ?>">
                
                <!-- Basic Information -->
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">Badge (short) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="card[badge]" value="<?= esc($card['badge'] ?? '') ?>" placeholder="e.g., MIT, PhD" required>
                    <div class="form-text">Short identifier like MIT, PhD, MSCS, etc.</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="card[title]" value="<?= esc($card['title'] ?? '') ?>" placeholder="Master in Information Technology" required>
                    <div class="form-text">Full program title</div>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">Muted (optional)</label>
                    <input type="text" class="form-control" name="card[title_muted]" value="<?= esc($card['title_muted'] ?? '') ?>" placeholder="(with Research Track)">
                    <div class="form-text">Subtitle text in parentheses</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Slug (optional)</label>
                    <input type="text" class="form-control" name="card[slug]" value="<?= esc($card['slug'] ?? '') ?>" placeholder="mit">
                    <div class="form-text">If empty, auto-generated from title</div>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Summary</label>
                  <textarea class="form-control" name="card[summary]" rows="2" placeholder="Short blurb for the card"><?= esc($card['summary'] ?? '') ?></textarea>
                </div>

                <!-- Specializations/Tracks -->
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">Pillbox Title</label>
                    <input type="text" class="form-control" name="card[pillbox_title]" value="<?= esc($card['pillbox_title'] ?? '') ?>" placeholder="Program Emphases">
                    <div class="form-text">Title for the specializations section</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Pills (one per line)</label>
                    <textarea class="form-control" name="card[pills]" rows="4" placeholder="Advanced Computing&#10;IT Leadership&#10;Ethics & Responsibility"><?= esc($card['pills'] ?? '') ?></textarea>
                    <div class="form-text">Each line becomes a pill/tag</div>
                  </div>
                </div>

                <!-- URLs and Links -->
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">Learn More URL</label>
                    <input type="url" class="form-control" name="card[learn_more_url]" value="<?= esc($card['learn_more_url'] ?? '') ?>" placeholder="https://...">
                  </div>
                  <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="card[learn_more_external]" value="1" <?= !empty($card['learn_more_external']) ? 'checked' : '' ?>>
                      <label class="form-check-label">Open in new tab</label>
                    </div>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">Curriculum URL</label>
                    <input type="url" class="form-control" name="card[curriculum_url]" value="<?= esc($card['curriculum_url'] ?? '') ?>" placeholder="https://...">
                  </div>
                  <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="card[curriculum_external]" value="1" <?= !empty($card['curriculum_external']) ? 'checked' : '' ?>>
                      <label class="form-check-label">Open in new tab</label>
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Apply URL</label>
                  <input type="url" class="form-control" name="card[apply_url]" value="<?= esc($card['apply_url'] ?? '') ?>" placeholder="/adamson-ccit/public/index.php?page=admission_graduate_school">
                </div>

                <!-- Settings -->
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">Position</label>
                    <input type="number" class="form-control" name="card[position]" value="<?= esc($card['position'] ?? 1) ?>" min="1">
                    <div class="form-text">Display order (lower numbers first)</div>
                  </div>
                  <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="card[is_active]" value="1" <?= !empty($card['is_active']) ? 'checked' : '' ?>>
                      <label class="form-check-label">Active (visible on public site)</label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save"></i> Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('gradForm');
  const saveStatus = document.getElementById('saveStatus');
  let dirty = false;

  // Track changes
  form?.addEventListener('input', () => {
    dirty = true;
    saveStatus.textContent = 'Unsaved changes';
  });

  form?.addEventListener('submit', () => {
    dirty = false;
    saveStatus.textContent = 'Saving...';
  });

  // Prevent accidental navigation
  window.addEventListener('beforeunload', (e) => { if(dirty){ e.preventDefault(); e.returnValue = ''; } });

  document.getElementById('discardBtn')?.addEventListener('click', () => {
    if(!dirty || confirm('Discard all unsaved changes?')) location.reload();
  });

  window.addEventListener('keydown', (e) => {
    if((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's'){
      e.preventDefault();
      document.querySelector('.savebar [type="submit"]')?.click();
    }
  });

  const notices = document.querySelectorAll('.alert');
  if (notices.length) setTimeout(() => { notices.forEach(n => n.style.display='none'); }, 4000);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

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
      <h1 class="admin-cms-section__title">Graduate Programs Management</h1>
      
      <!-- Notifications -->
      <?php if ($notice): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fas fa-check-circle me-2"></i><?= esc($notice) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      
      <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fas fa-exclamation-circle me-2"></i><?= esc($error_message) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Quick Stats Card -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="row text-center">
            <div class="col-md-3">
              <div class="h3 text-primary mb-0"><?= count($cards) ?></div>
              <small class="text-muted">Total Programs</small>
            </div>
            <div class="col-md-3">
              <div class="h3 text-success mb-0"><?= count(array_filter($cards, fn($c) => !empty($c['is_active']))) ?></div>
              <small class="text-muted">Active Programs</small>
            </div>
            <div class="col-md-3">
              <div class="h3 text-warning mb-0"><?= count(array_filter($cards, fn($c) => empty($c['is_active']))) ?></div>
              <small class="text-muted">Inactive Programs</small>
            </div>
            <div class="col-md-3">
              <div class="h3 text-info mb-0"><?= count(array_filter($cards, fn($c) => !empty($c['summary']))) ?></div>
              <small class="text-muted">With Summaries</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Programs Table -->
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">
            <i class="fas fa-graduation-cap me-2"></i>Program Cards Management
          </h5>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editCardModal" 
                  onclick="openCreateCardModal()">
            <i class="fas fa-plus me-2"></i>Add New Program
          </button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 60px;">#</th>
                  <th style="width: 80px;">Status</th>
                  <th style="width: 80px;">Position</th>
                  <th>Program Details</th>
                  <th style="width: 150px;">Actions</th>
                  <th style="width: 160px;">Links</th>
                  <th style="width: 150px;">Last Updated</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($cards)): ?>
                  <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                      <i class="fas fa-graduation-cap fa-2x mb-3"></i>
                      <div>No graduate programs configured yet.</div>
                      <button type="button" class="btn btn-outline-primary mt-2" data-bs-toggle="modal" 
                              data-bs-target="#editCardModal" onclick="openCreateCardModal()">
                        Add First Program
                      </button>
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($cards as $index => $card): ?>
                    <tr>
                      <td class="text-muted"><?= $card['id'] ?></td>
                      <td>
                        <?php if (!empty($card['is_active'])): ?>
                          <span class="badge bg-success"><i class="fas fa-check me-1"></i>Active</span>
                        <?php else: ?>
                          <span class="badge bg-secondary"><i class="fas fa-pause me-1"></i>Inactive</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span class="badge bg-info"><?= $card['position'] ?? $card['id'] ?></span>
                      </td>
                      <td>
                        <div class="d-flex align-items-start">
                          <div class="flex-grow-1">
                            <div class="fw-bold text-primary">
                              <?= esc($card['title'] ?: 'Untitled Program') ?>
                              <?php if (!empty($card['badge'])): ?>
                                <span class="badge bg-light text-dark ms-2"><?= esc($card['badge']) ?></span>
                              <?php endif; ?>
                            </div>
                            <?php if (!empty($card['title_muted'])): ?>
                              <div class="text-muted small"><?= esc($card['title_muted']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($card['summary'])): ?>
                              <div class="text-secondary small mt-1">
                                <?= esc(substr($card['summary'], 0, 120)) ?><?= strlen($card['summary']) > 120 ? '...' : '' ?>
                              </div>
                            <?php endif; ?>
                            <?php if (!empty($card['pillbox_title']) || !empty($card['pills'])): ?>
                              <div class="mt-2">
                                <?php if (!empty($card['pillbox_title'])): ?>
                                  <small class="text-info"><strong><?= esc($card['pillbox_title']) ?>:</strong></small>
                                <?php endif; ?>
                                <?php if (!empty($card['pills'])): ?>
                                  <?php 
                                  $pills = is_array($card['pills']) ? $card['pills'] : explode("\n", $card['pills']);
                                  $pills = array_filter(array_map('trim', $pills));
                                  foreach (array_slice($pills, 0, 3) as $pill): ?>
                                    <span class="badge bg-light text-dark me-1"><?= esc($pill) ?></span>
                                  <?php endforeach; ?>
                                  <?php if (count($pills) > 3): ?>
                                    <span class="badge bg-secondary">+<?= count($pills) - 3 ?> more</span>
                                  <?php endif; ?>
                                <?php endif; ?>
                              </div>
                            <?php endif; ?>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="btn-group-vertical gap-1" role="group">
                          <button type="button" class="btn btn-outline-primary btn-sm" 
                                  data-bs-toggle="modal" data-bs-target="#editCardModal" 
                                  onclick="openEditCardModal(<?= htmlspecialchars(json_encode($card), ENT_QUOTES, 'UTF-8') ?>)">
                            <i class="fas fa-edit me-1"></i>Edit
                          </button>
                          <button type="button" class="btn btn-outline-danger btn-sm" 
                                  onclick="deleteCard(<?= $card['id'] ?>, '<?= esc($card['title']) ?>')">
                            <i class="fas fa-trash me-1"></i>Delete
                          </button>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex flex-column gap-1">
                          <?php if (!empty($card['learn_more_url'])): ?>
                            <a href="<?= esc($card['learn_more_url']) ?>" target="_blank" class="btn btn-outline-info btn-sm">
                              <i class="fas fa-info-circle me-1"></i>Learn More
                            </a>
                          <?php endif; ?>
                          <?php if (!empty($card['curriculum_url'])): ?>
                            <a href="<?= esc($card['curriculum_url']) ?>" target="_blank" class="btn btn-outline-success btn-sm">
                              <i class="fas fa-book me-1"></i>Curriculum
                            </a>
                          <?php endif; ?>
                          <?php if (!empty($card['apply_url'])): ?>
                            <a href="<?= esc($card['apply_url']) ?>" target="_blank" class="btn btn-outline-warning btn-sm">
                              <i class="fas fa-paper-plane me-1"></i>Apply
                            </a>
                          <?php endif; ?>
                        </div>
                      </td>
                      <td class="text-muted small">
                        <?php if (!empty($card['updated_at'])): ?>
                          <?= date('M j, Y', strtotime($card['updated_at'])) ?><br>
                          <?= date('g:i A', strtotime($card['updated_at'])) ?>
                        <?php else: ?>
                          —
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Page Settings -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="fas fa-cog me-2"></i>Page Settings
            </h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#pageSettingsCollapse" aria-expanded="false" aria-controls="pageSettingsCollapse">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="pageSettingsCollapse">
          <form method="post">
            <input type="hidden" name="action" value="save_settings">
            <div class="card-body">
              <!-- Subhero Section -->
              <h6 class="text-primary mb-3"><i class="fas fa-image me-2"></i>Hero Section</h6>
              <div class="row mb-4">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Hero Image URL</label>
                  <input type="text" class="form-control" name="gs[subhero_image_url]" 
                         value="<?= esc($gs['subhero_image_url'] ?? '/adamson-ccit/public/assets/images/programs/graduate.jpg') ?>">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Hero Lead Text</label>
                  <input type="text" class="form-control" name="gs[subhero_lead]" 
                         value="<?= esc($gs['subhero_lead'] ?? '') ?>" 
                         placeholder="e.g., Advance your career with our graduate programs">
                </div>
              </div>

              <!-- Call to Action Section -->
              <h6 class="text-primary mb-3"><i class="fas fa-bullhorn me-2"></i>Call to Action</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">CTA Title</label>
                  <input type="text" class="form-control" name="gs[cta_title]" 
                         value="<?= esc($gs['cta_title'] ?? '') ?>" 
                         placeholder="e.g., Ready to take the next step?">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">CTA Description</label>
                  <input type="text" class="form-control" name="gs[cta_description]" 
                         value="<?= esc($gs['cta_description'] ?? '') ?>" 
                         placeholder="e.g., Apply now for our graduate programs">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">CTA Button URL</label>
                  <input type="text" class="form-control" name="gs[cta_action_url]" 
                         value="<?= esc($gs['cta_action_url'] ?? '') ?>" 
                         placeholder="e.g., /adamson-ccit/public/index.php?page=admission_graduate">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">CTA Button Text</label>
                  <input type="text" class="form-control" name="gs[cta_action_label]" 
                         value="<?= esc($gs['cta_action_label'] ?? '') ?>" 
                         placeholder="e.g., Apply Now">
                </div>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Page Settings
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="page-end-spacer" style="height:160px" aria-hidden="true"></div>

    </section>
  </main>
</div>

<!-- Edit/Create Card Modal -->
<div class="modal fade" id="editCardModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" id="cardForm">
        <div class="modal-header">
          <h5 class="modal-title" id="cardModalTitle">
            <i class="fas fa-edit me-2"></i>Edit Program
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" id="cardAction" value="update_card">
          <input type="hidden" name="card_id" id="cardId" value="">
          
          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <div class="form-check form-switch">
                <input type="hidden" name="card[is_active]" value="0">
                <input type="checkbox" name="card[is_active]" value="1" class="form-check-input" id="cardActive">
                <label class="form-check-label" for="cardActive">Program is active</label>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Position/Order</label>
              <input type="number" class="form-control" name="card[position]" id="cardPosition" min="1" placeholder="1">
            </div>
            <div class="col-md-4">
              <label class="form-label">Badge/Degree Type</label>
              <input type="text" class="form-control" name="card[badge]" id="cardBadge" placeholder="e.g., MS, PhD">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-8">
              <label class="form-label">Program Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="card[title]" id="cardTitle" required 
                     placeholder="e.g., Master of Science in Computer Science">
            </div>
            <div class="col-md-4">
              <label class="form-label">Title Suffix <small class="text-muted">(muted)</small></label>
              <input type="text" class="form-control" name="card[title_muted]" id="cardTitleMuted" 
                     placeholder="e.g., with Research Track">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Program Summary</label>
            <textarea class="form-control" name="card[summary]" id="cardSummary" rows="3" 
                      placeholder="Brief description of the program, its focus, and what students will learn..."></textarea>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Specializations Title</label>
              <input type="text" class="form-control" name="card[pillbox_title]" id="cardPillboxTitle" 
                     placeholder="e.g., Specializations, Tracks, Focus Areas">
            </div>
            <div class="col-md-6">
              <label class="form-label">Specializations <small class="text-muted">(one per line)</small></label>
              <textarea class="form-control" name="card[pills]" id="cardPills" rows="3" 
                        placeholder="Artificial Intelligence&#10;Data Science&#10;Cybersecurity"></textarea>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Learn More URL</label>
              <input type="url" class="form-control" name="card[learn_more_url]" id="cardLearnMoreUrl" 
                     placeholder="https://example.com/program-details">
              <div class="form-check mt-2">
                <input type="hidden" name="card[learn_more_external]" value="0">
                <input type="checkbox" name="card[learn_more_external]" value="1" class="form-check-input" id="cardLearnMoreExternal">
                <label class="form-check-label" for="cardLearnMoreExternal">Open in new tab</label>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Curriculum URL</label>
              <input type="url" class="form-control" name="card[curriculum_url]" id="cardCurriculumUrl" 
                     placeholder="https://example.com/curriculum">
              <div class="form-check mt-2">
                <input type="hidden" name="card[curriculum_external]" value="0">
                <input type="checkbox" name="card[curriculum_external]" value="1" class="form-check-input" id="cardCurriculumExternal">
                <label class="form-check-label" for="cardCurriculumExternal">Open in new tab</label>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Application URL</label>
            <input type="url" class="form-control" name="card[apply_url]" id="cardApplyUrl" 
                   placeholder="/adamson-ccit/public/index.php?page=admission_graduate_school">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="cardSubmitBtn">
            <i class="fas fa-save me-2"></i>Save Program
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCardModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-exclamation-triangle text-warning me-2"></i>Confirm Deletion
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this program?</p>
        <div class="alert alert-warning">
          <strong id="deleteCardName">Program Name</strong><br>
          <small>This action cannot be undone.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="post" class="d-inline">
          <input type="hidden" name="action" value="delete_card">
          <input type="hidden" name="card_id" id="deleteCardId" value="">
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash me-2"></i>Delete Program
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Modal management functions
function openCreateCardModal() {
    document.getElementById('cardModalTitle').innerHTML = '<i class="fas fa-plus me-2"></i>Add New Program';
    document.getElementById('cardAction').value = 'create_card';
    document.getElementById('cardId').value = '';
    document.getElementById('cardSubmitBtn').innerHTML = '<i class="fas fa-plus me-2"></i>Create Program';
    
    // Clear all form fields
    document.getElementById('cardForm').reset();
    document.getElementById('cardActive').checked = true;
    document.getElementById('cardPosition').value = <?= count($cards) + 1 ?>;
}

function openEditCardModal(card) {
    document.getElementById('cardModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Program';
    document.getElementById('cardAction').value = 'update_card';
    document.getElementById('cardId').value = card.id;
    document.getElementById('cardSubmitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Update Program';
    
    // Populate form fields
    document.getElementById('cardActive').checked = Boolean(card.is_active);
    document.getElementById('cardPosition').value = card.position || card.id;
    document.getElementById('cardBadge').value = card.badge || '';
    document.getElementById('cardTitle').value = card.title || '';
    document.getElementById('cardTitleMuted').value = card.title_muted || '';
    document.getElementById('cardSummary').value = card.summary || card.description || '';
    document.getElementById('cardPillboxTitle').value = card.pillbox_title || '';
    document.getElementById('cardPills').value = card.pills || '';
    document.getElementById('cardLearnMoreUrl').value = card.learn_more_url || '';
    document.getElementById('cardLearnMoreExternal').checked = Boolean(card.learn_more_external);
    document.getElementById('cardCurriculumUrl').value = card.curriculum_url || '';
    document.getElementById('cardCurriculumExternal').checked = Boolean(card.curriculum_external);
    document.getElementById('cardApplyUrl').value = card.apply_url || '';
}

function deleteCard(cardId, cardName) {
    document.getElementById('deleteCardId').value = cardId;
    document.getElementById('deleteCardName').textContent = cardName;
    new bootstrap.Modal(document.getElementById('deleteCardModal')).show();
}

// Auto-dismiss alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert && alert.parentNode) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
                        <input type="hidden" name="gs[<?= $p ?>learn_more_external]" value="0">
                        <div class="form-check mt-2">
                          <input type="checkbox" name="gs[<?= $p ?>learn_more_external]" value="1" class="form-check-input" id="learn_ext_<?= $i ?>" <?= !empty($gs[$p.'learn_more_external'])?'checked':''; ?>>
                          <label class="form-check-label" for="learn_ext_<?= $i ?>">Open in new tab</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Curriculum URL</label>
                        <input type="text" class="form-control" name="gs[<?= $p ?>curriculum_url]" value="<?= esc($gs[$p.'curriculum_url'] ?? '') ?>">
                        <input type="hidden" name="gs[<?= $p ?>curriculum_external]" value="0">
                        <div class="form-check mt-2">
                          <input type="checkbox" name="gs[<?= $p ?>curriculum_external]" value="1" class="form-check-input" id="curr_ext_<?= $i ?>" <?= !empty($gs[$p.'curriculum_external'])?'checked':''; ?>>
                          <label class="form-check-label" for="curr_ext_<?= $i ?>">Open in new tab</label>
                        </div>
                      </div>
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Apply URL</label>
                      <input type="text" class="form-control" name="gs[<?= $p ?>apply_url]" value="<?= esc($gs[$p.'apply_url'] ?? '/adamson-ccit/public/index.php?page=admission_graduate_school') ?>">
                    </div>
                  </div>
                </div>
              <?php endfor; ?>
            </div>
          </div>
        </div>

        <!-- Call to Action Section -->
        <div class="card mb-4">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Call to Action</h5>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                      data-bs-target="#ctaCollapse" aria-expanded="false" aria-controls="ctaCollapse">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </div>
          
          <div class="collapse" id="ctaCollapse">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Title</label>
                  <input type="text" class="form-control" name="gs[cta_title]" value="<?= esc($gs['cta_title'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Description</label>
                  <input type="text" class="form-control" name="gs[cta_description]" value="<?= esc($gs['cta_description'] ?? '') ?>">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Action URL</label>
                  <input type="text" class="form-control" name="gs[cta_action_url]" value="<?= esc($gs['cta_action_url'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Action Label</label>
                  <input type="text" class="form-control" name="gs[cta_action_label]" value="<?= esc($gs['cta_action_label'] ?? '') ?>">
                </div>
              </div>
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
  let dirty = false;

  // Track changes
  form?.addEventListener('input', () => {
    dirty = true;
    saveStatus.textContent = 'Unsaved changes';
  });

  form?.addEventListener('submit', () => {
    dirty = false;
    saveStatus.textContent = 'Saving...';
  });

  // Prevent accidental navigation
  window.addEventListener('beforeunload', (e) => { if(dirty){ e.preventDefault(); e.returnValue = ''; } });

  document.getElementById('discardBtn')?.addEventListener('click', () => {
    if(!dirty || confirm('Discard all unsaved changes?')) location.reload();
  });

  window.addEventListener('keydown', (e) => {
    if((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's'){
      e.preventDefault();
      document.querySelector('.savebar [type="submit"]')?.click();
    }
  });

  const notices = document.querySelectorAll('.alert');
  if (notices.length) setTimeout(() => { notices.forEach(n => n.style.display='none'); }, 4000);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>