<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Foreign Key Constraint Investigation</h2>\n";
    
    // Check faculty_profile table
    echo "<h3>1. Faculty Profile Records</h3>\n";
    $stmt = $pdo->query("SELECT id, name, first_name, surname FROM faculty_profile LIMIT 10");
    $facultyProfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>\n";
    echo "<tr><th>ID</th><th>Name</th><th>First Name</th><th>Surname</th></tr>\n";
    foreach ($facultyProfiles as $fp) {
        echo "<tr><td>{$fp['id']}</td><td>{$fp['name']}</td><td>{$fp['first_name']}</td><td>{$fp['surname']}</td></tr>\n";
    }
    echo "</table>\n<br>";
    
    // Check users table faculty
    echo "<h3>2. Users Table Faculty Records</h3>\n";
    $stmt = $pdo->query("SELECT id, username, first_name, last_name, role FROM users WHERE role = 'faculty'");
    $facultyUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>\n";
    echo "<tr><th>ID</th><th>Username</th><th>First Name</th><th>Last Name</th><th>Role</th></tr>\n";
    foreach ($facultyUsers as $fu) {
        echo "<tr><td>{$fu['id']}</td><td>{$fu['username']}</td><td>{$fu['first_name']}</td><td>{$fu['last_name']}</td><td>{$fu['role']}</td></tr>\n";
    }
    echo "</table>\n<br>";
    
    // Check foreign key constraints
    echo "<h3>3. Foreign Key Constraints on faculty_submissions</h3>\n";
    $stmt = $pdo->query("
        SELECT 
            CONSTRAINT_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = 'adamson_ccit' 
        AND TABLE_NAME = 'faculty_submissions' 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    $constraints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>\n";
    echo "<tr><th>Constraint</th><th>Column</th><th>References Table</th><th>References Column</th></tr>\n";
    foreach ($constraints as $constraint) {
        echo "<tr><td>{$constraint['CONSTRAINT_NAME']}</td><td>{$constraint['COLUMN_NAME']}</td><td>{$constraint['REFERENCED_TABLE_NAME']}</td><td>{$constraint['REFERENCED_COLUMN_NAME']}</td></tr>\n";
    }
    echo "</table>\n<br>";
    
    // Check which faculty user IDs don't exist in faculty_profile
    echo "<h3>4. Missing Faculty Profile Records</h3>\n";
    $stmt = $pdo->query("
        SELECT u.id, u.username, u.first_name, u.last_name 
        FROM users u 
        WHERE u.role = 'faculty' 
        AND u.id NOT IN (SELECT id FROM faculty_profile WHERE id IS NOT NULL)
    ");
    $missingProfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($missingProfiles)) {
        echo "✅ All faculty users have corresponding faculty_profile records<br>\n";
    } else {
        echo "❌ Faculty users missing from faculty_profile table:<br>\n";
        echo "<table border='1' style='border-collapse: collapse;'>\n";
        echo "<tr><th>User ID</th><th>Username</th><th>First Name</th><th>Last Name</th></tr>\n";
        foreach ($missingProfiles as $mp) {
            echo "<tr><td>{$mp['id']}</td><td>{$mp['username']}</td><td>{$mp['first_name']}</td><td>{$mp['last_name']}</td></tr>\n";
        }
        echo "</table>\n<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n<br>";
}
?>