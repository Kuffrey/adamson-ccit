<?php
// Check EXACT column names in programs_undergraduate table
require_once __DIR__ . '/app/config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h3>EXACT Column Names in programs_undergraduate Table:</h3>\n";
    
    // Get table structure
    $stmt = $db->query("DESCRIBE programs_undergraduate");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>\n";
    echo "<tr><th>#</th><th>Exact Field Name</th><th>Type</th><th>Sample Values</th></tr>\n";
    foreach ($columns as $i => $col) {
        echo "<tr>";
        echo "<td>" . ($i + 1) . "</td>";
        echo "<td><strong style='color: blue;'>" . htmlspecialchars($col['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
        
        // Get sample values
        try {
            $sampleStmt = $db->prepare("SELECT DISTINCT `" . $col['Field'] . "` FROM programs_undergraduate LIMIT 3");
            $sampleStmt->execute();
            $samples = $sampleStmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<td>" . htmlspecialchars(implode(', ', array_filter($samples))) . "</td>";
        } catch (Exception $e) {
            echo "<td>Error getting samples</td>";
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
    
    // Show ALL data
    echo "\n<h3>ALL Data in Table:</h3>\n";
    $stmt = $db->query("SELECT * FROM programs_undergraduate");
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($programs)) {
        echo "❌ No data in programs_undergraduate table.\n";
    } else {
        echo "<p><strong>Total records:</strong> " . count($programs) . "</p>\n";
        echo "<table border='1' style='border-collapse: collapse;'>\n";
        echo "<tr>";
        foreach (array_keys($programs[0]) as $field) {
            echo "<th style='background: #f0f0f0;'>" . htmlspecialchars($field) . "</th>";
        }
        echo "</tr>\n";
        
        foreach ($programs as $program) {
            echo "<tr>";
            foreach ($program as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    
    // Generate correct field mapping
    echo "\n<h3>🔧 Field Mapping Needed:</h3>\n";
    echo "<table border='1'>\n";
    echo "<tr><th>Admin Interface</th><th>Database Column</th><th>Status</th></tr>\n";
    
    $fieldMap = [
        'title' => ['program_name', 'name', 'title', 'program_title'],
        'description' => ['description', 'desc', 'program_description'],
        'badge' => ['degree_type', 'type', 'program_type', 'badge']
    ];
    
    $actualFields = array_column($columns, 'Field');
    
    foreach ($fieldMap as $adminField => $possibleDbFields) {
        $found = null;
        foreach ($possibleDbFields as $dbField) {
            if (in_array($dbField, $actualFields)) {
                $found = $dbField;
                break;
            }
        }
        
        echo "<tr>";
        echo "<td><strong>$adminField</strong></td>";
        echo "<td>" . ($found ? "<span style='color: green;'>$found</span>" : "<span style='color: red;'>NOT FOUND</span>") . "</td>";
        echo "<td>" . ($found ? "✅ Available" : "❌ Missing") . "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>