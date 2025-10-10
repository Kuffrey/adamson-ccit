<?php
require_once 'app/models/FacultyProfile.php';

echo "=== TESTING UPDATED FACULTY PROFILE MODEL ===\n\n";

try {
    // Test getAll method
    echo "1. Testing getAll() method:\n";
    $faculty = FacultyProfile::getAll();
    echo "Count: " . count($faculty) . "\n";
    
    if (!empty($faculty)) {
        foreach ($faculty as $f) {
            echo "ID: {$f['id']}\n";
            echo "  Name: {$f['name']}\n";
            echo "  Dept: {$f['dept']}\n";
            echo "  Role: {$f['role']}\n";
            echo "  Title: {$f['title']}\n";
            echo "  Avatar URL: " . ($f['avatar_url'] ?? 'NULL') . "\n";
            echo "  Avatar Initials: " . ($f['avatar_initials'] ?? 'NULL') . "\n";
            echo "  Badges: " . ($f['badges'] ?? 'NULL') . "\n";
            echo "  Ordering: " . ($f['ordering'] ?? 'NULL') . "\n";
            echo "---\n";
        }
    }
    
    // Test getById method
    if (!empty($faculty)) {
        $testId = $faculty[0]['id'];
        echo "\n2. Testing getById($testId) method:\n";
        $single = FacultyProfile::getById($testId);
        if ($single) {
            print_r($single);
        } else {
            echo "Not found\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>