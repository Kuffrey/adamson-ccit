<?php
// ----------------------
// Enable error reporting (Railway debugging)
// ----------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ----------------------
// Start session and output buffering
// ----------------------
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
ob_start();

// ----------------------
// Include database configuration
// ----------------------
$databaseConfigPath = null;
foreach ([__DIR__ . '/../app/config/database.php', __DIR__ . '/../config/database.php', dirname(__DIR__) . '/app/config/database.php', dirname(__DIR__) . '/config/database.php'] as $candidate) {
    if (is_file($candidate)) {
        $databaseConfigPath = $candidate;
        break;
    }
}
if (!$databaseConfigPath) {
    throw new RuntimeException('Database configuration file not found.');
}
require_once $databaseConfigPath;

// ----------------------
// Include router/controller
// ----------------------
$routerPath = null;
foreach ([__DIR__ . '/../controllers/Router.php', dirname(__DIR__) . '/controllers/Router.php'] as $candidate) {
    if (is_file($candidate)) {
        $routerPath = $candidate;
        break;
    }
}
if (!$routerPath) {
    throw new RuntimeException('Router file not found.');
}
require_once $routerPath;

// ----------------------
// Get current URI and page
// ----------------------
$uri   = $_SERVER['REQUEST_URI'] ?? '';
$page  = $_GET['page'] ?? '';

// ----------------------
// Determine page type
// ----------------------

// Admin pages
$isAdmin = (strpos($page, 'admin_') === 0) || preg_match('#/(admin|cms)(/|$)#i', $uri);

// Faculty admin/dashboard pages
$isFacultyDashboard = strpos($page, 'faculty_dashboard') === 0 
    || strpos($page, 'faculty_manage_') === 0 
    || strpos($page, 'faculty_portfolio') === 0;

// Dean admin/dashboard pages
$isDeanDashboard = strpos($page, 'dean_') === 0 || $page === 'dean_dashboard';

// ----------------------
// Route requests
// ----------------------
if ($isAdmin || $isFacultyDashboard || $isDeanDashboard) {
    // Admin, faculty, dean — no public header/footer
    Router::route();
} else {
    // Public pages — include header, footer, chatbot
    include __DIR__ . '/../views/layouts/header.php';
    Router::route();
    include __DIR__ . '/../views/layouts/chatbot.php';
    include __DIR__ . '/../views/layouts/footer.php';
}

// Flush output buffer
ob_end_flush();
