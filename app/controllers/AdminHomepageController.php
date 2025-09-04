<?php
declare(strict_types=1);
require_once __DIR__ . '/AdminController.php';
require_once __DIR__ . '/../models/HomepageSettings.php';

class AdminHomepageController extends AdminController {
    public function index(): string {
        $this->requireAdmin();
    $settings = (new HomepageSettings())->get();
    ob_start();
    extract(['settings' => $settings], EXTR_SKIP);
    include __DIR__ . '/../views/admin/homepage_form.php';
    return ob_get_clean();
    }
    public function save(): void {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new HomepageSettings();
            $model->update($_POST);
            header('Location: /adamson-ccit/public/index.php?page=admin_homepage&success=1');
            exit;
        }
    }
}
