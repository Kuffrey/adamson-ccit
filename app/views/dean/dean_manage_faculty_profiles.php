<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../lib/Auth.php';

if (!Auth::check() || !Auth::is('dean')) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

require_once __DIR__ . '/../../models/FacultyProfile.php';
require_once __DIR__ . '/../../models/FacultySubmissions.php';

function esc($v) { 
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); 
}

$user = Auth::user();
$username = $user['username'] ?? 'Dean';
$notice = '';

try {
    $facultyProfiles = FacultyProfile::getAll();
    $totalFaculty = count($facultyProfiles);
    $pendingSubmissions = FacultySubmissions::getPendingCountsByType();
    $activeFaculty = count(array_filter($facultyProfiles, function($f) { 
        return ($f['status'] ?? 'active') === 'active'; 
    }));
} catch (Exception $e) {
    $facultyProfiles = [];
    $totalFaculty = 0;
    $pendingSubmissions = ['research' => 0, 'news' => 0];
    $activeFaculty = 0;
    $notice = 'Error loading faculty data: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Hub | CCIT Dean</title>
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-cms-layout">
        <?php include __DIR__ . '/_dean_sidebar.php'; ?>
        
        <main class="admin-main">
            <header class="admin-topbar">
                <span class="admin-topbar__title">Faculty Hub</span>
                <div class="admin-topbar__spacer"></div>
                <div class="admin-topbar__user">
                    <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'D')) ?></span>
                    <span class="admin-topbar__name"><?= esc($username) ?></span>
                </div>
            </header>

            <section class="admin-cms-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="admin-cms-section__title mb-1">Faculty Hub</h1>
                        <p class="text-muted mb-0">Overview of faculty members and their activities</p>
                    </div>
                </div>

                <?php if ($notice): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= esc($notice) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Overview Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Faculty</h5>
                                <h2><?= $totalFaculty ?></h2>
                                <i class="fas fa-users fa-2x float-end"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Active Faculty</h5>
                                <h2><?= $activeFaculty ?></h2>
                                <i class="fas fa-user-check fa-2x float-end"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Pending Research</h5>
                                <h2><?= $pendingSubmissions['research'] ?></h2>
                                <i class="fas fa-microscope fa-2x float-end"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Pending News</h5>
                                <h2><?= $pendingSubmissions['news'] ?></h2>
                                <i class="fas fa-newspaper fa-2x float-end"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="?page=dean_approvals" class="btn btn-warning btn-lg w-100 mb-3">
                                    <i class="fas fa-tasks me-2"></i>
                                    Review Pending Approvals
                                    <span class="badge bg-light text-dark ms-2"><?= ($pendingSubmissions['research'] + $pendingSubmissions['news']) ?></span>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="?page=dean_manage_faculty_research" class="btn btn-info btn-lg w-100 mb-3">
                                    <i class="fas fa-microscope me-2"></i>
                                    Manage Faculty Research
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Faculty List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i>Faculty Overview</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($facultyProfiles)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No faculty profiles found.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($facultyProfiles as $faculty): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($faculty['name']) ?></strong>
                                                    <?php if (!empty($faculty['title'])): ?>
                                                        <br><small class="text-muted"><?= esc($faculty['title']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= esc($faculty['dept']) ?></td>
                                                <td>
                                                    <span class="badge bg-secondary"><?= esc($faculty['role'] ?? 'Faculty') ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success"><?= esc($faculty['status'] ?? 'active') ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>