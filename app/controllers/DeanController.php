<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/FacultySubmissions.php';

class DeanController {
    private $facultySubmissions;
    
    public function __construct() {
        $this->facultySubmissions = new FacultySubmissions();
    }
    
    public function dashboard(): string {
        ob_start();
        include __DIR__ . '/../views/dean_dashboard.php';
        return ob_get_clean();
    }

    // Queue of items that require dean approval (featured research, homepage news, etc.)
    public function approvals(): string {
        ob_start();
        include __DIR__ . '/../views/dean_approvals.php';
        return ob_get_clean();
    }

    // Optional convenience wrappers for dean-specific “create” pages.
    // These can post to the existing admin manage routes (action=new/create).
    public function createNews(): string {
        ob_start();
        include __DIR__ . '/../views/dean_create_news.php';
        return ob_get_clean();
    }

    public function createEvent(): string {
        ob_start();
        include __DIR__ . '/../views/dean_create_event.php';
        return ob_get_clean();
    }

    public function createAnnouncement(): string {
        ob_start();
        include __DIR__ . '/../views/dean_create_announcement.php';
        return ob_get_clean();
    }

    // Handle faculty submission approval/rejection actions (research and news only)
}
