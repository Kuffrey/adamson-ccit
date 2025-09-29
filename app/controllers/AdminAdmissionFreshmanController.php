<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/AdmissionFreshmanSettings.php';
require_once __DIR__ . '/../lib/Auth.php';

class AdminAdmissionFreshmanController {
    public static function handle(): void {
        Auth::requireRole(['admin','dean'], '/adamson-ccit/public/index.php?page=login');

        $msg = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = AdmissionFreshmanSettings::updateSettings([
                'subhero_lead' => trim((string)($_POST['subhero_lead'] ?? '')),
                'how_to_apply' => trim((string)($_POST['how_to_apply'] ?? '')),
                'initial_uploads' => trim((string)($_POST['initial_uploads'] ?? '')),
                'requirements_shs' => trim((string)($_POST['requirements_shs'] ?? '')),
                'requirements_als' => trim((string)($_POST['requirements_als'] ?? '')),
                'requirements_abroad' => trim((string)($_POST['requirements_abroad'] ?? '')),
                'enrollment_procedure' => trim((string)($_POST['enrollment_procedure'] ?? '')),
                'sidebar_office' => trim((string)($_POST['sidebar_office'] ?? '')),
                'sidebar_links' => trim((string)($_POST['sidebar_links'] ?? '')),
                'sidebar_image_url' => trim((string)($_POST['sidebar_image_url'] ?? '')),
                'sidebar_image_caption' => trim((string)($_POST['sidebar_image_caption'] ?? '')),
                'cta_title' => trim((string)($_POST['cta_title'] ?? '')),
                'cta_description' => trim((string)($_POST['cta_description'] ?? '')),
                'cta_action_label' => trim((string)($_POST['cta_action_label'] ?? '')),
                'cta_action_url' => trim((string)($_POST['cta_action_url'] ?? '')),
            ]);
            $msg = $ok ? 'Freshman Admission page updated.' : 'Failed to update settings.';
        }
        $settings = AdmissionFreshmanSettings::getSettings();
        $username = $_SESSION['user']['username'] ?? 'Admin';
        require __DIR__ . '/../views/admin/admission_freshman_form.php';
    }
}
