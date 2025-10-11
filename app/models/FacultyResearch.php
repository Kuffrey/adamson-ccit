<?php
// app/models/FacultyResearch.php

class FacultyResearch {
    
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
     * Initialize/update database table with status column
     */
    public static function initializeTable() {
        try {
            $pdo = self::getConnection();
            
            // Check if table exists, if not create it
            $stmt = $pdo->query("SHOW TABLES LIKE 'faculty_research'");
            if ($stmt->rowCount() == 0) {
                $sql = "CREATE TABLE faculty_research (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    authors TEXT,
                    venue VARCHAR(255),
                    year VARCHAR(4),
                    type ENUM('journal', 'conference', 'chapter', 'patent', 'other') DEFAULT 'journal',
                    dept ENUM('itis', 'cs') DEFAULT 'cs',
                    pdf_url VARCHAR(500),
                    view_url VARCHAR(500),
                    image_url VARCHAR(500),
                    status ENUM('draft', 'published', 'archived') DEFAULT 'published',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
                $pdo->exec($sql);
                return true;
            }
            
            // Check if status column exists, if not add it
            $stmt = $pdo->query("SHOW COLUMNS FROM faculty_research LIKE 'status'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE faculty_research ADD COLUMN status ENUM('draft', 'published', 'archived') DEFAULT 'published' AFTER image_url");
            }
            
            // Check if created_at column exists, if not add it
            $stmt = $pdo->query("SHOW COLUMNS FROM faculty_research LIKE 'created_at'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE faculty_research ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER status");
            }
            
            // Check if updated_at column exists, if not add it
            $stmt = $pdo->query("SHOW COLUMNS FROM faculty_research LIKE 'updated_at'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE faculty_research ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at");
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Table initialization error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create new research
     */
    public static function create($data) {
        try {
            // Ensure table exists and has proper structure
            self::initializeTable();
            
            $pdo = self::getConnection();
            
            $sql = "INSERT INTO faculty_research (title, authors, venue, year, type, dept, pdf_url, view_url, image_url, status, created_at, updated_at) 
                    VALUES (:title, :authors, :venue, :year, :type, :dept, :pdf_url, :view_url, :image_url, :status, NOW(), NOW())";
            
            $stmt = $pdo->prepare($sql);
            
            $result = $stmt->execute([
                'title' => $data['title'] ?? '',
                'authors' => $data['authors'] ?? '',
                'venue' => $data['venue'] ?? '',
                'year' => $data['year'] ?? date('Y'),
                'type' => $data['type'] ?? 'journal',
                'dept' => $data['dept'] ?? 'cs',
                'pdf_url' => $data['pdf_url'] ?? null,
                'view_url' => $data['view_url'] ?? null,
                'image_url' => $data['image_url'] ?? null,
                'status' => $data['status'] ?? 'published'
            ]);
            
            return $result ? $pdo->lastInsertId() : false;
            
        } catch (PDOException $e) {
            error_log("FacultyResearch creation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update research
     */
    public static function update($id, $data) {
        try {
            $pdo = self::getConnection();
            
            $sql = "UPDATE faculty_research SET 
                    title = :title, 
                    authors = :authors, 
                    venue = :venue, 
                    year = :year, 
                    type = :type, 
                    dept = :dept, 
                    pdf_url = :pdf_url, 
                    view_url = :view_url, 
                    image_url = :image_url,
                    status = :status,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            
            return $stmt->execute([
                'id' => $id,
                'title' => $data['title'] ?? '',
                'authors' => $data['authors'] ?? '',
                'venue' => $data['venue'] ?? '',
                'year' => $data['year'] ?? date('Y'),
                'type' => $data['type'] ?? 'journal',
                'dept' => $data['dept'] ?? 'cs',
                'pdf_url' => $data['pdf_url'] ?? null,
                'view_url' => $data['view_url'] ?? null,
                'image_url' => $data['image_url'] ?? null,
                'status' => $data['status'] ?? 'published'
            ]);
            
        } catch (PDOException $e) {
            error_log("FacultyResearch update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete research
     */
    public static function delete($id) {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("DELETE FROM faculty_research WHERE id = :id");
            
            return $stmt->execute(['id' => $id]);
            
        } catch (PDOException $e) {
            error_log("FacultyResearch delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all research (for backward compatibility)
     */
    public static function getAll() {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM faculty_research ORDER BY created_at DESC");
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("FacultyResearch getAll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get research by ID
     */
    public static function getById($id) {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM faculty_research WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("FacultyResearch getById error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * List research with optional status filter
     */
    public static function list($status = 'all') {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            
            if ($status === 'all') {
                $stmt = $pdo->prepare("SELECT * FROM faculty_research ORDER BY created_at DESC");
                $stmt->execute();
            } else {
                $stmt = $pdo->prepare("SELECT * FROM faculty_research WHERE status = :status ORDER BY created_at DESC");
                $stmt->execute(['status' => $status]);
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("FacultyResearch list error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get status counts
     */
    public static function statusCounts() {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            
            $counts = ['all' => 0, 'published' => 0, 'draft' => 0, 'archived' => 0];
            
            // Get total count
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM faculty_research");
            $stmt->execute();
            $counts['all'] = $stmt->fetch()['count'];
            
            // Get status specific counts
            $stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM faculty_research GROUP BY status");
            $stmt->execute();
            
            while ($row = $stmt->fetch()) {
                if (isset($counts[$row['status']])) {
                    $counts[$row['status']] = $row['count'];
                }
            }
            
            return $counts;
            
        } catch (PDOException $e) {
            error_log("FacultyResearch status counts error: " . $e->getMessage());
            return ['all' => 0, 'published' => 0, 'draft' => 0, 'archived' => 0];
        }
    }

    /**
     * Update research status
     */
    public static function updateStatus($id, $status) {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("UPDATE faculty_research SET status = :status, updated_at = NOW() WHERE id = :id");
            
            return $stmt->execute([
                'id' => $id,
                'status' => $status
            ]);
            
        } catch (PDOException $e) {
            error_log("FacultyResearch status update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Publish research (for faculty submissions)
     */
    public static function publish($submissionId, $userId, $notes) {
        try {
            // This would handle publishing faculty submitted research
            // For now, just return true
            return true;
        } catch (Exception $e) {
            error_log("FacultyResearch publish error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject research (for faculty submissions)
     */
    public static function reject($submissionId, $userId, $notes) {
        try {
            // This would handle rejecting faculty submitted research
            // For now, just return true
            return true;
        } catch (Exception $e) {
            error_log("FacultyResearch reject error: " . $e->getMessage());
            return false;
        }
    }
}
