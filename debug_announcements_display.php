<?php
require_once 'app/models/Announcement.php';

echo "=== DEBUGGING ANNOUNCEMENTS DISPLAY ISSUE ===\n\n";

// 1. Check what's in the database
echo "1. Direct database query - all announcements:\n";
try {
    $db = Model::db();
    $stmt = $db->query('SELECT * FROM announcements ORDER BY id DESC');
    $allAnnouncements = $stmt->fetchAll();
    
    if (empty($allAnnouncements)) {
        echo "NO ANNOUNCEMENTS FOUND IN DATABASE!\n";
    } else {
        foreach ($allAnnouncements as $a) {
            echo "ID: {$a['id']}\n";
            echo "Title: {$a['title']}\n";
            echo "Status: {$a['status']}\n";
            echo "Category: {$a['category']}\n";
            echo "Date: {$a['date']}\n";
            echo "Published At: {$a['published_at']}\n";
            echo "Excerpt: " . ($a['excerpt'] ?? 'NULL') . "\n";
            echo "Body: " . substr($a['body'], 0, 50) . "...\n";
            echo "---\n";
        }
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

// 2. Test Announcement::list() method
echo "\n2. Testing Announcement::list() method:\n";
try {
    $listAll = Announcement::list();
    echo "Count from list(): " . count($listAll) . "\n";
    if (!empty($listAll)) {
        foreach ($listAll as $a) {
            echo "ID: {$a['id']}, Title: {$a['title']}, Status: {$a['status']}\n";
        }
    } else {
        echo "No announcements returned by list() method\n";
    }
} catch (Exception $e) {
    echo "Error in list(): " . $e->getMessage() . "\n";
}

// 3. Test searchPublished() method (what the announcements page uses)
echo "\n3. Testing Announcement::searchPublished() method:\n";
try {
    $searchResult = Announcement::searchPublished(
        ['cat' => null, 'year' => null, 'q' => null],
        ['page' => 1, 'perPage' => 10]
    );
    
    echo "Search result structure:\n";
    echo "Total: " . ($searchResult['total'] ?? 'NULL') . "\n";
    echo "Items count: " . count($searchResult['items'] ?? []) . "\n";
    
    if (!empty($searchResult['items'])) {
        echo "Published announcements:\n";
        foreach ($searchResult['items'] as $item) {
            echo "ID: {$item['id']}, Title: {$item['title']}, Status: {$item['status']}\n";
        }
    } else {
        echo "No published announcements found!\n";
    }
} catch (Exception $e) {
    echo "Error in searchPublished(): " . $e->getMessage() . "\n";
}

// 4. Check for published announcements specifically
echo "\n4. Checking for published announcements directly:\n";
try {
    $db = Model::db();
    $stmt = $db->query("SELECT * FROM announcements WHERE status = 'published' ORDER BY id DESC");
    $published = $stmt->fetchAll();
    
    echo "Published announcements count: " . count($published) . "\n";
    if (!empty($published)) {
        foreach ($published as $p) {
            echo "ID: {$p['id']}, Title: {$p['title']}, Date: {$p['date']}, Published At: {$p['published_at']}\n";
        }
    } else {
        echo "No announcements with status 'published' found!\n";
    }
} catch (Exception $e) {
    echo "Error checking published: " . $e->getMessage() . "\n";
}

echo "\n=== DIAGNOSIS COMPLETE ===\n";
?>