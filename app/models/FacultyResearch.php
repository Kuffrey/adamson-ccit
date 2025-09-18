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

    public static function getById($id) {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' WHERE id = ?';
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = self::db();
        $sql = 'INSERT INTO ' . self::$table . ' 
                (title, authors, dept, type, year, venue, pdf_url, view_url, image_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['title'] ?? '',
            $data['authors'] ?? '',
            $data['dept'] ?? '',
            $data['type'] ?? '',
            $data['year'] ?? date('Y'),
            $data['venue'] ?? '',
            $data['pdf_url'] ?? '',
            $data['view_url'] ?? '',
            $data['image_url'] ?? ''
        ]);
    }

    public static function update($id, $data) {
        $db = self::db();
        $sql = 'UPDATE ' . self::$table . ' SET 
                title = ?, authors = ?, dept = ?, type = ?, year = ?, 
                venue = ?, pdf_url = ?, view_url = ?, image_url = ? 
                WHERE id = ?';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['title'] ?? '',
            $data['authors'] ?? '',
            $data['dept'] ?? '',
            $data['type'] ?? '',
            $data['year'] ?? date('Y'),
            $data['venue'] ?? '',
            $data['pdf_url'] ?? '',
            $data['view_url'] ?? '',
            $data['image_url'] ?? '',
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
