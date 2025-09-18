<?php
// Database connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/models/ProgramsUndergraduateSettings.php';

echo "<h2>Database Connection Test</h2>";

try {
    // Get database connection using the model's method
    $db = ProgramsUndergraduateSettings::db();
    echo "<p style='color:green'>✅ Database connection successful</p>";
    
    // Constants in PHP
    $tableName = 'ug_cards'; // Same as ProgramsUndergraduateSettings::CARDS_TABLE
    $checkStmt = $db->prepare("SHOW TABLES LIKE ?");
    $checkStmt->execute([$tableName]);
    
    if ($checkStmt->rowCount() > 0) {
        echo "<p style='color:green'>✅ Table '{$tableName}' exists</p>";
        
        // Show table structure
        $structureStmt = $db->prepare("DESCRIBE {$tableName}");
        $structureStmt->execute();
        $columns = $structureStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        foreach ($columns as $column) {
            echo "<tr>";
            foreach ($column as $key => $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Show a few records
        $recordsStmt = $db->prepare("SELECT * FROM {$tableName} LIMIT 5");
        $recordsStmt->execute();
        $records = $recordsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Sample Records:</h3>";
        if (count($records) > 0) {
            echo "<table border='1' cellpadding='5'>";
            
            // Headers
            echo "<tr>";
            foreach (array_keys($records[0]) as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr>";
            
            // Data
            foreach ($records as $record) {
                echo "<tr>";
                foreach ($record as $value) {
                    echo "<td>" . (is_null($value) ? '<em>NULL</em>' : htmlspecialchars((string)$value)) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No records found in table.</p>";
        }
        
        // Try a direct SQL update
        echo "<h3>Direct SQL Update Test:</h3>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='action' value='direct_update'>";
        echo "Card ID: <input type='number' name='card_id' value='1' min='1' required>";
        echo "<button type='submit'>Run Direct SQL Update</button>";
        echo "</form>";
        
        // Process direct SQL update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'direct_update') {
            $cardId = (int)$_POST['card_id'];
            $newTitle = 'DIRECT UPDATE ' . date('H:i:s');
            
            echo "<p>Running direct SQL update on card ID: {$cardId}</p>";
            
            try {
                // First verify card exists
                $checkCard = $db->prepare("SELECT id, title FROM {$tableName} WHERE id = ?");
                $checkCard->execute([$cardId]);
                $card = $checkCard->fetch(PDO::FETCH_ASSOC);
                
                if (!$card) {
                    echo "<p style='color:red'>❌ Card ID {$cardId} not found!</p>";
                } else {
                    echo "<p>Original title: " . htmlspecialchars($card['title']) . "</p>";
                    
                    // Direct update with parameters
                    $updateStmt = $db->prepare("UPDATE {$tableName} SET title = ?, updated_at = NOW() WHERE id = ?");
                    $updateStmt->execute([$newTitle, $cardId]);
                    $rowCount = $updateStmt->rowCount();
                    
                    if ($rowCount > 0) {
                        echo "<p style='color:green'>✅ Direct SQL update successful! {$rowCount} rows affected.</p>";
                        
                        // Verify the update
                        $checkCard->execute([$cardId]);
                        $updatedCard = $checkCard->fetch(PDO::FETCH_ASSOC);
                        echo "<p>New title: " . htmlspecialchars($updatedCard['title']) . "</p>";
                    } else {
                        echo "<p style='color:orange'>⚠️ Update statement executed but no rows were affected.</p>";
                    }
                }
            } catch (PDOException $e) {
                echo "<p style='color:red'>❌ SQL error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
        
        // Try using the model's update method
        echo "<h3>Model Update Test:</h3>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='action' value='test_update'>";
        echo "Card ID: <input type='number' name='card_id' value='1' min='1' required>";
        echo "<button type='submit'>Test Model Update</button>";
        echo "</form>";
        
        // Process test update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'test_update') {
            $cardId = (int)$_POST['card_id'];
            
            $updateData = [
                'title' => 'MODEL UPDATE ' . date('H:i:s'),
                '_force_update' => true
            ];
            
            echo "<p>Attempting to update card ID: {$cardId} using model</p>";
            echo "<pre>";
            $result = ProgramsUndergraduateSettings::updateCardById($cardId, $updateData);
            print_r($result);
            echo "</pre>";
            
            if ($result['success']) {
                echo "<p style='color:green'>✅ Update successful!</p>";
            } else {
                echo "<p style='color:red'>❌ Update failed!</p>";
            }
        }
        
    } else {
        echo "<p style='color:red'>❌ Table '{$tableName}' does not exist</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}