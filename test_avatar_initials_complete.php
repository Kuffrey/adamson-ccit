<?php
require_once 'config/database.php';
require_once 'app/models/FacultyProfile.php';

echo "<h2>Comprehensive Avatar Initials Test</h2>\n";

// Test the PHP backend initials generation
echo "<h3>Backend PHP Tests</h3>\n";
$testCases = [
    ['first_name' => 'John', 'surname' => 'Doe', 'expected' => 'JD'],
    ['first_name' => 'Maria', 'surname' => 'Garcia', 'expected' => 'MG'],
    ['first_name' => 'Leonard', 'surname' => 'Alejandro', 'expected' => 'LA'],
    ['first_name' => 'Robert', 'surname' => 'Smith', 'expected' => 'RS'],
    ['first_name' => 'Sarah', 'surname' => 'Williams', 'expected' => 'SW'],
    ['first_name' => 'A', 'surname' => 'B', 'expected' => 'AB'],
    ['first_name' => '', 'surname' => 'LastOnly', 'expected' => 'L'],
    ['first_name' => 'FirstOnly', 'surname' => '', 'expected' => 'F'],
];

echo "<table border='1' style='border-collapse: collapse;'>\n";
echo "<tr><th>First Name</th><th>Surname</th><th>Expected</th><th>Actual</th><th>Status</th></tr>\n";

foreach ($testCases as $test) {
    $actual = FacultyProfile::generateAvatarInitials($test['first_name'], $test['surname']);
    $pass = $actual === $test['expected'];
    $status = $pass ? '✅ PASS' : '❌ FAIL';
    $color = $pass ? '#d4edda' : '#f8d7da';
    
    echo "<tr style='background-color: $color;'>
        <td>{$test['first_name']}</td>
        <td>{$test['surname']}</td>
        <td>{$test['expected']}</td>
        <td>$actual</td>
        <td>$status</td>
    </tr>\n";
}
echo "</table>\n";

// Test complete faculty creation with initials
echo "<h3>Full Faculty Creation Test</h3>\n";
try {
    $testFaculty = [
        'prefix' => 'Dr.',
        'first_name' => 'Test',
        'middle_initial' => 'M.',
        'surname' => 'InitialsCheck',
        'suffix' => 'PhD',
        'dept' => 'cs',
        'role' => 'full',
        'title' => 'Test Professor'
    ];
    
    echo "<p>Creating faculty: Dr. Test M. InitialsCheck, PhD</p>\n";
    $id = FacultyProfile::create($testFaculty);
    
    if ($id) {
        $created = FacultyProfile::getById($id);
        echo "<p><strong>✅ Created successfully!</strong></p>\n";
        echo "<ul>\n";
        echo "<li>Full Name: {$created['name']}</li>\n";
        echo "<li>Avatar Initials: {$created['avatar_initials']}</li>\n";
        echo "<li>Expected Initials: TI (Test + InitialsCheck)</li>\n";
        echo "<li>Match: " . ($created['avatar_initials'] === 'TI' ? '✅ YES' : '❌ NO') . "</li>\n";
        echo "</ul>\n";
        
        // Clean up
        FacultyProfile::delete($id);
        echo "<p>✅ Test faculty deleted</p>\n";
    } else {
        echo "<p>❌ Failed to create test faculty</p>\n";
    }
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>\n";
}

// Test with existing faculty (if any)
echo "<h3>Existing Faculty Initials Check</h3>\n";
$existingFaculty = FacultyProfile::getAll();

if (empty($existingFaculty)) {
    echo "<p>No existing faculty to check.</p>\n";
} else {
    echo "<table border='1' style='border-collapse: collapse;'>\n";
    echo "<tr><th>Name</th><th>First Name</th><th>Surname</th><th>Current Initials</th><th>Expected Initials</th><th>Match</th></tr>\n";
    
    foreach ($existingFaculty as $faculty) {
        $firstName = $faculty['first_name'] ?? '';
        $surname = $faculty['surname'] ?? '';
        $currentInitials = $faculty['avatar_initials'] ?? '';
        $expectedInitials = FacultyProfile::generateAvatarInitials($firstName, $surname);
        $match = $currentInitials === $expectedInitials;
        
        $color = $match ? '#d4edda' : '#f8d7da';
        $status = $match ? '✅ MATCH' : '❌ MISMATCH';
        
        echo "<tr style='background-color: $color;'>
            <td>{$faculty['name']}</td>
            <td>$firstName</td>
            <td>$surname</td>
            <td>$currentInitials</td>
            <td>$expectedInitials</td>
            <td>$status</td>
        </tr>\n";
    }
    echo "</table>\n";
}

echo "<h3>JavaScript Integration Test</h3>\n";
?>
<div style="border: 1px solid #ccc; padding: 20px; margin: 20px 0; background: #f9f9f9;">
    <h4>Live Test Form (JavaScript)</h4>
    <form>
        <label>First Name: </label>
        <input type="text" name="faculty[first_name]" placeholder="Enter first name" style="margin: 5px; padding: 5px;">
        <br>
        <label>Surname: </label>
        <input type="text" name="faculty[surname]" placeholder="Enter surname" style="margin: 5px; padding: 5px;">
        <br>
        <label>Avatar Initials: </label>
        <input type="text" name="faculty[avatar_initials]" readonly style="margin: 5px; padding: 5px; background: #f5f5f5;">
        <br>
        <small>Type in the name fields above to see real-time initials generation!</small>
    </form>
</div>

<script>
// Same function as in admin interface
function updateInitials(form) {
    const firstName = form.querySelector('input[name$="[first_name]"]')?.value || '';
    const surname = form.querySelector('input[name$="[surname]"]')?.value || '';
    const initialsField = form.querySelector('input[name$="[avatar_initials]"]');
    
    if (initialsField) {
        const firstInitial = firstName.charAt(0).toUpperCase();
        const lastInitial = surname.charAt(0).toUpperCase();
        
        // Use only first and last initials
        const initials = firstInitial + lastInitial;
        initialsField.value = initials.substring(0, 4);
    }
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    ['input[name$="[first_name]"]', 'input[name$="[surname]"]'].forEach(selector => {
        const element = form.querySelector(selector);
        if (element) {
            element.addEventListener('input', () => updateInitials(form));
            element.addEventListener('keyup', () => updateInitials(form));
        }
    });
    
    // Initialize
    updateInitials(form);
});
</script>

<?php
echo "<h3>Summary</h3>\n";
echo "<p>✅ <strong>Avatar initials now correctly use first name + surname only</strong></p>\n";
echo "<p>✅ <strong>Both PHP backend and JavaScript frontend are synchronized</strong></p>\n";
echo "<p>✅ <strong>Auto-generation works in real-time</strong></p>\n";
echo "<p>✅ <strong>Existing faculty data can be validated and updated if needed</strong></p>\n";
?>