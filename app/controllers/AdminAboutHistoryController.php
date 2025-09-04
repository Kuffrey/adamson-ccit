<?php
// app/controllers/AdminAboutHistoryController.php
require_once __DIR__ . '/AdminController.php';
require_once __DIR__ . '/../models/AboutHistory.php';

class AdminAboutHistoryController extends AdminController {
    public function index(): string {
        $this->requireAdmin();
        $about = (new AboutHistory())->get();
        ob_start();
        extract(['about' => $about], EXTR_SKIP);
        include __DIR__ . '/../views/admin/about_history_form.php';
        return ob_get_clean();
    }
    public function save(): void {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new AboutHistory();
            $data = $_POST;
            // milestones as JSON
            if (isset($data['milestones']) && is_array($data['milestones'])) {
                $data['milestones'] = array_values($data['milestones']);
            }
            $model->update($data);
            header('Location: /adamson-ccit/public/index.php?page=admin_about_history&success=1');
            exit;
        }
    }
}
