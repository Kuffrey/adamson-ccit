<?php
// view-only: controller provides $quickActions, $success, $error
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
  <title>Manage Quick Actions | CCIT CMS</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
  <style>
    .quick-actions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1rem;
      margin: 1rem 0;
    }
    .quick-action-card {
      background: white;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      padding: 1rem;
      position: relative;
    }
    .quick-action-card.inactive {
      opacity: 0.5;
      background: #f5f5f5;
    }
    .quick-action-header {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.5rem;
    }
    .quick-action-icon {
      flex-shrink: 0;
    }
    .quick-action-label {
      font-weight: 600;
      color: #333;
    }
    .quick-action-url {
      font-size: 0.85rem;
      color: #666;
      word-break: break-all;
    }
    .quick-action-controls {
      display: flex;
      gap: 0.5rem;
      margin-top: 1rem;
      flex-wrap: wrap;
    }
    .btn-small {
      padding: 0.25rem 0.5rem;
      font-size: 0.75rem;
      border-radius: 4px;
      border: 1px solid;
      background: none;
      cursor: pointer;
    }
    .btn-edit { border-color: #007bff; color: #007bff; }
    .btn-edit:hover { background: #007bff; color: white; }
    .btn-toggle { border-color: #28a745; color: #28a745; }
    .btn-toggle:hover { background: #28a745; color: white; }
    .btn-toggle.inactive { border-color: #ffc107; color: #856404; }
    .btn-toggle.inactive:hover { background: #ffc107; color: #856404; }
    .btn-delete { border-color: #dc3545; color: #dc3545; }
    .btn-delete:hover { background: #dc3545; color: white; }
    .form-modal {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.5);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 1000;
    }
    .form-modal.show { display: flex; }
    .modal-content {
      background: white;
      padding: 2rem;
      border-radius: 8px;
      width: 90%;
      max-width: 500px;
      max-height: 80vh;
      overflow-y: auto;
    }
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      margin-bottom: 1rem;
    }
    .icon-preview {
      display: inline-block;
      margin-left: 0.5rem;
      vertical-align: middle;
    }
    .sortable-hint {
      font-size: 0.85rem;
      color: #666;
      margin-bottom: 1rem;
      padding: 0.5rem;
      background: #f8f9fa;
      border-radius: 4px;
    }
  </style>
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/admin/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Manage Quick Actions</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username); ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Quick Actions Management</h1>
      <p class="intro">Manage the quick action cards displayed on the homepage. You can add, edit, reorder, and toggle their visibility.</p>

      <?php if ($success): ?>
        <p class="notice success"><?= esc($success) ?></p>
      <?php elseif ($error): ?>
        <p class="notice error"><?= esc($error) ?></p>
      <?php endif; ?>

      <div class="cms-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
          <h2 class="cms-card-legend">Current Quick Actions</h2>
          <button type="button" class="btn--solid" onclick="openModal('create')">Add New Quick Action</button>
        </div>
        
        <div class="sortable-hint">
          💡 <strong>Tip:</strong> The cards are displayed in order by "Display Order". Lower numbers appear first. You can drag and drop to reorder them.
        </div>

        <div class="quick-actions-grid" id="quickActionsGrid">
          <?php foreach ($quickActions as $action): ?>
            <div class="quick-action-card <?= $action['is_active'] ? '' : 'inactive' ?>" data-id="<?= $action['id'] ?>">
              <div class="quick-action-header">
                <span class="quick-action-icon"><?= $action['icon'] ?></span>
                <span class="quick-action-label"><?= esc($action['label']) ?></span>
                <span style="margin-left: auto; font-size: 0.75rem; color: #999;">
                  Order: <?= $action['display_order'] ?>
                </span>
              </div>
              <div class="quick-action-url"><?= esc($action['url']) ?></div>
              <div class="quick-action-controls">
                <button type="button" class="btn-small btn-edit" 
                        onclick="editQuickAction(<?= htmlspecialchars(json_encode($action), ENT_QUOTES) ?>)">
                  Edit
                </button>
                <button type="button" class="btn-small btn-toggle <?= $action['is_active'] ? '' : 'inactive' ?>" 
                        onclick="toggleQuickAction(<?= $action['id'] ?>)">
                  <?= $action['is_active'] ? 'Deactivate' : 'Activate' ?>
                </button>
                <button type="button" class="btn-small btn-delete" 
                        onclick="deleteQuickAction(<?= $action['id'] ?>, '<?= esc($action['label']) ?>')">
                  Delete
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  </main>
</div>

<!-- Modal for Create/Edit -->
<div class="form-modal" id="quickActionModal">
  <div class="modal-content">
    <h3 id="modalTitle">Add Quick Action</h3>
    <form id="quickActionForm" method="post" action="?page=admin_manage_quick_actions&action=save">
      <input type="hidden" name="action" id="formAction" value="create">
      <input type="hidden" name="id" id="formId" value="">
      
      <div class="form-row">
        <div class="field">
          <label>Label <span style="color: red;">*</span></label>
          <input type="text" name="label" id="formLabel" required>
        </div>
        <div class="field">
          <label>Display Order</label>
          <input type="number" name="display_order" id="formDisplayOrder" min="0" value="0">
        </div>
      </div>
      
      <div class="field">
        <label>URL <span style="color: red;">*</span></label>
        <input type="text" name="url" id="formUrl" required>
        <small class="help">Full URL path, e.g., /adamson-ccit/public/index.php?page=programs</small>
      </div>
      
      <div class="field">
        <label>
          SVG Icon Code
          <span class="icon-preview" id="iconPreview"></span>
        </label>
        <textarea name="icon" id="formIcon" rows="3" placeholder="<svg viewBox=&quot;0 0 24 24&quot; width=&quot;22&quot; height=&quot;22&quot;><path fill=&quot;currentColor&quot; d=&quot;...&quot;/></svg>"></textarea>
        <small class="help">Paste SVG code here. Icon will update as you type.</small>
      </div>
      
      <div class="field">
        <label>
          <input type="checkbox" name="is_active" id="formIsActive" value="1" checked>
          Active (visible on homepage)
        </label>
      </div>
      
      <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
        <button type="submit" class="btn--solid">Save Quick Action</button>
        <button type="button" class="btn--ghost" onclick="closeModal()">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Hidden forms for actions -->
<form id="toggleForm" method="post" action="?page=admin_manage_quick_actions&action=save" style="display: none;">
  <input type="hidden" name="action" value="toggle">
  <input type="hidden" name="id" id="toggleId">
</form>

<form id="deleteForm" method="post" action="?page=admin_manage_quick_actions&action=save" style="display: none;">
  <input type="hidden" name="action" value="delete">
  <input type="hidden" name="id" id="deleteId">
</form>

<script>
// Modal management
function openModal(action, data = null) {
  const modal = document.getElementById('quickActionModal');
  const form = document.getElementById('quickActionForm');
  const title = document.getElementById('modalTitle');
  
  if (action === 'create') {
    title.textContent = 'Add Quick Action';
    document.getElementById('formAction').value = 'create';
    form.reset();
    document.getElementById('formIsActive').checked = true;
  } else if (action === 'edit' && data) {
    title.textContent = 'Edit Quick Action';
    document.getElementById('formAction').value = 'update';
    document.getElementById('formId').value = data.id;
    document.getElementById('formLabel').value = data.label;
    document.getElementById('formIcon').value = data.icon;
    document.getElementById('formUrl').value = data.url;
    document.getElementById('formDisplayOrder').value = data.display_order;
    document.getElementById('formIsActive').checked = data.is_active == 1;
    updateIconPreview();
  }
  
  modal.classList.add('show');
}

function closeModal() {
  document.getElementById('quickActionModal').classList.remove('show');
}

function editQuickAction(data) {
  openModal('edit', data);
}

function toggleQuickAction(id) {
  document.getElementById('toggleId').value = id;
  document.getElementById('toggleForm').submit();
}

function deleteQuickAction(id, label) {
  if (confirm(`Are you sure you want to delete "${label}"?`)) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteForm').submit();
  }
}

// Icon preview
document.getElementById('formIcon').addEventListener('input', updateIconPreview);

function updateIconPreview() {
  const iconCode = document.getElementById('formIcon').value;
  const preview = document.getElementById('iconPreview');
  preview.innerHTML = iconCode;
}

// Close modal when clicking outside
document.getElementById('quickActionModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeModal();
  }
});
</script>

</body>
</html>