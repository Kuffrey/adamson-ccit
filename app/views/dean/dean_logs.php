<?php
// app/views/dean/dean_logs.php - Dean Activity Logs
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../lib/Auth.php';

// Check authentication using Auth class
if (!Auth::check() || !Auth::is('dean')) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

require_once __DIR__ . '/../../models/DeanLogs.php';

// Helper function for escaping
if (!function_exists('esc')) {
    function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = Auth::user();
$username = $user['username'] ?? 'Dean';
$notice = '';

// Get filter parameters
$page = max(1, (int)($_GET['p'] ?? 1));
$limit = 25;
$actionFilter = $_GET['action'] ?? '';
$tableFilter = $_GET['table'] ?? '';

try {
    // Get logs with pagination and filtering
    $logs = DeanLogs::getAllWithPagination($page, $limit, $actionFilter ?: null, $tableFilter ?: null);
    $totalLogs = DeanLogs::getTotalCount($actionFilter ?: null, $tableFilter ?: null);
    $totalPages = ceil($totalLogs / $limit);
    
    // Get filter options
    $actionTypes = DeanLogs::getActionTypes();
    $tableNames = DeanLogs::getTableNames();
    
    // Get activity stats
    $stats = DeanLogs::getStats(7); // Last 7 days
    
    // Process stats for display
    $dailyActivity = [];
    $actionCounts = [];
    
    foreach ($stats as $stat) {
        $date = $stat['date'];
        $action = $stat['action'];
        $count = (int)$stat['count'];
        
        if (!isset($dailyActivity[$date])) {
            $dailyActivity[$date] = 0;
        }
        $dailyActivity[$date] += $count;
        
        if (!isset($actionCounts[$action])) {
            $actionCounts[$action] = 0;
        }
        $actionCounts[$action] += $count;
    }
    
    // Sort by most common actions
    arsort($actionCounts);
    
} catch (Exception $e) {
    $logs = [];
    $totalLogs = 0;
    $totalPages = 0;
    $actionTypes = [];
    $tableNames = [];
    $dailyActivity = [];
    $actionCounts = [];
    $notice = 'Error loading logs: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs | CCIT Dean</title>
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
                <span class="admin-topbar__title">Activity Logs</span>
                <div class="admin-topbar__spacer"></div>
                <div class="admin-topbar__user">
                    <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'D')) ?></span>
                    <span class="admin-topbar__name"><?= esc($username) ?></span>
                </div>
            </header>

            <section class="admin-cms-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="admin-cms-section__title mb-1">Activity Logs</h1>
                        <p class="text-muted mb-0">Track and monitor dean system activities</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="badge bg-primary">Total: <?= number_format($totalLogs) ?></div>
                        <div class="badge bg-info">Page: <?= $page ?>/<?= $totalPages ?></div>
                    </div>
                </div>

                <?php if ($notice): ?>
                    <div class="alert <?= str_starts_with($notice, 'Error') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show" role="alert">
                        <i class="fas <?= str_starts_with($notice, 'Error') ? 'fa-exclamation-triangle' : 'fa-check-circle' ?> me-2"></i>
                        <?= esc($notice) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Activity Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-chart-line text-primary me-2"></i>Daily Activity (Last 7 Days)
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if (empty($dailyActivity)): ?>
                                    <p class="text-muted mb-0">No activity recorded</p>
                                <?php else: ?>
                                    <?php foreach (array_slice($dailyActivity, 0, 5, true) as $date => $count): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted"><?= date('M j, Y', strtotime($date)) ?></span>
                                            <span class="badge bg-primary"><?= $count ?> activities</span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-chart-pie text-success me-2"></i>Top Actions (Last 7 Days)
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if (empty($actionCounts)): ?>
                                    <p class="text-muted mb-0">No actions recorded</p>
                                <?php else: ?>
                                    <?php foreach (array_slice($actionCounts, 0, 5, true) as $action => $count): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted"><?= esc(str_replace('_', ' ', ucwords($action, '_'))) ?></span>
                                            <span class="badge bg-success"><?= $count ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <input type="hidden" name="page" value="dean_logs">
                            
                            <div class="col-md-4">
                                <label class="form-label">Filter by Action</label>
                                <select name="action" class="form-select">
                                    <option value="">All Actions</option>
                                    <?php foreach ($actionTypes as $actionType): ?>
                                        <option value="<?= esc($actionType) ?>" <?= $actionFilter === $actionType ? 'selected' : '' ?>>
                                            <?= esc(str_replace('_', ' ', ucwords($actionType, '_'))) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Filter by Table</label>
                                <select name="table" class="form-select">
                                    <option value="">All Tables</option>
                                    <?php foreach ($tableNames as $tableName): ?>
                                        <option value="<?= esc($tableName) ?>" <?= $tableFilter === $tableName ? 'selected' : '' ?>>
                                            <?= esc($tableName) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <a href="?page=dean_logs" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Logs Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>Activity Logs
                            <?php if ($actionFilter || $tableFilter): ?>
                                <small class="text-muted">
                                    (Filtered: <?= $actionFilter ? 'Action: ' . esc($actionFilter) : '' ?>
                                    <?= $actionFilter && $tableFilter ? ', ' : '' ?>
                                    <?= $tableFilter ? 'Table: ' . esc($tableFilter) : '' ?>)
                                </small>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($logs)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No activity logs found.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>User</th>
                                            <th>Action</th>
                                            <th>Table</th>
                                            <th>Details</th>
                                            <th>IP Address</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($logs as $log): ?>
                                            <tr>
                                                <td>
                                                    <small>
                                                        <?= date('M j, Y', strtotime($log['created_at'])) ?><br>
                                                        <span class="text-muted"><?= date('g:i A', strtotime($log['created_at'])) ?></span>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php if ($log['username']): ?>
                                                        <strong><?= esc($log['username']) ?></strong>
                                                    <?php else: ?>
                                                        <span class="text-muted">System</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= 
                                                        str_contains($log['action'], 'approve') ? 'success' : 
                                                        (str_contains($log['action'], 'reject') ? 'danger' : 
                                                        (str_contains($log['action'], 'login') ? 'info' : 'secondary'))
                                                    ?>">
                                                        <?= esc(str_replace('_', ' ', ucwords($log['action'], '_'))) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($log['table_name']): ?>
                                                        <code><?= esc($log['table_name']) ?></code>
                                                        <?php if ($log['record_id']): ?>
                                                            <br><small class="text-muted">ID: <?= esc($log['record_id']) ?></small>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($log['details']): ?>
                                                        <div style="max-width: 300px; overflow: hidden;">
                                                            <?= esc(strlen($log['details']) > 100 ? substr($log['details'], 0, 100) . '...' : $log['details']) ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <code><?= esc($log['ip_address']) ?></code>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                                <nav aria-label="Logs pagination" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=dean_logs&p=<?= $page - 1 ?>&action=<?= urlencode($actionFilter) ?>&table=<?= urlencode($tableFilter) ?>">Previous</a>
                                        </li>
                                        
                                        <?php
                                        $startPage = max(1, $page - 2);
                                        $endPage = min($totalPages, $page + 2);
                                        ?>
                                        
                                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=dean_logs&p=<?= $i ?>&action=<?= urlencode($actionFilter) ?>&table=<?= urlencode($tableFilter) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=dean_logs&p=<?= $page + 1 ?>&action=<?= urlencode($actionFilter) ?>&table=<?= urlencode($tableFilter) ?>">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>