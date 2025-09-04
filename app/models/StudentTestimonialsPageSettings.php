<?php
// app/models/StudentTestimonialsPageSettings.php
require_once __DIR__ . '/Model.php';
class StudentTestimonialsPageSettings extends Model {
    protected static $table = 'student_testimonials_page_settings';
    public static function getSettings() {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' ORDER BY id DESC LIMIT 1';
        $stmt = $db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
    public static function updateSettings($data) {
        $db = self::db();
        $fields = ['subhero_image_url', 'subhero_lead'];
        $set = implode(', ', array_map(function($f) { return "$f = :$f"; }, $fields));
        $sql = 'UPDATE ' . self::$table . ' SET ' . $set . ', updated_at = CURRENT_TIMESTAMP WHERE id = (SELECT id FROM ' . self::$table . ' ORDER BY id DESC LIMIT 1)';
        $stmt = $db->prepare($sql);
        foreach ($fields as $f) {
            $stmt->bindValue(":$f", $data[$f] ?? null);
        }
        $stmt->execute();
    }
}
