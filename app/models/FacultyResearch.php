<?php
// app/models/FacultyResearch.php

class FacultyResearch {

    /** Get database connection */
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

    /** Ensure table exists and columns are up to date (idempotent) */
    private static function initializeTable() {
        try {
            $pdo = self::getConnection();

            // 1) Create table if not exists
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS faculty_research (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    authors TEXT NULL,
                    doi VARCHAR(255) NULL,
                    publisher VARCHAR(255) NULL,
                    conference VARCHAR(255) NULL,
                    year VARCHAR(4) NULL,
                    view_url VARCHAR(500) NULL,
                    status ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
                    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");

            // 2) Add any missing columns (migrations for existing installs)
            $wantCols = [
                'doi'        => "ALTER TABLE faculty_research ADD COLUMN doi VARCHAR(255) AFTER authors",
                'publisher'  => "ALTER TABLE faculty_research ADD COLUMN publisher VARCHAR(255) AFTER doi",
                'conference' => "ALTER TABLE faculty_research ADD COLUMN conference VARCHAR(255) AFTER publisher",
                'year'       => "ALTER TABLE faculty_research ADD COLUMN year VARCHAR(4) AFTER conference",
                'view_url'   => "ALTER TABLE faculty_research ADD COLUMN view_url VARCHAR(500) AFTER year",
                'status'     => "ALTER TABLE faculty_research ADD COLUMN status ENUM('draft','published','archived') NOT NULL DEFAULT 'draft' AFTER view_url",
                'created_at' => "ALTER TABLE faculty_research ADD COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER status",
                'updated_at' => "ALTER TABLE faculty_research ADD COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at",
            ];
            foreach ($wantCols as $col => $ddl) {
                $q = $pdo->prepare("SHOW COLUMNS FROM faculty_research LIKE :c");
                $q->execute([':c' => $col]);
                if (!$q->fetch()) {
                    $pdo->exec($ddl);
                }
            }

            // 3) Ensure index for filtering by status/year
            $idx = $pdo->query("SHOW INDEX FROM faculty_research WHERE Key_name = 'idx_status_year'")->fetch();
            if (!$idx) {
                $pdo->exec("CREATE INDEX idx_status_year ON faculty_research (status, year)");
            }

            // NOTE: Do NOT drop 'status' (or other columns) here.
            return true;
        } catch (PDOException $e) {
            error_log("Table initialization error: " . $e->getMessage());
            return false;
        }
    }

    /** Create a new research row */
    public static function create(array $data): int {
        try {
            self::initializeTable(); // Ensure the table exists
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO faculty_research (title, authors, doi, publisher, conference, year, view_url, status)
                VALUES (:title, :authors, :doi, :publisher, :conference, :year, :view_url, :status)
            ");
            $stmt->execute([
                ':title'      => $data['title'],
                ':authors'    => $data['authors'],
                ':doi'        => $data['doi'],
                ':publisher'  => $data['publisher'],
                ':conference' => $data['conference'],
                ':year'       => $data['year'],
                ':view_url'   => $data['view_url'],
                ':status'     => $data['status'] ?? 'draft' // Default to 'draft' if not provided
            ]);
            return (int)$pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("FacultyResearch create error: " . $e->getMessage());
            return 0;
        }
    }

    /** Update a research row */
    public static function update(int $id, array $data) {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            $sql = "UPDATE faculty_research SET
                        title = :title,
                        authors = :authors,
                        doi = :doi,
                        publisher = :publisher,
                        conference = :conference,
                        year = :year,
                        view_url = :view_url,
                        status = :status,
                        updated_at = NOW()
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':id'         => $id,
                ':title'      => $data['title'] ?? '',
                ':authors'    => $data['authors'] ?? '',
                ':doi'        => $data['doi'] ?? null,
                ':publisher'  => $data['publisher'] ?? null,
                ':conference' => $data['conference'] ?? null,
                ':year'       => $data['year'] ?? date('Y'),
                ':view_url'   => $data['view_url'] ?? null,
                ':status'     => in_array(($data['status'] ?? 'draft'), ['draft','published','archived'], true)
                                 ? $data['status'] : 'draft',
            ]);
        } catch (PDOException $e) {
            error_log("FacultyResearch update error: " . $e->getMessage());
            return false;
        }
    }

    /** Delete a row */
    public static function delete(int $id) {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("DELETE FROM faculty_research WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("FacultyResearch delete error: " . $e->getMessage());
            return false;
        }
    }

    /** Get all rows (all statuses) */
    public static function getAll(): array {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->query("
                SELECT id, title, authors, doi, publisher, conference, year, view_url, 
                       COALESCE(status, 'draft') AS status 
                FROM faculty_research
                WHERE status = 'published' -- Only retrieve published entries
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("FacultyResearch getAll error: " . $e->getMessage());
            return [];
        }
    }

    /** Get one by id */
    public static function getById(int $id) {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("
                SELECT id, title, authors, doi, publisher, conference, year, view_url, status, created_at, updated_at
                FROM faculty_research WHERE id = :id
            ");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("FacultyResearch getById error: " . $e->getMessage());
            return false;
        }
    }

    /** List rows by status (published/draft/archived/all) */
    public static function list(string $status = 'all'): array {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            if ($status === 'all') {
                $stmt = $pdo->query("
                    SELECT id, title, authors, doi, publisher, conference, year, view_url, status, created_at, updated_at
                    FROM faculty_research
                    ORDER BY year DESC, id DESC
                ");
                return $stmt->fetchAll();
            }
            $stmt = $pdo->prepare("
                SELECT id, title, authors, doi, publisher, conference, year, view_url, status, created_at, updated_at
                FROM faculty_research
                WHERE status = :status
                ORDER BY year DESC, id DESC
            ");
            $stmt->execute([':status' => $status]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("FacultyResearch list error: " . $e->getMessage());
            return [];
        }
    }

    /** Counts per status for tabs */
    public static function statusCounts(): array {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            $all       = (int)$pdo->query("SELECT COUNT(*) FROM faculty_research")->fetchColumn();
            $published = (int)$pdo->query("SELECT COUNT(*) FROM faculty_research WHERE status='published'")->fetchColumn();
            $draft     = (int)$pdo->query("SELECT COUNT(*) FROM faculty_research WHERE status='draft'")->fetchColumn();
            $archived  = (int)$pdo->query("SELECT COUNT(*) FROM faculty_research WHERE status='archived'")->fetchColumn();
            return compact('all','published','draft','archived');
        } catch (PDOException $e) {
            error_log("FacultyResearch statusCounts error: " . $e->getMessage());
            return ['all'=>0,'published'=>0,'draft'=>0,'archived'=>0];
        }
    }

    /** Convenience: publish (used by dean approval) */
    public static function publish(array $data) {
        $data['status'] = 'published';
        return self::create($data);
    }

    /** Optional utility: list by year (kept for compatibility) */
    public static function listByYear($year = null): array {
        try {
            self::initializeTable();
            $pdo = self::getConnection();
            if ($year) {
                $stmt = $pdo->prepare("
                    SELECT id, title, authors, doi, publisher, conference, year, view_url, status, created_at, updated_at
                    FROM faculty_research WHERE year = :year
                ");
                $stmt->execute([':year' => $year]);
                return $stmt->fetchAll();
            } else {
                $stmt = $pdo->query("
                    SELECT id, title, authors, doi, publisher, conference, year, view_url, status, created_at, updated_at
                    FROM faculty_research
                    ORDER BY created_at DESC
                ");
                return $stmt->fetchAll();
            }
        } catch (PDOException $e) {
            error_log("FacultyResearch listByYear error: " . $e->getMessage());
            return [];
        }
    }

    private static function db(): PDO {
        static $pdo = null;
        if ($pdo === null) {
            $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $pdo;
    }
}
