<?php
// Database configuration for adamson_ccit
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'adamson_ccit');
    define('DB_USER', 'root');
    define('DB_PASS', '');
}

// Test database connection
try {
    $test_pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    // Connection successful
} catch (PDOException $e) {
    error_log("Database connection test failed: " . $e->getMessage());
    die("Database connection failed. Please check your configuration.");
}
