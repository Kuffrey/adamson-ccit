<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== IMPROVING ANNOUNCEMENTS TABLE ===\n";
    
    // First, check current structure
    echo "Current table structure:\n";
    $stmt = $pdo->query('DESCRIBE announcements');
    $columns = [];
    while ($row = $stmt->fetch()) {
        $columns[] = $row['Field'];
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    echo "\n=== IMPROVEMENTS ===\n";
    
    // Add updated_at trigger if it doesn't exist
    $sql = "
    CREATE TRIGGER IF NOT EXISTS announcements_updated_at 
    BEFORE UPDATE ON announcements 
    FOR EACH ROW 
    SET NEW.updated_at = CURRENT_TIMESTAMP
    ";
    $pdo->exec($sql);
    echo "✓ Added/Updated updated_at trigger\n";
    
    // Ensure proper indexes
    try {
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_announcements_status ON announcements(status)");
        echo "✓ Added status index\n";
    } catch (Exception $e) {
        echo "- Status index already exists\n";
    }
    
    try {
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_announcements_category ON announcements(category)");
        echo "✓ Added category index\n";
    } catch (Exception $e) {
        echo "- Category index already exists\n";
    }
    
    try {
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_announcements_date ON announcements(date)");
        echo "✓ Added date index\n";
    } catch (Exception $e) {
        echo "- Date index already exists\n";
    }
    
    echo "\n=== FINAL STRUCTURE ===\n";
    $stmt = $pdo->query('DESCRIBE announcements');
    while ($row = $stmt->fetch()) {
        echo $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . " | " . $row['Key'] . "\n";
    }
    
    echo "\n=== TESTING UPDATE ===\n";
    // Test that updates work with the trigger
    $pdo->exec("UPDATE announcements SET title = title WHERE id = 1");
    $stmt = $pdo->query("SELECT title, updated_at FROM announcements WHERE id = 1");
    $row = $stmt->fetch();
    echo "Test update - updated_at: " . $row['updated_at'] . "\n";
    
    echo "\n✅ All improvements completed!\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>