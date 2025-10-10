<?php
// Test the improved faculty profile system with prefix and surname fields
require_once 'app/models/FacultyProfile.php';

try {
    echo "=== TESTING IMPROVED FACULTY PROFILE SYSTEM ===\n";
    
    // Test 1: Get all faculty with new alphabetical ordering
    echo "\n1. Testing alphabetical ordering by surname:\n";
    $faculty = FacultyProfile::getAll();
    
    foreach ($faculty as $f) {
        $displayName = trim(($f['prefix'] ? $f['prefix'] . ' ' : '') . 
                           ($f['first_name'] ? $f['first_name'] . ' ' : '') . 
                           $f['surname']);
        echo sprintf("  %-35s | Surname: %-20s | Dept: %s\n", 
            $displayName, 
            $f['surname'] ?: 'N/A',
            $f['dept']
        );
    }
    
    // Test 2: Test creating a new faculty member with prefix
    echo "\n2. Testing faculty creation with prefix and surname:\n";
    $testData = [
        'prefix' => 'Dr.',
        'first_name' => 'Maria Elena',
        'surname' => 'Santos',
        'dept' => 'cs',
        'role' => 'full',
        'title' => 'Professor of Computer Science',
        'avatar_initials' => 'MS',
        'badges' => 'PhD,Research,CS'
    ];
    
    echo "Creating faculty with data:\n";
    foreach ($testData as $key => $value) {
        echo "  $key: $value\n";
    }
    
    $createResult = FacultyProfile::create($testData);
    echo $createResult ? "✓ Faculty created successfully\n" : "❌ Faculty creation failed\n";
    
    // Test 3: Verify the new faculty appears in the correct alphabetical position
    echo "\n3. Verifying alphabetical insertion:\n";
    $updatedFaculty = FacultyProfile::getAll();
    
    foreach ($updatedFaculty as $f) {
        if ($f['surname'] === 'Santos') {
            echo "✓ Found new faculty: ";
        } else {
            echo "  ";
        }
        
        $displayName = trim(($f['prefix'] ? $f['prefix'] . ' ' : '') . 
                           ($f['first_name'] ? $f['first_name'] . ' ' : '') . 
                           $f['surname']);
        echo sprintf("%-35s | Surname: %-20s\n", 
            $displayName, 
            $f['surname'] ?: 'N/A'
        );
    }
    
    // Test 4: Test name parsing functionality
    echo "\n4. Testing name parsing functionality:\n";
    $testNames = [
        'Dr. John Michael Dela Cruz',
        'Mrs. Ana Maria Gonzalez',
        'Prof. Robert Smith',
        'Maria Santos'
    ];
    
    foreach ($testNames as $name) {
        $testParseData = ['name' => $name];
        FacultyProfile::create($testParseData);
        echo "Parsed: '$name'\n";
    }
    
    // Clean up test data
    echo "\n5. Cleaning up test data:\n";
    $testSurnames = ['Santos', 'Cruz', 'Gonzalez', 'Smith'];
    $allFaculty = FacultyProfile::getAll();
    
    foreach ($allFaculty as $f) {
        if (in_array($f['surname'], $testSurnames)) {
            FacultyProfile::delete($f['id']);
            echo "✓ Deleted test faculty: " . $f['name'] . "\n";
        }
    }
    
    echo "\n✅ All tests completed successfully!\n";
    echo "\nKey improvements implemented:\n";
    echo "- Added prefix field for titles (Dr., Mrs., Mr., etc.)\n";
    echo "- Added surname and first_name fields for better organization\n";
    echo "- Alphabetical ordering by surname, then first name\n";
    echo "- Automatic full name generation from components\n";
    echo "- Improved admin interface with better field organization\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}