<?php
require_once 'config/database.php';
require_once 'app/models/FacultyProfile.php';

echo "<h2>Fixing Existing Faculty Avatar Initials</h2>\n";

$faculty = FacultyProfile::getAll();
$fixed = 0;
$alreadyCorrect = 0;

echo "<table border='1' style='border-collapse: collapse;'>\n";
echo "<tr><th>Name</th><th>First Name</th><th>Surname</th><th>Old Initials</th><th>New Initials</th><th>Action</th></tr>\n";

foreach ($faculty as $f) {
    $firstName = $f['first_name'] ?? '';
    $surname = $f['surname'] ?? '';
    $currentInitials = $f['avatar_initials'] ?? '';
    $correctInitials = FacultyProfile::generateAvatarInitials($firstName, $surname);
    
    if ($currentInitials !== $correctInitials) {
        // Update the faculty member
        try {
            FacultyProfile::update($f['id'], ['avatar_initials' => $correctInitials]);
            $action = "✅ FIXED";
            $color = "#d1ecf1";
            $fixed++;
        } catch (Exception $e) {
            $action = "❌ ERROR: " . $e->getMessage();
            $color = "#f8d7da";
        }
    } else {
        $action = "✓ Already Correct";
        $color = "#d4edda";
        $alreadyCorrect++;
    }
    
    echo "<tr style='background-color: $color;'>
        <td>{$f['name']}</td>
        <td>$firstName</td>
        <td>$surname</td>
        <td>$currentInitials</td>
        <td>$correctInitials</td>
        <td>$action</td>
    </tr>\n";
}

echo "</table>\n";

echo "<h3>Summary</h3>\n";
echo "<p>✅ <strong>Fixed initials for $fixed faculty members</strong></p>\n";
echo "<p>✅ <strong>$alreadyCorrect faculty members already had correct initials</strong></p>\n";
echo "<p>🎯 <strong>All faculty members now have proper first + last name initials!</strong></p>\n";

// Verify the fix
echo "<h3>Verification</h3>\n";
$facultyAfterFix = FacultyProfile::getAll();
$allCorrect = true;

foreach ($facultyAfterFix as $f) {
    $firstName = $f['first_name'] ?? '';
    $surname = $f['surname'] ?? '';
    $currentInitials = $f['avatar_initials'] ?? '';
    $expectedInitials = FacultyProfile::generateAvatarInitials($firstName, $surname);
    
    if ($currentInitials !== $expectedInitials) {
        echo "<p>❌ Still incorrect: {$f['name']} has '$currentInitials' but should be '$expectedInitials'</p>\n";
        $allCorrect = false;
    }
}

if ($allCorrect) {
    echo "<p>🎉 <strong>PERFECT! All faculty members now have correct avatar initials!</strong></p>\n";
}
?>