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

    public static function getById($id) {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' WHERE id = :id';
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = self::db();
        $sql = 'INSERT INTO ' . self::$table . ' 
                (name, program, grad_year, role, quote, avatar_url, avatar_initials, is_alumni) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['name'] ?? '',
            $data['program'] ?? '',
            $data['grad_year'] ?? date('Y'),
            $data['role'] ?? '',
            $data['quote'] ?? '',
            $data['avatar_url'] ?? '',
            $data['avatar_initials'] ?? '',
            $data['is_alumni'] ?? 0
        ]);
    }

    public static function update($id, $data) {
        $db = self::db();
        $sql = 'UPDATE ' . self::$table . ' SET 
                name = ?, program = ?, grad_year = ?, role = ?, quote = ?, 
                avatar_url = ?, avatar_initials = ?, is_alumni = ? 
                WHERE id = ?';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['name'] ?? '',
            $data['program'] ?? '',
            $data['grad_year'] ?? date('Y'),
            $data['role'] ?? '',
            $data['quote'] ?? '',
            $data['avatar_url'] ?? '',
            $data['avatar_initials'] ?? '',
            $data['is_alumni'] ?? 0,
            $id
        ]);
    }

    public static function delete($id) {
        $db = self::db();
        $sql = 'DELETE FROM ' . self::$table . ' WHERE id = ?';
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
