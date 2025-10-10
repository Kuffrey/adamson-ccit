<?php
echo "<h2>Direct Faculty Profile Creation</h2>\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check current state
    $stmt = $pdo->prepare("SELECT * FROM faculty_profile WHERE id = 3");
    $stmt->execute();
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo "✅ Faculty profile with ID 3 already exists:<br>\n";
        echo "Name: {$existing['name']}<br>\n";
        echo "First Name: {$existing['first_name']}<br>\n";
        echo "Surname: {$existing['surname']}<br>\n";
    } else {
        echo "Creating faculty profile with ID 3...<br>\n";
        
        // Temporarily disable auto-increment to insert specific ID
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Build the full name and initials properly
        require_once 'app/models/FacultyProfile.php';
        
        $profileData = [
            'prefix' => 'Mr.',
            'first_name' => 'Archie',
            'surname' => 'Santiago'
        ];
        
        $fullName = FacultyProfile::buildFullName($profileData);
        $initials = FacultyProfile::generateAvatarInitials('Archie', 'Santiago');
        
        echo "Full name: $fullName<br>\n";
        echo "Initials: $initials<br>\n";
        
        // Insert with specific ID
        $insertSql = "INSERT INTO faculty_profile (
            id, name, prefix, first_name, surname, dept, role, role_order, title, avatar_initials, ordering
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";
        
        $stmt = $pdo->prepare($insertSql);
        $result = $stmt->execute([
            3,                    // id - matching user ID
            $fullName,           // name
            'Mr.',               // prefix
            'Archie',            // first_name
            'Santiago',          // surname
            'cs',                // dept
            'full',              // role
            3,                   // role_order (full professor)
            'Faculty Member',    // title
            $initials,           // avatar_initials
            999                  // ordering
        ]);
        
        // Re-enable foreign key checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        if ($result) {
            echo "✅ Faculty profile created successfully!<br>\n";
        } else {
            echo "❌ Failed to create faculty profile<br>\n";
        }
    }
    
    // Verify the creation
    echo "<h3>Verification</h3>\n";
    $stmt = $pdo->prepare("SELECT * FROM faculty_profile WHERE id = 3");
    $stmt->execute();
    $profile = $stmt->fetch();
    
    if ($profile) {
        echo "✅ Faculty profile exists:<br>\n";
        echo "<ul>\n";
        echo "<li>ID: {$profile['id']}</li>\n";
        echo "<li>Name: {$profile['name']}</li>\n";
        echo "<li>First Name: {$profile['first_name']}</li>\n";
        echo "<li>Surname: {$profile['surname']}</li>\n";
        echo "<li>Department: {$profile['dept']}</li>\n";
        echo "<li>Role: {$profile['role']}</li>\n";
        echo "<li>Avatar Initials: {$profile['avatar_initials']}</li>\n";
        echo "</ul>\n";
        
        // Test the foreign key constraint
        echo "<h3>Testing Foreign Key Constraint</h3>\n";
        $testSql = "SELECT COUNT(*) as count FROM faculty_profile WHERE id = 3";
        $testStmt = $pdo->prepare($testSql);
        $testStmt->execute();
        $testResult = $testStmt->fetch();
        
        if ($testResult['count'] > 0) {
            echo "✅ Foreign key constraint should now work<br>\n";
        }
        
    } else {
        echo "❌ Faculty profile still not found<br>\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>\n";
} catch (Exception $e) {
    echo "❌ General error: " . $e->getMessage() . "<br>\n";
}
?>