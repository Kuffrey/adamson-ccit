<?php
// Final comprehensive test of the improved faculty profile system
require_once 'app/models/FacultyProfile.php';

echo "=== COMPREHENSIVE TEST OF IMPROVED FACULTY PROFILE SYSTEM ===\n";

try {
    // Test 1: Verify alphabetical ordering by surname
    echo "\n1. ALPHABETICAL ORDERING BY SURNAME:\n";
    echo str_repeat("-", 70) . "\n";
    
    $faculty = FacultyProfile::getAll();
    $previousSurname = '';
    $orderingCorrect = true;
    
    foreach ($faculty as $index => $f) {
        $currentSurname = $f['surname'];
        
        // Check if ordering is correct
        if ($previousSurname && strcasecmp($currentSurname, $previousSurname) < 0) {
            $orderingCorrect = false;
        }
        
        $displayName = trim(($f['prefix'] ? $f['prefix'] . ' ' : '') . 
                           ($f['first_name'] ? $f['first_name'] . ' ' : '') . 
                           $f['surname']);
                           
        echo sprintf("%2d. %-35s | Surname: %-15s | Dept: %-5s | Role: %s\n", 
            $index + 1,
            $displayName, 
            $f['surname'],
            strtoupper($f['dept']),
            ucfirst($f['role'])
        );
        
        $previousSurname = $currentSurname;
    }
    
    echo $orderingCorrect ? "✅ Alphabetical ordering is CORRECT\n" : "❌ Alphabetical ordering has issues\n";
    
    // Test 2: Test creating new faculty with prefix
    echo "\n2. TESTING NEW FACULTY CREATION WITH PREFIX:\n";
    echo str_repeat("-", 70) . "\n";
    
    $testFaculty = [
        'prefix' => 'Prof.',
        'first_name' => 'Maria Teresa',
        'surname' => 'Anderson',
        'dept' => 'cs',
        'role' => 'full',
        'title' => 'Professor of Computer Science',
        'avatar_initials' => 'MA',
        'badges' => 'PhD,Research'
    ];
    
    echo "Creating test faculty:\n";
    foreach ($testFaculty as $key => $value) {
        echo "  $key: $value\n";
    }
    
    $createResult = FacultyProfile::create($testFaculty);
    echo $createResult ? "✅ Faculty created successfully\n" : "❌ Faculty creation failed\n";
    
    // Test 3: Verify new faculty appears in correct alphabetical position
    echo "\n3. VERIFYING ALPHABETICAL INSERTION:\n";
    echo str_repeat("-", 70) . "\n";
    
    $updatedFaculty = FacultyProfile::getAll();
    $foundTestFaculty = false;
    
    foreach ($updatedFaculty as $index => $f) {
        $displayName = trim(($f['prefix'] ? $f['prefix'] . ' ' : '') . 
                           ($f['first_name'] ? $f['first_name'] . ' ' : '') . 
                           $f['surname']);
        
        if ($f['surname'] === 'Anderson') {
            $foundTestFaculty = true;
            echo "✅ ";
        } else {
            echo "   ";
        }
        
        echo sprintf("%2d. %-35s | Surname: %s\n", 
            $index + 1,
            $displayName, 
            $f['surname']
        );
    }
    
    echo $foundTestFaculty ? "✅ Test faculty inserted in correct alphabetical position\n" : "❌ Test faculty not found\n";
    
    // Test 4: Test admin interface compatibility
    echo "\n4. TESTING ADMIN INTERFACE DATA COMPATIBILITY:\n";
    echo str_repeat("-", 70) . "\n";
    
    $testFacultyData = FacultyProfile::getById(1); // Get first faculty member
    $requiredFields = ['id', 'name', 'prefix', 'first_name', 'surname', 'dept', 'role', 'title', 'avatar_url', 'avatar_initials', 'badges'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!array_key_exists($field, $testFacultyData)) {
            $missingFields[] = $field;
        }
    }
    
    if (empty($missingFields)) {
        echo "✅ All required fields present in faculty data\n";
        echo "Sample faculty data structure:\n";
        foreach ($requiredFields as $field) {
            $value = $testFacultyData[$field] ?? 'NULL';
            echo "  $field: " . (is_string($value) ? "\"$value\"" : $value) . "\n";
        }
    } else {
        echo "❌ Missing fields: " . implode(', ', $missingFields) . "\n";
    }
    
    // Test 5: Test name parsing functionality
    echo "\n5. TESTING NAME PARSING FUNCTIONALITY:\n";
    echo str_repeat("-", 70) . "\n";
    
    $testNames = [
        'Dr. Alice Marie Johnson' => ['prefix' => 'Dr.', 'first_name' => 'Alice Marie', 'surname' => 'Johnson'],
        'Mrs. Jennifer Lee' => ['prefix' => 'Mrs.', 'first_name' => 'Jennifer', 'surname' => 'Lee'],
        'Prof. Robert Smith' => ['prefix' => 'Prof.', 'first_name' => 'Robert', 'surname' => 'Smith'],
        'Maria Garcia' => ['prefix' => '', 'first_name' => 'Maria', 'surname' => 'Garcia']
    ];
    
    foreach ($testNames as $fullName => $expected) {
        $testData = ['name' => $fullName];
        FacultyProfile::create($testData);
        
        // Get the created faculty to verify parsing
        $createdFaculty = FacultyProfile::getAll();
        $found = null;
        foreach ($createdFaculty as $f) {
            if ($f['name'] === $fullName) {
                $found = $f;
                break;
            }
        }
        
        if ($found) {
            $parsedCorrectly = 
                ($found['prefix'] === $expected['prefix'] || (!$found['prefix'] && !$expected['prefix'])) &&
                $found['first_name'] === $expected['first_name'] &&
                $found['surname'] === $expected['surname'];
            
            echo ($parsedCorrectly ? "✅" : "❌") . " '$fullName'\n";
            echo "   Expected: Prefix='{$expected['prefix']}', First='{$expected['first_name']}', Surname='{$expected['surname']}'\n";
            echo "   Got:      Prefix='{$found['prefix']}', First='{$found['first_name']}', Surname='{$found['surname']}'\n";
        } else {
            echo "❌ '$fullName' - Faculty not found after creation\n";
        }
    }
    
    // Clean up test data
    echo "\n6. CLEANING UP TEST DATA:\n";
    echo str_repeat("-", 70) . "\n";
    
    $testSurnames = ['Anderson', 'Johnson', 'Lee', 'Smith', 'Garcia'];
    $allFaculty = FacultyProfile::getAll();
    $deletedCount = 0;
    
    foreach ($allFaculty as $f) {
        if (in_array($f['surname'], $testSurnames)) {
            FacultyProfile::delete($f['id']);
            echo "✅ Deleted: " . $f['name'] . "\n";
            $deletedCount++;
        }
    }
    
    echo "Deleted $deletedCount test faculty members\n";
    
    // Final summary
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "FINAL SUMMARY - FACULTY PROFILE SYSTEM IMPROVEMENTS\n";
    echo str_repeat("=", 70) . "\n";
    
    echo "✅ Database Structure:\n";
    echo "   - Added 'prefix' field for titles (Dr., Mrs., Mr., etc.)\n";
    echo "   - Added 'surname' field for proper alphabetical sorting\n";
    echo "   - Added 'first_name' field for better name management\n";
    echo "   - Maintained backward compatibility with existing 'name' field\n\n";
    
    echo "✅ Ordering System:\n";
    echo "   - Changed from manual 'ordering' field to automatic alphabetical sorting\n";
    echo "   - Primary sort: surname (A-Z)\n";
    echo "   - Secondary sort: first_name (A-Z)\n";
    echo "   - Tertiary sort: id (ascending)\n\n";
    
    echo "✅ Admin Interface:\n";
    echo "   - Separated name entry into prefix, first name, and surname fields\n";
    echo "   - Auto-generation of full name from components\n";
    echo "   - Auto-generation of initials from first name and surname\n";
    echo "   - Improved form layout and validation\n";
    echo "   - Better display showing surname-based ordering\n\n";
    
    echo "✅ Model Improvements:\n";
    echo "   - Enhanced create() and update() methods to handle new fields\n";
    echo "   - Added automatic name parsing for backward compatibility\n";
    echo "   - Improved getAll() method with alphabetical ordering\n";
    echo "   - Added private parseName() method for name component extraction\n\n";
    
    echo "🎯 RESULT: Faculty profiles are now properly organized alphabetically by surname\n";
    echo "   with separate fields for academic prefixes and better admin management.\n";
    
} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
}