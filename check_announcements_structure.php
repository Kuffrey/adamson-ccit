<?php
require_once 'app/models/Announcement.php';

echo "=== ANNOUNCEMENTS TABLE STRUCTURE ===\n\n";

try {
    $db = Model::db();
    $stmt = $db->query('DESCRIBE announcements');
    $columns = $stmt->fetchAll();
    
    echo "Column Name | Type | Nullable\n";
    echo "-----------|------|----------\n";
    foreach($columns as $col) {
        echo $col['Field'] . ' | ' . $col['Type'] . ' | ' . ($col['Null'] === 'YES' ? 'YES' : 'NO') . "\n";
    }
    
    echo "\n=== SAMPLE DATA ===\n";
    $stmt = $db->query('SELECT * FROM announcements LIMIT 1');
    $sample = $stmt->fetch();
    if ($sample) {
        foreach($sample as $field => $value) {
            echo "$field: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
        }
    } else {
        echo "No data found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>