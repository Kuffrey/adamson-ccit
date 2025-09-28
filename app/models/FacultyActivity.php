<?php
// app/models/FacultyActivity.php
require_once __DIR__ . '/Model.php';

class FacultyActivity extends Model {
    protected static $table = 'faculty_activity_log';
    
    public static function logActivity($facultyId, $type, $description, $relatedId = null, $relatedType = null) {
        $db = self::db();
        $sql = 'INSERT INTO ' . self::$table . ' 
                (faculty_id, activity_type, description, related_id, related_type, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $facultyId,
            $type,
            $description,
            $relatedId,
            $relatedType,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
    
    public static function getByFaculty($facultyId, $limit = 20) {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' 
                WHERE faculty_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?';
        $stmt = $db->prepare($sql);
        $stmt->execute([$facultyId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getRecentActivity($limit = 50) {
        $db = self::db();
        $sql = 'SELECT fal.*, fp.name as faculty_name, fp.dept
                FROM ' . self::$table . ' fal
                JOIN faculty_profile fp ON fal.faculty_id = fp.id
                ORDER BY fal.created_at DESC
                LIMIT ?';
        $stmt = $db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getCountsByType() {
        $db = self::db();
        $sql = 'SELECT activity_type, COUNT(*) as count 
                FROM ' . self::$table . ' 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY activity_type';
        $stmt = $db->query($sql);
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[$row['activity_type']] = (int)$row['count'];
        }
        return $result;
    }
    
    public static function getFacultyActivitySummary() {
        $db = self::db();
        $sql = 'SELECT fp.id, fp.name, fp.dept,
                COUNT(fal.id) as total_activities,
                MAX(fal.created_at) as last_activity
                FROM faculty_profile fp
                LEFT JOIN ' . self::$table . ' fal ON fp.id = fal.faculty_id
                WHERE fal.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY fp.id, fp.name, fp.dept
                ORDER BY total_activities DESC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}