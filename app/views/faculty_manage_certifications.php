<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'faculty') {
    header('Location: ?page=login_faculty');
    exit;
}
require_once __DIR__ . '/../models/Certification.php';

// Check if user is logged in and get faculty ID
$faculty_id = $_SESSION['user']['id'] ?? null;
if (!$faculty_id) {
    header('Location: ?page=login_faculty');
    exit;
}
$certModel = new Certification();

if (isset($_POST['add_cert'])) {
    $certModel->create([
        'title' => $_POST['name'],
        'issuer' => $_POST['issuer'],
        'owner_user_id' => $faculty_id,
        'department_id' => $_SESSION['user']['department_id'] ?? 0,
        'issued_at' => date('Y-m-d'),
        'expires_at' => null,
        'status' => 'pending',
    ]);
    header('Location: ?page=faculty_manage_certifications');
    exit;
}
if (isset($_GET['delete'])) {
    // Only allow delete if status is pending and owned by this faculty
    $cert = null;
    foreach ($certModel->listByOwner($faculty_id) as $c) {
        if ($c['id'] == (int)$_GET['delete']) $cert = $c;
    }
    if ($cert && $cert['status'] === 'pending') {
        $certModel->delete((int)$_GET['delete']);
    }
    header('Location: ?page=faculty_manage_certifications');
    exit;
}
$certs = $certModel->listByOwner($faculty_id);

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Certifications | Faculty Dashboard</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css" />
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Manage Certifications</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($_SESSION['user']['username'][0] ?? 'F')) ?></span>
        <span class="admin-topbar__name"><?= esc($_SESSION['user']['username'] ?? 'Faculty') ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <div class="admin-section-header">
        <h1 class="admin-cms-section__title">Manage Certifications</h1>
        <p>Add and manage your professional certifications.</p>
      </div>

      <div class="admin-form-card">
        <h2>Add New Certification</h2>
        <form method="post" action="?page=faculty_manage_certifications">
          <div class="form-group">
            <label for="name">Certification Name</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Enter certification name" required>
          </div>
          <div class="form-group">
            <label for="issuer">Issuer</label>
            <input type="text" id="issuer" name="issuer" class="form-control" placeholder="Enter issuing organization" required>
          </div>
          <button type="submit" name="add_cert" class="btn btn--primary">Add Certification</button>
        </form>
      </div>

      <div class="admin-form-card">
        <h2>Your Certifications</h2>
        <?php if (empty($certs)): ?>
          <div class="admin-notice admin-notice--info">
            No certifications found. Add your first certification above.
          </div>
        <?php else: ?>
          <div class="certifications-list">
            <?php foreach ($certs as $item): ?>
              <div class="cert-item" style="border: 1px solid var(--edgec); margin-bottom: 15px; padding: 20px; border-radius: 8px; background: #fff;">
                <div class="cert-header" style="display: flex; justify-content: between; align-items: start; margin-bottom: 10px;">
                  <div style="flex: 1;">
                    <h3 style="margin: 0 0 5px 0; color: #0b234c;"><?= esc($item['title']) ?></h3>
                    <p style="margin: 5px 0; color: #64748b;"><em>Issuer: <?= esc($item['issuer']) ?></em></p>
                    <span class="status-badge status-<?= esc($item['status']) ?>" style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; 
                      <?php 
                        switch($item['status']) {
                          case 'pending': echo 'background: #fef3c7; color: #92400e;'; break;
                          case 'approved': echo 'background: #dcfce7; color: #166534;'; break;
                          case 'rejected': echo 'background: #fee2e2; color: #dc2626;'; break;
                          default: echo 'background: #f3f4f6; color: #374151;';
                        }
                      ?>">
                      <?= esc(ucfirst($item['status'])) ?>
                    </span>
                  </div>
                </div>
                <?php if ($item['status'] === 'pending'): ?>
                  <div class="cert-actions" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                    <a href="?page=faculty_manage_certifications&delete=<?= $item['id'] ?>" class="btn btn--small btn--danger" onclick="return confirm('Delete this certification?')">Delete</a>
                  </div>
                <?php endif; ?>
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
