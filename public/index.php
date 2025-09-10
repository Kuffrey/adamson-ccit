<?php
// public/index.php — front controller
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
ob_start();

require_once __DIR__ . '/../app/controllers/Router.php';

$uri   = $_SERVER['REQUEST_URI'] ?? '';
$page  = $_GET['page'] ?? '';
$isAdmin = (strpos($page, 'admin_') === 0) || preg_match('#/(admin|cms)(/|$)#i', $uri);

if ($isAdmin) {
    Router::route();
} else {
    include __DIR__ . '/../app/views/layouts/header.php';
    Router::route();

    include __DIR__ . '/../app/views/layouts/chatbot.php';

    include __DIR__ . '/../app/views/layouts/footer.php';
}

ob_end_flush();

