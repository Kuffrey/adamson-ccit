<?php
declare(strict_types=1);

class HomeController {
    public function index(): string {
        ob_start();
        include __DIR__ . '/../views/home.php';
        return ob_get_clean();
    }

    // NEW: public list of announcements
    public function announcements(): string {
        ob_start();
        include __DIR__ . '/../views/announcements.php';
        return ob_get_clean();
    }

    // NEW: public single announcement view
    public function announcementView(): string {
        ob_start();
        include __DIR__ . '/../views/announcement_view.php';
        return ob_get_clean();
    }
}
