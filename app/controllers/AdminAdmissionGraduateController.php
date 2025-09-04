<?php
// app/controllers/AdminAdmissionGraduateController.php
declare(strict_types=1);
require_once __DIR__ . '/../models/AdmissionGraduateSettings.php';

class AdminAdmissionGraduateController {
    public static function handle(): void {
        $msg = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = AdmissionGraduateSettings::updateSettings([
                'subhero_lead' => trim((string)($_POST['subhero_lead'] ?? '')),
                'how_to_apply' => trim((string)($_POST['how_to_apply'] ?? '')),
                'initial_uploads' => trim((string)($_POST['initial_uploads'] ?? '')),
                'qualifications_masters' => trim((string)($_POST['qualifications_masters'] ?? '')),
                'qualifications_doctoral' => trim((string)($_POST['qualifications_doctoral'] ?? '')),
                'qualifications_jd' => trim((string)($_POST['qualifications_jd'] ?? '')),
                'requirements_enrollment' => trim((string)($_POST['requirements_enrollment'] ?? '')),
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
            $msg = $ok ? 'Graduate School & JD Admission page updated.' : 'Failed to update settings.';
        }
        $settings = AdmissionGraduateSettings::getSettings();
        require __DIR__ . '/../views/admin/admission_graduate_form.php';
    }
}
