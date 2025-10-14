<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Improved autoloader to handle namespaces
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/../models/';
    $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
