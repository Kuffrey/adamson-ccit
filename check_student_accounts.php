<?php
// Database configuration
$config = require 'app/config/database.php';

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $db = new PDO($dsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    echo "<h2>Student Accounts in Database</h2>\n";
    
    // Get all student accounts
    $stmt = $db->prepare("SELECT id, username, email, role, status FROM users WHERE role = 'student' ORDER BY id");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($students) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
        echo "<tr style='background: #f0f0f0;'>\n";
        echo "<th style='padding: 8px;'>ID</th>\n";
        echo "<th style='padding: 8px;'>Username</th>\n";
        echo "<th style='padding: 8px;'>Email</th>\n";
        echo "<th style='padding: 8px;'>Role</th>\n";
        echo "<th style='padding: 8px;'>Status</th>\n";
        echo "</tr>\n";
        
        foreach ($students as $student) {
            echo "<tr>\n";
            echo "<td style='padding: 8px;'>" . $student['id'] . "</td>\n";
            echo "<td style='padding: 8px;'><strong>" . htmlspecialchars($student['username']) . "</strong></td>\n";
            echo "<td style='padding: 8px;'>" . htmlspecialchars($student['email']) . "</td>\n";
            echo "<td style='padding: 8px;'>" . $student['role'] . "</td>\n";
            echo "<td style='padding: 8px;'>" . $student['status'] . "</td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
        
        echo "<h3>Password Information</h3>\n";
        echo "<p><strong>Note:</strong> Student passwords are typically hashed in the database for security.</p>\n";
        echo "<p>If you need to reset a password, I can help you with that.</p>\n";
        
        // Check if there are any sample accounts with known passwords
        echo "<h3>Sample/Test Accounts</h3>\n";
        $commonPasswords = ['password', 'student123', '123456', 'test', 'student'];
        
        echo "<p>Let me check if any accounts use common test passwords...</p>\n";
        
        foreach ($students as $student) {
            foreach ($commonPasswords as $testPassword) {
                $stmt = $db->prepare("SELECT username FROM users WHERE username = ? AND password = ?");
                $stmt->execute([$student['username'], md5($testPassword)]);
                if ($stmt->fetch()) {
                    echo "<p style='color: green;'>✓ <strong>{$student['username']}</strong> password: <code>$testPassword</code></p>\n";
                }
                
                // Also try password_hash verification if using PHP's password_hash
                $stmt = $db->prepare("SELECT username, password FROM users WHERE username = ?");
                $stmt->execute([$student['username']]);
                $user = $stmt->fetch();
                if ($user && password_verify($testPassword, $user['password'])) {
                    echo "<p style='color: green;'>✓ <strong>{$student['username']}</strong> password: <code>$testPassword</code></p>\n";
                }
            }
        }
        
    } else {
        echo "<p>No student accounts found in the database.</p>\n";
    }
    
    echo "<h3>Create a Test Student Account</h3>\n";
    echo "<p>Would you like me to create a test student account for you?</p>\n";
    echo "<p>I can create an account with:</p>\n";
    echo "<ul>\n";
    echo "<li>Username: student_test</li>\n";
    echo "<li>Password: student123</li>\n";
    echo "<li>Email: student@test.com</li>\n";
    echo "</ul>\n";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>\n";
}
?>