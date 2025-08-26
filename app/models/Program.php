<?php
require_once __DIR__ . '/../../config/database.php';
class Program {
    public static function all() {
        global $pdo;
        $stmt = $pdo->query('SELECT * FROM programs ORDER BY id DESC');
        return $stmt->fetchAll();
    }
    public static function create($name, $description) {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO programs (name, description) VALUES (?, ?)');
        return $stmt->execute([$name, $description]);
    }
    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare('DELETE FROM programs WHERE id = ?');
        return $stmt->execute([$id]);
    }
    /**
     * Get the latest published programs, limited by $limit
     * @param int $limit
     * @return array
     */
    public static function latest($limit = 4) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM programs ORDER BY id DESC LIMIT ?');
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
