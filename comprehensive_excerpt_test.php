<?php
require_once 'app/models/Announcement.php';

echo "=== COMPREHENSIVE EXCERPT FUNCTIONALITY TEST ===\n\n";

// Test 1: Check current announcements and their excerpts
echo "1. Current announcements:\n";
$announcements = Announcement::list();
foreach ($announcements as $a) {
    echo "ID: {$a['id']}\n";
    echo "Title: {$a['title']}\n";
    echo "Excerpt: " . ($a['excerpt'] ?? 'NULL') . "\n";
    echo "Has excerpt: " . (empty($a['excerpt']) ? 'NO' : 'YES') . "\n";
    echo "---\n";
}

// Test 2: Test admin form simulation
echo "\n2. Simulating admin form submission with excerpt:\n";
$_POST = [
    'add_announcement' => '1',
    'title' => 'Test Admin Form with Excerpt',
    'excerpt' => 'Admin form excerpt test - short description for cards.',
    'content' => 'Full content from admin form test. This is the complete announcement content.',
    'category' => 'general',
    'status' => 'draft',
    'date' => date('Y-m-d')
];

echo "Simulated POST data:\n";
foreach ($_POST as $key => $value) {
    echo "$key: $value\n";
}

// Create the announcement
$title = trim($_POST['title']);
$content = trim($_POST['content'] ?? '');
$excerpt = trim($_POST['excerpt'] ?? '');
$category = $_POST['category'] ?? 'general';
$status = $_POST['status'] ?? 'draft';
$date = $_POST['date'] ?? date('Y-m-d');

$newId = Announcement::create($title, $content, $status, $category, $date, null, $excerpt);
echo "\nCreated announcement ID: $newId\n";

// Verify creation
$created = Announcement::get($newId);
echo "Verification:\n";
echo "Title: {$created['title']}\n";
echo "Excerpt: {$created['excerpt']}\n";
echo "Content: " . substr($created['body'], 0, 50) . "...\n";

// Test 3: Test edit form simulation  
echo "\n3. Simulating edit form submission:\n";
$_POST = [
    'edit_announcement' => '1',
    'id' => $newId,
    'title' => 'Updated Admin Form with New Excerpt',
    'excerpt' => 'Updated excerpt from edit form - modified description.',
    'content' => 'Updated full content from edit form.',
    'category' => 'advisory',
    'edit_status' => 'published',
    'date' => date('Y-m-d')
];

$updateData = [
    'title' => trim($_POST['title'] ?? ''),
    'excerpt' => trim($_POST['excerpt'] ?? ''),
    'content' => trim($_POST['content'] ?? ''),
    'category' => $_POST['category'] ?? 'general',
    'date' => $_POST['date'] ?? date('Y-m-d'),
    'status' => $_POST['edit_status'] ?? 'draft'
];

echo "Update data:\n";
foreach ($updateData as $key => $value) {
    echo "$key: $value\n";
}

$updateResult = Announcement::update($newId, $updateData);
echo "\nUpdate result: " . ($updateResult ? 'SUCCESS' : 'FAILED') . "\n";

// Verify update
$updated = Announcement::get($newId);
echo "After update:\n";
echo "Title: {$updated['title']}\n";
echo "Excerpt: {$updated['excerpt']}\n";
echo "Content: " . substr($updated['body'], 0, 50) . "...\n";
echo "Status: {$updated['status']}\n";

// Test 4: Test how it appears in list
echo "\n4. How it appears in announcement list:\n";
$listResult = Announcement::searchPublished(['cat' => null, 'year' => null, 'q' => null], ['page' => 1, 'perPage' => 10]);
$items = $listResult['items'] ?? [];

foreach ($items as $item) {
    if ($item['id'] == $newId) {
        echo "Found in published list:\n";
        echo "Title: {$item['title']}\n";
        echo "Excerpt: " . ($item['excerpt'] ?? 'NULL') . "\n";
        echo "Body: " . substr($item['body'] ?? $item['content'] ?? '', 0, 50) . "...\n";
        break;
    }
}

// Clean up
Announcement::delete($newId);
echo "\nTest announcement deleted.\n";

echo "\n=== ALL TESTS COMPLETE ===\n";
?>