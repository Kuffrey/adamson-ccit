<?php
require_once 'config/database.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== CURRENT FACULTY_PROFILE TABLE STRUCTURE ===\n";
    $stmt = $pdo->query('DESCRIBE faculty_profile');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-20s %-15s %-10s\n", $row['Field'], $row['Type'], $row['Null']);
    }
    
    echo "\n=== SAMPLE DATA ===\n";
    $stmt = $pdo->query('SELECT id, name, dept, role, ordering FROM faculty_profile LIMIT 5');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("ID: %-3s Name: %-30s Dept: %-10s Order: %s\n", 
            $row['id'], $row['name'], $row['dept'], $row['ordering'] ?? 'NULL');
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}