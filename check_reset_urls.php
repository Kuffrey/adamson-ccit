<?php
// Quick script to check recent password reset URLs from PHP error log
echo "Password Reset URL Checker\n";
echo "=========================\n\n";

// Common PHP error log locations
$logPaths = [
    'C:\xampp\php\logs\php_error_log',
    'C:\xampp\apache\logs\error.log',
    'C:\xampp\logs\php_error_log',
    'C:\php\logs\php_error_log'
];

$found = false;

foreach ($logPaths as $logPath) {
    if (file_exists($logPath)) {
        echo "Found log file: {$logPath}\n";
        
        // Read last 50 lines of the log
        $lines = file($logPath);
        $recentLines = array_slice($lines, -50);
        
        echo "Searching for password reset URLs...\n\n";
        
        foreach ($recentLines as $line) {
            if (strpos($line, 'Password reset email for') !== false) {
                echo "Found: " . trim($line) . "\n";
                
                // Extract the URL
                if (preg_match('/http:\/\/localhost\/adamson-ccit\/[^\s]+/', $line, $matches)) {
                    echo "Reset URL: " . $matches[0] . "\n";
                    echo "Copy this URL and paste it in your browser to test!\n\n";
                }
            }
        }
        $found = true;
        break;
    }
}

if (!$found) {
    echo "Could not find PHP error log. Try checking:\n";
    foreach ($logPaths as $path) {
        echo "- {$path}\n";
    }
    echo "\nOr check your XAMPP control panel for log file locations.\n";
}

echo "\nTip: After submitting the forgot password form, check this script again for new URLs!\n";
?>