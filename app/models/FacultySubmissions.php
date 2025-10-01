<?php
// app/models/FacultySubmissions.php
require_once __DIR__ . '/../config/database.php';

class FacultySubmissions {
    private static function getConnection() {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    public static function create($data) {
        try {
            $pdo = self::getConnection();
            
            $sql = "INSERT INTO faculty_submissions (
                faculty_id, submission_type, title, description, content, 
                category, related_item_id, status, submitted_at, created_at
            ) VALUES (
                :faculty_id, :submission_type, :title, :description, :content,
                :category, :related_item_id, :status, NOW(), NOW()
            )";
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':faculty_id' => $data['faculty_id'],
                ':submission_type' => $data['submission_type'],
                ':title' => $data['title'],
                ':description' => $data['description'] ?? '',
                ':content' => $data['content'] ?? '',
                ':category' => $data['category'] ?? null,
                ':related_item_id' => $data['related_item_id'] ?? null,
                ':status' => $data['status'] ?? 'submitted'
            ]);
        } catch (PDOException $e) {
            error_log("Error creating faculty submission: " . $e->getMessage());
            throw new Exception("Failed to create submission");
        }
    }

    public static function createFromCertification($certificationId, $facultyId) {
        try {
            // Get certification details
            require_once __DIR__ . '/Certification.php';
            $pdo = self::getConnection();
            
            $sql = "SELECT c.*, u.username FROM certifications c 
                    LEFT JOIN users u ON c.owner_user_id = u.id 
                    WHERE c.id = :cert_id AND c.owner_user_id = :faculty_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':cert_id' => $certificationId, ':faculty_id' => $facultyId]);
            $cert = $stmt->fetch();
            
            if (!$cert) {
                throw new Exception("Certification not found or not owned by faculty");
            }
            
            return self::create([
                'faculty_id' => $facultyId,
                'submission_type' => 'certification',
                'title' => $cert['title'],
                'description' => 'Certification: ' . $cert['title'] . ' issued by ' . $cert['issuer'],
                'content' => 'Issuer: ' . $cert['issuer'] . "\nIssued Date: " . $cert['issued_at'],
                'category' => 'Professional Certification',
                'related_item_id' => $certificationId,
                'status' => 'submitted'
            ]);
        } catch (Exception $e) {
            error_log("Error creating certification submission: " . $e->getMessage());
            throw new Exception("Failed to create certification submission");
        }
    }

    public static function createFromResearch($researchId, $facultyId) {
        try {
            // Get research details
            require_once __DIR__ . '/Research.php';
            $pdo = self::getConnection();
            
            $sql = "SELECT r.*, u.username FROM research r 
                    LEFT JOIN users u ON r.owner_user_id = u.id 
                    WHERE r.id = :research_id AND r.owner_user_id = :faculty_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':research_id' => $researchId, ':faculty_id' => $facultyId]);
            $research = $stmt->fetch();
            
            if (!$research) {
                throw new Exception("Research not found or not owned by faculty");
            }
            
            return self::create([
                'faculty_id' => $facultyId,
                'submission_type' => 'research',
                'title' => $research['title'],
                'description' => $research['abstract'],
                'content' => $research['abstract'],
                'category' => 'Academic Research',
                'related_item_id' => $researchId,
                'status' => 'submitted'
            ]);
        } catch (Exception $e) {
            error_log("Error creating research submission: " . $e->getMessage());
            throw new Exception("Failed to create research submission");
        }
    }

    /**
     * Create submission record from news
     */
    public static function createFromNews($newsId, $facultyId) {
        try {
            require_once __DIR__ . '/News.php';
            $pdo = self::getConnection();
            
            $sql = "SELECT * FROM news WHERE id = :news_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':news_id' => $newsId]);
            $news = $stmt->fetch();
            
            if (!$news) {
                throw new Exception("News not found");
            }
            
            $content = $news['body'] ?? $news['content'] ?? '';
            
            return self::create([
                'faculty_id' => $facultyId,
                'submission_type' => 'news',
                'title' => $news['title'],
                'description' => $content,
                'content' => $content,
                'category' => ucfirst($news['category'] ?? 'news'),
                'related_item_id' => $newsId,
                'status' => 'submitted'
            ]);
        } catch (Exception $e) {
            error_log("Error creating news submission: " . $e->getMessage());
            throw new Exception("Failed to create news submission");
        }
    }

    /**
     * Create submission record from event
     */
    public static function createFromEvent($eventId, $facultyId) {
        try {
            require_once __DIR__ . '/Event.php';
            $pdo = self::getConnection();
            
            $sql = "SELECT * FROM events WHERE id = :event_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':event_id' => $eventId]);
            $event = $stmt->fetch();
            
            if (!$event) {
                throw new Exception("Event not found");
            }
            
            return self::create([
                'faculty_id' => $facultyId,
                'submission_type' => 'event',
                'title' => $event['title'],
                'description' => $event['description'],
                'content' => $event['description'],
                'category' => ucfirst($event['category'] ?? 'event'),
                'related_item_id' => $eventId,
                'status' => 'submitted'
            ]);
        } catch (Exception $e) {
            error_log("Error creating event submission: " . $e->getMessage());
            throw new Exception("Failed to create event submission");
        }
    }

    /**
     * Create submission record from announcement
     */
    public static function createFromAnnouncement($announcementId, $facultyId) {
        try {
            require_once __DIR__ . '/Announcement.php';
            $pdo = self::getConnection();
            
            $sql = "SELECT * FROM announcements WHERE id = :announcement_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':announcement_id' => $announcementId]);
            $announcement = $stmt->fetch();
            
            if (!$announcement) {
                throw new Exception("Announcement not found");
            }
            
            $content = $announcement['content'] ?? $announcement['body'] ?? '';
            
            return self::create([
                'faculty_id' => $facultyId,
                'submission_type' => 'announcement',
                'title' => $announcement['title'],
                'description' => $content,
                'content' => $content,
                'category' => ucfirst($announcement['category'] ?? 'general'),
                'related_item_id' => $announcementId,
                'status' => 'submitted'
            ]);
        } catch (Exception $e) {
            error_log("Error creating announcement submission: " . $e->getMessage());
            throw new Exception("Failed to create announcement submission");
        }
    }

    public static function getAllSubmissionsWithFacultyDetails() {
        try {
            $pdo = self::getConnection();
            
            $sql = "SELECT 
                fs.*,
                CONCAT(u.first_name, ' ', u.last_name) as faculty_name,
                u.username as faculty_username,
                d.name as dept,
                CONCAT(r.first_name, ' ', r.last_name) as reviewer_name
            FROM faculty_submissions fs
            LEFT JOIN users u ON fs.faculty_id = u.id
            LEFT JOIN departments d ON u.department_id = d.id
            LEFT JOIN users r ON fs.reviewed_by = r.id
            ORDER BY fs.submitted_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching submissions: " . $e->getMessage());
            throw new Exception("Failed to fetch submissions");
        }
    }

    public static function updateStatus($submissionId, $status, $reviewerId = null, $reviewNotes = '') {
        try {
            $pdo = self::getConnection();
            
            // Start transaction
            $pdo->beginTransaction();
            
            // Get submission details first
            $sql = "SELECT * FROM faculty_submissions WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $submissionId]);
            $submission = $stmt->fetch();
            
            if (!$submission) {
                throw new Exception("Submission not found");
            }
            
            // Update submission status
            $sql = "UPDATE faculty_submissions 
                    SET status = :status, 
                        reviewed_by = :reviewed_by, 
                        review_notes = :review_notes, 
                        reviewed_at = NOW() 
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                ':status' => $status,
                ':reviewed_by' => $reviewerId,
                ':review_notes' => $reviewNotes,
                ':id' => $submissionId
            ]);
            
            if ($success && $submission['related_item_id']) {
                // Update the related item's status based on submission type
                if ($submission['submission_type'] === 'certification') {
                    $sql = "UPDATE certifications SET status = :status WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':status' => $status, ':id' => $submission['related_item_id']]);
                } elseif ($submission['submission_type'] === 'research') {
                    $sql = "UPDATE research SET status = :status, approved_by = :approver WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':status' => $status, 
                        ':approver' => $reviewerId,
                        ':id' => $submission['related_item_id']
                    ]);
                } elseif ($submission['submission_type'] === 'news') {
                    // For news: set status to 'published' when approved, 'rejected' when rejected
                    $newsStatus = ($status === 'approved') ? 'published' : 'rejected';
                    $sql = "UPDATE news SET status = :status WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':status' => $newsStatus, ':id' => $submission['related_item_id']]);
                } elseif ($submission['submission_type'] === 'event') {
                    // For events: set status to 'published' when approved, 'rejected' when rejected
                    $eventStatus = ($status === 'approved') ? 'published' : 'rejected';
                    $sql = "UPDATE events SET status = :status WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':status' => $eventStatus, ':id' => $submission['related_item_id']]);
                } elseif ($submission['submission_type'] === 'announcement') {
                    // For announcements: set status to 'published' when approved, 'rejected' when rejected
                    $announcementStatus = ($status === 'approved') ? 'published' : 'rejected';
                    $sql = "UPDATE announcements SET status = :status WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':status' => $announcementStatus, ':id' => $submission['related_item_id']]);
                }
            }
            
            $pdo->commit();
            return $success;
            
        } catch (PDOException $e) {
            $pdo->rollback();
            error_log("Error updating submission status: " . $e->getMessage());
            throw new Exception("Failed to update submission status");
        }
    }

    public static function getByFacultyId($facultyId) {
        try {
            $pdo = self::getConnection();
            
            $sql = "SELECT * FROM faculty_submissions 
                    WHERE faculty_id = :faculty_id 
                    ORDER BY submitted_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':faculty_id' => $facultyId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching faculty submissions: " . $e->getMessage());
            throw new Exception("Failed to fetch submissions");
        }
    }

    public static function getPendingSubmissions() {
        try {
            $pdo = self::getConnection();
            
            $sql = "SELECT 
                fs.*,
                CONCAT(u.first_name, ' ', u.last_name) as faculty_name,
                u.username as faculty_username,
                d.name as dept
            FROM faculty_submissions fs
            LEFT JOIN users u ON fs.faculty_id = u.id
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE fs.status IN ('submitted', 'under_review')
            ORDER BY fs.submitted_at ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching pending submissions: " . $e->getMessage());
            throw new Exception("Failed to fetch pending submissions");
        }
    }

    public static function getSubmissionsByType($type) {
        try {
            $pdo = self::getConnection();
            
            $sql = "SELECT 
                fs.*,
                CONCAT(u.first_name, ' ', u.last_name) as faculty_name,
                u.username as faculty_username,
                d.name as dept,
                CONCAT(r.first_name, ' ', r.last_name) as reviewer_name
            FROM faculty_submissions fs
            LEFT JOIN users u ON fs.faculty_id = u.id
            LEFT JOIN departments d ON u.department_id = d.id
            LEFT JOIN users r ON fs.reviewed_by = r.id
            WHERE fs.submission_type = :type
            ORDER BY fs.submitted_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':type' => $type]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching submissions by type: " . $e->getMessage());
            throw new Exception("Failed to fetch submissions");
        }
    }

    /**
     * Get pending submissions that need reviewer approval
     */
    public static function getPendingByReviewer() {
        try {
            $pdo = self::getConnection();
            
            $sql = "SELECT fs.*, 
                           CONCAT(u.first_name, ' ', u.last_name) as faculty_name,
                           u.email as faculty_email,
                           d.name as department_name
                    FROM faculty_submissions fs
                    LEFT JOIN users u ON fs.faculty_id = u.id
                    LEFT JOIN departments d ON u.department_id = d.id
                    WHERE fs.status IN ('pending', 'submitted', 'under_review')
                    ORDER BY fs.submitted_at ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching pending submissions by reviewer: " . $e->getMessage());
            throw new Exception("Failed to fetch pending submissions");
        }
    }

    /**
     * Get count of pending submissions grouped by type
     */
    public static function getPendingCountsByType() {
        try {
            $pdo = self::getConnection();
            
            $sql = "SELECT submission_type, COUNT(*) as count 
                    FROM faculty_submissions 
                    WHERE status IN ('pending', 'submitted', 'under_review')
                    GROUP BY submission_type";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();
            
            // Initialize counts array with all types set to 0
            $counts = [
                'certification' => 0,
                'research' => 0,
                'news' => 0,
                'event' => 0,
                'announcement' => 0
            ];
            
            // Update counts based on query results
            foreach ($results as $result) {
                $counts[$result['submission_type']] = (int)$result['count'];
            }
            
            return $counts;
        } catch (PDOException $e) {
            error_log("Error fetching pending counts by type: " . $e->getMessage());
            throw new Exception("Failed to fetch pending counts");
        }
    }

    /**
     * Get pending submissions by specific type
     */
    public static function getPendingByType($type) {
        try {
            $pdo = self::getConnection();
            
            $sql = "SELECT fs.*, 
                           CONCAT(u.first_name, ' ', u.last_name) as faculty_name,
                           u.email as faculty_email,
                           d.name as department_name
                    FROM faculty_submissions fs
                    LEFT JOIN users u ON fs.faculty_id = u.id
                    LEFT JOIN departments d ON u.department_id = d.id
                    WHERE fs.status IN ('pending', 'submitted', 'under_review')
                    AND fs.submission_type = :type
                    ORDER BY fs.submitted_at ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':type' => $type]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching pending submissions by type: " . $e->getMessage());
            throw new Exception("Failed to fetch pending submissions by type");
        }
    }

    /**
     * Get submissions by faculty ID and type
     */
    public static function getByFacultyAndType($facultyId, $type) {
        try {
            $pdo = self::getConnection();
            
            $sql = "SELECT fs.*, 
                           CONCAT(u.first_name, ' ', u.last_name) as faculty_name,
                           u.username as faculty_username,
                           u.email as faculty_email
                    FROM faculty_submissions fs
                    LEFT JOIN users u ON fs.faculty_id = u.id
                    WHERE fs.faculty_id = :faculty_id AND fs.submission_type = :type 
                    ORDER BY fs.submitted_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':faculty_id' => $facultyId,
                ':type' => $type
            ]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching faculty submissions by type: " . $e->getMessage());
            throw new Exception("Failed to fetch submissions by faculty and type");
        }
    }

    /**
     * Get a submission by ID
     */
    public static function getById($id) {
        try {
            $pdo = self::getConnection();
            
            $sql = "SELECT fs.*, 
                           CONCAT(u.first_name, ' ', u.last_name) as faculty_name,
                           u.username as faculty_username,
                           u.email as faculty_email
                    FROM faculty_submissions fs
                    LEFT JOIN users u ON fs.faculty_id = u.id 
                    WHERE fs.id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching submission by ID: " . $e->getMessage());
            throw new Exception("Failed to fetch submission");
        }
    }

    /**
     * Delete a submission
     */
    public static function delete($id) {
        try {
            $pdo = self::getConnection();
            
            $sql = "DELETE FROM faculty_submissions WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error deleting submission: " . $e->getMessage());
            throw new Exception("Failed to delete submission");
        }
    }
}