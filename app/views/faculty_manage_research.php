<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'faculty') {
    header('Location: ?page=login_faculty');
    exit;
}
require_once __DIR__ . '/../models/Research.php';

// Check if user is logged in and get faculty ID
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug: Let's see what's in the session
error_log("Session debug: " . print_r($_SESSION, true));

$faculty_id = $_SESSION['user']['id'] ?? null;
if (!$faculty_id) {
    error_log("Faculty ID not found in session. Redirecting to login.");
    header('Location: ?page=login_faculty');
    exit;
}

error_log("Faculty ID found: " . $faculty_id);
$dept_id = $_SESSION['user']['department_id'] ?? 0;
$researchModel = new Research();

if (isset($_POST['add_research'])) {
    $researchModel->create([
        'title' => $_POST['title'],
        'abstract' => $_POST['description'],
        'owner_user_id' => $faculty_id,
        'department_id' => $dept_id,
        'status' => 'draft',
        'requires_dean_approval' => 1, // or 0 if not needed
    ]);
    header('Location: ?page=faculty_manage_research');
    exit;
}

if (isset($_POST['submit_for_review']) && isset($_POST['research_id'])) {
    $researchId = (int)$_POST['research_id'];
    // Only allow submission if owned by this faculty and status is draft
    $myResearch = $researchModel->listByOwner($faculty_id);
    $target = null;
    foreach ($myResearch as $r) {
        if ($r['id'] == $researchId && $r['status'] === 'draft') {
            $target = $r;
            break;
        }
    }
    if ($target) {
        $researchModel->submitForReview($researchId);
    }
    header('Location: ?page=faculty_manage_research');
    exit;
}
if (isset($_GET['delete'])) {
    // Only allow delete if owned by this faculty and status is draft or review
    $myResearch = $researchModel->listByOwner($faculty_id);
    $target = null;
    foreach ($myResearch as $r) {
        if ($r['id'] == (int)$_GET['delete']) $target = $r;
    }
    if ($target && in_array($target['status'], ['draft','review'])) {
        // You need to implement a delete method in Research model if not present
        if (method_exists($researchModel, 'delete')) {
            $researchModel->delete((int)$_GET['delete']);
        }
    }
    header('Location: ?page=faculty_manage_research');
    exit;
}
$research = $researchModel->listByOwner($faculty_id);

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Research | Faculty Dashboard</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css" />
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Manage Research</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($_SESSION['user']['username'][0] ?? 'F')) ?></span>
        <span class="admin-topbar__name"><?= esc($_SESSION['user']['username'] ?? 'Faculty') ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <div class="admin-section-header">
        <h1 class="admin-cms-section__title">Manage Research</h1>
        <p>Add and manage your research publications.</p>
      </div>

      <div class="admin-form-card">
        <h2>Add New Research</h2>
        <form method="post" action="?page=faculty_manage_research">
          <div class="form-group">
            <label for="title">Research Title</label>
            <input type="text" id="title" name="title" class="form-control" placeholder="Enter research title" required>
          </div>
          <div class="form-group">
            <label for="description">Research Description/Abstract</label>
            <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter research description or abstract" required></textarea>
          </div>
          <button type="submit" name="add_research" class="btn btn--primary">Add Research</button>
        </form>
      </div>

      <div class="admin-form-card">
        <h2>Your Research</h2>
        <?php if (empty($research)): ?>
          <div class="admin-notice admin-notice--info">
            No research entries found. Add your first research above.
          </div>
        <?php else: ?>
          <div class="research-list">
            <?php foreach ($research as $item): ?>
              <div class="research-item" style="border: 1px solid var(--edgec); margin-bottom: 15px; padding: 20px; border-radius: 8px; background: #fff;">
                <div class="research-header" style="display: flex; justify-content: between; align-items: start; margin-bottom: 10px;">
                  <div style="flex: 1;">
                    <h3 style="margin: 0 0 5px 0; color: #0b234c;"><?= esc($item['title']) ?></h3>
                    <span class="status-badge status-<?= esc($item['status']) ?>" style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; 
                      <?php 
                        switch($item['status']) {
                          case 'draft': echo 'background: #f3f4f6; color: #374151;'; break;
                          case 'review': echo 'background: #fef3c7; color: #92400e;'; break;
                          case 'approved': echo 'background: #dcfce7; color: #166534;'; break;
                          case 'rejected': echo 'background: #fee2e2; color: #dc2626;'; break;
                          default: echo 'background: #f3f4f6; color: #374151;';
                        }
                      ?>">
                      <?= esc(ucfirst($item['status'])) ?>
                    </span>
                  </div>
                </div>
                <p style="margin: 10px 0; color: #64748b; line-height: 1.5;"><?= nl2br(esc($item['abstract'])) ?></p>
                <div class="research-actions" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                  <?php if ($item['status'] === 'draft'): ?>
                    <form method="post" style="display:inline; margin-right:10px;">
                      <input type="hidden" name="research_id" value="<?= $item['id'] ?>">
                      <button type="submit" name="submit_for_review" class="btn btn--small btn--primary" onclick="return confirm('Submit this research for dean approval?')">Submit for Review</button>
                    </form>
                  <?php endif; ?>
                  
                  <?php if (in_array($item['status'], ['draft','review'])): ?>
                    <a href="?page=faculty_manage_research&delete=<?= $item['id'] ?>" class="btn btn--small btn--danger" onclick="return confirm('Delete this research?')">Delete</a>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </main>
</div>
</body>
</html>
