<?php
// app/models/StudentCertification.php
require_once __DIR__ . '/Model.php';
class StudentCertification extends Model {
    protected static $table = 'student_certification';
    public static function getAll() {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' ORDER BY id ASC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
