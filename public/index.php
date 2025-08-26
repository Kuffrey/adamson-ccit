<?php
// public/index.php — front controller

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
ob_start();

require_once __DIR__ . '/../app/controllers/Router.php';

// Detect admin route early (supports ?page=admin_* and /admin… URLs)
$uri   = $_SERVER['REQUEST_URI'] ?? '';
$page  = $_GET['page'] ?? '';
$isAdmin = (strpos($page, 'admin_') === 0) || preg_match('#/(admin|cms)(/|$)#i', $uri);

if ($isAdmin) {
    // ADMIN PAGES: do NOT include the public header/footer.
    // Your admin views should output their own <html><head>… and admin header.
    Router::route();

} else {
    // PUBLIC PAGES
    include __DIR__ . '/../app/views/layouts/header.php';
    Router::route();
    include __DIR__ . '/../app/views/layouts/footer.php';
}

ob_end_flush();
