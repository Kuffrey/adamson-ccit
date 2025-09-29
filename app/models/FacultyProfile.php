<?php
// app/models/FacultyProfile.php
require_once __DIR__ . '/Model.php';

class FacultyProfile extends Model {
    /**
     * Create a faculty profile from a student profile array
     * Sets role to 'faculty' and maps relevant fields
     */
    public static function createFromStudentProfile(array $studentProfile): bool {
        $db = self::db();
        $sql = 'INSERT INTO ' . self::$table . ' (name, dept, role, title, avatar_url, avatar_initials, badges, ordering) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $name = trim(($studentProfile['first_name'] ?? '') . ' ' . ($studentProfile['last_name'] ?? ''));
        $dept = $studentProfile['program'] ?? '';
        $role = 'faculty';
        $title = $studentProfile['title'] ?? '';
        $avatar_url = $studentProfile['profile_image'] ?? '';
        $avatar_initials = strtoupper(substr($studentProfile['first_name'] ?? '', 0, 1) . substr($studentProfile['last_name'] ?? '', 0, 1));
        $badges = '';
        $ordering = 0;
        return $db->prepare($sql)->execute([
            $name,
            $dept,
            $role,
            $title,
            $avatar_url,
            $avatar_initials,
            $badges,
            $ordering
        ]);
    }
    protected static $table = 'faculty_profile';
    
    public static function getAll() {
        $db = self::db();
        $sql = 'SELECT id, name, dept, role, title, avatar_url, 
                CASE WHEN avatar_url IS NOT NULL AND avatar_url != "" THEN "active" ELSE "active" END as status
                FROM ' . self::$table . ' ORDER BY ordering ASC, id ASC';
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

    public static function getById($id) {
        $db = self::db();
        $sql = 'SELECT id, name, dept, role, title, avatar_url FROM ' . self::$table . ' WHERE id = ?';
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = self::db();
        $sql = 'INSERT INTO ' . self::$table . ' 
                (name, dept, role, title, avatar_url, avatar_initials, badges, ordering) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['name'] ?? '',
            $data['dept'] ?? '',
            $data['role'] ?? '',
            $data['title'] ?? '',
            $data['avatar_url'] ?? '',
            $data['avatar_initials'] ?? '',
            $data['badges'] ?? '',
            $data['ordering'] ?? 0
        ]);
    }

    public static function update($id, $data) {
        $db = self::db();
        $sql = 'UPDATE ' . self::$table . ' SET 
                name = ?, dept = ?, role = ?, title = ?, avatar_url = ?, 
                avatar_initials = ?, badges = ?, ordering = ? 
                WHERE id = ?';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['name'] ?? '',
            $data['dept'] ?? '',
            $data['role'] ?? '',
            $data['title'] ?? '',
            $data['avatar_url'] ?? '',
            $data['avatar_initials'] ?? '',
            $data['badges'] ?? '',
            $data['ordering'] ?? 0,
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
