<?php
require_once __DIR__ . '/Model.php';
class Program extends Model {
    public static function all() {
        $stmt = self::db()->query('SELECT * FROM programs ORDER BY id DESC');
        return $stmt->fetchAll();
    }
    public static function create($name, $description) {
        $stmt = self::db()->prepare('INSERT INTO programs (name, description) VALUES (?, ?)');
        return $stmt->execute([$name, $description]);
    }
    public static function delete($id) {
        $stmt = self::db()->prepare('DELETE FROM programs WHERE id = ?');
        return $stmt->execute([$id]);
    }
    /**
     * Get the latest published programs, limited by $limit
     * @param int $limit
     * @return array
     */
    public static function latest($limit = 4) {
        $stmt = self::db()->prepare('SELECT * FROM programs ORDER BY id DESC LIMIT ?');
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
