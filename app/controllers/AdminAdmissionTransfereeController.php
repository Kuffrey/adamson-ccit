<?php
// app/controllers/AdminAdmissionTransfereeController.php
declare(strict_types=1);
require_once __DIR__ . '/../models/AdmissionTransfereeSettings.php';

class AdminAdmissionTransfereeController {
    public static function handle(): void {
        $msg = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = AdmissionTransfereeSettings::updateSettings([
                'subhero_lead' => trim((string)($_POST['subhero_lead'] ?? '')),
                'how_to_apply' => trim((string)($_POST['how_to_apply'] ?? '')),
                'requirements' => trim((string)($_POST['requirements'] ?? '')),
                'enrollment_procedure' => trim((string)($_POST['enrollment_procedure'] ?? '')),
                'enrollment_note' => trim((string)($_POST['enrollment_note'] ?? '')),
                'sidebar_office' => trim((string)($_POST['sidebar_office'] ?? '')),
                'sidebar_links' => trim((string)($_POST['sidebar_links'] ?? '')),
                'sidebar_image_url' => trim((string)($_POST['sidebar_image_url'] ?? '')),
                'sidebar_image_caption' => trim((string)($_POST['sidebar_image_caption'] ?? '')),
                'cta_title' => trim((string)($_POST['cta_title'] ?? '')),
                'cta_description' => trim((string)($_POST['cta_description'] ?? '')),
                'cta_action_label' => trim((string)($_POST['cta_action_label'] ?? '')),
                'cta_action_url' => trim((string)($_POST['cta_action_url'] ?? '')),
            ]);
            $msg = $ok ? 'Transferee Admission page updated.' : 'Failed to update settings.';
        }
        $settings = AdmissionTransfereeSettings::getSettings();
        require __DIR__ . '/../views/admin/admission_transferee_form.php';
    }
}
