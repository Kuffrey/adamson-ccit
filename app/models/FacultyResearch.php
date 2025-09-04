<?php
// app/models/FacultyResearch.php
require_once __DIR__ . '/Model.php';
class FacultyResearch extends Model {
    protected static $table = 'faculty_research';
    public static function getAll() {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' ORDER BY year DESC, id DESC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getYears() {
        $db = self::db();
        $sql = 'SELECT DISTINCT year FROM ' . self::$table . ' ORDER BY year DESC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
