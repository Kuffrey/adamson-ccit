<?php
// app/models/FacultyCertification.php
require_once __DIR__ . '/Model.php';
class FacultyCertification extends Model {
    protected string $table = 'faculty_certification_award';
    
    // Grouped by year > issuer > certification > faculty
    public static function getGrouped() {
        $db = self::db();
        $sql = 'SELECT fca.year_earned, c.issuer, c.issuer_key, c.cert_title, c.badge_url, c.cert_url, c.verify_url, CONCAT(u.first_name, " ", u.last_name) AS faculty_name, fca.status
                FROM faculty_certification_award fca
                JOIN users u ON fca.faculty_id = u.id
                JOIN certification c ON fca.certification_id = c.id
                WHERE COALESCE(fca.is_archived, 0) = 0
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
                    'status' => $row['status'], // Include status for expired/revoked indicators
                    'faculty' => []
                ];
            }
            $grouped[$year][$issuer][$cert]['faculty'][] = $row['faculty_name'];
        }
        return $grouped;
    }
    
    /**
     * Get database connection
     */
    private function getConnection() {
        try {
            $pdo = new PDO(
                "mysql:host=localhost;dbname=adamson_ccit;charset=utf8mb4",
                "root",
                "",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    /**
     * Ensure table has is_archived column
     */
    private function ensureArchivedColumn() {
        try {
            $pdo = $this->getConnection();
            
            // Check if is_archived column exists
            $stmt = $pdo->query("SHOW COLUMNS FROM faculty_certification_award LIKE 'is_archived'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE faculty_certification_award ADD COLUMN is_archived TINYINT(1) DEFAULT 0 AFTER status");
                
                // Update existing expired/revoked records to be archived
                $pdo->exec("UPDATE faculty_certification_award SET is_archived = 1 WHERE status IN ('Expired', 'Revoked')");
            }
            
            // Check if created_at column exists
            $stmt = $pdo->query("SHOW COLUMNS FROM faculty_certification_award LIKE 'created_at'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE faculty_certification_award ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER is_archived");
            }
            
            // Check if updated_at column exists
            $stmt = $pdo->query("SHOW COLUMNS FROM faculty_certification_award LIKE 'updated_at'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE faculty_certification_award ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at");
            }
            
        } catch (PDOException $e) {
            error_log("Archive column creation error: " . $e->getMessage());
        }
    }

    /**
     * Create new certification award
     */
    public function create($data) {
        try {
            $this->ensureArchivedColumn();
            $pdo = $this->getConnection();
            
            // Don't auto-archive expired/revoked - keep them visible with clear status
            $status = $data['status'] ?? 'Active';
            $isArchived = 0; // Only archive when explicitly moved to archive
            
            $sql = "INSERT INTO faculty_certification_award (faculty_id, certification_id, year_earned, year_expiry, status, is_archived, created_at, updated_at) 
                    VALUES (:faculty_id, :certification_id, :year_earned, :year_expiry, :status, :is_archived, NOW(), NOW())";
            
            $stmt = $pdo->prepare($sql);
            
            $result = $stmt->execute([
                'faculty_id' => $data['faculty_id'],
                'certification_id' => $data['certification_id'],
                'year_earned' => $data['year_earned'] ?? null,
                'year_expiry' => $data['year_expiry'] ?? null,
                'status' => $status,
                'is_archived' => $isArchived
            ]);
            
            return $result ? $pdo->lastInsertId() : false;
            
        } catch (PDOException $e) {
            error_log("FacultyCertification creation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update certification award
     */
    public function update($id, $data) {
        try {
            $pdo = $this->getConnection();
            
            // Don't auto-archive expired/revoked - keep them visible
            $status = $data['status'] ?? 'Active';
            
            // Only archive if explicitly set to archived, or if it was already archived
            $currentRecord = $this->getById($id);
            $isArchived = ($data['archived'] ?? false) ? 1 : ($currentRecord['is_archived'] ?? 0);
            
            $sql = "UPDATE faculty_certification_award SET 
                    faculty_id = :faculty_id,
                    certification_id = :certification_id,
                    year_earned = :year_earned,
                    year_expiry = :year_expiry,
                    status = :status,
                    is_archived = :is_archived,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            
            return $stmt->execute([
                'id' => $id,
                'faculty_id' => $data['faculty_id'],
                'certification_id' => $data['certification_id'],
                'year_earned' => $data['year_earned'] ?? null,
                'year_expiry' => $data['year_expiry'] ?? null,
                'status' => $status,
                'is_archived' => $isArchived
            ]);
            
        } catch (PDOException $e) {
            error_log("FacultyCertification update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get certification by ID
     */
    public function getById($id) {
        try {
            $pdo = $this->getConnection();
            $stmt = $pdo->prepare("SELECT * FROM faculty_certification_award WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("FacultyCertification getById error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Archive a certification (manual action)
     */
    public function archive($id) {
        try {
            $pdo = $this->getConnection();
            $stmt = $pdo->prepare("UPDATE faculty_certification_award SET is_archived = 1, updated_at = NOW() WHERE id = :id");
            
            return $stmt->execute(['id' => $id]);
            
        } catch (PDOException $e) {
            error_log("FacultyCertification archive error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Unarchive a certification
     */
    public function unarchive($id) {
        try {
            $pdo = $this->getConnection();
            $stmt = $pdo->prepare("UPDATE faculty_certification_award SET is_archived = 0, updated_at = NOW() WHERE id = :id");
            
            return $stmt->execute(['id' => $id]);
            
        } catch (PDOException $e) {
            error_log("FacultyCertification unarchive error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete certification award
     */
    public function delete($id) {
        try {
            $pdo = $this->getConnection();
            $stmt = $pdo->prepare("DELETE FROM faculty_certification_award WHERE id = :id");
            
            return $stmt->execute(['id' => $id]);
            
        } catch (PDOException $e) {
            error_log("FacultyCertification delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Static delete method for backwards compatibility
     */
    public static function deleteById($id) {
        try {
            $instance = new self();
            return $instance->delete($id);
        } catch (Exception $e) {
            error_log("FacultyCertification static delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all certifications with optional status filter
     */
    public function getAll($status = 'all') {
        try {
            $this->ensureArchivedColumn();
            $pdo = $this->getConnection();
            
            // First check if we have data in the main table
            $checkStmt = $pdo->query("SELECT COUNT(*) as count FROM faculty_certification_award");
            $totalRecords = $checkStmt->fetch()['count'];
            error_log("Total records in faculty_certification_award: " . $totalRecords);
            
            if ($totalRecords == 0) {
                error_log("No records found in faculty_certification_award table");
                return [];
            }
            
            // Enhanced query with better error handling and correct table names
            $sql = "SELECT 
                        fca.*, 
                        COALESCE(CONCAT(u.first_name, ' ', u.last_name), u.username, 'Unknown Faculty') as faculty_name,
                        COALESCE(c.cert_title, 'Unknown Certification') as cert_title,
                        COALESCE(c.issuer, 'Unknown Issuer') as issuer
                    FROM faculty_certification_award fca
                    LEFT JOIN users u ON fca.faculty_id = u.id
                    LEFT JOIN certification c ON fca.certification_id = c.id";
            
            $params = [];
            
            if ($status !== 'all') {
                if ($status === 'archived') {
                    $sql .= " WHERE COALESCE(fca.is_archived, 0) = 1";
                } else {
                    $sql .= " WHERE fca.status = :status AND COALESCE(fca.is_archived, 0) = 0";
                    $params['status'] = ucfirst($status);
                }
            }
            
            $sql .= " ORDER BY COALESCE(fca.created_at, fca.id) DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Query results count: " . count($results));
            
            if (count($results) > 0) {
                error_log("Sample record: " . print_r($results[0], true));
            } else {
                // If JOIN failed, try simple query without JOINs
                error_log("JOIN query returned no results, trying simple query");
                
                $simpleSql = "SELECT fca.*, 'Unknown Faculty' as faculty_name, 'Unknown Certification' as cert_title, 'Unknown Issuer' as issuer FROM faculty_certification_award fca";
                
                if ($status !== 'all') {
                    if ($status === 'archived') {
                        $simpleSql .= " WHERE COALESCE(fca.is_archived, 0) = 1";
                    } else {
                        $simpleSql .= " WHERE fca.status = :status AND COALESCE(fca.is_archived, 0) = 0";
                    }
                }
                
                $simpleSql .= " ORDER BY COALESCE(fca.created_at, fca.id) DESC";
                
                $stmt = $pdo->prepare($simpleSql);
                $stmt->execute($params);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                error_log("Simple query results count: " . count($results));
                
                // Now try to enhance with actual names
                foreach ($results as &$result) {
                    // Try to get faculty name
                    try {
                        $facultyStmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) as name FROM users WHERE id = :id LIMIT 1");
                        $facultyStmt->execute(['id' => $result['faculty_id']]);
                        $faculty = $facultyStmt->fetch();
                        if ($faculty && $faculty['name']) {
                            $result['faculty_name'] = $faculty['name'];
                        }
                    } catch (Exception $e) {
                        error_log("Faculty lookup failed for ID {$result['faculty_id']}: " . $e->getMessage());
                    }
                    
                    // Try to get certification details - check both table names
                    try {
                        // First try 'certification' table
                        $certStmt = $pdo->prepare("SELECT cert_title, issuer FROM certification WHERE id = :id LIMIT 1");
                        $certStmt->execute(['id' => $result['certification_id']]);
                        $cert = $certStmt->fetch();
                        
                        if (!$cert) {
                            // Try 'certifications' table as fallback
                            $certStmt = $pdo->prepare("SELECT cert_title, issuer FROM certifications WHERE id = :id LIMIT 1");
                            $certStmt->execute(['id' => $result['certification_id']]);
                            $cert = $certStmt->fetch();
                        }
                        
                        if ($cert) {
                            $result['cert_title'] = $cert['cert_title'];
                            $result['issuer'] = $cert['issuer'];
                        }
                    } catch (Exception $e) {
                        error_log("Certification lookup failed for ID {$result['certification_id']}: " . $e->getMessage());
                    }
                }
            }
            
            return $results;
            
        } catch (PDOException $e) {
            error_log("FacultyCertification getAll error: " . $e->getMessage());
            error_log("SQL error code: " . $e->getCode());
            return [];
        }
    }

    /**
     * Get status counts for certifications
     */
    public function statusCounts() {
        try {
            $this->ensureArchivedColumn();
            $pdo = $this->getConnection();
            
            $counts = ['all' => 0, 'active' => 0, 'expired' => 0, 'revoked' => 0, 'archived' => 0];
            
            // Get total count with error handling
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM faculty_certification_award");
                $stmt->execute();
                $result = $stmt->fetch();
                $counts['all'] = $result ? $result['count'] : 0;
                error_log("Total count query result: " . $counts['all']);
            } catch (Exception $e) {
                error_log("Count query failed: " . $e->getMessage());
                return $counts;
            }
            
            // Get status specific counts only if we have records
            if ($counts['all'] > 0) {
                try {
                    $stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM faculty_certification_award GROUP BY status");
                    $stmt->execute();
                    
                    while ($row = $stmt->fetch()) {
                        $status = strtolower($row['status']);
                        if (isset($counts[$status])) {
                            $counts[$status] = $row['count'];
                        }
                        error_log("Status count: " . $status . " = " . $row['count']);
                    }
                } catch (Exception $e) {
                    error_log("Status count query failed: " . $e->getMessage());
                }
                
                // Get archived count
                try {
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM faculty_certification_award WHERE COALESCE(is_archived, 0) = 1");
                    $stmt->execute();
                    $result = $stmt->fetch();
                    $counts['archived'] = $result ? $result['count'] : 0;
                    error_log("Archived count: " . $counts['archived']);
                } catch (Exception $e) {
                    error_log("Archived count query failed: " . $e->getMessage());
                }
            }
            
            return $counts;
            
        } catch (PDOException $e) {
            error_log("FacultyCertification status counts error: " . $e->getMessage());
            return ['all' => 0, 'active' => 0, 'expired' => 0, 'revoked' => 0, 'archived' => 0];
        }
    }

    /**
     * Get all available certifications - check both table names
     */
    public static function getAllCertifications() {
        try {
            $pdo = new PDO(
                "mysql:host=localhost;dbname=adamson_ccit;charset=utf8mb4",
                "root",
                "",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            
            // Try 'certification' table first
            try {
                $stmt = $pdo->prepare("SELECT * FROM certification ORDER BY cert_title");
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($results)) {
                    return $results;
                }
            } catch (Exception $e) {
                error_log("certification table query failed: " . $e->getMessage());
            }
            
            // Fallback to 'certifications' table
            try {
                $stmt = $pdo->prepare("SELECT * FROM certifications ORDER BY cert_title");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log("certifications table query failed: " . $e->getMessage());
                return [];
            }
            
        } catch (PDOException $e) {
            error_log("Get certifications error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all faculty members from faculty_profile table
     */
    public static function getAllFaculty() {
        try {
            $pdo = new PDO(
                "mysql:host=localhost;dbname=adamson_ccit;charset=utf8mb4",
                "root",
                "",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            
            // Query faculty_profile table instead of users table
            $stmt = $pdo->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM faculty_profile ORDER BY first_name, last_name");
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug logging
            error_log("Faculty from faculty_profile table: " . count($results) . " records found");
            if (!empty($results)) {
                error_log("Sample faculty: " . print_r($results[0], true));
            }
            
            return $results;
            
        } catch (PDOException $e) {
            error_log("Get faculty error: " . $e->getMessage());
            
            // Fallback to users table if faculty_profile doesn't exist or fails
            try {
                $stmt = $pdo->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM users WHERE role = 'faculty' ORDER BY first_name, last_name");
                $stmt->execute();
                
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("Fallback to users table: " . count($results) . " faculty found");
                
                return $results;
                
            } catch (PDOException $e2) {
                error_log("Fallback query also failed: " . $e2->getMessage());
                return [];
            }
        }
    }
}
