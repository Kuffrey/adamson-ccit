<?php
// app/models/DeanLogs.php - Model for managing dean activity logs
require_once __DIR__ . '/Model.php';

class DeanLogs extends Model {
    protected static $table = 'dean_logs';
    
    // Standard CRUD actions that should be logged
    const CRUD_ACTIONS = [
        'CREATE'  => 'create',
        'UPDATE'  => 'update', 
        'DELETE'  => 'delete',
        'APPROVE' => 'approve',
        'REJECT'  => 'reject'
    ];
    
    // Important tables to log activities for
    const LOGGED_TABLES = [
        'faculty_submissions',
        'faculty_research',
        'faculty_profiles',
        'news',
        'events',
        'announcements'
    ];
    
    /**
     * Log essential CRUD actions only
     */
    public static function logCrud($userId, $action, $tableName, $recordId = null, $details = null) {
        // Only log if it's a valid CRUD action and important table
        if (!in_array($action, self::CRUD_ACTIONS, true) || !in_array($tableName, self::LOGGED_TABLES, true)) {
            return true; // Skip logging but don't fail
        }
        return self::log($userId, $action, $tableName, $recordId, $details);
    }
    
    /**
     * Log an action (internal method)
     */
    private static function log($userId, $action, $tableName = null, $recordId = null, $details = null) {
        try {
            $db = self::db();
            
            // Get client info
            $ipAddress = $_SERVER['REMOTE_ADDR']     ?? '127.0.0.1';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            $sql = "INSERT INTO dean_logs (user_id, action, table_name, record_id, details, ip_address, user_agent) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                $userId,
                $action,
                $tableName,
                $recordId,
                $details,
                $ipAddress,
                substr($userAgent, 0, 500) // Limit user agent length
            ]);
            
        } catch (PDOException $e) {
            error_log("DeanLogs::log error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Convenience methods for standard CRUD operations
     */
    public static function logCreate($userId, $tableName, $recordId, $details = null) {
        return self::logCrud($userId, self::CRUD_ACTIONS['CREATE'], $tableName, $recordId, $details);
    }
    public static function logUpdate($userId, $tableName, $recordId, $details = null) {
        return self::logCrud($userId, self::CRUD_ACTIONS['UPDATE'], $tableName, $recordId, $details);
    }
    public static function logDelete($userId, $tableName, $recordId, $details = null) {
        return self::logCrud($userId, self::CRUD_ACTIONS['DELETE'], $tableName, $recordId, $details);
    }
    public static function logApprove($userId, $tableName, $recordId, $details = null) {
        return self::logCrud($userId, self::CRUD_ACTIONS['APPROVE'], $tableName, $recordId, $details);
    }
    public static function logReject($userId, $tableName, $recordId, $details = null) {
        return self::logCrud($userId, self::CRUD_ACTIONS['REJECT'], $tableName, $recordId, $details);
    }
    
    /**
     * Get all logs with pagination and filtering
     * @param int $page
     * @param int $limit
     * @param ?string $action     Filter by action
     * @param ?string $tableName  Filter by table name
     * @param ?string $search     Free-text search across details/username/etc.
     */
    public static function getAllWithPagination($page = 1, $limit = 50, $action = null, $tableName = null, $search = null) {
        try {
            $db = self::db();
            
            $page   = max(1, (int)$page);
            $limit  = max(1, (int)$limit);
            $offset = ($page - 1) * $limit;
            
            // Build WHERE clause
            $where   = [];
            $params  = [];

            if ($action) {
                $where[]  = "dl.action = ?";
                $params[] = $action;
            }
            if ($tableName) {
                $where[]  = "dl.table_name = ?";
                $params[] = $tableName;
            }
            if ($search !== null && $search !== '') {
                // Search across multiple fields
                $where[]  = "(dl.details LIKE ? OR dl.action LIKE ? OR dl.table_name LIKE ? OR CAST(dl.record_id AS CHAR) LIKE ? OR dl.ip_address LIKE ? OR u.username LIKE ?)";
                $like     = '%' . $search . '%';
                // push same $like for each placeholder
                array_push($params, $like, $like, $like, $like, $like, $like);
            }
            
            $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
            
            // LIMIT/OFFSET are integers; safe to inject after casting
            $sql = "SELECT dl.*, u.username
                    FROM dean_logs dl
                    LEFT JOIN users u ON dl.user_id = u.id
                    {$whereSql}
                    ORDER BY dl.created_at DESC
                    LIMIT {$limit} OFFSET {$offset}";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("DeanLogs::getAllWithPagination error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get total count of logs for pagination
     * @param ?string $action
     * @param ?string $tableName
     * @param ?string $search
     */
    public static function getTotalCount($action = null, $tableName = null, $search = null) {
        try {
            $db = self::db();
            
            $where  = [];
            $params = [];

            if ($action) {
                $where[]  = "dl.action = ?";
                $params[] = $action;
            }
            if ($tableName) {
                $where[]  = "dl.table_name = ?";
                $params[] = $tableName;
            }
            if ($search !== null && $search !== '') {
                $where[]  = "(dl.details LIKE ? OR dl.action LIKE ? OR dl.table_name LIKE ? OR CAST(dl.record_id AS CHAR) LIKE ? OR dl.ip_address LIKE ? OR u.username LIKE ?)";
                $like     = '%' . $search . '%';
                array_push($params, $like, $like, $like, $like, $like, $like);
            }

            $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
            
            $sql = "SELECT COUNT(*) 
                    FROM dean_logs dl
                    LEFT JOIN users u ON dl.user_id = u.id
                    {$whereSql}";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
            
        } catch (PDOException $e) {
            error_log("DeanLogs::getTotalCount error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get activity statistics
     */
    public static function getStats($days = 30) {
        try {
            $db = self::db();
            
            // Get stats for the last X days
            $sql = "SELECT 
                        action,
                        COUNT(*) as count,
                        DATE(created_at) as date
                    FROM dean_logs 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    GROUP BY action, DATE(created_at)
                    ORDER BY DATE(created_at) DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([ (int)$days ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("DeanLogs::getStats error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get action types for filtering
     */
    public static function getActionTypes() {
        try {
            $db = self::db();
            $sql = "SELECT DISTINCT action FROM dean_logs ORDER BY action";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'action');
        } catch (PDOException $e) {
            error_log("DeanLogs::getActionTypes error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get table names for filtering
     */
    public static function getTableNames() {
        try {
            $db = self::db();
            $sql = "SELECT DISTINCT table_name FROM dean_logs WHERE table_name IS NOT NULL ORDER BY table_name";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'table_name');
        } catch (PDOException $e) {
            error_log("DeanLogs::getTableNames error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Clean old logs (keep only last X days)
     */
    public static function cleanOldLogs($daysToKeep = 90) {
        try {
            $db = self::db();
            $sql = "DELETE FROM dean_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
            $stmt = $db->prepare($sql);
            $stmt->execute([ (int)$daysToKeep ]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("DeanLogs::cleanOldLogs error: " . $e->getMessage());
            return 0;
        }
    }
}
?>
