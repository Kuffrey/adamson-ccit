<?php
declare(strict_types=1);

class FacultyController {
    public function dashboard(): string {
        ob_start();
        include __DIR__ . '/../views/faculty_dashboard.php';
        return ob_get_clean();
    }

    public function manageResearch(): string {
        ob_start();
        include __DIR__ . '/../views/faculty_manage_research.php';
        return ob_get_clean();
    }

    public function manageCertifications(): string {
        ob_start();
        include __DIR__ . '/../views/faculty_manage_certifications.php';
        return ob_get_clean();
    }
}
