<?php
// Minimal dynamic dashboard model for dean_dashboard.php

class DeanDashboard {
    protected $pdo;

    public function __construct() {
        $this->pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Ensure required tables exist (run only if missing, safe to call)
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS dean_manage_events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_title VARCHAR(255) NOT NULL,
                event_date DATE NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS dean_manage_news (
                id INT AUTO_INCREMENT PRIMARY KEY,
                news_title VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS dean_manage_announcements (
                id INT AUTO_INCREMENT PRIMARY KEY,
                announcement_title VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    public function getStats() {
        // Pending approvals
        $pendingApprovals = $this->pdo->query("SELECT COUNT(*) FROM faculty_submissions WHERE status='submitted'")->fetchColumn();
        // Research submissions
        $researchSubmissions = $this->pdo->query("SELECT COUNT(*) FROM faculty_submissions WHERE submission_type='research'")->fetchColumn();
        // Upcoming events
        $upcomingEvents = $this->pdo->query("SELECT COUNT(*) FROM dean_manage_events WHERE event_date >= CURDATE()")->fetchColumn();
        // Certification requests
        $certificationRequests = $this->pdo->query("SELECT COUNT(*) FROM faculty_submissions WHERE submission_type='certification'")->fetchColumn();
        // News articles
        $newsArticles = $this->pdo->query("SELECT COUNT(*) FROM dean_manage_news")->fetchColumn();
        // Announcements
        $announcements = $this->pdo->query("SELECT COUNT(*) FROM dean_manage_announcements")->fetchColumn();
        // Activity logs
        $activityLogs = $this->pdo->query("SELECT COUNT(*) FROM dean_logs")->fetchColumn();

        return [
            'pending_approvals' => $pendingApprovals ?: 0,
            'research_submissions' => $researchSubmissions ?: 0,
            'upcoming_events' => $upcomingEvents ?: 0,
            'certification_requests' => $certificationRequests ?: 0,
            'news_articles' => $newsArticles ?: 0,
            'announcements' => $announcements ?: 0,
            'activity_logs' => $activityLogs ?: 0,
        ];
    }

    public function getRecentSubmissions($limit = 10) {
        // Fix: Use LIMIT as integer, not parameter binding (MySQL/MariaDB does not allow LIMIT ? with PDO unless emulated prepares)
        $limit = (int)$limit;
        $sql = "
            SELECT 
                s.title, 
                u.first_name, u.last_name, 
                s.submission_type AS type, 
                s.submitted_at AS date, 
                s.status
            FROM faculty_submissions s
            LEFT JOIN users u ON s.faculty_id = u.id
            ORDER BY s.submitted_at DESC
            LIMIT $limit
        ";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'title' => $row['title'],
                'submitted_by' => trim($row['first_name'] . ' ' . $row['last_name']),
                'type' => $row['type'],
                'date' => $row['date'],
                'status' => ucfirst($row['status']),
            ];
        }
        return $result;
    }
}
