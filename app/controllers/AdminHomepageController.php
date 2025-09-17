<?php
declare(strict_types=1);

require_once __DIR__ . '/AdminController.php';
require_once __DIR__ . '/../models/HomepageSettings.php';

final class AdminHomepageController extends AdminController
{
    public function index(): string
    {
        $this->requireAdminOrDean();

        $settingsModel = new HomepageSettings();
        $settings = $settingsModel->get();

        // Your view expects $homepage; keep an alias.
        $homepage = $settings;
        $success  = !empty($_GET['success']);
        $error    = isset($_GET['error']) ? (string)$_GET['error'] : '';

        ob_start();
        include __DIR__ . '/../views/admin_manage_homepage.php';
        return (string) ob_get_clean();
    }

    public function save(): void
    {
        $this->requireAdminOrDean();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/adamson-ccit/public/index.php?page=admin_manage_homepage');
        }

        try {
            (new HomepageSettings())->update($_POST);
            $this->redirect('/adamson-ccit/public/index.php?page=admin_manage_homepage&success=1');
        } catch (\Throwable $e) {
            $msg = rawurlencode('Save failed. ' . $e->getMessage());
            $this->redirect('/adamson-ccit/public/index.php?page=admin_manage_homepage&error=' . $msg);
        }
    }
}
