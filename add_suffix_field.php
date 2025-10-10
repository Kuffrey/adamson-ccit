<?php
// Add suffix field to faculty_profile table
try {
    $pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== ADDING SUFFIX FIELD TO FACULTY PROFILE ===\n";
    
    // Add suffix column after surname
    $pdo->exec("ALTER TABLE faculty_profile ADD COLUMN suffix VARCHAR(20) DEFAULT NULL AFTER surname");
    echo "✓ Added suffix column\n";
    
    echo "\n=== UPDATED TABLE STRUCTURE ===\n";
    $stmt = $pdo->query('DESCRIBE faculty_profile');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (in_array($row['Field'], ['prefix', 'first_name', 'middle_initial', 'surname', 'suffix', 'name'])) {
            echo sprintf("%-20s %-15s %-10s\n", $row['Field'], $row['Type'], $row['Null']);
        }
    }
    
    echo "\n=== PARSING EXISTING NAMES FOR SUFFIXES ===\n";
    
    // Get all existing faculty to check for suffixes in names
    $stmt = $pdo->query("SELECT id, name, surname FROM faculty_profile");
    $faculty = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $commonSuffixes = ['Jr.', 'Sr.', 'III', 'IV', 'V', 'PhD', 'MD', 'Esq.', 'CPA', 'PE', 'MSIT'];
    
    foreach ($faculty as $f) {
        $originalName = $f['name'];
        $foundSuffix = '';
        
        // Check if name contains common suffixes
        foreach ($commonSuffixes as $suffix) {
            if (stripos($originalName, $suffix) !== false) {
                $foundSuffix = $suffix;
                break;
            }
        }
        
        if ($foundSuffix) {
            echo "Processing: {$originalName}\n";
            echo "  Found suffix: $foundSuffix\n";
            
            // For MSIT case (already in database)
            if ($foundSuffix === 'MSIT' && $f['id'] == 2) {
                // Update Santiago's record
                $pdo->prepare("UPDATE faculty_profile SET suffix = ? WHERE id = ?")->execute(['MSIT', 2]);
                // Clean up the name by removing MSIT from it
                $cleanName = str_replace(', MSIT', '', $originalName);
                $pdo->prepare("UPDATE faculty_profile SET name = ? WHERE id = ?")->execute([$cleanName, 2]);
                echo "  ✓ Updated Santiago's record with MSIT suffix\n";
            }
        }
    }
    
    echo "\n=== FINAL FACULTY LIST WITH NAME COMPONENTS ===\n";
    $stmt = $pdo->query("
        SELECT id, name, prefix, first_name, middle_initial, surname, suffix, role, role_order 
        FROM faculty_profile 
        ORDER BY role_order ASC, surname ASC, first_name ASC
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("ID: %-2s | %s\n", $row['id'], $row['name']);
        echo sprintf("        Components: [%s] [%s] [%s] [%s] [%s]\n", 
            $row['prefix'] ?: 'None',
            $row['first_name'] ?: 'None', 
            $row['middle_initial'] ?: 'None',
            $row['surname'] ?: 'None',
            $row['suffix'] ?: 'None'
        );
        echo str_repeat("-", 60) . "\n";
    }
    
    echo "\n✅ Suffix field added successfully!\n";
    echo "Common suffixes supported: " . implode(', ', $commonSuffixes) . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}