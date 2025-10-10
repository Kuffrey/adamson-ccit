<?php
require_once 'app/models/Announcement.php';

echo "=== FIXING ANNOUNCEMENT STATUS ===\n\n";

// Get all announcements
$db = Model::db();
$stmt = $db->query('SELECT * FROM announcements');
$announcements = $stmt->fetchAll();

echo "Current announcements:\n";
foreach ($announcements as $a) {
    echo "ID: {$a['id']}, Title: {$a['title']}, Status: {$a['status']}\n";
}

// Update the status to published for existing announcements
echo "\nUpdating announcements to published status...\n";
foreach ($announcements as $a) {
    if ($a['status'] !== 'published') {
        $result = Announcement::updateStatus($a['id'], 'published');
        echo "Updated ID {$a['id']} to published: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    }
}

// Verify the update
echo "\nAfter update:\n";
$stmt = $db->query('SELECT * FROM announcements');
$updated = $stmt->fetchAll();
foreach ($updated as $a) {
    echo "ID: {$a['id']}, Title: {$a['title']}, Status: {$a['status']}\n";
}

// Test searchPublished again
echo "\nTesting searchPublished after update:\n";
$searchResult = Announcement::searchPublished(
    ['cat' => null, 'year' => null, 'q' => null],
    ['page' => 1, 'perPage' => 10]
);

echo "Total published: " . ($searchResult['total'] ?? 0) . "\n";
echo "Items found: " . count($searchResult['items'] ?? []) . "\n";

if (!empty($searchResult['items'])) {
    foreach ($searchResult['items'] as $item) {
        echo "Found: ID {$item['id']}, Title: {$item['title']}\n";
    }
}

echo "\n=== FIX COMPLETE ===\n";
?>