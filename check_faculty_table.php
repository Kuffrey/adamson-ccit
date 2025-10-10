<?php
require_once 'app/models/FacultyProfile.php';

echo "=== FACULTY PROFILE TABLE STRUCTURE ===\n\n";

try {
    $db = Model::db();
    $stmt = $db->query('DESCRIBE faculty_profile');
    $columns = $stmt->fetchAll();
    
    echo "Column Name | Type | Nullable | Default\n";
    echo "-----------|------|----------|----------\n";
    foreach($columns as $col) {
        echo $col['Field'] . ' | ' . $col['Type'] . ' | ' . ($col['Null'] === 'YES' ? 'YES' : 'NO') . ' | ' . ($col['Default'] ?? 'NULL') . "\n";
    }
    
    echo "\n=== SAMPLE DATA ===\n";
    $stmt = $db->query('SELECT * FROM faculty_profile LIMIT 3');
    $sample = $stmt->fetchAll();
    if ($sample) {
        foreach($sample as $row) {
            echo "ID: {$row['id']}\n";
            foreach($row as $field => $value) {
                if ($field !== 'id') {
                    echo "  $field: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
                }
            }
            echo "---\n";
        }
    } else {
        echo "No data found\n";
    }
    
    echo "\n=== CHECKING FACULTY PROFILE MODEL METHODS ===\n";
    $methods = get_class_methods('FacultyProfile');
    echo "Available methods: " . implode(', ', $methods) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>