<?php
// app/models/StudentTestimonial.php
require_once __DIR__ . '/Model.php';
class StudentTestimonial extends Model {
    protected static $table = 'student_testimonial';
    public static function getAll() {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' ORDER BY id DESC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getYears() {
        $db = self::db();
        $sql = 'SELECT DISTINCT grad_year FROM ' . self::$table . ' ORDER BY grad_year DESC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
