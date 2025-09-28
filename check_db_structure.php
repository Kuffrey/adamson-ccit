<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Database Tables ===\n";
    $stmt = $pdo->query("SHOW TABLES");
    while ($table = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "- {$table[0]}\n";
    }
    
    echo "\n=== Users Table Structure ===\n";
    $stmt = $pdo->query("DESCRIBE users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']} ({$row['Type']}) - {$row['Null']} - {$row['Key']}\n";
    }
    
    echo "\n=== Check if student_profiles table exists ===\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'student_profiles'");
    $exists = $stmt->fetch();
    echo $exists ? "student_profiles table exists\n" : "student_profiles table does not exist\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>