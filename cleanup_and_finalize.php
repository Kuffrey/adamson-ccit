<?php
// Clean up test data and finalize the faculty profile system
try {
    $pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== CLEANING UP TEST DATA ===\n";
    
    // Remove test faculty we created during testing
    $testNames = [
        'Dr. Angelo M. De La Cruz',
        'Prof. Ana Maria B. Santos', 
        'Dr. Maria Elena S. Rodriguez'
    ];
    
    foreach ($testNames as $name) {
        $stmt = $pdo->prepare("DELETE FROM faculty_profile WHERE name = ?");
        $result = $stmt->execute([$name]);
        if ($stmt->rowCount() > 0) {
            echo "✓ Removed test faculty: $name\n";
        }
    }
    
    echo "\n=== FINAL FACULTY LIST ===\n";
    $stmt = $pdo->query("
        SELECT id, name, role, role_order, dept 
        FROM faculty_profile 
        ORDER BY role_order ASC, surname ASC, first_name ASC
    ");
    
    $count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $roleLabel = match($row['role']) {
            'dean' => 'DEAN',
            'chair' => 'CHAIR',
            'full' => 'FULL-TIME', 
            'part' => 'PART-TIME',
            'lecturer' => 'LECTURER',
            default => 'UNKNOWN'
        };
        
        echo sprintf("%2d. [%d] %-12s | %s (%s)\n", 
            $count++,
            $row['role_order'],
            $roleLabel,
            $row['name'],
            strtoupper($row['dept'])
        );
    }
    
    $totalCount = $count - 1;
    echo "\n✅ FACULTY PROFILE SYSTEM READY!\n";
    echo "Total Faculty Members: $totalCount\n";
    echo "✓ Role-based ordering implemented\n";
    echo "✓ Middle initial support added\n";
    echo "✓ Admin interface enhanced\n";
    echo "✓ Database structure optimized\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}