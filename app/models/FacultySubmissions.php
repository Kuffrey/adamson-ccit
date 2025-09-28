<?php
// app/models/FacultySubmissions.php
require_once __DIR__ . '/Model.php';

class FacultySubmissions extends Model {
    protected static $table = 'faculty_submissions';
    
    public static function getByFaculty($facultyId, $type = null) {
        $db = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' WHERE faculty_id = ?';
        $params = [$facultyId];
        
        if ($type) {
            $sql .= ' AND submission_type = ?';
            $params[] = $type;
        }
        
        $sql .= ' ORDER BY submitted_at DESC';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getPendingByReviewer($reviewerRole = null) {
        $db = self::db();
        $sql = 'SELECT fs.*, fp.name as faculty_name, fp.dept 
                FROM ' . self::$table . ' fs
                JOIN faculty_profile fp ON fs.faculty_id = fp.id
                WHERE fs.status IN ("submitted", "under_review")
                ORDER BY fs.submitted_at ASC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getCountsByStatus() {
        $db = self::db();
        $sql = 'SELECT status, COUNT(*) as count FROM ' . self::$table . ' GROUP BY status';
        $stmt = $db->query($sql);
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[$row['status']] = (int)$row['count'];
        }
        return $result;
    }
    
    public static function getPendingCountsByType() {
        $db = self::db();
        $sql = 'SELECT submission_type, COUNT(*) as count FROM ' . self::$table . ' 
                WHERE status IN ("submitted", "under_review") 
                GROUP BY submission_type';
        $stmt = $db->query($sql);
        $result = ['research' => 0, 'certification' => 0, 'news' => 0];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[$row['submission_type']] = (int)$row['count'];
        }
        return $result;
    }
    
    public static function getSubmissionsByFacultyWithPendingCounts() {
        $db = self::db();
        $sql = 'SELECT fp.id, fp.name, fp.dept, fp.role, fp.title, fp.avatar_url,
                COUNT(CASE WHEN fs.submission_type = "research" AND fs.status IN ("submitted", "under_review") THEN 1 END) as pending_research,
                COUNT(CASE WHEN fs.submission_type = "certification" AND fs.status IN ("submitted", "under_review") THEN 1 END) as pending_certifications,
                COUNT(CASE WHEN fs.submission_type = "news" AND fs.status IN ("submitted", "under_review") THEN 1 END) as pending_news,
                MAX(fs.submitted_at) as last_submission_date
                FROM faculty_profile fp
                LEFT JOIN ' . self::$table . ' fs ON fp.id = fs.faculty_id
                GROUP BY fp.id, fp.name, fp.dept, fp.role, fp.title, fp.avatar_url
                ORDER BY fp.name ASC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function create($data) {
        $db = self::db();
        $sql = 'INSERT INTO ' . self::$table . ' 
                (faculty_id, submission_type, title, description, content, category, status, submitted_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['faculty_id'],
            $data['submission_type'],
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['content'] ?? '',
            $data['category'] ?? null,
            $data['status'] ?? 'draft',
            $data['status'] === 'submitted' ? date('Y-m-d H:i:s') : null
        ]);
    }
    
    public static function updateStatus($id, $status, $reviewerId = null, $notes = null) {
        $db = self::db();
        $sql = 'UPDATE ' . self::$table . ' SET status = ?, reviewed_at = ?, reviewed_by = ?, review_notes = ? WHERE id = ?';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $status,
            date('Y-m-d H:i:s'),
            $reviewerId,
            $notes,
            $id
        ]);
    }
    
    public static function getById($id) {
        $db = self::db();
        $sql = 'SELECT fs.*, fp.name as faculty_name, fp.dept, u.username as reviewer_name
                FROM ' . self::$table . ' fs
                LEFT JOIN faculty_profile fp ON fs.faculty_id = fp.id
                LEFT JOIN users u ON fs.reviewed_by = u.id
                WHERE fs.id = ?';
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function approveSubmission(int $submissionId): bool {
        try {
            $this->db->beginTransaction();
            
            // Update submission status
            $stmt = $this->db->prepare("
                UPDATE faculty_submissions 
                SET status = 'approved', approved_date = NOW() 
                WHERE id = ? AND status = 'submitted'
            ");
            $stmt->execute([$submissionId]);
            
            if ($stmt->rowCount() > 0) {
                // Log the approval action
                $this->logActivity($submissionId, 'approved', 'Submission approved by dean');
                $this->db->commit();
                return true;
            } else {
                $this->db->rollback();
                return false;
            }
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error approving submission: " . $e->getMessage());
            return false;
        }
    }

    public function rejectSubmission(int $submissionId, string $rejectionNotes): bool {
        try {
            $this->db->beginTransaction();
            
            // Update submission status with rejection notes
            $stmt = $this->db->prepare("
                UPDATE faculty_submissions 
                SET status = 'rejected', rejection_notes = ?, rejected_date = NOW() 
                WHERE id = ? AND status = 'submitted'
            ");
            $stmt->execute([$rejectionNotes, $submissionId]);
            
            if ($stmt->rowCount() > 0) {
                // Log the rejection action
                $this->logActivity($submissionId, 'rejected', 'Submission rejected: ' . $rejectionNotes);
                $this->db->commit();
                return true;
            } else {
                $this->db->rollback();
                return false;
            }
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error rejecting submission: " . $e->getMessage());
            return false;
        }
    }

    private function logActivity(int $submissionId, string $action, string $details): void {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO faculty_activity_log (submission_id, action, details, created_date) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$submissionId, $action, $details]);
        } catch (Exception $e) {
            error_log("Error logging activity: " . $e->getMessage());
        }
    }

    // Get pending submissions by type for detailed review
    public static function getPendingByType($type = null) {
        $db = self::db();
        $whereClause = "WHERE fs.status IN ('submitted', 'under_review')";
        $params = [];
        
        if ($type) {
            $whereClause .= " AND fs.submission_type = ?";
            $params[] = $type;
        }
        
        $sql = "SELECT fs.*, fp.name, fp.dept, fp.role
                FROM " . self::$table . " fs
                LEFT JOIN faculty_profile fp ON fs.faculty_id = fp.id
                $whereClause
                ORDER BY fs.submitted_at DESC";
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching pending submissions: " . $e->getMessage());
            return [];
        }
    }

    // Get all submissions with faculty details for dean oversight
    public static function getAllSubmissionsWithFacultyDetails() {
        $db = self::db();
        $sql = "SELECT fs.*, 
                       fp.name as faculty_name, 
                       fp.dept, 
                       fp.role as faculty_role,
                       reviewer.username as reviewer_name
                FROM " . self::$table . " fs
                LEFT JOIN faculty_profile fp ON fs.faculty_id = fp.id
                LEFT JOIN users reviewer ON fs.reviewed_by = reviewer.id
                ORDER BY fs.submitted_at DESC";
        
        try {
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching all submissions: " . $e->getMessage());
            return [];
        }
    }
}