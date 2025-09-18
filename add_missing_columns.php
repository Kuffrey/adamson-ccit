<?php
// SQL commands to add missing columns to programs_undergraduate table
require_once __DIR__ . '/app/config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h3>Adding Missing Columns to programs_undergraduate Table</h3>\n";
    
    $alterCommands = [
        "ALTER TABLE programs_undergraduate ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER degree_type",
        "ALTER TABLE programs_undergraduate ADD COLUMN ordering INT DEFAULT 0 AFTER status", 
        "ALTER TABLE programs_undergraduate ADD COLUMN website_url VARCHAR(255) DEFAULT NULL AFTER ordering",
        "ALTER TABLE programs_undergraduate ADD COLUMN icon VARCHAR(255) DEFAULT NULL AFTER website_url"
    ];
    
    $success = 0;
    $errors = 0;
    
    foreach ($alterCommands as $sql) {
        try {
            $db->exec($sql);
            echo "✅ " . htmlspecialchars($sql) . "<br>\n";
            $success++;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "⚠️ Column already exists: " . htmlspecialchars($sql) . "<br>\n";
            } else {
                echo "❌ Error: " . htmlspecialchars($e->getMessage()) . " - " . htmlspecialchars($sql) . "<br>\n";
                $errors++;
            }
        }
    }
    
    echo "<br><strong>Summary:</strong> $success successful, $errors errors<br>\n";
    
    if ($errors == 0) {
        echo "<br>✅ <strong>All columns added successfully!</strong> You can now use the full Program Cards functionality.<br>\n";
        echo "<a href='/adamson-ccit/public/index.php?page=admin_programs_undergraduate' class='btn btn-primary'>Go to Program Management</a><br>\n";
    }
    
    // Show updated structure
    echo "<br><h4>Updated Table Structure:</h4>\n";
    $stmt = $db->query("DESCRIBE programs_undergraduate");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>\n";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>\n";
    foreach ($columns as $col) {
        $isNew = in_array($col['Field'], ['status', 'ordering', 'website_url', 'icon']);
        $rowStyle = $isNew ? 'background-color: #d4edda;' : '';
        echo "<tr style='$rowStyle'>";
        echo "<td><strong>" . htmlspecialchars($col['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Default']) . "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
    echo "<p><em>New columns are highlighted in green.</em></p>\n";
    
} catch (Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}
?>