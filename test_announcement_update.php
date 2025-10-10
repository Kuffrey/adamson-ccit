<?php
require_once 'app/models/Announcement.php';

try {
    echo "=== TESTING ANNOUNCEMENT UPDATE ===\n";
    
    // Get the first announcement
    $announcements = Announcement::list();
    if (empty($announcements)) {
        echo "No announcements found!\n";
        exit;
    }
    
    $testAnnouncement = $announcements[0];
    $id = $testAnnouncement['id'];
    
    echo "Original announcement ID: $id\n";
    echo "Original title: " . $testAnnouncement['title'] . "\n";
    echo "Original content: " . substr($testAnnouncement['body'], 0, 50) . "...\n";
    echo "Original image_url: " . ($testAnnouncement['image_url'] ?? 'NULL') . "\n\n";
    
    // Test update
    $updateData = [
        'title' => 'UPDATED: ' . $testAnnouncement['title'],
        'content' => 'This is updated content: ' . $testAnnouncement['body'],
        'category' => 'advisory',
        'date' => '2025-01-15',
        'status' => 'published',
        'image_url' => '/test/path/image.jpg'
    ];
    
    echo "Attempting to update announcement...\n";
    $result = Announcement::update($id, $updateData);
    
    if ($result) {
        echo "Update successful!\n\n";
        
        // Fetch updated data
        $updated = Announcement::get($id);
        if ($updated) {
            echo "Updated title: " . $updated['title'] . "\n";
            echo "Updated content: " . substr($updated['body'], 0, 50) . "...\n";
            echo "Updated category: " . $updated['category'] . "\n";
            echo "Updated image_url: " . ($updated['image_url'] ?? 'NULL') . "\n";
        } else {
            echo "Could not fetch updated data!\n";
        }
    } else {
        echo "Update failed!\n";
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    echo 'Stack trace: ' . $e->getTraceAsString() . "\n";
}
?>