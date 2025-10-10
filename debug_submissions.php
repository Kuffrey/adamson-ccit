<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Investigation</h2>\n";
    
    // Check if faculty_submissions table exists
    echo "<h3>1. Checking faculty_submissions table</h3>\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'faculty_submissions'");
    $table_exists = $stmt->rowCount() > 0;
    
    if ($table_exists) {
        echo "✅ faculty_submissions table exists\n<br>";
        
        // Show table structure
        echo "<h4>Table Structure:</h4>\n";
        $stmt = $pdo->query("DESCRIBE faculty_submissions");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse;'>\n";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>\n";
        foreach ($columns as $col) {
            echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td><td>{$col['Default']}</td></tr>\n";
        }
        echo "</table>\n<br>";
        
        // Check recent submissions
        echo "<h4>Recent Submissions (last 10):</h4>\n";
        $stmt = $pdo->query("SELECT * FROM faculty_submissions ORDER BY submitted_at DESC LIMIT 10");
        $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($submissions)) {
            echo "❌ No submissions found in faculty_submissions table\n<br>";
        } else {
            echo "<table border='1' style='border-collapse: collapse;'>\n";
            echo "<tr><th>ID</th><th>Faculty ID</th><th>Type</th><th>Title</th><th>Status</th><th>Submitted At</th></tr>\n";
            foreach ($submissions as $sub) {
                echo "<tr>";
                echo "<td>{$sub['id']}</td>";
                echo "<td>{$sub['faculty_id']}</td>";
                echo "<td>{$sub['submission_type']}</td>";
                echo "<td>" . (strlen($sub['title'] ?? '') > 50 ? substr($sub['title'], 0, 50) . '...' : ($sub['title'] ?? '')) . "</td>";
                echo "<td>{$sub['status']}</td>";
                echo "<td>{$sub['submitted_at']}</td>";
                echo "</tr>\n";
            }
            echo "</table>\n<br>";
        }
    } else {
        echo "❌ faculty_submissions table does NOT exist\n<br>";
    }
    
    // Check users table to see faculty login info
    echo "<h3>2. Checking users table for faculty</h3>\n";
    $stmt = $pdo->query("SELECT id, username, first_name, last_name, role FROM users WHERE role = 'faculty' LIMIT 5");
    $faculty = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($faculty)) {
        echo "❌ No faculty users found\n<br>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>\n";
        echo "<tr><th>ID</th><th>Username</th><th>First Name</th><th>Last Name</th><th>Role</th></tr>\n";
        foreach ($faculty as $f) {
            echo "<tr>";
            echo "<td>{$f['id']}</td>";
            echo "<td>{$f['username']}</td>";
            echo "<td>{$f['first_name']}</td>";
            echo "<td>{$f['last_name']}</td>";
            echo "<td>{$f['role']}</td>";
            echo "</tr>\n";
        }
        echo "</table>\n<br>";
    }
    
    // Check for dean users
    echo "<h3>3. Checking for dean users</h3>\n";
    $stmt = $pdo->query("SELECT id, username, first_name, last_name, role FROM users WHERE role = 'dean' LIMIT 5");
    $deans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($deans)) {
        echo "❌ No dean users found\n<br>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>\n";
        echo "<tr><th>ID</th><th>Username</th><th>First Name</th><th>Last Name</th><th>Role</th></tr>\n";
        foreach ($deans as $d) {
            echo "<tr>";
            echo "<td>{$d['id']}</td>";
            echo "<td>{$d['username']}</td>";
            echo "<td>{$d['first_name']}</td>";
            echo "<td>{$d['last_name']}</td>";
            echo "<td>{$d['role']}</td>";
            echo "</tr>\n";
        }
        echo "</table>\n<br>";
    }
    
    // Check all tables
    echo "<h3>4. All tables in adamson_ccit database</h3>\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<ul>\n";
    foreach ($tables as $table) {
        echo "<li>$table</li>\n";
    }
    echo "</ul>\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n<br>";
}
?>