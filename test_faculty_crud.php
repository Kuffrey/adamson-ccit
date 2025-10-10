<?php
require_once 'app/models/FacultyProfile.php';

echo "=== TESTING FACULTY PROFILE ADMIN OPERATIONS ===\n\n";

// Test create operation
echo "1. Testing create operation:\n";
$testData = [
    'name' => 'Test Faculty Member',
    'dept' => 'cs',
    'role' => 'full',
    'title' => 'Test Professor',
    'avatar_url' => '',
    'avatar_initials' => 'TF',
    'badges' => 'CS,Test',
    'ordering' => 99
];

try {
    $result = FacultyProfile::create($testData);
    echo "Create result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    if ($result) {
        // Get the created faculty member
        $db = Model::db();
        $stmt = $db->query('SELECT * FROM faculty_profile WHERE name = "Test Faculty Member"');
        $created = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($created) {
            echo "Created faculty member:\n";
            print_r($created);
            
            $testId = $created['id'];
            
            // Test update operation
            echo "\n2. Testing update operation:\n";
            $updateData = [
                'name' => 'Updated Test Faculty',
                'dept' => 'itis',
                'role' => 'part',
                'title' => 'Updated Test Professor',
                'avatar_url' => 'https://example.com/avatar.jpg',
                'avatar_initials' => 'UT',
                'badges' => 'ITIS,Updated',
                'ordering' => 88
            ];
            
            $updateResult = FacultyProfile::update($testId, $updateData);
            echo "Update result: " . ($updateResult ? 'SUCCESS' : 'FAILED') . "\n";
            
            if ($updateResult) {
                $updated = FacultyProfile::getById($testId);
                echo "Updated faculty member:\n";
                print_r($updated);
            }
            
            // Test delete operation
            echo "\n3. Testing delete operation:\n";
            $deleteResult = FacultyProfile::delete($testId);
            echo "Delete result: " . ($deleteResult ? 'SUCCESS' : 'FAILED') . "\n";
            
            // Verify deletion
            $deleted = FacultyProfile::getById($testId);
            echo "Faculty member after deletion: " . ($deleted ? 'STILL EXISTS' : 'DELETED') . "\n";
        } else {
            echo "Could not find created faculty member\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CRUD OPERATIONS TEST COMPLETE ===\n";
?>