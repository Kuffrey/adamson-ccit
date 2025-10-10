<?php
require_once 'app/models/Announcement.php';

echo "=== TESTING EXCERPT UPDATE ===\n\n";

// Get current announcement
$announcements = Announcement::list();
$testId = $announcements[0]['id'] ?? 1;

echo "Before update:\n";
$before = Announcement::get($testId);
echo "Title: {$before['title']}\n";
echo "Excerpt: " . ($before['excerpt'] ?? 'NULL') . "\n";
echo "Content: " . substr($before['body'], 0, 50) . "...\n\n";

// Test update with excerpt
$updateData = [
    'title' => 'Test Update with New Excerpt',
    'excerpt' => 'This is a completely new excerpt for testing.',
    'content' => 'This is updated content.',
    'category' => 'general',
    'status' => 'draft'
];

echo "Updating with data:\n";
print_r($updateData);

$result = Announcement::update($testId, $updateData);
echo "Update result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n\n";

if ($result) {
    echo "After update:\n";
    $after = Announcement::get($testId);
    echo "Title: {$after['title']}\n";
    echo "Excerpt: " . ($after['excerpt'] ?? 'NULL') . "\n";
    echo "Content: " . substr($after['body'], 0, 50) . "...\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>