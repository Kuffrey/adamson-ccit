<?php
// Database configuration using Railway environment variables
if (!defined('DB_HOST')) {
    // Use Railway's internal MySQL hostname for faster, more reliable connections
    define('DB_HOST', getenv('MYSQLHOST') ?: 'mysql.railway.internal');
    define('DB_NAME', getenv('MYSQLDATABASE') ?: 'railway');
    define('DB_USER', getenv('MYSQLUSER') ?: 'root');
    define('DB_PASS', getenv('MYSQLPASSWORD') ?: 'oKFZhDcFsLetntJIYUprcgdnIHHEqrlY');
    define('DB_PORT', getenv('MYSQLPORT') ?: 3306);
}

// Check if PDO MySQL driver is available
if (!extension_loaded('pdo_mysql')) {
    error_log("PDO MySQL extension is not loaded!");
    die("Database driver not available. Please contact the administrator.");
}

// Test database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
        ]
    );
    // Connection successful
    error_log("Database connected successfully to " . DB_HOST);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    error_log("Host: " . DB_HOST . ", Database: " . DB_NAME . ", User: " . DB_USER);
    die("Database connection failed. Please check your configuration.");
}
