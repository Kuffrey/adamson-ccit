<?php
// app/models/StudentResearch.php
require_once __DIR__ . '/Model.php';
class StudentResearch extends Model {
    protected static $table = 'student_research';
    public static function getAll() {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' ORDER BY year DESC, id DESC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getById($id) {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' WHERE id = :id';
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
