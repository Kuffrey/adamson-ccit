<?php
declare(strict_types=1);

final class DB {
    private static ?PDO $pdo = null;

    public static function pdo(): PDO {
        if (self::$pdo === null) {
            $dsn  = 'mysql:host=127.0.0.1;dbname=ccit_website;charset=utf8mb4';
            $user = 'root';
            $pass = '';
            $opt  = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            self::$pdo = new PDO($dsn, $user, $pass, $opt);
        }
        return self::$pdo;
    }
}
