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
// Detect the project root
// ----------------------
$projectRoot = null;
foreach ([__DIR__, dirname(__DIR__), dirname(__DIR__, 2)] as $candidate) {
    if (is_dir($candidate . '/app') || is_dir($candidate . '/controllers')) {
        $projectRoot = realpath($candidate);
        break;
    }
}
if (!$projectRoot) {
    $projectRoot = realpath(__DIR__);
}

// ----------------------
// Include database configuration
// ----------------------
$resolveFile = static function (array $candidates, string $label) {
    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            return $candidate;
        }
    }
    throw new RuntimeException("$label file not found.");
};
require_once $resolveFile([
    $projectRoot . '/app/config/database.php',
    $projectRoot . '/config/database.php',
    $projectRoot . '/config/db.php',
], 'Database configuration');

// ----------------------
// Include router/controller
// ----------------------
require_once $resolveFile([
    $projectRoot . '/app/controllers/Router.php',
    $projectRoot . '/controllers/Router.php',
], 'Router');

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
    include $resolveFile([
        $projectRoot . '/app/views/layouts/header.php',
        $projectRoot . '/views/layouts/header.php',
    ], 'Header layout');
    Router::route();
    include $resolveFile([
        $projectRoot . '/app/views/layouts/chatbot.php',
        $projectRoot . '/views/layouts/chatbot.php',
    ], 'Chatbot layout');
    include $resolveFile([
        $projectRoot . '/app/views/layouts/footer.php',
        $projectRoot . '/views/layouts/footer.php',
    ], 'Footer layout');
}

// Flush output buffer
ob_end_flush();
