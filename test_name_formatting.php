<?php
require_once 'config/database.php';
require_once 'app/models/FacultyProfile.php';

echo "<h2>Testing Name Formatting and Avatar Initials</h2>\n";

// Test cases for different name combinations
$testCases = [
    [
        'prefix' => 'Dr.',
        'first_name' => 'John',
        'middle_initial' => 'M.',
        'surname' => 'Smith',
        'suffix' => 'PhD',
        'expected_format' => 'Dr. John M. Smith, PhD',
        'expected_initials' => 'JS'
    ],
    [
        'prefix' => 'Prof.',
        'first_name' => 'Maria',
        'middle_initial' => 'L.',
        'surname' => 'Garcia',
        'suffix' => 'Jr.',
        'expected_format' => 'Prof. Maria L. Garcia Jr.',
        'expected_initials' => 'MG'
    ],
    [
        'prefix' => 'Dr.',
        'first_name' => 'Robert',
        'middle_initial' => '',
        'surname' => 'Johnson',
        'suffix' => 'MSIT',
        'expected_format' => 'Dr. Robert Johnson, MSIT',
        'expected_initials' => 'RJ'
    ],
    [
        'prefix' => '',
        'first_name' => 'Sarah',
        'middle_initial' => 'K.',
        'surname' => 'Williams',
        'suffix' => 'Sr.',
        'expected_format' => 'Sarah K. Williams Sr.',
        'expected_initials' => 'SW'
    ],
    [
        'prefix' => 'Mrs.',
        'first_name' => 'Lisa',
        'middle_initial' => 'A.',
        'surname' => 'Brown',
        'suffix' => 'III',
        'expected_format' => 'Mrs. Lisa A. Brown III',
        'expected_initials' => 'LB'
    ]
];

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
echo "<tr>
    <th>Input</th>
    <th>Expected Full Name</th>
    <th>Actual Full Name</th>
    <th>Expected Initials</th>
    <th>Actual Initials</th>
    <th>Status</th>
</tr>\n";

foreach ($testCases as $i => $test) {
    // Test buildFullName method
    $actualFullName = FacultyProfile::buildFullName($test);
    $actualInitials = FacultyProfile::generateAvatarInitials($test['first_name'], $test['surname']);
    
    $nameMatch = $actualFullName === $test['expected_format'];
    $initialsMatch = $actualInitials === $test['expected_initials'];
    
    $status = ($nameMatch && $initialsMatch) ? '✅ PASS' : '❌ FAIL';
    
    $input = sprintf("%s %s %s %s %s", 
        $test['prefix'], 
        $test['first_name'], 
        $test['middle_initial'], 
        $test['surname'], 
        $test['suffix']
    );
    
    echo "<tr>
        <td>$input</td>
        <td>{$test['expected_format']}</td>
        <td style='background-color: " . ($nameMatch ? '#d4edda' : '#f8d7da') . "'>$actualFullName</td>
        <td>{$test['expected_initials']}</td>
        <td style='background-color: " . ($initialsMatch ? '#d4edda' : '#f8d7da') . "'>$actualInitials</td>
        <td>$status</td>
    </tr>\n";
}

echo "</table>\n";

// Test the comma rules specifically
echo "<h3>Comma Rules Test</h3>\n";
echo "<ul>\n";

$academicSuffixes = ['PhD', 'MSIT', 'MBA', 'Ed.D', 'MSc', 'BSc'];
$generationalSuffixes = ['Jr.', 'Sr.', 'II', 'III', 'IV'];

echo "<li><strong>Academic Suffixes (should have commas):</strong><br>\n";
foreach ($academicSuffixes as $suffix) {
    $testData = ['first_name' => 'John', 'surname' => 'Doe', 'suffix' => $suffix];
    $result = FacultyProfile::buildFullName($testData);
    $hasComma = strpos($result, ', ' . $suffix) !== false;
    echo "  $suffix → $result " . ($hasComma ? '✅' : '❌') . "<br>\n";
}

echo "</li><li><strong>Generational Suffixes (should NOT have commas):</strong><br>\n";
foreach ($generationalSuffixes as $suffix) {
    $testData = ['first_name' => 'John', 'surname' => 'Doe', 'suffix' => $suffix];
    $result = FacultyProfile::buildFullName($testData);
    $hasNoComma = strpos($result, ' ' . $suffix) !== false && strpos($result, ', ' . $suffix) === false;
    echo "  $suffix → $result " . ($hasNoComma ? '✅' : '❌') . "<br>\n";
}
echo "</li></ul>\n";

// Test actual database integration
echo "<h3>Database Integration Test</h3>\n";
try {
    // Create a test faculty member
    $testData = [
        'prefix' => 'Dr.',
        'first_name' => 'Test',
        'middle_initial' => 'M.',
        'surname' => 'Faculty',
        'suffix' => 'PhD',
        'dept' => 'Computer Science',
        'role' => 'full',
        'title' => 'Professor'
    ];
    
    echo "<p>Creating test faculty member...</p>\n";
    $id = FacultyProfile::create($testData);
    
    if ($id) {
        echo "<p>✅ Test faculty created with ID: $id</p>\n";
        
        // Retrieve and check the data
        $faculty = FacultyProfile::getById($id);
        if ($faculty) {
            echo "<p><strong>Retrieved Data:</strong><br>\n";
            echo "Full Name: {$faculty['name']}<br>\n";
            echo "Avatar Initials: {$faculty['avatar_initials']}<br>\n";
            echo "Role Order: {$faculty['role_order']}</p>\n";
            
            // Clean up - delete the test record
            FacultyProfile::delete($id);
            echo "<p>✅ Test faculty member deleted</p>\n";
        }
    } else {
        echo "<p>❌ Failed to create test faculty member</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database test failed: " . $e->getMessage() . "</p>\n";
}

echo "<h3>Test Complete</h3>\n";
echo "<p>Check the results above to verify that:</p>\n";
echo "<ul>\n";
echo "<li>Academic suffixes (PhD, MSIT, etc.) have commas before them</li>\n";
echo "<li>Generational suffixes (Jr., Sr., III, etc.) do NOT have commas</li>\n";
echo "<li>Avatar initials use only first and last name letters</li>\n";
echo "<li>Names are built correctly with all components</li>\n";
echo "</ul>\n";
?>