<?php
// app/models/StudentOrganization.php
require_once __DIR__ . '/Model.php';
class StudentOrganization extends Model {
    protected static $table = 'student_organization';
    public static function getAll() {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' ORDER BY name ASC';
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
                (name, type, logo_url, audience, summary, facebook_url, instagram_url, x_url, learn_more_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['name'] ?? '',
            $data['type'] ?? '',
            $data['logo_url'] ?? '',
            $data['audience'] ?? '',
            $data['summary'] ?? '',
            $data['facebook_url'] ?? '',
            $data['instagram_url'] ?? '',
            $data['x_url'] ?? '',
            $data['learn_more_url'] ?? ''
        ]);
    }

    public static function update($id, $data) {
        $db = self::db();
        $sql = 'UPDATE ' . self::$table . ' SET 
                name = ?, type = ?, logo_url = ?, audience = ?, summary = ?, 
                facebook_url = ?, instagram_url = ?, x_url = ?, learn_more_url = ? 
                WHERE id = ?';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['name'] ?? '',
            $data['type'] ?? '',
            $data['logo_url'] ?? '',
            $data['audience'] ?? '',
            $data['summary'] ?? '',
            $data['facebook_url'] ?? '',
            $data['instagram_url'] ?? '',
            $data['x_url'] ?? '',
            $data['learn_more_url'] ?? '',
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
