<?php
require_once 'app/models/Announcement.php';

// Test the complete flow
echo "=== COMPLETE ANNOUNCEMENT UPDATE FLOW TEST ===\n\n";

// 1. Get current announcements
echo "1. Current announcements:\n";
$announcements = Announcement::list();
if (!empty($announcements)) {
    foreach ($announcements as $a) {
        echo "ID: {$a['id']}, Title: {$a['title']}, Body content: " . substr($a['body'] ?? 'NULL', 0, 50) . "...\n";
    }
} else {
    echo "No announcements found!\n";
}

echo "\n";

// 2. Test getting specific announcement for edit
$testId = !empty($announcements) ? $announcements[0]['id'] : 1;
echo "2. Getting announcement ID $testId for editing:\n";
$announcement = Announcement::get($testId);
if ($announcement) {
    echo "Found: ID {$announcement['id']}, Title: {$announcement['title']}\n";
    echo "Current body: " . ($announcement['body'] ?? 'NULL') . "\n";
} else {
    echo "Announcement not found!\n";
    exit;
}

echo "\n";

// 3. Simulate admin form update
echo "3. Simulating admin form update:\n";
$updateData = [
    'title' => $announcement['title'] . ' [UPDATED]',
    'content' => 'This is updated content from complete flow test - ' . date('Y-m-d H:i:s'),
    'category' => $announcement['category'] ?? 'general',
    'date' => date('Y-m-d'),
    'status' => 'published',
    'image_url' => $announcement['image_url'] ?? null
];

echo "Update data:\n";
print_r($updateData);

$result = Announcement::update($testId, $updateData);
echo "Update result: " . ($result ? "SUCCESS" : "FAILED") . "\n\n";

// 4. Verify the update
echo "4. Verifying the update:\n";
$updated = Announcement::get($testId);
if ($updated) {
    echo "After update - Title: {$updated['title']}\n";
    echo "After update - Body: " . ($updated['body'] ?? 'NULL') . "\n";
    echo "After update - Status: {$updated['status']}\n";
} else {
    echo "Could not retrieve updated announcement!\n";
}

echo "\n";

// 5. Test direct database query
echo "5. Direct database verification:\n";
try {
    $db = Model::db();
    $stmt = $db->prepare("SELECT id, title, body, status FROM announcements WHERE id = ?");
    $stmt->execute([$testId]);
    $directResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($directResult) {
        echo "Direct DB - Title: {$directResult['title']}\n";
        echo "Direct DB - Body: " . ($directResult['body'] ?? 'NULL') . "\n";
        echo "Direct DB - Status: {$directResult['status']}\n";
    } else {
        echo "No direct database result!\n";
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>