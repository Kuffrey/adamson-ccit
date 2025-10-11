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

// Get filter parameters (INCLUDES search string; this was missing from model calls)
$page          = max(1, (int)($_GET['p'] ?? 1));
$limit         = 25;
$actionFilter  = $_GET['action'] ?? '';
$tableFilter   = $_GET['table'] ?? '';
$searchQuery   = trim($_GET['search'] ?? ''); // <-- new

try {
    // Get logs with pagination and filtering (pass $searchQuery through)
    // Make sure your DeanLogs methods accept the extra $search parameter.
    // Signature example:
    // DeanLogs::getAllWithPagination(int $page, int $limit, ?string $action, ?string $table, ?string $search)
    $logs       = DeanLogs::getAllWithPagination($page, $limit, $actionFilter ?: null, $tableFilter ?: null, $searchQuery ?: null);
    $totalLogs  = DeanLogs::getTotalCount($actionFilter ?: null, $tableFilter ?: null, $searchQuery ?: null);
    $totalPages = max(1, (int)ceil($totalLogs / $limit));

    // Get filter options
    $actionTypes = DeanLogs::getActionTypes();
    $tableNames  = DeanLogs::getTableNames();

    // Get activity stats
    $stats = DeanLogs::getStats(7); // Last 7 days

    // Process stats for display
    $dailyActivity = [];
    $actionCounts  = [];

    foreach ($stats as $stat) {
        $date   = $stat['date'];
        $action = $stat['action'];
        $count  = (int)$stat['count'];

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
    $logs        = [];
    $totalLogs   = 0;
    $totalPages  = 1;
    $actionTypes = [];
    $tableNames  = [];
    $dailyActivity = [];
    $actionCounts  = [];
    $notice = 'Error loading logs: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs | CCIT Dean</title>
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/dean.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    .activity-icon { width: 22px; text-align: center; }
    .activity-row td { vertical-align: middle; }
    .activity-details { max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .activity-user { font-weight: 600; color: #003169; }
    .activity-action { font-weight: 600; }
    .activity-date { font-size: .95rem; color: #374151; }
    .activity-ip { font-size: .85rem; color: #6b7280; }
    .activity-table { font-size: .95rem; color: #0080c9; }
    .activity-badge { font-size: .8rem; font-weight: 700; border-radius: 6px; padding: 6px 12px; }
    .activity-badge.approve { background: #dcfce7; color: #166534; }
    .activity-badge.reject { background: #fee2e2; color: #dc2626; }
    .activity-badge.login { background: #e0f2fe; color: #0369a1; }
    .activity-badge.other { background: #f3f4f6; color: #374151; }
    .activity-badge.update { background: #fef3c7; color: #92400e; }
    .activity-badge.delete { background: #fef2f2; color: #dc2626; }
    .activity-badge.create { background: #dbeafe; color: #1d4ed8; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/_dean_sidebar.php'; ?>
        <main class="admin-main">
            <header class="admin-topbar">
                <button class="topbar__btn hide-desktop" type="button" aria-label="Open navigation menu" data-sb-open>
                    <i class="fas fa-bars"></i>
                </button>
                <span class="admin-topbar__title">Activity Logs</span>
                <span class="admin-topbar__spacer"></span>
            </header>
            <section class="admin-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex gap-2">
                        <div class="badge bg-primary">Total: <?= number_format((int)$totalLogs) ?></div>
                        <div class="badge bg-info">Page: <?= (int)$page ?>/<?= (int)$totalPages ?></div>
                    </div>
                </div>

                <?php if (!empty($notice)): ?>
                    <div class="alert <?= str_starts_with($notice, 'Error') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show modern-alert" role="alert">
                        <i class="fas <?= str_starts_with($notice, 'Error') ? 'fa-exclamation-circle' : 'fa-check-circle' ?>"></i>
                        <?= esc($notice) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Quick Filter/Search Bar -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="GET" class="row g-2 align-items-end">
                            <input type="hidden" name="page" value="dean_logs">
                            <div class="col-md-3">
                                <label class="form-label mb-1">Action</label>
                                <select name="action" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <?php foreach ($actionTypes as $actionType): ?>
                                        <option value="<?= esc($actionType) ?>" <?= ($actionFilter === $actionType) ? 'selected' : '' ?>>
                                            <?= esc(str_replace('_', ' ', ucwords($actionType, '_'))) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-1">Table</label>
                                <select name="table" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <?php foreach ($tableNames as $tableName): ?>
                                        <option value="<?= esc($tableName) ?>" <?= ($tableFilter === $tableName) ? 'selected' : '' ?>>
                                            <?= esc($tableName) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-1">Search Details</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                       value="<?= esc($searchQuery) ?>" placeholder="Search details...">
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="?page=dean_logs" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Logs Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>Activity Logs
                            <?php if ($actionFilter || $tableFilter || $searchQuery !== ''): ?>
                                <small class="text-muted">
                                    (Filtered:
                                    <?= $actionFilter ? 'Action: ' . esc($actionFilter) : '' ?>
                                    <?= $actionFilter && $tableFilter ? ', ' : '' ?>
                                    <?= $tableFilter ? 'Table: ' . esc($tableFilter) : '' ?>
                                    <?= ($actionFilter || $tableFilter) && $searchQuery !== '' ? ', ' : '' ?>
                                    <?= $searchQuery !== '' ? 'Search: ' . esc($searchQuery) : '' ?>
                                    )
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
                                <table class="table table-hover table-striped align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width:40px;"></th>
                                            <th>Date & Time</th>
                                            <th>User</th>
                                            <th>Action</th>
                                            <th>Table</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($logs as $log): ?>
                                            <?php
                                            // Icon and badge for action
                                            $action = strtolower($log['action'] ?? '');
                                            $icon   = 'fa-file-alt'; $badge = 'other';
                                            if (str_contains($action, 'approve')) { $icon = 'fa-check-circle'; $badge = 'approve'; }
                                            elseif (str_contains($action, 'reject')) { $icon = 'fa-times-circle'; $badge = 'reject'; }
                                            elseif (str_contains($action, 'login')) { $icon = 'fa-sign-in-alt'; $badge = 'login'; }
                                            elseif (str_contains($action, 'update')) { $icon = 'fa-edit'; $badge = 'update'; }
                                            elseif (str_contains($action, 'delete')) { $icon = 'fa-trash'; $badge = 'delete'; }
                                            elseif (str_contains($action, 'create')) { $icon = 'fa-plus-circle'; $badge = 'create'; }
                                            ?>
                                            <tr class="activity-row">
                                                <td class="activity-icon"><i class="fas <?= $icon ?> text-<?= $badge ?>"></i></td>
                                                <td class="activity-date">
                                                    <?= !empty($log['created_at']) ? date('M j, Y', strtotime($log['created_at'])) : '' ?><br>
                                                    <span class="text-muted"><?= !empty($log['created_at']) ? date('g:i A', strtotime($log['created_at'])) : '' ?></span>
                                                </td>
                                                <td class="activity-user">
                                                    <?= !empty($log['username']) ? esc($log['username']) : '<span class="text-muted">System</span>' ?>
                                                </td>
                                                <td>
                                                    <span class="activity-badge <?= $badge ?>">
                                                        <?= esc(str_replace('_', ' ', ucwords($log['action'] ?? '', '_'))) ?>
                                                    </span>
                                                </td>
                                                <td class="activity-table">
                                                    <?php if (!empty($log['table_name'])): ?>
                                                        <code><?= esc($log['table_name']) ?></code>
                                                        <?php if (!empty($log['record_id'])): ?>
                                                            <br><small class="text-muted">ID: <?= esc($log['record_id']) ?></small>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="activity-details">
                                                    <?php if (!empty($log['details'])): ?>
                                                        <?= esc(strlen($log['details']) > 100 ? substr($log['details'], 0, 100) . '...' : $log['details']) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
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
                                        <?php
                                            // Helper to build page links preserving filters
                                            $buildLink = function($targetPage) use ($actionFilter, $tableFilter, $searchQuery) {
                                                $q = [
                                                    'page'   => 'dean_logs',
                                                    'p'      => $targetPage,
                                                    'action' => $actionFilter,
                                                    'table'  => $tableFilter,
                                                    'search' => $searchQuery,
                                                ];
                                                return '?' . http_build_query($q);
                                            };
                                        ?>
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= $buildLink($page - 1) ?>">Previous</a>
                                        </li>
                                        <?php
                                        $startPage = max(1, $page - 2);
                                        $endPage   = min($totalPages, $page + 2);
                                        for ($i = $startPage; $i <= $endPage; $i++):
                                        ?>
                                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                                <a class="page-link" href="<?= $buildLink($i) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= $buildLink($page + 1) ?>">Next</a>
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
