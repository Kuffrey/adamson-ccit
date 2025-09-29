<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dean') {
    header('Location: ?page=login');
    exit;
}
require_once __DIR__ . '/../models/Research.php';

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$researchModel = new Research();

// Approve or reject research
if (isset($_POST['action']) && isset($_POST['research_id'])) {
    $researchId = (int)$_POST['research_id'];
    if ($_POST['action'] === 'approve') {
        $researchModel->updateStatus($researchId, 'approved');
    } elseif ($_POST['action'] === 'reject') {
        $researchModel->updateStatus($researchId, 'rejected');
    }
    header('Location: ?page=dean_manage_faculty_research');
    exit;
}

// List all research with status 'review'
$pendingResearch = $researchModel->listByStatus('review');
// Optionally, show history
$approvedResearch = $researchModel->listByStatus('approved');
$rejectedResearch = $researchModel->listByStatus('rejected');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Research Management | Dean Dashboard</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/dean/_dean_sidebar.php'; ?>
  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Research Management</span>
    </header>
    <section class="admin-cms-section">
      <div class="faculty-table-card">
        <div class="faculty-table-card-header">
          <i class="fas fa-list"></i> Pending Research for Approval
        </div>
        <?php if (empty($pendingResearch)): ?>
          <div class="admin-notice admin-notice--info">
            <i class="fas fa-info-circle"></i> No pending research submissions.
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Faculty</th>
                  <th>Description</th>
                  <th style="width: 160px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pendingResearch as $item): ?>
                  <tr>
                    <td><strong><?= esc($item['title']) ?></strong></td>
                    <td><?= esc($item['owner_user_id']) ?></td>
                    <td><?= nl2br(esc($item['abstract'])) ?></td>
                    <td class="text-center">
                      <form method="post" style="display:inline;">
                        <input type="hidden" name="research_id" value="<?= $item['id'] ?>">
                        <button type="submit" name="action" value="approve" class="btn btn-sm btn--primary" onclick="return confirm('Approve this research?')">
                          <i class="fas fa-check"></i> Approve
                        </button>
                        <button type="submit" name="action" value="reject" class="btn btn-sm btn--danger" onclick="return confirm('Reject this research?')">
                          <i class="fas fa-times"></i> Reject
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
      <div class="faculty-table-card">
        <div class="faculty-table-card-header">
          <i class="fas fa-history"></i> Approved Research
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Faculty</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($approvedResearch as $item): ?>
                <tr>
                  <td><strong><?= esc($item['title']) ?></strong></td>
                  <td><?= esc($item['owner_user_id']) ?></td>
                  <td><?= nl2br(esc($item['abstract'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="faculty-table-card">
        <div class="faculty-table-card-header">
          <i class="fas fa-ban"></i> Rejected Research
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Faculty</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rejectedResearch as $item): ?>
                <tr>
                  <td><strong><?= esc($item['title']) ?></strong></td>
                  <td><?= esc($item['owner_user_id']) ?></td>
                  <td><?= nl2br(esc($item['abstract'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
