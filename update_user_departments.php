<?php
// Script to update users table with department information

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=adamson_ccit;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "Connected to database successfully.\n";
    
    // Check if department_id column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'department_id'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN department_id INT DEFAULT NULL AFTER role");
        echo "Added department_id column to users table.\n";
    }
    
    // Check if department column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'department'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN department VARCHAR(50) DEFAULT NULL AFTER department_id");
        echo "Added department column to users table.\n";
    }
    
    // Update existing users with default departments
    $pdo->exec("UPDATE users SET department = 'CCIT', department_id = 1 WHERE role IN ('faculty', 'dean') AND department IS NULL");
    echo "Updated existing faculty and dean users with CCIT department.\n";
    
    $pdo->exec("UPDATE users SET department = 'Administration', department_id = 2 WHERE role = 'admin' AND department IS NULL");
    echo "Updated existing admin users with Administration department.\n";
    
    // Show updated users
    $stmt = $pdo->query("SELECT id, username, role, department FROM users ORDER BY role, username");
    $users = $stmt->fetchAll();
    
    echo "\nUpdated users:\n";
    foreach ($users as $user) {
        echo "- {$user['username']} ({$user['role']}) - {$user['department']}\n";
    }
    
    echo "\nDepartment update complete!\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
