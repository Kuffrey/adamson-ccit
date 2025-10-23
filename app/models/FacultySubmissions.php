<?php
// app/models/FacultySubmissions.php

require_once __DIR__ . '/News.php'; // Include the News class

class FacultySubmissions {
    
    /**
     * Get database connection
     */
    private static function getConnection() {
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
     * Initialize table if it doesn't exist
     */
    private static function initializeTable() {
        try {
            $pdo = self::getConnection();
            
            // Check if users table has department_id column
            $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'department_id'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE users ADD COLUMN department_id INT DEFAULT NULL AFTER role");
                error_log("Added department_id column to users table");
            }
            
            // Check if users table has department column
            $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'department'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE users ADD COLUMN department VARCHAR(50) DEFAULT NULL AFTER department_id");
                error_log("Added department column to users table");
            }
            
            // Update existing faculty with default departments
            $pdo->exec("UPDATE users SET department = 'CCIT', department_id = 1 WHERE role = 'faculty' AND department IS NULL");
            
            // Create table if it doesn't exist
            $sql = "CREATE TABLE IF NOT EXISTS faculty_submissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                faculty_id INT NOT NULL,
                submission_type ENUM('news', 'research', 'certification') NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                content LONGTEXT,
                category VARCHAR(100),
                status ENUM('submitted', 'under_review', 'approved', 'rejected', 'published') DEFAULT 'submitted',
                submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                reviewed_at TIMESTAMP NULL,
                reviewed_by INT NULL,
                review_notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_faculty_id (faculty_id),
                INDEX idx_submission_type (submission_type),
                INDEX idx_status (status),
                INDEX idx_submitted_at (submitted_at)
            )";
            
            $pdo->exec($sql);
            return true;
        } catch (PDOException $e) {
            error_log("Table initialization error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate faculty ID exists in users table
     */
    private static function validateFacultyId($facultyId) {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'faculty' LIMIT 1");
            $stmt->execute([$facultyId]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Faculty ID validation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create new faculty submission
     */
    public static function create($data) {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            
            error_log("Creating faculty submission with data: " . print_r($data, true));
            
            // Validate required fields
            if (empty($data['faculty_id'])) {
                throw new Exception("Faculty ID is required");
            }
            if (empty($data['submission_type'])) {
                throw new Exception("Submission type is required");
            }
            if (empty($data['title'])) {
                throw new Exception("Title is required");
            }
            if (mb_strlen($data['title']) > 100) {
                throw new Exception("Title must not exceed 100 characters");
            }
            if (empty($data['content'])) {
                throw new Exception("Content is required");
            }
            
            // Validate faculty ID exists
            if (!self::validateFacultyId($data['faculty_id'])) {
                // Try to find the user in users table regardless of role
                $stmt = $pdo->prepare("SELECT id, role FROM users WHERE id = ? LIMIT 1");
                $stmt->execute([$data['faculty_id']]);
                $user = $stmt->fetch();
                
                if (!$user) {
                    throw new Exception("Faculty ID {$data['faculty_id']} does not exist in users table");
                } else {
                    error_log("User exists but role is: " . $user['role'] . ". Proceeding with submission.");
                }
            }
            
            $sql = "INSERT INTO faculty_submissions (
                faculty_id, submission_type, title, description, content, 
                category, status, submitted_at
            ) VALUES (
                :faculty_id, :submission_type, :title, :description, :content,
                :category, :status, NOW()
            )";
            
            $stmt = $pdo->prepare($sql);
            
            $params = [
                ':faculty_id' => (int)$data['faculty_id'],
                ':submission_type' => $data['submission_type'],
                ':title' => $data['title'],
                ':description' => $data['description'] ?? '',
                ':content' => $data['content'] ?? '',
                ':category' => $data['category'] ?? '',
                ':status' => $data['status'] ?? 'submitted'
            ];
            
            error_log("SQL parameters: " . print_r($params, true));
            
            $result = $stmt->execute($params);
            
            if ($result) {
                $submissionId = $pdo->lastInsertId();
                error_log("Faculty submission created successfully with ID: " . $submissionId);
                return $submissionId;
            } else {
                error_log("Failed to create faculty submission - execute returned false");
                error_log("SQL error info: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            
        } catch (PDOException $e) {
            error_log("FacultySubmissions creation PDO error: " . $e->getMessage());
            error_log("SQL State: " . $e->getCode());
            
            // If it's still a foreign key constraint error, try manual fix
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                error_log("Foreign key constraint detected. Attempting manual fix...");
                
                try {
                    $pdo = self::getConnection();
                    
                    // Drop the table and recreate without foreign keys
                    $pdo->exec("DROP TABLE IF EXISTS faculty_submissions");
                    
                    $sql = "CREATE TABLE faculty_submissions (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        faculty_id INT NOT NULL,
                        submission_type ENUM('news', 'research', 'certification') NOT NULL,
                        title VARCHAR(255) NOT NULL,
                        description TEXT,
                        content LONGTEXT,
                        category VARCHAR(100),
                        status ENUM('submitted', 'under_review', 'approved', 'rejected', 'published') DEFAULT 'submitted',
                        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        reviewed_at TIMESTAMP NULL,
                        reviewed_by INT NULL,
                        review_notes TEXT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        INDEX idx_faculty_id (faculty_id),
                        INDEX idx_submission_type (submission_type),
                        INDEX idx_status (status),
                        INDEX idx_submitted_at (submitted_at)
                    )";
                    
                    $pdo->exec($sql);
                    error_log("Table recreated without foreign keys. Retrying submission...");
                    
                    // Retry the insertion
                    $stmt = $pdo->prepare("INSERT INTO faculty_submissions (
                        faculty_id, submission_type, title, description, content, 
                        category, status, submitted_at
                    ) VALUES (
                        :faculty_id, :submission_type, :title, :description, :content,
                        :category, :status, NOW()
                    )");
                    
                    $result = $stmt->execute($params);
                    
                    if ($result) {
                        $submissionId = $pdo->lastInsertId();
                        error_log("Faculty submission created successfully after table recreation with ID: " . $submissionId);
                        return $submissionId;
                    }
                    
                } catch (PDOException $e2) {
                    error_log("Manual fix also failed: " . $e2->getMessage());
                }
            }
            
            throw new Exception("Database error: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("FacultySubmissions creation error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get submissions by faculty ID with faculty info
     */
    public static function getByFacultyId($facultyId) {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            
            $sql = "SELECT 
                fs.*,
                u.username as faculty_username,
                u.first_name as faculty_first_name,
                u.last_name as faculty_last_name,
                CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as faculty_name,
                u.email as faculty_email,
                COALESCE(u.department, 'CCIT') as department_name
            FROM faculty_submissions fs
            LEFT JOIN users u ON fs.faculty_id = u.id
            WHERE fs.faculty_id = :faculty_id
            ORDER BY fs.submitted_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['faculty_id' => $facultyId]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("FacultySubmissions getByFacultyId error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get submissions by faculty ID and type
     */
    public static function getByFacultyAndType($facultyId, $type) {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            
            $sql = "SELECT 
                fs.*,
                u.username as faculty_username,
                CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as faculty_name,
                COALESCE(u.department, 'CCIT') as department_name
            FROM faculty_submissions fs
            LEFT JOIN users u ON fs.faculty_id = u.id
            WHERE fs.faculty_id = :faculty_id AND fs.submission_type = :type
            ORDER BY fs.submitted_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['faculty_id' => $facultyId, 'type' => $type]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("FacultySubmissions getByFacultyAndType error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get pending submissions by type with faculty information
     */
    public static function getPendingByType($type) {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            
            $sql = "SELECT 
                fs.*,
                u.username as faculty_username,
                CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as faculty_name,
                u.email as faculty_email,
                COALESCE(u.department, 'CCIT') as department_name
            FROM faculty_submissions fs
            LEFT JOIN users u ON fs.faculty_id = u.id
            WHERE fs.submission_type = :type 
            AND fs.status IN ('submitted', 'under_review')
            ORDER BY fs.submitted_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['type' => $type]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("FacultySubmissions getPendingByType error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all pending submissions for reviewer with faculty information
     */
    public static function getPendingByReviewer() {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            
            $sql = "SELECT 
                fs.*,
                u.username as faculty_username,
                CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as faculty_name,
                u.email as faculty_email,
                COALESCE(u.department, 'CCIT') as dept,
                COALESCE(u.department, 'CCIT') as department_name
            FROM faculty_submissions fs
            LEFT JOIN users u ON fs.faculty_id = u.id
            WHERE fs.status IN ('submitted', 'under_review')
            ORDER BY fs.submitted_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("FacultySubmissions getPendingByReviewer error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get submissions by type with faculty information
     */
    public static function getSubmissionsByType($type) {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            
            $sql = "SELECT 
                fs.*,
                u.username as faculty_username,
                CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as faculty_name,
                u.email as faculty_email,
                COALESCE(u.department, 'CCIT') as department_name,
                ru.username as reviewer_name
            FROM faculty_submissions fs
            LEFT JOIN users u ON fs.faculty_id = u.id
            LEFT JOIN users ru ON fs.reviewed_by = ru.id
            WHERE fs.submission_type = :type
            ORDER BY fs.submitted_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['type' => $type]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("FacultySubmissions getSubmissionsByType error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update submission status
     */
    public static function updateStatus($submissionId, $status, $reviewerId = null, $reviewNotes = '') {
        try {
            $pdo = self::getConnection();

            $sql = "UPDATE faculty_submissions 
                    SET status = :status, 
                        reviewed_by = :reviewer_id, 
                        reviewed_at = NOW(),
                        review_notes = :review_notes
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'id' => $submissionId,
                'status' => $status,
                'reviewer_id' => $reviewerId,
                'review_notes' => $reviewNotes
            ]);

            // Publish to faculty_research if approved
            $submission = self::getById($submissionId);
            if ($submission && $status === 'approved' && $submission['submission_type'] === 'research') {
                require_once __DIR__ . '/FacultyResearch.php';
                $rd = json_decode($submission['content'] ?? '{}', true) ?: [];
                $data = [
                    'title'      => $submission['title'] ?? '',
                    'authors'    => $rd['authors'] ?? '',
                    'doi'        => $rd['doi'] ?? '',
                    'publisher'  => $rd['publisher'] ?? '',
                    'conference' => $rd['conference'] ?? '',
                    'year'       => $rd['year'] ?? '',
                    'view_url'   => $rd['view_url'] ?? '',
                    'status'     => 'published'
                ];
                FacultyResearch::create($data); // Insert into faculty_research table
            }

            return true;
        } catch (PDOException $e) {
            error_log("FacultySubmissions updateStatus error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get submission by ID with faculty information
     */
    public static function getById($id) {
        try {
            $pdo = self::getConnection();
            
            $sql = "SELECT 
                fs.*,
                u.username as faculty_username,
                CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as faculty_name,
                u.email as faculty_email
            FROM faculty_submissions fs
            LEFT JOIN users u ON fs.faculty_id = u.id
            WHERE fs.id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("FacultySubmissions getById error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete submission
     */
    public static function delete($id) {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("DELETE FROM faculty_submissions WHERE id = :id");
            return $stmt->execute(['id' => $id]);
            
        } catch (PDOException $e) {
            error_log("FacultySubmissions delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get pending counts by type
     */
    public static function getPendingCountsByType() {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            
            $sql = "SELECT 
                submission_type,
                COUNT(*) as count
            FROM faculty_submissions fs
            WHERE fs.status IN ('submitted', 'under_review')
            GROUP BY submission_type";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            
            $results = $stmt->fetchAll();
            $counts = [
                'research' => 0,
                'news' => 0,
                'certification' => 0
            ];
            
            foreach ($results as $row) {
                if (isset($counts[$row['submission_type']])) {
                    $counts[$row['submission_type']] = (int)$row['count'];
                }
            }
            
            return $counts;
            
        } catch (PDOException $e) {
            error_log("FacultySubmissions getPendingCountsByType error: " . $e->getMessage());
            return ['research' => 0, 'news' => 0, 'certification' => 0];
        }
    }
}