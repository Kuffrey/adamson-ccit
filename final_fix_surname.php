<?php
$pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit', 'root', '');
$pdo->exec("UPDATE faculty_profile SET prefix = 'Dr.', first_name = 'Carmelita H.', surname = 'Benito' WHERE id = 13");
echo "Fixed faculty ID 13\n";

echo "\nFinal check - all faculty in alphabetical order:\n";
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
        $row['surname'],
        $row['dept']
    );
}