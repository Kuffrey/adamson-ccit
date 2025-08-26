<?php
require_once __DIR__ . '/../../config/database.php';
class Faculty {
    public static function all() {
        global $pdo;
        $stmt = $pdo->query('SELECT * FROM faculty ORDER BY id DESC');
        return $stmt->fetchAll();
    }
    public static function create($name, $profile) {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO faculty (name, profile) VALUES (?, ?)');
        return $stmt->execute([$name, $profile]);
    }
    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare('DELETE FROM faculty WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
