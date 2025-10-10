<?php
// Add middle initial field and implement role-based ordering
try {
    $pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== ADDING MIDDLE INITIAL FIELD ===\n";
    
    // Add middle_initial column
    $pdo->exec("ALTER TABLE faculty_profile ADD COLUMN middle_initial VARCHAR(10) DEFAULT NULL AFTER first_name");
    echo "✓ Added middle_initial column\n";
    
    // Add role_order column for custom ordering
    $pdo->exec("ALTER TABLE faculty_profile ADD COLUMN role_order INT DEFAULT 999 AFTER role");
    echo "✓ Added role_order column\n";
    
    echo "\n=== UPDATING ROLE ORDERING ===\n";
    
    // Set role_order based on hierarchy: Dean = 1, Chair = 2, Full = 3, Part = 4, Lecturer = 5
    $roleOrders = [
        'dean' => 1,
        'chair' => 2, 
        'full' => 3,
        'part' => 4,
        'lecturer' => 5
    ];
    
    foreach ($roleOrders as $role => $order) {
        $stmt = $pdo->prepare("UPDATE faculty_profile SET role_order = ? WHERE role = ?");
        $stmt->execute([$order, $role]);
        echo "✓ Set role '$role' to order $order\n";
    }
    
    echo "\n=== UPDATED TABLE STRUCTURE ===\n";
    $stmt = $pdo->query('DESCRIBE faculty_profile');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-20s %-15s %-10s\n", $row['Field'], $row['Type'], $row['Null']);
    }
    
    echo "\n=== FACULTY WITH NEW ORDERING ===\n";
    $stmt = $pdo->query("
        SELECT id, prefix, first_name, middle_initial, surname, name, role, role_order, dept 
        FROM faculty_profile 
        ORDER BY role_order ASC, surname ASC, first_name ASC
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("Order: %-2s | Role: %-10s | %s %s %s %s (%s) - %s\n", 
            $row['role_order'],
            ucfirst($row['role']),
            $row['prefix'] ?: '',
            $row['first_name'] ?: '',
            $row['middle_initial'] ?: '',
            $row['surname'] ?: '',
            $row['name'],
            strtoupper($row['dept'])
        );
    }
    
    echo "\n✅ Database structure updated successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}