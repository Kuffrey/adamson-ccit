<?php
// app/controllers/AdminEventsPageController.php
require_once __DIR__ . '/AdminController.php';
require_once __DIR__ . '/../models/EventsPageSettings.php';

class AdminEventsPageController extends AdminController {
    public function index(): string {
        $this->requireAdminOrDean(); // ← changed
        $settings = (new EventsPageSettings())->get();
        $username = $_SESSION['user']['username'] ?? 'Admin';

        ob_start();
        // make $settings available to the view
        extract(['settings' => $settings, 'username' => $username], EXTR_SKIP);
        include __DIR__ . '/../views/admin/events_page_form.php';
        return ob_get_clean();
    }

    public function save(): void {
        $this->requireAdminOrDean(); // ← changed
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new EventsPageSettings())->update($_POST);
            header('Location: /adamson-ccit/public/index.php?page=admin_events_page&success=1');
            exit;
        }
        header('Location: /adamson-ccit/public/index.php?page=admin_events_page');
        exit;
    }
}
