<?php
// app/controllers/AdminEventsPageController.php
require_once __DIR__ . '/AdminController.php';
require_once __DIR__ . '/../models/EventsPageSettings.php';

class AdminEventsPageController extends AdminController {
    public function index(): string {
        $this->requireAdmin();
        $settings = (new EventsPageSettings())->get();
        ob_start();
        extract(['settings' => $settings], EXTR_SKIP);
        include __DIR__ . '/../views/admin/events_page_form.php';
        return ob_get_clean();
    }
    public function save(): void {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new EventsPageSettings();
            $model->update($_POST);
            header('Location: /adamson-ccit/public/index.php?page=admin_events_page&success=1');
            exit;
        }
    }
}
