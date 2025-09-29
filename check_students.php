<?php
$config = require 'app/config/database.php';
$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
$db = new PDO($dsn, $config['user'], $config['pass']);
$stmt = $db->prepare('SELECT username, email FROM users WHERE role = "student"');
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($students) {
    echo "Student accounts found:\n";
    foreach ($students as $student) {
        echo "- Username: {$student['username']}, Email: {$student['email']}\n";
    }
} else {
    echo "No student accounts found.\n";
}
?>