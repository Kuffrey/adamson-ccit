<?php
// app/controllers/AdminAboutController.php
require_once __DIR__ . '/AdminController.php';
require_once __DIR__ . '/../models/AboutVisionMission.php';

class AdminAboutController extends AdminController {
    public function visionMission(): string {
        $this->requireAdmin();
        $about = (new AboutVisionMission())->get();
        ob_start();
        extract(['about' => $about], EXTR_SKIP);
        include __DIR__ . '/../views/admin/about_vision_mission_form.php';
        return ob_get_clean();
    }
    public function saveVisionMission(): void {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new AboutVisionMission();
            $model->update($_POST);
            header('Location: /adamson-ccit/public/index.php?page=admin_about_vision_mission&success=1');
            exit;
        }
    }
}
