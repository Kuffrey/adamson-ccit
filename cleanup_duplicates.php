<?php
$pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit', 'root', '');

echo "Checking for duplicate faculty entries:\n";
$stmt = $pdo->query("SELECT id, name, surname FROM faculty_profile WHERE surname = 'Benito'");
$benitos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($benitos as $benito) {
    echo "ID: {$benito['id']} | Name: {$benito['name']}\n";
}

// Remove the duplicate (keep the lower ID)
if (count($benitos) > 1) {
    $maxId = max(array_column($benitos, 'id'));
    $pdo->exec("DELETE FROM faculty_profile WHERE id = $maxId");
    echo "Removed duplicate with ID: $maxId\n";
}

echo "\nFinal faculty list (alphabetical by surname):\n";
$stmt = $pdo->query("
    SELECT id, prefix, first_name, surname, name, dept, role
    FROM faculty_profile 
    ORDER BY surname ASC, first_name ASC
");

$count = 1;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $displayName = trim(($row['prefix'] ? $row['prefix'] . ' ' : '') . 
                       ($row['first_name'] ? $row['first_name'] . ' ' : '') . 
                       $row['surname']);
    echo sprintf("%2d. %-35s | %-15s | %s\n", 
        $count++,
        $displayName, 
        ucfirst($row['role']),
        ucfirst($row['dept'])
    );
}