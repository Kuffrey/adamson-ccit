<?php
// app/models/FacultyCertification.php
require_once __DIR__ . '/Model.php';
class FacultyCertification extends Model {
    protected string $table = 'faculty_certification_award';
    
    // Grouped by year > issuer > certification > faculty
    public static function getGrouped() {
        $db = self::db();
        $sql = 'SELECT fca.year_earned, c.issuer, c.issuer_key, c.cert_title, c.badge_url, c.cert_url, c.verify_url, CONCAT(u.first_name, " ", u.last_name) AS faculty_name
                FROM faculty_certification_award fca
                JOIN users u ON fca.faculty_id = u.id
                JOIN certification c ON fca.certification_id = c.id
                ORDER BY fca.year_earned DESC, c.issuer, c.cert_title, u.first_name, u.last_name';
        $stmt = $db->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $grouped = [];
        foreach ($rows as $row) {
            $year = $row['year_earned'];
            $issuer = $row['issuer'];
            $cert = $row['cert_title'];
            if (!isset($grouped[$year])) $grouped[$year] = [];
            if (!isset($grouped[$year][$issuer])) $grouped[$year][$issuer] = [];
            if (!isset($grouped[$year][$issuer][$cert])) {
                $grouped[$year][$issuer][$cert] = [
                    'badge_url' => $row['badge_url'],
                    'cert_url' => $row['cert_url'],
                    'verify_url' => $row['verify_url'],
                    'faculty' => []
                ];
            }
            $grouped[$year][$issuer][$cert]['faculty'][] = $row['faculty_name'];
        }
        return $grouped;
    }
    
    public function create($data) {
        $requiredFields = ['faculty_id', 'certification_id', 'year_earned', 'status'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                throw new Exception("Field '$field' is required");
            }
        }
        
        $db = self::db();
        $sql = 'INSERT INTO faculty_certification_award (faculty_id, certification_id, year_earned, year_expiry, status) 
                VALUES (:faculty_id, :certification_id, :year_earned, :year_expiry, :status)';
        $stmt = $db->prepare($sql);
        
        return $stmt->execute([
            ':faculty_id' => $data['faculty_id'],
            ':certification_id' => $data['certification_id'],
            ':year_earned' => $data['year_earned'],
            ':year_expiry' => $data['year_expiry'] ?? null,
            ':status' => $data['status']
        ]);
    }
    
    public function update($id, $data) {
        if (!$id) {
            throw new Exception("ID is required for update");
        }
        
        $db = self::db();
        $sql = 'UPDATE faculty_certification_award 
                SET faculty_id = :faculty_id, certification_id = :certification_id, 
                    year_earned = :year_earned, year_expiry = :year_expiry, status = :status 
                WHERE id = :id';
        $stmt = $db->prepare($sql);
        
        return $stmt->execute([
            ':id' => $id,
            ':faculty_id' => $data['faculty_id'],
            ':certification_id' => $data['certification_id'],
            ':year_earned' => $data['year_earned'],
            ':year_expiry' => $data['year_expiry'] ?? null,
            ':status' => $data['status']
        ]);
    }
    
    public function delete($id) {
        if (!$id) {
            throw new Exception("ID is required for delete");
        }
        
        $db = self::db();
        $sql = 'DELETE FROM faculty_certification_award WHERE id = :id';
        $stmt = $db->prepare($sql);
        
        return $stmt->execute([':id' => $id]);
    }
    
    public function getById($id) {
        if (!$id) {
            return null;
        }
        
        $db = self::db();
        $sql = 'SELECT fca.*, c.cert_title, c.issuer, CONCAT(u.first_name, " ", u.last_name) as faculty_name 
                FROM faculty_certification_award fca
                LEFT JOIN certification c ON fca.certification_id = c.id
                LEFT JOIN users u ON fca.faculty_id = u.id
                WHERE fca.id = :id';
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function getAllCertifications() {
        $db = self::db();
        $sql = 'SELECT * FROM certification ORDER BY issuer, cert_title';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getAllFaculty() {
        $db = self::db();
        $sql = 'SELECT id, CONCAT(first_name, " ", last_name) as name FROM users WHERE role = "faculty" ORDER BY first_name, last_name';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAll() {
        $db = self::db();
        $sql = 'SELECT fca.*, c.cert_title, c.issuer, CONCAT(u.first_name, " ", u.last_name) as faculty_name 
                FROM faculty_certification_award fca
                LEFT JOIN certification c ON fca.certification_id = c.id
                LEFT JOIN users u ON fca.faculty_id = u.id
                ORDER BY fca.year_earned DESC, u.first_name, u.last_name, c.cert_title';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
