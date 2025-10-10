<?php
// admin_manage_events.php — CMS for managing Events with consistent layout
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../models/Event.php';

function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$user = $_SESSION['user'] ?? [];
$username = $user['username'] ?? 'Admin';
$notice = '';

// Get current data
try {
  $status = $_GET['status'] ?? 'all';
  $events = Event::list($status);
  $counts = Event::statusCounts();
} catch (Exception $e) {
  $notice = 'Error loading data: ' . $e->getMessage();
  $events = [];
  $counts = ['all' => 0, 'draft' => 0, 'published' => 0, 'archived' => 0];
}

// Helper function for dates
function dtfix(?string $v): ?string {
  $v = trim((string)$v);
  if ($v === '') return null;
  $v = str_replace('T', ' ', $v);
  if (strlen($v) === 16) $v .= ':00';
  return $v;
}

/* ---------- Handle POST (CRUD) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // Add new event
    if (!empty($_POST['add_event']) && !empty($_POST['title'])) {
      $title = trim($_POST['title']);
      $description = trim($_POST['description'] ?? '');
      $category = $_POST['category'] ?? 'career';
      $location = trim($_POST['location'] ?? '');
      $registration = trim($_POST['registration_url'] ?? '');
      $start = dtfix($_POST['start_at'] ?? null);
      $end = dtfix($_POST['end_at'] ?? null);
      $nstatus = $_POST['status'] ?? 'draft';
      $imageUrl = null;

      // Ensure end >= start
      if ($start && $end && strtotime($end) < strtotime($start)) $end = $start;

      // Handle image upload
      if (!empty($_FILES['image']['tmp_name'])) {
        $imgTmp = $_FILES['image']['tmp_name'];
        $imgName = time() . '-' . basename($_FILES['image']['name']);
        $destDir = __DIR__ . '/../../public/uploads/events/';
        if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
        $dest = $destDir . $imgName;
        if (move_uploaded_file($imgTmp, $dest)) {
          $imageUrl = '/adamson-ccit/public/uploads/events/' . $imgName;
        }
      }

      $data = [
        'title' => $title,
        'description' => $description,
        'location' => $location,
        'category' => $category,
        'image_url' => $imageUrl,
        'start_at' => $start,
        'end_at' => $end,
        'registration_url' => $registration ?: null,
        'status' => $nstatus,
      ];

      $eid = Event::create($data);
      $notice = $eid ? 'Event added successfully!' : 'Failed to add event.';
    }

    // Update event status
    if (!empty($_POST['update_status']) && !empty($_POST['id'])) {
      Event::updateStatus((int)$_POST['id'], $_POST['update_status']);
      $notice = 'Status updated successfully!';
    }

    // Delete event
    if (!empty($_POST['delete_event'])) {
      Event::delete((int)$_POST['delete_event']);
      $notice = 'Event deleted successfully!';
    }

    // Edit event
    if (!empty($_POST['edit_event']) && !empty($_POST['id'])) {
      $eventId = (int)$_POST['id'];
      $imageUrl = null;
      $currentImageUrl = trim($_POST['current_image_url'] ?? '');
      
      // Handle image upload or removal
      if (!empty($_POST['remove_image'])) {
        // User wants to remove the current image
        $imageUrl = null;
        // Optionally delete the physical file
        if ($currentImageUrl && file_exists(__DIR__ . '/../../public' . str_replace('/adamson-ccit/public', '', $currentImageUrl))) {
          @unlink(__DIR__ . '/../../public' . str_replace('/adamson-ccit/public', '', $currentImageUrl));
        }
      } elseif (!empty($_FILES['image']['tmp_name'])) {
        // User uploaded a new image
        $imgTmp = $_FILES['image']['tmp_name'];
        $imgName = time() . '-' . basename($_FILES['image']['name']);
        $destDir = __DIR__ . '/../../public/uploads/events/';
        if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
        $dest = $destDir . $imgName;
        if (move_uploaded_file($imgTmp, $dest)) {
          $imageUrl = '/adamson-ccit/public/uploads/events/' . $imgName;
          // Remove old image file if it exists
          if ($currentImageUrl && file_exists(__DIR__ . '/../../public' . str_replace('/adamson-ccit/public', '', $currentImageUrl))) {
            @unlink(__DIR__ . '/../../public' . str_replace('/adamson-ccit/public', '', $currentImageUrl));
          }
        } else {
          $imageUrl = $currentImageUrl; // Keep current image if upload fails
        }
      } else {
        // No new image uploaded and not removing, keep current image
        $imageUrl = $currentImageUrl ?: null;
      }
      
      $data = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'location' => trim($_POST['location'] ?? ''),
        'category' => $_POST['category'] ?? 'career',
        'image_url' => $imageUrl,
        'start_at' => dtfix($_POST['start_at'] ?? null),
        'end_at' => dtfix($_POST['end_at'] ?? null),
        'registration_url' => trim($_POST['registration_url'] ?? '') ?: null,
        'status' => $_POST['edit_status'] ?? 'draft'
      ];
      Event::update($eventId, $data);
      $notice = 'Event updated successfully!';
    }

    // Redirect to prevent double submission
    if ($notice) {
      header('Location: ?page=admin_manage_events&status=' . urlencode($status) . '&success=' . urlencode($notice));
      exit;
    }
  } catch (Exception $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

// Handle success message from redirect
if (!empty($_GET['success'])) {
  $notice = $_GET['success'];
}

// Refresh data after operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $events = Event::list($status);
  $counts = Event::statusCounts();
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
      <span class="admin-topbar__title">Events → Management</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h1 class="admin-cms-section__title mb-1">Event Management</h1>
          <p class="text-muted mb-0">Manage upcoming events, schedules, and registrations</p>
        </div>
        <div class="d-flex gap-2">
          <div class="badge bg-primary">Total: <?= $counts['all'] ?></div>
          <div class="badge bg-success">Published: <?= $counts['published'] ?></div>
          <div class="badge bg-warning text-dark">Drafts: <?= $counts['draft'] ?></div>
        </div>
      </div>
      
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
              Manage <strong>events and their visibility</strong>. Control event schedules and registration details.
            </div>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr><th>Field</th><th>Value</th></tr>
                </thead>
                <tbody>
                  <tr><td>Total Events</td><td><?= $counts['all'] ?></td></tr>
                  <tr><td>Published</td><td><?= $counts['published'] ?></td></tr>
                  <tr><td>Drafts</td><td><?= $counts['draft'] ?></td></tr>
                  <tr><td>Archived</td><td><?= $counts['archived'] ?></td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Management Form -->
      <form id="eventsForm" method="post" enctype="multipart/form-data" autocomplete="off">

        <!-- Add New Event -->
        <div class="card mb-4">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0"><i class="fas fa-plus me-2"></i>Add New Event</h5>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                      data-bs-target="#addEventCollapse" aria-expanded="false" aria-controls="addEventCollapse">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </div>
          
          <div class="collapse" id="addEventCollapse">
            <div class="card-body">
              <input type="hidden" name="add_event" value="1">
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label class="form-label">Title</label>
                  <input type="text" name="title" class="form-control" placeholder="Event title" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Status</label>
                  <select name="status" class="form-select">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Category</label>
                  <select name="category" class="form-select">
                    <option value="career">Career</option>
                    <option value="forum">Forum</option>
                    <option value="workshop">Workshop</option>
                    <option value="competition">Competition</option>
                    <option value="community">Community</option>
                  </select>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Location</label>
                  <input type="text" name="location" class="form-control" placeholder="e.g. Ozanam Bldg, Rm 405">
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Featured Image</label>
                  <input type="file" name="image" class="form-control" accept="image/*">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Start Date & Time</label>
                  <input type="datetime-local" name="start_at" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">End Date & Time</label>
                  <input type="datetime-local" name="end_at" class="form-control">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Registration URL (optional)</label>
                <input type="url" name="registration_url" class="form-control" placeholder="https://...">
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Event description"></textarea>
              </div>
            </div>
          </div>
        </div>
      </form>

      <!-- Events List with Status Filter -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-table me-2"></i>Events</h5>
            <div class="d-flex align-items-center">
              <div class="btn-group me-3" role="group" aria-label="Status filter">
                <a href="?page=admin_manage_events&status=all" class="btn btn-sm <?= $status === 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">
                  <i class="fas fa-list me-1"></i>All <span class="badge bg-light text-dark ms-1"><?= $counts['all'] ?></span>
                </a>
                <a href="?page=admin_manage_events&status=published" class="btn btn-sm <?= $status === 'published' ? 'btn-success' : 'btn-outline-success' ?>">
                  <i class="fas fa-check-circle me-1"></i>Published <span class="badge bg-light text-dark ms-1"><?= $counts['published'] ?></span>
                </a>
                <a href="?page=admin_manage_events&status=draft" class="btn btn-sm <?= $status === 'draft' ? 'btn-warning' : 'btn-outline-warning' ?>">
                  <i class="fas fa-edit me-1"></i>Drafts <span class="badge bg-light text-dark ms-1"><?= $counts['draft'] ?></span>
                </a>
                <a href="?page=admin_manage_events&status=archived" class="btn btn-sm <?= $status === 'archived' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
                  <i class="fas fa-archive me-1"></i>Archived <span class="badge bg-light text-dark ms-1"><?= $counts['archived'] ?></span>
                </a>
              </div>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                      data-bs-target="#eventsTableCollapse" aria-expanded="true" aria-controls="eventsTableCollapse">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </div>
        </div>
        
        <div class="collapse show" id="eventsTableCollapse">
          <div class="card-body">
            <?php if (empty($events)): ?>
              <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>No events found for status: <?= esc($status) ?>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Status</th>
                      <th>Event Details</th>
                      <th>Schedule</th>
                      <th>Location</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($events as $ev): ?>
                      <tr>
                        <td><?= esc($ev['id']) ?></td>
                        <td>
                          <span class="badge bg-<?= $ev['status'] === 'published' ? 'success' : ($ev['status'] === 'draft' ? 'warning' : 'secondary') ?>">
                            <?= esc(ucfirst($ev['status'])) ?>
                          </span>
                        </td>
                        <td>
                          <strong><?= esc($ev['title']) ?></strong>
                          <br><span class="badge bg-info"><?= esc($ev['category'] ?? 'career') ?></span>
                          <?php if (!empty($ev['description'])): ?>
                            <br><small class="text-muted"><?= esc(substr(strip_tags($ev['description']), 0, 60)) ?>...</small>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php 
                          $startText = !empty($ev['start_at']) ? date('M j, Y g:ia', strtotime($ev['start_at'])) : '—';
                          $endText = !empty($ev['end_at']) ? date('M j, Y g:ia', strtotime($ev['end_at'])) : '';
                          ?>
                          <small><?= esc($startText) ?></small>
                          <?php if ($endText): ?><br><small class="text-muted">to <?= esc($endText) ?></small><?php endif; ?>
                        </td>
                        <td><small><?= esc($ev['location'] ?? '—') ?></small></td>
                        <td>
                          <button class="btn btn-sm btn-warning me-1 edit-event-btn" 
                                  data-event-id="<?= $ev['id'] ?>" 
                                  title="Edit Event">
                            <i class="fas fa-edit"></i>
                          </button>
                          <div class="btn-group me-1">
                            <button class="btn btn-sm btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                              <i class="fas fa-exchange-alt"></i>
                            </button>
                            <ul class="dropdown-menu">
                              <?php foreach (['draft', 'published', 'archived'] as $st): ?>
                                <?php if ($st !== $ev['status']): ?>
                                  <li>
                                    <form method="post" class="d-inline">
                                      <input type="hidden" name="update_status" value="<?= $st ?>">
                                      <input type="hidden" name="id" value="<?= $ev['id'] ?>">
                                      <button type="submit" class="dropdown-item"><?= ucfirst($st) ?></button>
                                    </form>
                                  </li>
                                <?php endif; ?>
                              <?php endforeach; ?>
                            </ul>
                          </div>
                          <form method="post" class="d-inline">
                            <button type="submit" name="delete_event" value="<?= (int)$ev['id'] ?>" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Really delete this event?')" title="Delete Event">
                              <i class="fas fa-trash"></i>
                            </button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="page-end-spacer" style="height:160px" aria-hidden="true"></div>

      <div class="savebar">
        <div class="savebar__inner">
          <span class="savebar__status" id="saveStatus">All changes saved</span>
          <div class="savebar__actions">
            <button type="button" class="btn" id="discardBtn">Discard</button>
            <button type="submit" form="eventsForm" class="btn btn--primary">Save Changes</button>
            <a class="btn btn-outline-secondary" href="?page=admin_manage_news">News</a>
            <a class="btn btn-outline-secondary" href="?page=admin_manage_announcements">Announcements</a>
          </div>
        </div>
      </div>

    </section>
  </main>
</div>

<!-- Edit Event Modals -->
<?php foreach ($events as $ev): ?>
<div class="modal fade" id="editEventModal<?= $ev['id'] ?>" tabindex="-1" aria-labelledby="editEventModalLabel<?= $ev['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="edit_event" value="1">
        <input type="hidden" name="id" value="<?= $ev['id'] ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="editEventModalLabel<?= $ev['id'] ?>">Edit Event: <?= esc($ev['title']) ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8 mb-3">
              <label class="form-label">Title</label>
              <input type="text" name="title" class="form-control" value="<?= esc($ev['title']) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Status</label>
              <select name="edit_status" class="form-select">
                <option value="draft" <?= $ev['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= $ev['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="archived" <?= $ev['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Category</label>
              <select name="category" class="form-select">
                <option value="career" <?= ($ev['category'] ?? '') === 'career' ? 'selected' : '' ?>>Career</option>
                <option value="forum" <?= ($ev['category'] ?? '') === 'forum' ? 'selected' : '' ?>>Forum</option>
                <option value="workshop" <?= ($ev['category'] ?? '') === 'workshop' ? 'selected' : '' ?>>Workshop</option>
                <option value="competition" <?= ($ev['category'] ?? '') === 'competition' ? 'selected' : '' ?>>Competition</option>
                <option value="community" <?= ($ev['category'] ?? '') === 'community' ? 'selected' : '' ?>>Community</option>
              </select>
            </div>
            <div class="col-md-8 mb-3">
              <label class="form-label">Location</label>
              <input type="text" name="location" class="form-control" value="<?= esc($ev['location'] ?? '') ?>">
            </div>
          </div>
          
          <!-- Current Image Display and New Image Upload -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Current Image</label>
              <?php if (!empty($ev['image_url'])): ?>
                <div class="current-image-preview">
                  <img src="<?= esc($ev['image_url']) ?>" alt="Current event image" 
                       style="max-width: 100%; max-height: 150px; border-radius: 8px; border: 1px solid #ddd;">
                  <input type="hidden" name="current_image_url" value="<?= esc($ev['image_url']) ?>">
                </div>
              <?php else: ?>
                <p class="text-muted">No image uploaded</p>
              <?php endif; ?>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Upload New Image</label>
              <input type="file" name="image" class="form-control" accept="image/*">
              <div class="form-text">Leave empty to keep current image. Upload a new image to replace it.</div>
              
              <?php if (!empty($ev['image_url'])): ?>
                <div class="form-check mt-2">
                  <input type="checkbox" name="remove_image" value="1" class="form-check-input" id="removeImage<?= $ev['id'] ?>">
                  <label class="form-check-label text-danger" for="removeImage<?= $ev['id'] ?>">
                    Remove current image
                  </label>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Start Date & Time</label>
              <input type="datetime-local" name="start_at" class="form-control" 
                     value="<?= !empty($ev['start_at']) ? date('Y-m-d\TH:i', strtotime($ev['start_at'])) : '' ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">End Date & Time</label>
              <input type="datetime-local" name="end_at" class="form-control" 
                     value="<?= !empty($ev['end_at']) ? date('Y-m-d\TH:i', strtotime($ev['end_at'])) : '' ?>">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Registration URL</label>
            <input type="url" name="registration_url" class="form-control" value="<?= esc($ev['registration_url'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"><?= esc($ev['description'] ?? '') ?></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('eventsForm');
  const saveStatus = document.getElementById('saveStatus');
  let dirty = false;

  // Manual modal handling for edit buttons
  document.querySelectorAll('.edit-event-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const eventId = btn.getAttribute('data-event-id');
      const modalId = `editEventModal${eventId}`;
      const modalElement = document.getElementById(modalId);
      
      if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
      } else {
        console.error('Modal not found:', modalId);
      }
    });
  });

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
