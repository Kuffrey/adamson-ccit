<?php
// app/views/dean/_dean_header.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../lib/Auth.php';

$user = Auth::user();
$username = $user['username'] ?? 'Dean';
?>
<header class="admin-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-6">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggle me-3" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="h5 mb-0">Dean Dashboard</h1>
                </div>
            </div>
            <div class="col-6 text-end">
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" 
                            id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>
                        <?= htmlspecialchars($username) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="?page=dean_profile">
                            <i class="fas fa-user me-2"></i>Profile
                        </a></li>
                        <li><a class="dropdown-item" href="?page=dean_settings">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="?page=logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }
});
</script>