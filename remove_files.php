<?php
// Script to remove unused faculty management files

$filesToRemove = [
    __DIR__ . '/app/views/faculty_manage_events.php',
    __DIR__ . '/app/views/faculty_manage_announcements.php'
];

foreach ($filesToRemove as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "Removed: " . basename($file) . "\n";
        } else {
            echo "Failed to remove: " . basename($file) . "\n";
        }
    } else {
        echo "File not found: " . basename($file) . "\n";
    }
}

echo "File removal complete.\n";
?>
