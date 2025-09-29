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
    
    echo "<h2>Create Test Student Account</h2>\n";
    
    // Check if test student already exists
    $stmt = $db->prepare("SELECT username FROM users WHERE username = 'student_test'");
    $stmt->execute();
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo "<p style='color: orange;'>Test student account already exists!</p>\n";
        echo "<p><strong>Username:</strong> student_test</p>\n";
        echo "<p><strong>Password:</strong> student123</p>\n";
    } else {
        // Create test student account
        $username = 'student_test';
        $password = password_hash('student123', PASSWORD_DEFAULT); // Using secure password hashing
        $email = 'student@test.com';
        $role = 'student';
        $status = 'active';
        
        $stmt = $db->prepare("INSERT INTO users (username, password, email, role, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $result = $stmt->execute([$username, $password, $email, $role, $status]);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Test student account created successfully!</p>\n";
            echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 8px; border: 1px solid #4caf50;'>\n";
            echo "<h3>Login Credentials:</h3>\n";
            echo "<p><strong>Username:</strong> <code>student_test</code></p>\n";
            echo "<p><strong>Password:</strong> <code>student123</code></p>\n";
            echo "<p><strong>Role:</strong> Student</p>\n";
            echo "</div>\n";
            
            echo "<p><a href='public/index.php?page=login' style='background: #00713D; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>\n";
        } else {
            echo "<p style='color: red;'>❌ Failed to create test account.</p>\n";
        }
    }
    
    // Also show other existing student accounts
    echo "<h3>All Student Accounts</h3>\n";
    $stmt = $db->prepare("SELECT username, email, status FROM users WHERE role = 'student'");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($students) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
        echo "<tr style='background: #f0f0f0;'>\n";
        echo "<th style='padding: 8px;'>Username</th>\n";
        echo "<th style='padding: 8px;'>Email</th>\n";
        echo "<th style='padding: 8px;'>Status</th>\n";
        echo "</tr>\n";
        
        foreach ($students as $student) {
            echo "<tr>\n";
            echo "<td style='padding: 8px;'><strong>" . htmlspecialchars($student['username']) . "</strong></td>\n";
            echo "<td style='padding: 8px;'>" . htmlspecialchars($student['email']) . "</td>\n";
            echo "<td style='padding: 8px;'>" . $student['status'] . "</td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>\n";
    echo "<p>Make sure your database is running and the configuration is correct.</p>\n";
}
?>