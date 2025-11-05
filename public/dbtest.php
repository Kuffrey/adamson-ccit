<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
$db   = getenv('MYSQLDATABASE') ?: 'railway';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: 'YOUR_PASSWORD_HERE';
$port = getenv('MYSQLPORT') ?: 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    echo "✅ Connected successfully to MySQL!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
