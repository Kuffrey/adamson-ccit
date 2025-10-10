<?php
$pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit', 'root', '');
$stmt = $pdo->query('SELECT id, name FROM faculty_profile WHERE name = "Dr. Carmelita H. Benito"');
$duplicates = $stmt->fetchAll();
if (count($duplicates) > 1) {
    $maxId = max(array_column($duplicates, 'id'));
    $pdo->exec('DELETE FROM faculty_profile WHERE id = ' . $maxId);
    echo "Removed duplicate Dr. Benito (ID: $maxId)\n";
} else {
    echo "No duplicates found\n";
}

echo "\nFinal clean faculty list:\n";
$stmt = $pdo->query("
    SELECT id, name, role, role_order 
    FROM faculty_profile 
    ORDER BY role_order ASC, surname ASC, first_name ASC
");

$count = 1;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("%2d. [%d] %s\n", $count++, $row['role_order'], $row['name']);
}