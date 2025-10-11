<?php
// Script to fix faculty_submissions table foreign key constraints

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
    
    // Check if faculty_submissions table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'faculty_submissions'");
    if ($stmt->rowCount() > 0) {
        echo "Faculty submissions table exists. Checking constraints...\n";
        
        // Get current foreign key constraints
        $stmt = $pdo->query("SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
                            FROM information_schema.KEY_COLUMN_USAGE 
                            WHERE TABLE_NAME = 'faculty_submissions' 
                            AND TABLE_SCHEMA = 'adamson_ccit' 
                            AND REFERENCED_TABLE_NAME IS NOT NULL");
        
        $constraints = $stmt->fetchAll();
        
        if (!empty($constraints)) {
            echo "Found foreign key constraints:\n";
            foreach ($constraints as $constraint) {
                echo "- {$constraint['CONSTRAINT_NAME']} -> {$constraint['REFERENCED_TABLE_NAME']}.{$constraint['REFERENCED_COLUMN_NAME']}\n";
                
                // Drop the constraint
                try {
                    $pdo->exec("ALTER TABLE faculty_submissions DROP FOREIGN KEY {$constraint['CONSTRAINT_NAME']}");
                    echo "  Dropped constraint: {$constraint['CONSTRAINT_NAME']}\n";
                } catch (Exception $e) {
                    echo "  Failed to drop constraint: " . $e->getMessage() . "\n";
                }
            }
        } else {
            echo "No foreign key constraints found.\n";
        }
        
        // Check table structure
        $stmt = $pdo->query("DESCRIBE faculty_submissions");
        $columns = $stmt->fetchAll();
        echo "Current table structure:\n";
        foreach ($columns as $col) {
            echo "- {$col['Field']}: {$col['Type']}\n";
        }
        
    } else {
        echo "Faculty submissions table does not exist. Will be created by the model.\n";
    }
    
    // Check if users table has faculty users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'faculty'");
    $result = $stmt->fetch();
    echo "Faculty users in users table: {$result['count']}\n";
    
    // Check if faculty_profile table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'faculty_profile'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM faculty_profile");
        $result = $stmt->fetch();
        echo "Records in faculty_profile table: {$result['count']}\n";
    } else {
        echo "Faculty_profile table does not exist.\n";
    }
    
    echo "Database analysis complete. The FacultySubmissions model will handle table creation.\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
