<?php
// dean_manage_faculty_comprehensive.php — Comprehensive Faculty Management with Activity Tracking
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../lib/Auth.php';
require_once __DIR__ . '/../../models/FacultyProfile.php';

// Check authentication
if (!Auth::check() || !Auth::is('dean')) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$user = Auth::user();
$username = $user['username'] ?? 'Dean';
$notice = '';

// Get current data with activity counts
try {
    $status = $_GET['status'] ?? 'all';
    $profiles = FacultyProfile::getAll();
    
    // TODO: Add these methods to FacultyProfile model or create separate models
    // $pendingSubmissions = FacultySubmissions::getPendingByDean($user['id']);
    // $activityCounts = FacultyActivity::getCountsByStatus();
    
    // Mock data for now - replace with actual database calls
    $pendingSubmissions = [
        'research' => 3,
        'certifications' => 5,
        'news' => 2
    ];
    $activityCounts = [
        'active_faculty' => count($profiles),
        'pending_approvals' => 10,
        'recent_activities' => 15
    ];
} catch (Exception $e) {
    $notice = 'Error loading data: ' . $e->getMessage();
    $profiles = [];
    $pendingSubmissions = ['research' => 0, 'certifications' => 0, 'news' => 0];
    $activityCounts = ['active_faculty' => 0, 'pending_approvals' => 0, 'recent_activities' => 0];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle various actions similar to news management
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'approve_research':
                // TODO: Implement research approval
                $notice = 'Research submission approved successfully!';
                break;
            case 'approve_certification':
                // TODO: Implement certification approval
                $notice = 'Certification approved successfully!';
                break;
            case 'approve_news':
                // TODO: Implement news approval
                $notice = 'News article approved and published!';
                break;
            case 'update_profile_status':
                // TODO: Implement profile status update
                $notice = 'Faculty profile status updated!';
                break;
        }
        
        if ($notice) {
            header('Location: ?page=dean_manage_faculty_comprehensive&success=' . urlencode($notice));
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Management | CCIT Dean</title>
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="admin-cms-layout">
        <?php include __DIR__ . '/_dean_sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-topbar">
                <span class="admin-topbar__title">Faculty → Comprehensive Management</span>
                <div class="admin-topbar__spacer"></div>
                <div class="admin-topbar__user">
                    <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'D')) ?></span>
                    <span class="admin-topbar__name"><?= esc($username) ?></span>
                </div>
            </header>

            <section class="admin-cms-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h1 class="admin-cms-section__title mb-1">Faculty Management Hub</h1>
                        <p class="text-muted mb-0">Manage faculty profiles, submissions, and approvals</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="badge bg-primary">Active Faculty: <?= $activityCounts['active_faculty'] ?></div>
                        <div class="badge bg-warning">Pending: <?= $activityCounts['pending_approvals'] ?></div>
                        <div class="badge bg-info">Recent: <?= $activityCounts['recent_activities'] ?></div>
                    </div>
                </div>
                
                <?php if ($notice): ?>
                    <div class="alert <?= str_starts_with($notice, 'Error') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show" role="alert">
                        <?= esc($notice) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Quick Actions Dashboard -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-microscope fa-2x text-warning"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="card-title">Research Submissions</h5>
                                        <p class="card-text"><?= $pendingSubmissions['research'] ?> pending approval</p>
                                        <a href="#researchSubmissions" class="btn btn-warning btn-sm">Review</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-info">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-certificate fa-2x text-info"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="card-title">Certifications</h5>
                                        <p class="card-text"><?= $pendingSubmissions['certifications'] ?> pending review</p>
                                        <a href="#certificationSubmissions" class="btn btn-info btn-sm">Review</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-newspaper fa-2x text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="card-title">News Articles</h5>
                                        <p class="card-text"><?= $pendingSubmissions['news'] ?> awaiting approval</p>
                                        <a href="#newsSubmissions" class="btn btn-success btn-sm">Review</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Faculty Profiles Overview -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i>Faculty Profiles Overview</h5>
                            <div class="d-flex gap-2">
                                <a href="?page=dean_add_faculty_profile" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i>Add Faculty
                                </a>
                                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#facultyProfilesCollapse" aria-expanded="true">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="collapse show" id="facultyProfilesCollapse">
                        <div class="card-body">
                            <?php if (empty($profiles)): ?>
                                <div class="alert alert-warning" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>No faculty profiles found.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Profile</th>
                                                <th>Department</th>
                                                <th>Role</th>
                                                <th>Recent Activity</th>
                                                <th>Pending Items</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($profiles as $profile): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if (!empty($profile['avatar_url'])): ?>
                                                                <img src="<?= esc($profile['avatar_url']) ?>" 
                                                                     alt="<?= esc($profile['name']) ?>" 
                                                                     class="rounded-circle me-2" 
                                                                     width="40" height="40">
                                                            <?php else: ?>
                                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                                     style="width: 40px; height: 40px;">
                                                                    <?= esc(strtoupper(substr($profile['name'] ?? 'F', 0, 1))) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div>
                                                                <strong><?= esc($profile['name'] ?? 'N/A') ?></strong>
                                                                <?php if (!empty($profile['title'])): ?>
                                                                    <br><small class="text-muted"><?= esc($profile['title']) ?></small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= esc($profile['dept'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <span class="badge bg-primary"><?= esc($profile['role'] ?? 'N/A') ?></span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <!-- TODO: Show last activity -->
                                                            Last activity: 2 days ago
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-1">
                                                            <!-- TODO: Show actual pending counts per faculty -->
                                                            <span class="badge bg-warning">2 research</span>
                                                            <span class="badge bg-info">1 cert</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="?page=dean_edit_faculty_profile&id=<?= $profile['id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary" title="Edit Profile">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button class="btn btn-sm btn-outline-info" 
                                                                    title="View Activities"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#activitiesModal<?= $profile['id'] ?>">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-success" 
                                                                    title="Approve All Pending">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </div>
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

                <!-- Pending Research Submissions -->
                <div class="card mb-4" id="researchSubmissions">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="fas fa-microscope me-2"></i>Pending Research Submissions</h5>
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#researchCollapse" aria-expanded="false">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="collapse" id="researchCollapse">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Review and approve research submissions from faculty members.
                            </div>
                            <!-- TODO: Add actual research submission table -->
                            <p class="text-muted">Research submissions will be displayed here...</p>
                        </div>
                    </div>
                </div>

                <!-- Pending Certification Submissions -->
                <div class="card mb-4" id="certificationSubmissions">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="fas fa-certificate me-2"></i>Pending Certification Submissions</h5>
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#certificationCollapse" aria-expanded="false">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="collapse" id="certificationCollapse">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Review and verify certification submissions from faculty members.
                            </div>
                            <!-- TODO: Add actual certification submission table -->
                            <p class="text-muted">Certification submissions will be displayed here...</p>
                        </div>
                    </div>
                </div>

                <!-- Pending News Submissions -->
                <div class="card mb-4" id="newsSubmissions">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="fas fa-newspaper me-2"></i>Pending News Submissions</h5>
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#newsCollapse" aria-expanded="false">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="collapse" id="newsCollapse">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Review and approve news articles submitted by faculty members.
                            </div>
                            <!-- TODO: Add actual news submission table -->
                            <p class="text-muted">News submissions will be displayed here...</p>
                        </div>
                    </div>
                </div>

                <div class="page-end-spacer" style="height:160px" aria-hidden="true"></div>

                <!-- Action Bar -->
                <div class="savebar">
                    <div class="savebar__inner">
                        <span class="savebar__status" id="saveStatus">Ready to review submissions</span>
                        <div class="savebar__actions">
                            <a class="btn btn-outline-secondary" href="?page=dean_dashboard">Dashboard</a>
                            <a class="btn btn-outline-primary" href="?page=dean_manage_faculty_profiles">Simple View</a>
                            <button type="button" class="btn btn--primary" onclick="approveAllPending()">
                                <i class="fas fa-check-double me-1"></i>Approve All Pending
                            </button>
                        </div>
                    </div>
                </div>

            </section>
        </main>
    </div>

    <!-- Faculty Activities Modal Template -->
    <?php foreach ($profiles as $profile): ?>
    <div class="modal fade" id="activitiesModal<?= $profile['id'] ?>" tabindex="-1" 
         aria-labelledby="activitiesModalLabel<?= $profile['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="activitiesModalLabel<?= $profile['id'] ?>">
                        Activities for <?= esc($profile['name']) ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <?php if (!empty($profile['avatar_url'])): ?>
                                    <img src="<?= esc($profile['avatar_url']) ?>" 
                                         alt="<?= esc($profile['name']) ?>" 
                                         class="rounded-circle mb-3" 
                                         width="80" height="80">
                                <?php else: ?>
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" 
                                         style="width: 80px; height: 80px; font-size: 24px;">
                                        <?= esc(strtoupper(substr($profile['name'] ?? 'F', 0, 1))) ?>
                                    </div>
                                <?php endif; ?>
                                <h6><?= esc($profile['name']) ?></h6>
                                <p class="text-muted"><?= esc($profile['role']) ?></p>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <!-- TODO: Add actual activity data -->
                            <h6>Recent Submissions</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Machine Learning Research Paper</span>
                                    <span class="badge bg-warning">Pending</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>AWS Cloud Certification</span>
                                    <span class="badge bg-info">Under Review</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>AI Conference News</span>
                                    <span class="badge bg-success">Approved</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="?page=dean_edit_faculty_profile&id=<?= $profile['id'] ?>" class="btn btn-primary">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function approveAllPending() {
            if (confirm('Are you sure you want to approve all pending submissions? This action cannot be undone.')) {
                // TODO: Implement bulk approval
                document.getElementById('saveStatus').textContent = 'Processing approvals...';
                // Add AJAX call here
            }
        }

        // Auto-hide alerts after 4 seconds
        document.addEventListener('DOMContentLoaded', () => {
            const notices = document.querySelectorAll('.alert');
            if (notices.length) {
                setTimeout(() => {
                    notices.forEach(n => n.style.display = 'none');
                }, 4000);
            }
        });
    </script>
</body>
</html>