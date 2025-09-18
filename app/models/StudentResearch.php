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

    public static function create($data) {
        $db = self::db();
        $sql = 'INSERT INTO ' . self::$table . ' 
                (title, category, year, image_url, link_url, link_label, meta, authors, description) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['title'] ?? '',
            $data['category'] ?? 'publication',
            $data['year'] ?? date('Y'),
            $data['image_url'] ?? '',
            $data['link_url'] ?? '',
            $data['link_label'] ?? '',
            $data['meta'] ?? '',
            $data['authors'] ?? '',
            $data['description'] ?? ''
        ]);
    }

    public static function update($id, $data) {
        $db = self::db();
        $sql = 'UPDATE ' . self::$table . ' SET 
                title = ?, category = ?, year = ?, image_url = ?, link_url = ?, 
                link_label = ?, meta = ?, authors = ?, description = ? 
                WHERE id = ?';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['title'] ?? '',
            $data['category'] ?? 'publication',
            $data['year'] ?? date('Y'),
            $data['image_url'] ?? '',
            $data['link_url'] ?? '',
            $data['link_label'] ?? '',
            $data['meta'] ?? '',
            $data['authors'] ?? '',
            $data['description'] ?? '',
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
