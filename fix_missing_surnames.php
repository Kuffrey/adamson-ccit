<?php
// Fix any faculty members missing surname data
try {
    $pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== FIXING MISSING SURNAME DATA ===\n";
    
    // Find faculty with missing surname
    $stmt = $pdo->query("SELECT id, name, surname FROM faculty_profile WHERE surname IS NULL OR surname = ''");
    $missingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($missingData as $faculty) {
        echo "Fixing faculty ID {$faculty['id']}: {$faculty['name']}\n";
        
        // Manually set surname for known entries
        if ($faculty['id'] == 12) { // This appears to be the one with missing surname
            $pdo->exec("UPDATE faculty_profile SET surname = 'Benito', first_name = 'Carmelita H.', prefix = 'Dr.' WHERE id = 12");
            echo "✓ Fixed Dr. Carmelita H. Benito\n";
        }
    }
    
    echo "\n=== FINAL ALPHABETICAL ORDER ===\n";
    $stmt = $pdo->query("
        SELECT id, prefix, first_name, surname, name, dept 
        FROM faculty_profile 
        ORDER BY surname ASC, first_name ASC
    ");
    
    $count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $displayName = trim(($row['prefix'] ? $row['prefix'] . ' ' : '') . 
                           ($row['first_name'] ? $row['first_name'] . ' ' : '') . 
                           $row['surname']);
        echo sprintf("%2d. %-35s | Surname: %-20s | Dept: %s\n", 
            $count++,
            $displayName, 
            $row['surname'] ?: 'N/A',
            $row['dept']
        );
    }
    
    echo "\n✅ Faculty ordering fixed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}