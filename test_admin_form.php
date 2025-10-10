<?php
require_once 'app/models/Announcement.php';

echo "=== TESTING ADMIN FORM SIMULATION ===\n";

// Simulate admin form submission
$_POST = [
    'edit_announcement' => '1',
    'id' => '1',
    'title' => 'Admin Test: Updated Title',
    'content' => 'Admin Test: This is the updated content from admin form',
    'category' => 'deadline',
    'date' => '2025-02-15',
    'edit_status' => 'published',
    'current_image_url' => '/adamson-ccit/public/uploads/announcements/1759950050-alumni-basketball.jpg'
];

$_FILES = []; // No new image upload

try {
    // Simulate the admin processing logic
    if (!empty($_POST['edit_announcement']) && !empty($_POST['id'])) {
        $announcementId = (int)$_POST['id'];
        $imageUrl = null;
        $currentImageUrl = trim($_POST['current_image_url'] ?? '');
        
        // Handle image upload or removal
        if (!empty($_POST['remove_image'])) {
            $imageUrl = null;
        } elseif (!empty($_FILES['image']['tmp_name'])) {
            // Would handle upload here
            $imageUrl = '/new/image/path.jpg';
        } else {
            // No new image uploaded and not removing, keep current image
            $imageUrl = $currentImageUrl ?: null;
        }
        
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
            'category' => $_POST['category'] ?? 'general',
            'date' => $_POST['date'] ?? date('Y-m-d'),
            'status' => $_POST['edit_status'] ?? 'draft',
            'image_url' => $imageUrl
        ];
        
        echo "Update data to be sent:\n";
        print_r($data);
        echo "\n";
        
        $result = Announcement::update($announcementId, $data);
        
        if ($result) {
            echo "✅ Admin form simulation successful!\n\n";
            
            // Fetch and display updated data
            $updated = Announcement::get($announcementId);
            if ($updated) {
                echo "Updated record from database:\n";
                echo "- ID: " . $updated['id'] . "\n";
                echo "- Title: " . $updated['title'] . "\n";
                echo "- Content: " . substr($updated['body'], 0, 100) . "...\n";
                echo "- Category: " . $updated['category'] . "\n";
                echo "- Date: " . $updated['date'] . "\n";
                echo "- Status: " . $updated['status'] . "\n";
                echo "- Image URL: " . ($updated['image_url'] ?? 'NULL') . "\n";
                echo "- Updated At: " . $updated['updated_at'] . "\n";
            }
        } else {
            echo "❌ Admin form simulation failed!\n";
        }
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    echo 'Stack trace: ' . $e->getTraceAsString() . "\n";
}
?>