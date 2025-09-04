<?php
// app/models/StudentScholarship.php
require_once __DIR__ . '/Model.php';
class StudentScholarship extends Model {
    protected static $table = 'student_scholarship';
    public static function getAll() {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' ORDER BY FIELD(type,\'Freshmen\',\'University\',\'External\'), name ASC';
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
