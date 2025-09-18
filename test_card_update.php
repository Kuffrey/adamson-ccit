<?php
// Direct SQL test for updating cards
// Place this file in the root folder and access via browser

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once __DIR__ . '/app/models/Model.php';
require_once __DIR__ . '/app/models/ProgramsUndergraduateSettings.php';

function testDirectUpdate($id, $title) {
    try {
        // Use ProgramsUndergraduateSettings which extends Model
        $db = ProgramsUndergraduateSettings::db();
        
        // First check if record exists
        $check = $db->prepare("SELECT id, title FROM ug_cards WHERE id = ?");
        $check->execute([$id]);
        $card = $check->fetch(PDO::FETCH_ASSOC);
        
        if (!$card) {
            return "Error: Card ID $id not found";
        }
        
        $oldTitle = $card['title'];
        
        // Direct SQL update
        $updateSql = "UPDATE ug_cards SET 
                     title = ?, 
                     updated_at = NOW() 
                     WHERE id = ?";
        
        $stmt = $db->prepare($updateSql);
        $result = $stmt->execute([$title, $id]);
        $rowCount = $stmt->rowCount();
        
        if ($result) {
            return "Success: Updated card ID $id. Changed title from '$oldTitle' to '$title'. Rows affected: $rowCount";
        } else {
            $error = $stmt->errorInfo();
            return "SQL Error: " . implode(", ", $error);
        }
    } catch (Exception $e) {
        return "Exception: " . $e->getMessage();
    }
}

function testModelUpdate($id, $title) {
    try {
        $updateData = [
            'title' => $title,
            '_force_update' => true
        ];
        
        $result = ProgramsUndergraduateSettings::updateCardById($id, $updateData);
        
        return [
            'success' => $result['success'],
            'message' => $result['message'],
            'rows' => $result['rows_affected'],
            'details' => $result
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => "Exception: " . $e->getMessage()
        ];
    }
}

// HTML Output
echo '<!DOCTYPE html>
<html>
<head>
    <title>Card Update Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; }
        .success { color: green; }
        .error { color: red; }
        form { margin-bottom: 20px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Card Update Test</h1>';

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['card_id']) ? (int)$_POST['card_id'] : 0;
    $title = isset($_POST['title']) ? $_POST['title'] : 'Test ' . date('H:i:s');
    $method = $_POST['method'] ?? 'direct';
    
    echo "<h2>Test Results</h2>";
    
    if ($method === 'direct') {
        $result = testDirectUpdate($id, $title);
        echo "<pre>" . htmlspecialchars($result) . "</pre>";
    } else {
        $result = testModelUpdate($id, $title);
        echo "<pre>" . ($result['success'] ? '<span class="success">SUCCESS</span>' : '<span class="error">ERROR</span>') . "\n";
        echo htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . "</pre>";
    }
}

// Display test forms
echo '<h2>Test Direct SQL Update</h2>
<form method="post">
    <input type="hidden" name="method" value="direct">
    Card ID: <input type="number" name="card_id" value="1" required><br>
    New Title: <input type="text" name="title" value="Direct Update ' . date('H:i:s') . '" required><br>
    <button type="submit">Update with Direct SQL</button>
</form>

<h2>Test Model Update</h2>
<form method="post">
    <input type="hidden" name="method" value="model">
    Card ID: <input type="number" name="card_id" value="1" required><br>
    New Title: <input type="text" name="title" value="Model Update ' . date('H:i:s') . '" required><br>
    <button type="submit">Update with Model</button>
</form>

</body>
</html>';