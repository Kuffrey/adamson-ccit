<?php
// Create a simple mapping based on actual columns
require_once __DIR__ . '/app/config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Get actual column names
    $stmt = $db->query("DESCRIBE programs_undergraduate");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $actualFields = array_column($columns, 'Field');
    
    echo "<h3>Actual Columns Found:</h3>\n";
    echo implode(', ', $actualFields) . "\n\n";
    
    // Create field mapping
    $mapping = [];
    
    // Map title field
    $titleFields = ['name', 'title', 'program_name', 'program_title'];
    foreach ($titleFields as $field) {
        if (in_array($field, $actualFields)) {
            $mapping['title'] = $field;
            break;
        }
    }
    
    // Map description field
    $descFields = ['description', 'desc', 'program_description'];
    foreach ($descFields as $field) {
        if (in_array($field, $actualFields)) {
            $mapping['description'] = $field;
            break;
        }
    }
    
    // Map badge field
    $badgeFields = ['degree_type', 'type', 'program_type', 'badge'];
    foreach ($badgeFields as $field) {
        if (in_array($field, $actualFields)) {
            $mapping['badge'] = $field;
            break;
        }
    }
    
    echo "<h3>Field Mapping:</h3>\n";
    echo "Title -> " . ($mapping['title'] ?? 'NOT FOUND') . "\n";
    echo "Description -> " . ($mapping['description'] ?? 'NOT FOUND') . "\n";
    echo "Badge -> " . ($mapping['badge'] ?? 'NOT FOUND') . "\n\n";
    
    // Generate PHP code for the mapping
    echo "<h3>PHP Code to Use:</h3>\n";
    echo "<pre>\n";
    echo "// Field mapping for programs_undergraduate table\n";
    echo "private static function getFieldMapping() {\n";
    echo "    return [\n";
    echo "        'title' => '" . ($mapping['title'] ?? 'id') . "',\n";
    echo "        'description' => '" . ($mapping['description'] ?? 'id') . "',\n";
    echo "        'badge' => '" . ($mapping['badge'] ?? 'id') . "'\n";
    echo "    ];\n";
    echo "}\n";
    echo "</pre>\n";
    
    // Test query
    if (!empty($mapping)) {
        echo "<h3>Test Query:</h3>\n";
        $sql = "SELECT id";
        if (isset($mapping['title'])) $sql .= ", `" . $mapping['title'] . "` as title";
        if (isset($mapping['description'])) $sql .= ", `" . $mapping['description'] . "` as description";  
        if (isset($mapping['badge'])) $sql .= ", `" . $mapping['badge'] . "` as badge";
        if (in_array('slug', $actualFields)) $sql .= ", slug";
        if (in_array('created_at', $actualFields)) $sql .= ", created_at";
        if (in_array('updated_at', $actualFields)) $sql .= ", updated_at";
        $sql .= " FROM programs_undergraduate LIMIT 3";
        
        echo "<pre>" . htmlspecialchars($sql) . "</pre>\n";
        
        try {
            $stmt = $db->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<h4>Test Results:</h4>\n";
            echo "<pre>" . print_r($results, true) . "</pre>\n";
        } catch (Exception $e) {
            echo "<h4>Test Query Failed:</h4>\n";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>