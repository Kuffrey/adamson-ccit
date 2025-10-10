<?php
// Fix the name parsing issues and update problematic entries
try {
    $pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== FIXING NAME PARSING ISSUES ===\n";
    
    // Fix the Santiago entry
    $pdo->exec("
        UPDATE faculty_profile 
        SET first_name = 'Archie G.', 
            surname = 'Santiago', 
            title = CONCAT(COALESCE(title, ''), ' MSIT')
        WHERE id = 2
    ");
    echo "✓ Fixed Santiago entry\n";
    
    echo "\n=== CORRECTED DATA ===\n";
    $stmt = $pdo->query("
        SELECT id, prefix, first_name, surname, name, title, dept 
        FROM faculty_profile 
        ORDER BY surname ASC, first_name ASC
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $displayName = trim(($row['prefix'] ? $row['prefix'] . ' ' : '') . 
                           ($row['first_name'] ? $row['first_name'] . ' ' : '') . 
                           $row['surname']);
        echo sprintf("ID: %-3s %-35s | Title: %-25s | Dept: %s\n", 
            $row['id'], 
            $displayName,
            $row['title'] ?: 'N/A',
            $row['dept']
        );
    }
    
    echo "\n✅ Name parsing corrected!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}