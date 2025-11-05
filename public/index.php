<?php
// ----------------------
// Enable error reporting
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
require_once __DIR__ . '/../config/db.php'; // assuming db.php is at /config/db.php

// ----------------------
// Include router/controller
// ----------------------
require_once __DIR__ . '/../controllers/Router.php'; // fixed relative path

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
    // For admin, faculty, and dean dashboard/admin pages — no public header/footer
    Router::route();
} else {
    // For all other pages — include public header/footer/chatbot
    include __DIR__ . '/../app/views/layouts/header.php';
    Router::route();
    include __DIR__ . '/../app/views/layouts/chatbot.php';
    include __DIR__ . '/../app/views/layouts/footer.php';
}

// Flush output buffer
ob_end_flush();
