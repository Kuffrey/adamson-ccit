<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dean') {
    header('Location: ?page=login');
    exit;
}

require_once __DIR__ . '/../models/Certification.php';

// Handle approval/rejection actions
if (isset($_POST['action']) && isset($_POST['cert_id'])) {
    $certId = (int)$_POST['cert_id'];
    $action = $_POST['action'];
    
    if ($action === 'approve') {
        Certification::updateStatus($certId, 'approved');
        $message = "Certification approved successfully!";
    } elseif ($action === 'reject') {
        Certification::updateStatus($certId, 'rejected');
        $message = "Certification rejected.";
    }
    
    header('Location: ?page=dean_manage_faculty_certifications&msg=' . urlencode($message));
    exit;
}

$pendingCerts = Certification::getAllPending();
$message = isset($_GET['msg']) ? $_GET['msg'] : '';

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Faculty Certifications Management | Dean Dashboard</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css" />
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/dean/_dean_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Faculty Certifications Management</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($_SESSION['user']['username'][0] ?? 'D')) ?></span>
        <span class="admin-topbar__name"><?= esc($_SESSION['user']['username'] ?? 'Dean') ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <div class="admin-section-header">
        <h1 class="admin-cms-section__title">Faculty Certifications Approval Queue</h1>
        <p>Review and approve certification submissions from faculty members.</p>
      </div>

      <?php if ($message): ?>
        <div class="admin-notice admin-notice--success">
          <?= esc($message) ?>
        </div>
      <?php endif; ?>

      <?php if (empty($pendingCerts)): ?>
        <div class="admin-notice admin-notice--info">
          No certification submissions pending approval.
        </div>
      <?php else: ?>
        <div class="admin-table-wrapper">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Certification</th>
                <th>Issuer</th>
                <th>Faculty</th>
                <th>Issued Date</th>
                <th>Expires</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($pendingCerts as $cert): ?>
              <tr>
                <td>
                  <strong><?= esc($cert['title']) ?></strong>
                </td>
                <td><?= esc($cert['issuer']) ?></td>
                <td><?= esc($cert['faculty_name'] ?? 'Unknown') ?></td>
                <td><?= esc($cert['issued_at'] ?? 'N/A') ?></td>
                <td><?= esc($cert['expires_at'] ?? 'No expiration') ?></td>
                <td>
                  <div class="admin-actions">
                    <form method="post" style="display: inline-block; margin-right: 5px;">
                      <input type="hidden" name="cert_id" value="<?= $cert['id'] ?>">
                      <input type="hidden" name="action" value="approve">
                      <button type="submit" class="btn btn--small btn--success" 
                              onclick="return confirm('Approve this certification?')">
                        Approve
                      </button>
                    </form>
                    <form method="post" style="display: inline-block;">
                      <input type="hidden" name="cert_id" value="<?= $cert['id'] ?>">
                      <input type="hidden" name="action" value="reject">
                      <button type="submit" class="btn btn--small btn--danger" 
                              onclick="return confirm('Reject this certification?')">
                        Reject
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>
  </main>
</div>
</body>
</html>