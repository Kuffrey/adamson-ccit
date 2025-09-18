<?php
// app/models/StudentScholarship.php
require_once __DIR__ . '/Model.php';
class StudentScholarship extends Model {
    protected static $table = 'student_scholarship';
    public static function getAll() {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' ORDER BY FIELD(type,\'Freshmen\',\'University\',\'External\'), name ASC';
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
                (name, type, summary, conditions, requirements, examples, learn_more_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['name'] ?? '',
            $data['type'] ?? '',
            $data['summary'] ?? '',
            $data['conditions'] ?? '',
            $data['requirements'] ?? '',
            $data['examples'] ?? '',
            $data['learn_more_url'] ?? ''
        ]);
    }

    public static function update($id, $data) {
        $db = self::db();
        $sql = 'UPDATE ' . self::$table . ' SET 
                name = ?, type = ?, summary = ?, conditions = ?, requirements = ?, 
                examples = ?, learn_more_url = ? 
                WHERE id = ?';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['name'] ?? '',
            $data['type'] ?? '',
            $data['summary'] ?? '',
            $data['conditions'] ?? '',
            $data['requirements'] ?? '',
            $data['examples'] ?? '',
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
