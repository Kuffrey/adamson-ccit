<?php
// Database configuration using Railway environment variables with fallback to hardcoded values
if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('MYSQLHOST') ?: 'mysql.railway.internal');
    define('DB_NAME', getenv('MYSQLDATABASE') ?: 'railway');
    define('DB_USER', getenv('MYSQLUSER') ?: 'root');
    define('DB_PASS', getenv('MYSQLPASSWORD') ?: 'oKFZhDcFsLetntJIYUprcgdnIHHEqrlY');
    define('DB_PORT', getenv('MYSQLPORT') ?: 3306);
}

// Test database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    // Connection successful
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please check your configuration.");
}