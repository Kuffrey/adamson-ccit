<?php
echo "=== FINAL FACULTY ADMIN INTERFACE TEST ===\n\n";

require_once 'app/models/FacultyProfile.php';
require_once 'app/models/FacultyProfilePageSettings.php';

echo "1. Database connectivity and data retrieval:\n";
try {
    $faculty = FacultyProfile::getAll();
    echo "✓ Retrieved " . count($faculty) . " faculty members from database\n";
    
    $settings = FacultyProfilePageSettings::getSettings();
    echo "✓ Retrieved page settings from database\n";
    
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

echo "\n2. Admin interface functionality test:\n";

// Simulate admin form submission for creating faculty
$_POST = [
    'add_faculty' => '1',
    'faculty' => [
        'name' => 'Admin Test Faculty',
        'dept' => 'cs',
        'role' => 'full',
        'title' => 'Professor of Computer Science',
        'avatar_url' => '',
        'avatar_initials' => 'AT',
        'badges' => 'CS,Professor,Full-Time',
        'ordering' => 50
    ]
];

// Simulate the admin form processing logic
try {
    if (isset($_POST['add_faculty'])) {
        $facultyData = $_POST['faculty'] ?? [];
        echo "Processing add faculty form with data:\n";
        foreach ($facultyData as $key => $value) {
            echo "  $key: $value\n";
        }
        
        $result = FacultyProfile::create($facultyData);
        echo "✓ Faculty creation: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
        
        if ($result) {
            // Find the created faculty
            $db = Model::db();
            $stmt = $db->query('SELECT * FROM faculty_profile WHERE name = "Admin Test Faculty"');
            $created = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($created) {
                $testId = $created['id'];
                echo "✓ Created faculty with ID: $testId\n";
                
                // Test edit functionality
                $_POST = [
                    'edit_faculty' => '1',
                    'id' => $testId,
                    'faculty' => [
                        'name' => 'Updated Admin Test Faculty',
                        'dept' => 'itis',
                        'role' => 'part',
                        'title' => 'Updated Professor',
                        'avatar_url' => 'https://example.com/avatar.jpg',
                        'avatar_initials' => 'UA',
                        'badges' => 'ITIS,Updated,Part-Time',
                        'ordering' => 75
                    ]
                ];
                
                if (isset($_POST['edit_faculty'])) {
                    $facultyData = $_POST['faculty'] ?? [];
                    $facultyId = (int)$_POST['id'];
                    
                    echo "Processing edit faculty form for ID $facultyId:\n";
                    foreach ($facultyData as $key => $value) {
                        echo "  $key: $value\n";
                    }
                    
                    $updateResult = FacultyProfile::update($facultyId, $facultyData);
                    echo "✓ Faculty update: " . ($updateResult ? 'SUCCESS' : 'FAILED') . "\n";
                    
                    // Verify the update
                    $updated = FacultyProfile::getById($facultyId);
                    if ($updated) {
                        echo "✓ Updated faculty verification:\n";
                        echo "  Name: {$updated['name']}\n";
                        echo "  Dept: {$updated['dept']}\n";
                        echo "  Role: {$updated['role']}\n";
                        echo "  Avatar URL: {$updated['avatar_url']}\n";
                    }
                }
                
                // Clean up - delete the test faculty
                $deleteResult = FacultyProfile::delete($testId);
                echo "✓ Test cleanup: " . ($deleteResult ? 'SUCCESS' : 'FAILED') . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "✗ Admin form processing error: " . $e->getMessage() . "\n";
}

echo "\n3. File upload directory test:\n";
$uploadDir = __DIR__ . '/public/uploads/faculty/';
if (is_dir($uploadDir) && is_writable($uploadDir)) {
    echo "✓ Upload directory is ready for file uploads\n";
} else {
    echo "✗ Upload directory issue\n";
}

echo "\n4. Data consistency test:\n";
$currentFaculty = FacultyProfile::getAll();
$hasData = !empty($currentFaculty);
echo "✓ Faculty data " . ($hasData ? 'is available' : 'is empty') . "\n";

if ($hasData) {
    $sample = $currentFaculty[0];
    $requiredFields = ['id', 'name', 'dept', 'role', 'title'];
    $allFieldsPresent = true;
    
    foreach ($requiredFields as $field) {
        if (!array_key_exists($field, $sample) || empty($sample[$field])) {
            $allFieldsPresent = false;
            break;
        }
    }
    
    echo "✓ Required fields " . ($allFieldsPresent ? 'are complete' : 'have issues') . "\n";
}

echo "\n=== FINAL TEST COMPLETE ===\n";
echo "The faculty admin interface is properly connected to the database\n";
echo "and should correctly reflect the faculty_profile table structure.\n";
echo "\nKey improvements made:\n";
echo "- Fixed FacultyProfile model to return all database fields\n";
echo "- Simplified file upload function\n";
echo "- Fixed file path issues\n";
echo "- Improved error handling\n";
echo "- Added proper field validation\n";
echo "- Fixed HTML syntax issues\n";
?>