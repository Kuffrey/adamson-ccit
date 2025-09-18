<?php
// Test Faculty Certification Database Structure
require_once __DIR__ . '/app/config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h1>Faculty Certification Database Structure Test</h1>";
    
    // Check tables exist
    $tables = ['faculty', 'certification', 'faculty_certification_award'];
    
    foreach ($tables as $table) {
        echo "<h3>Table: $table</h3>";
        
        // Check if table exists
        $stmt = $db->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if ($stmt->rowCount() > 0) {
            echo "✅ Table exists<br>";
            
            // Show structure
            $stmt = $db->prepare("DESCRIBE $table");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<table border='1' style='margin: 10px 0;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            foreach ($columns as $col) {
                echo "<tr>";
                echo "<td>{$col['Field']}</td>";
                echo "<td>{$col['Type']}</td>";
                echo "<td>{$col['Null']}</td>";
                echo "<td>{$col['Key']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Show sample data
            try {
                $stmt = $db->prepare("SELECT * FROM $table LIMIT 3");
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if ($data) {
                    echo "<p><strong>Sample data:</strong></p>";
                    echo "<pre>" . print_r($data, true) . "</pre>";
                } else {
                    echo "<p>No data in table</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>Error reading data: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "❌ Table does not exist<br>";
        }
        echo "<hr>";
    }
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Database Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>