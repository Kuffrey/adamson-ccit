<?php
// Fix issues with faculty profile system
try {
    $pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== FIXING FACULTY PROFILE ISSUES ===\n";
    
    // 1. Remove duplicate entries
    echo "\n1. Checking for duplicates:\n";
    $stmt = $pdo->query("
        SELECT name, COUNT(*) as count, GROUP_CONCAT(id) as ids 
        FROM faculty_profile 
        GROUP BY name 
        HAVING COUNT(*) > 1
    ");
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($duplicates as $dup) {
        echo "Duplicate found: {$dup['name']} (IDs: {$dup['ids']})\n";
        $ids = explode(',', $dup['ids']);
        // Keep the first one, delete the rest
        for ($i = 1; $i < count($ids); $i++) {
            $pdo->prepare("DELETE FROM faculty_profile WHERE id = ?")->execute([$ids[$i]]);
            echo "  Deleted duplicate ID: {$ids[$i]}\n";
        }
    }
    
    // 2. Fix role_order for all entries
    echo "\n2. Fixing role_order values:\n";
    $roleOrders = [
        'dean' => 1,
        'chair' => 2,
        'full' => 3,
        'part' => 4,
        'lecturer' => 5
    ];
    
    foreach ($roleOrders as $role => $order) {
        $stmt = $pdo->prepare("UPDATE faculty_profile SET role_order = ? WHERE role = ?");
        $result = $stmt->execute([$order, $role]);
        $affected = $stmt->rowCount();
        echo "✓ Updated $affected faculty members with role '$role' to order $order\n";
    }
    
    // 3. Check database structure
    echo "\n3. Checking database structure:\n";
    $stmt = $pdo->query("DESCRIBE faculty_profile");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasMiddleInitial = false;
    $hasRoleOrder = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] === 'middle_initial') {
            $hasMiddleInitial = true;
            echo "✓ middle_initial column: " . $col['Type'] . "\n";
        }
        if ($col['Field'] === 'role_order') {
            $hasRoleOrder = true;
            echo "✓ role_order column: " . $col['Type'] . "\n";
        }
    }
    
    if (!$hasMiddleInitial) echo "❌ middle_initial column missing\n";
    if (!$hasRoleOrder) echo "❌ role_order column missing\n";
    
    // 4. Show final faculty list with proper ordering
    echo "\n4. Faculty list with proper role-based ordering:\n";
    echo str_repeat("-", 80) . "\n";
    
    $stmt = $pdo->query("
        SELECT id, name, prefix, first_name, middle_initial, surname, role, role_order, dept, avatar_initials
        FROM faculty_profile 
        ORDER BY role_order ASC, surname ASC, first_name ASC
    ");
    
    while ($f = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $roleLabel = match($f['role']) {
            'dean' => 'DEAN',
            'chair' => 'CHAIR', 
            'full' => 'FULL-TIME',
            'part' => 'PART-TIME',
            'lecturer' => 'LECTURER',
            default => 'UNKNOWN'
        };
        
        echo sprintf("[%d] %-12s | %s (ID: %d)\n", 
            $f['role_order'],
            $roleLabel,
            $f['name'],
            $f['id']
        );
        
        echo "    Components: " . ($f['prefix'] ?: 'N/A') . " | " . 
             ($f['first_name'] ?: 'N/A') . " | " . 
             ($f['middle_initial'] ?: 'N/A') . " | " . 
             ($f['surname'] ?: 'N/A') . "\n";
        echo "    " . strtoupper($f['dept']) . " | Initials: " . ($f['avatar_initials'] ?: 'N/A') . "\n";
        echo str_repeat("-", 80) . "\n";
    }
    
    echo "\n✅ FACULTY PROFILE SYSTEM FIXED!\n";
    echo "✓ Duplicates removed\n";
    echo "✓ Role ordering corrected\n";
    echo "✓ Hierarchical display: Dean → Chair → Full-Time → Part-Time → Lecturer\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}