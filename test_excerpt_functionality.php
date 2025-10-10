<?php
require_once 'app/models/Announcement.php';

echo "=== TESTING EXCERPT FUNCTIONALITY ===\n\n";

// Test 1: Get current announcements with excerpt
echo "1. Current announcements with excerpt:\n";
$announcements = Announcement::list();
foreach ($announcements as $a) {
    echo "ID: {$a['id']}\n";
    echo "Title: {$a['title']}\n";
    echo "Excerpt: " . ($a['excerpt'] ?? 'NULL') . "\n";
    echo "Body: " . substr($a['body'], 0, 50) . "...\n";
    echo "---\n";
}

// Test 2: Test creating a new announcement with excerpt
echo "\n2. Testing create with excerpt:\n";
try {
    $newId = Announcement::create(
        'Test Announcement with Excerpt',
        'This is the full content of the test announcement. It contains detailed information about the topic.',
        'draft',
        'general',
        date('Y-m-d'),
        null,
        'This is a test excerpt for the announcement card display.'
    );
    echo "Created announcement ID: $newId\n";
    
    // Verify the creation
    $created = Announcement::get($newId);
    echo "Verification:\n";
    echo "Title: {$created['title']}\n";
    echo "Excerpt: {$created['excerpt']}\n";
    echo "Content: " . substr($created['body'], 0, 50) . "...\n";
    
    // Clean up - delete the test announcement
    Announcement::delete($newId);
    echo "Test announcement deleted.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Test 3: Test updating existing announcement with excerpt
echo "\n3. Testing update with excerpt:\n";
$testId = $announcements[0]['id'] ?? 1;
$updateData = [
    'title' => 'Updated Title with Excerpt',
    'excerpt' => 'Updated excerpt for testing purposes.',
    'content' => 'Updated content for the announcement.',
    'category' => 'general',
    'status' => 'draft'
];

$result = Announcement::update($testId, $updateData);
echo "Update result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";

if ($result) {
    $updated = Announcement::get($testId);
    echo "Updated announcement:\n";
    echo "Title: {$updated['title']}\n";
    echo "Excerpt: {$updated['excerpt']}\n";
    echo "Content: " . substr($updated['body'], 0, 50) . "...\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>