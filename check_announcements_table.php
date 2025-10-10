<?php
try {
    // Direct database connection
    $pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== ANNOUNCEMENTS TABLE STRUCTURE ===\n";
    $stmt = $pdo->query('DESCRIBE announcements');
    while ($row = $stmt->fetch()) {
        echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . $row['Null'] . ' | ' . $row['Key'] . ' | ' . $row['Default'] . "\n";
    }
    
    echo "\n=== SAMPLE DATA ===\n";
    $stmt = $pdo->query('SELECT * FROM announcements LIMIT 3');
    while ($row = $stmt->fetch()) {
        $content = $row['content'] ?? $row['body'] ?? '';
        echo 'ID: ' . $row['id'] . ', Title: ' . $row['title'] . ', Content: ' . substr($content, 0, 50) . "\n";
        echo 'Image URL: ' . ($row['image_url'] ?? 'NULL') . "\n";
        echo 'Status: ' . $row['status'] . ', Category: ' . ($row['category'] ?? 'NULL') . "\n\n";
    }
    
    echo "\n=== TOTAL COUNT ===\n";
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM announcements');
    $total = $stmt->fetchColumn();
    echo "Total announcements: $total\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>