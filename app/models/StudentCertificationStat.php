<?php
// app/models/StudentCertificationStat.php
require_once __DIR__ . '/Model.php';
class StudentCertificationStat extends Model {
    protected static $table = 'student_certification_stat';
    public static function getAll() {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' ORDER BY id ASC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
