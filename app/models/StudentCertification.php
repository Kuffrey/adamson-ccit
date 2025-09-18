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
                (name, badge_url, issuer, description) 
                VALUES (?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['name'] ?? '',
            $data['badge_url'] ?? '',
            $data['issuer'] ?? '',
            $data['description'] ?? ''
        ]);
    }

    public static function update($id, $data) {
        $db = self::db();
        $sql = 'UPDATE ' . self::$table . ' SET 
                name = ?, badge_url = ?, issuer = ?, description = ? 
                WHERE id = ?';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['name'] ?? '',
            $data['badge_url'] ?? '',
            $data['issuer'] ?? '',
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
