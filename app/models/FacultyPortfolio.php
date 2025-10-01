<?php
require_once __DIR__ . '/Model.php';

class FacultyPortfolio extends Model {
    
    /**
     * Get all portfolio items for a faculty member
     */
    public static function getByUserId($userId) {
        $db = parent::db();
        $stmt = $db->prepare("
            SELECT fp.*, c.name AS company_name
            FROM faculty_portfolio fp
            JOIN companies c ON c.id = fp.company_id
            WHERE fp.user_id = ?
            ORDER BY COALESCE(fp.expire_year,9999), COALESCE(fp.expire_month,12),
                     fp.issue_year DESC, fp.issue_month DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get a specific portfolio item by ID
     */
    public static function getById($id) {
        $db = parent::db();
        $stmt = $db->prepare("
            SELECT fp.*, c.name AS company_name
            FROM faculty_portfolio fp
            JOIN companies c ON c.id = fp.company_id
            WHERE fp.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create a new portfolio item
     */
    public static function create($data) {
        $db = parent::db();
        $stmt = $db->prepare("
            INSERT INTO faculty_portfolio 
            (user_id, name, company_id, issue_month, issue_year, expires, expire_month, expire_year, credential_id, credential_url, visibility, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
        
        return $stmt->execute([
            $data['user_id'], $data['name'], $data['company_id'], 
            $data['issue_month'], $data['issue_year'], $data['expires'], 
            $data['expire_month'], $data['expire_year'], $data['credential_id'], 
            $data['credential_url'], $data['visibility']
        ]);
    }
    
    /**
     * Update an existing portfolio item
     */
    public static function update($id, $data) {
        $db = parent::db();
        $stmt = $db->prepare("
            UPDATE faculty_portfolio SET 
            name = ?, company_id = ?, issue_month = ?, issue_year = ?, expires = ?, 
            expire_month = ?, expire_year = ?, credential_id = ?, credential_url = ?, 
            visibility = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE id = ? AND user_id = ?
        ");
        
        return $stmt->execute([
            $data['name'], $data['company_id'], $data['issue_month'], $data['issue_year'], 
            $data['expires'], $data['expire_month'], $data['expire_year'], $data['credential_id'], 
            $data['credential_url'], $data['visibility'], $id, $data['user_id']
        ]);
    }
    
    /**
     * Delete a portfolio item
     */
    public static function delete($id, $userId) {
        $db = parent::db();
        $stmt = $db->prepare("DELETE FROM faculty_portfolio WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
    
    /**
     * Get portfolio statistics for a faculty member
     */
    public static function getStats($userId) {
        $db = parent::db();
        
        // Total count
        $totalStmt = $db->prepare("SELECT COUNT(*) FROM faculty_portfolio WHERE user_id = ?");
        $totalStmt->execute([$userId]);
        $total = $totalStmt->fetchColumn();
        
        // Active (non-expired) count
        $activeStmt = $db->prepare("
            SELECT COUNT(*) FROM faculty_portfolio 
            WHERE user_id = ? AND (
                expires = 0 OR 
                (expire_year > YEAR(CURDATE())) OR 
                (expire_year = YEAR(CURDATE()) AND expire_month >= MONTH(CURDATE()))
            )
        ");
        $activeStmt->execute([$userId]);
        $active = $activeStmt->fetchColumn();
        
        // Expired count
        $expired = $total - $active;
        
        return [
            'total' => (int)$total,
            'active' => (int)$active,
            'expired' => (int)$expired
        ];
    }
    
    /**
     * Check if a portfolio item belongs to a specific user
     */
    public static function belongsToUser($id, $userId) {
        $db = parent::db();
        $stmt = $db->prepare("SELECT COUNT(*) FROM faculty_portfolio WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetchColumn() > 0;
    }
}
?>