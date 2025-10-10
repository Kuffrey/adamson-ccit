<?php
// Improve faculty_profile table with prefix and surname fields for better ordering
try {
    $pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== IMPROVING FACULTY_PROFILE TABLE ===\n";
    
    // Add new columns
    echo "Adding prefix and surname columns...\n";
    
    // Add prefix column for titles like Dr., Mrs., Mr., etc.
    $pdo->exec("ALTER TABLE faculty_profile ADD COLUMN prefix VARCHAR(20) DEFAULT NULL AFTER name");
    echo "✓ Added prefix column\n";
    
    // Add surname column for proper alphabetical sorting
    $pdo->exec("ALTER TABLE faculty_profile ADD COLUMN surname VARCHAR(50) DEFAULT NULL AFTER prefix");
    echo "✓ Added surname column\n";
    
    // Add first_name column for better name management
    $pdo->exec("ALTER TABLE faculty_profile ADD COLUMN first_name VARCHAR(50) DEFAULT NULL AFTER surname");
    echo "✓ Added first_name column\n";
    
    echo "\n=== PARSING EXISTING NAMES ===\n";
    
    // Get all existing faculty
    $stmt = $pdo->query("SELECT id, name FROM faculty_profile");
    $faculty = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($faculty as $f) {
        $fullName = trim($f['name']);
        echo "Processing: $fullName\n";
        
        // Extract prefix (Dr., Mrs., Mr., Ms., Prof., etc.)
        $prefix = '';
        $nameWithoutPrefix = $fullName;
        
        $prefixes = ['Dr.', 'Prof.', 'Mrs.', 'Mr.', 'Ms.', 'Miss.', 'Sir.', 'Ma\'am.'];
        foreach ($prefixes as $p) {
            if (stripos($fullName, $p) === 0) {
                $prefix = $p;
                $nameWithoutPrefix = trim(substr($fullName, strlen($p)));
                break;
            }
        }
        
        // Split remaining name into parts
        $nameParts = explode(' ', $nameWithoutPrefix);
        $nameParts = array_filter($nameParts); // Remove empty parts
        
        if (count($nameParts) >= 2) {
            // Last part is surname, everything else is first name
            $surname = array_pop($nameParts);
            $firstName = implode(' ', $nameParts);
        } else {
            // Only one name part, treat as surname
            $firstName = '';
            $surname = $nameParts[0] ?? '';
        }
        
        // Update the record
        $updateStmt = $pdo->prepare("
            UPDATE faculty_profile 
            SET prefix = ?, surname = ?, first_name = ? 
            WHERE id = ?
        ");
        $updateStmt->execute([$prefix ?: null, $surname, $firstName ?: null, $f['id']]);
        
        echo "  → Prefix: '$prefix', First: '$firstName', Surname: '$surname'\n";
    }
    
    echo "\n=== UPDATED TABLE STRUCTURE ===\n";
    $stmt = $pdo->query('DESCRIBE faculty_profile');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-20s %-15s %-10s\n", $row['Field'], $row['Type'], $row['Null']);
    }
    
    echo "\n=== SAMPLE DATA WITH NEW FIELDS ===\n";
    $stmt = $pdo->query("
        SELECT id, prefix, first_name, surname, name, dept 
        FROM faculty_profile 
        ORDER BY surname ASC, first_name ASC
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("ID: %-3s %s %s %s (Original: %s) - %s\n", 
            $row['id'], 
            $row['prefix'] ?: '', 
            $row['first_name'] ?: '', 
            $row['surname'] ?: '', 
            $row['name'],
            $row['dept']
        );
    }
    
    echo "\n✅ Faculty profile table improved successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}