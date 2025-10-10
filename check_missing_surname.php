<?php
$pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit', 'root', '');
$stmt = $pdo->query("SELECT * FROM faculty_profile WHERE surname IS NULL OR surname = ''");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']} | Name: {$row['name']} | Dept: {$row['dept']}\n";
    echo "Prefix: {$row['prefix']} | First: {$row['first_name']} | Surname: {$row['surname']}\n\n";
}