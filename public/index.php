<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// public/index.php — front controller
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
ob_start();

// Go up one level from /public to the project root
$rootDir = dirname(__DIR__);

// Require the Router and DB config
require_once $rootDir . '/app/controllers/Router.php';
require_once $rootDir . '/config/db.php';

$uri   = $_SERVER['REQUEST_URI'] ?? '';
$page  = $_GET['page'] ?? '';

// Admin pages
$isAdmin = (strpos($page, 'admin_') === 0) || preg_match('#/(admin|cms)(/|$)#i', $uri);

// Faculty admin/dashboard pages
$isFacultyDashboard = strpos($page, 'faculty_dashboard') === 0 || strpos($page, 'faculty_manage_') === 0 || strpos($page, 'faculty_portfolio') === 0;

// Dean admin/dashboard pages
$isDeanDashboard = strpos($page, 'dean_') === 0 || $page === 'dean_dashboard';

if ($isAdmin || $isFacultyDashboard || $isDeanDashboard) {
    // For admin, faculty, and dean dashboard/admin pages — no public header/footer
    Router::route();
} else {
    // For all other pages (including public faculty pages) — include public header/footer/chatbot
    include $rootDir . '/app/views/layouts/header.php';
    Router::route();
    include $rootDir . '/app/views/layouts/chatbot.php';
    include $rootDir . '/app/views/layouts/footer.php';
}

ob_end_flush();
