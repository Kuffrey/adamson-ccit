<?php
// Test script to verify the faculty admin interface is working
echo "=== FACULTY ADMIN DATABASE INTERFACE VERIFICATION ===\n\n";

// Check if the FacultyProfile model is accessible
require_once 'app/models/FacultyProfile.php';

echo "1. Testing FacultyProfile model access:\n";
try {
    $faculty = FacultyProfile::getAll();
    echo "✓ Successfully retrieved " . count($faculty) . " faculty members\n";
    
    // Show sample data structure
    if (!empty($faculty)) {
        echo "Sample faculty member structure:\n";
        $sample = $faculty[0];
        foreach ($sample as $field => $value) {
            echo "  $field: " . (is_null($value) ? 'NULL' : (strlen($value) > 30 ? substr($value, 0, 30) . '...' : $value)) . "\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Check if the FacultyProfilePageSettings model is accessible
echo "\n2. Testing page settings:\n";
try {
    require_once 'app/models/FacultyProfilePageSettings.php';
    $settings = FacultyProfilePageSettings::getSettings();
    echo "✓ Successfully retrieved page settings\n";
    if (!empty($settings)) {
        foreach ($settings as $key => $value) {
            echo "  $key: " . (is_null($value) ? 'NULL' : (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value)) . "\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Error accessing settings: " . $e->getMessage() . "\n";
}

// Check if upload directory exists and is writable
echo "\n3. Testing upload directory:\n";
$uploadDir = __DIR__ . '/public/uploads/faculty/';
if (!is_dir($uploadDir)) {
    if (mkdir($uploadDir, 0755, true)) {
        echo "✓ Created upload directory: $uploadDir\n";
    } else {
        echo "✗ Failed to create upload directory: $uploadDir\n";
    }
} else {
    echo "✓ Upload directory exists: $uploadDir\n";
}

if (is_writable($uploadDir)) {
    echo "✓ Upload directory is writable\n";
} else {
    echo "✗ Upload directory is not writable\n";
}

// Test if all required fields are being handled correctly
echo "\n4. Testing faculty data structure:\n";
if (!empty($faculty)) {
    $sample = $faculty[0];
    $requiredFields = ['id', 'name', 'dept', 'role', 'title', 'avatar_url', 'avatar_initials', 'badges', 'ordering'];
    
    foreach ($requiredFields as $field) {
        if (array_key_exists($field, $sample)) {
            echo "✓ Field '$field' is available\n";
        } else {
            echo "✗ Field '$field' is missing\n";
        }
    }
}

// Test CRUD operations are working
echo "\n5. Testing basic CRUD operations:\n";
try {
    // Test create
    $testData = [
        'name' => 'Verification Test Faculty',
        'dept' => 'cs',
        'role' => 'full',
        'title' => 'Test Professor',
        'avatar_url' => '',
        'avatar_initials' => 'VT',
        'badges' => 'Test',
        'ordering' => 999
    ];
    
    $createResult = FacultyProfile::create($testData);
    echo "✓ Create operation: " . ($createResult ? 'SUCCESS' : 'FAILED') . "\n";
    
    if ($createResult) {
        // Find the created faculty
        $db = Model::db();
        $stmt = $db->query('SELECT * FROM faculty_profile WHERE name = "Verification Test Faculty"');
        $created = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($created) {
            $testId = $created['id'];
            echo "✓ Created faculty with ID: $testId\n";
            
            // Test update
            $updateData = [
                'name' => 'Updated Verification Test Faculty',
                'dept' => 'itis',
                'role' => 'part',
                'title' => 'Updated Test Professor',
                'avatar_url' => 'https://example.com/test.jpg',
                'avatar_initials' => 'UV',
                'badges' => 'Updated,Test',
                'ordering' => 888
            ];
            
            $updateResult = FacultyProfile::update($testId, $updateData);
            echo "✓ Update operation: " . ($updateResult ? 'SUCCESS' : 'FAILED') . "\n";
            
            // Test delete
            $deleteResult = FacultyProfile::delete($testId);
            echo "✓ Delete operation: " . ($deleteResult ? 'SUCCESS' : 'FAILED') . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ CRUD test error: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
echo "The faculty admin interface should now properly reflect the database table structure.\n";
?>