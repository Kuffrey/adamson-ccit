<?php
// app/models/FacultyProfile.php
require_once __DIR__ . '/Model.php';
class FacultyProfile extends Model {
    protected static $table = 'faculty_profile';
    public static function getAll() {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' ORDER BY ordering ASC, id ASC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getDepartments() {
        $db = self::db();
        $sql = 'SELECT DISTINCT dept FROM ' . self::$table . ' ORDER BY dept ASC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    public static function getRoles() {
        $db = self::db();
        $sql = 'SELECT DISTINCT role FROM ' . self::$table . ' ORDER BY role ASC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
