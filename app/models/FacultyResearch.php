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
     * Initialize/update database table for research publications only
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
                    doi VARCHAR(255),
                    publisher VARCHAR(255),
                    conference VARCHAR(255),
                    year VARCHAR(4),
                    view_url VARCHAR(500),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
                $pdo->exec($sql);
                return true;
            }
            // Add missing columns if needed (for migration)
            $columns = [
                'doi' => "ALTER TABLE faculty_research ADD COLUMN doi VARCHAR(255) AFTER authors",
                'publisher' => "ALTER TABLE faculty_research ADD COLUMN publisher VARCHAR(255) AFTER doi",
                'conference' => "ALTER TABLE faculty_research ADD COLUMN conference VARCHAR(255) AFTER publisher",
                'year' => "ALTER TABLE faculty_research ADD COLUMN year VARCHAR(4) AFTER conference",
                'view_url' => "ALTER TABLE faculty_research ADD COLUMN view_url VARCHAR(500) AFTER year",
                'created_at' => "ALTER TABLE faculty_research ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER view_url",
                'updated_at' => "ALTER TABLE faculty_research ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at"
            ];
            foreach ($columns as $col => $sql) {
                $stmt = $pdo->query("SHOW COLUMNS FROM faculty_research LIKE '$col'");
                if ($stmt->rowCount() == 0) {
                    $pdo->exec($sql);
                }
            }
            // Remove unnecessary columns
            $unwanted = ['venue','type','dept','pdf_url','image_url','status'];
            foreach ($unwanted as $col) {
                $stmt = $pdo->query("SHOW COLUMNS FROM faculty_research LIKE '$col'");
                if ($stmt->rowCount() > 0) {
                    $pdo->exec("ALTER TABLE faculty_research DROP COLUMN `$col`");
                }
            }
            return true;
        } catch (PDOException $e) {
            error_log("Table initialization error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create new research publication
     */
    public static function create($data) {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            $sql = "INSERT INTO faculty_research (title, authors, doi, publisher, conference, year, view_url, created_at, updated_at)
                    VALUES (:title, :authors, :doi, :publisher, :conference, :year, :view_url, NOW(), NOW())";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                'title' => $data['title'] ?? '',
                'authors' => $data['authors'] ?? '',
                'doi' => $data['doi'] ?? '',
                'publisher' => $data['publisher'] ?? '',
                'conference' => $data['conference'] ?? '',
                'year' => $data['year'] ?? date('Y'),
                'view_url' => $data['view_url'] ?? ''
            ]);
            return $result ? $pdo->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log("FacultyResearch creation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update research publication
     */
    public static function update($id, $data) {
        try {
            $pdo = self::getConnection();
            $sql = "UPDATE faculty_research SET 
                    title = :title, 
                    authors = :authors, 
                    doi = :doi, 
                    publisher = :publisher, 
                    conference = :conference, 
                    year = :year, 
                    view_url = :view_url,
                    updated_at = NOW()
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                'id' => $id,
                'title' => $data['title'] ?? '',
                'authors' => $data['authors'] ?? '',
                'doi' => $data['doi'] ?? '',
                'publisher' => $data['publisher'] ?? '',
                'conference' => $data['conference'] ?? '',
                'year' => $data['year'] ?? date('Y'),
                'view_url' => $data['view_url'] ?? ''
            ]);
        } catch (PDOException $e) {
            error_log("FacultyResearch update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete research publication
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
     * Get all research publications
     */
    public static function getAll() {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM faculty_research ORDER BY year DESC, created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("FacultyResearch getAll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get research publication by ID
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
     * List research publications by year
     */
    public static function listByYear($year = null) {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            if ($year) {
                $stmt = $pdo->prepare("SELECT * FROM faculty_research WHERE year = :year ORDER BY created_at DESC");
                $stmt->execute(['year' => $year]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM faculty_research ORDER BY created_at DESC");
                $stmt->execute();
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("FacultyResearch listByYear error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Publish research (used by dean approval)
     */
    public static function publish($data) {
        // This is just an alias for create, since all records in faculty_research are considered published
        return self::create($data);
    }
}
