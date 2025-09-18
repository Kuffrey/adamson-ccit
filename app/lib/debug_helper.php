<?php
// debug_helper.php
// Include this file at the top of your PHP scripts to enable debugging tools

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/cms_error.log');

// Debug functions
function dd($var, $exit = true) {
    echo '<pre style="background:#f8f8f8;color:#333;border:1px solid #ddd;border-radius:4px;padding:12px;margin:12px;font-size:14px;line-height:1.5;max-height:80vh;overflow:auto;">';
    var_dump($var);
    echo '</pre>';
    if ($exit) exit;
}

function dump($var) {
    dd($var, false);
}

function debug_log($message, $context = []) {
    $logFile = __DIR__ . '/cms_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = empty($context) ? '' : ' ' . json_encode($context);
    $logMessage = "[$timestamp] $message$contextStr" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Add a custom error handler
function custom_error_handler($errno, $errstr, $errfile, $errline) {
    $errorMessage = "[$errstr] in $errfile on line $errline";
    error_log($errorMessage);
    
    // Debug UI is completely disabled - errors are still logged but never displayed
    // Errors will only be available in server logs
    
    // Don't execute PHP internal error handler
    return true;
}

// Always register the custom error handler but it won't display errors
set_error_handler("custom_error_handler");

// Debug mode toggle URLs are disabled
// Any requests to enable or disable debug mode will be ignored

// Debug toolbar is completely disabled
// No debug UI will be shown

// Return whether debug mode is enabled - always returns false
function is_debug_enabled() {
    return false; // Debug mode is permanently disabled
}