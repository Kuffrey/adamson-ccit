<?php
require_once 'app/models/Model.php';

try {
    $method = new ReflectionMethod('Model', 'db');
    $method->setAccessible(true);
    $db = $method->invoke(null);
    
    echo "=== DATABASE: adamson_ccit ===\n";
    $stmt = $db->query("SELECT DATABASE() as current_db");
    $currentDb = $stmt->fetchColumn();
    echo "Current database: $currentDb\n\n";
    
    // Check all student tables and their data
    $tables = [
        'student_organization',
        'student_scholarship', 
        'student_research',
        'student_certification',
        'student_testimonial'
    ];
    
    foreach ($tables as $table) {
        echo "=== TABLE: $table ===\n";
        
        // Check if table exists
        $stmt = $db->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if ($stmt->rowCount() == 0) {
            echo "❌ Table does not exist!\n\n";
            continue;
        }
        
        // Get table structure
        $stmt = $db->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Columns:\n";
        foreach ($columns as $col) {
            echo "  - {$col['Field']} ({$col['Type']})\n";
        }
        
        // Count records
        $stmt = $db->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "Records: $count\n";
        
        // Show sample data
        if ($count > 0) {
            $stmt = $db->query("SELECT * FROM $table LIMIT 2");
            $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "Sample data:\n";
            foreach ($samples as $i => $row) {
                echo "  Record " . ($i + 1) . ":\n";
                foreach ($row as $key => $value) {
                    $displayValue = strlen($value) > 50 ? substr($value, 0, 47) . '...' : $value;
                    echo "    $key: $displayValue\n";
                }
                echo "\n";
            }
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>