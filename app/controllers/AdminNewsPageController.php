<?php
// app/controllers/AdminNewsPageController.php
declare(strict_types=1);

require_once __DIR__ . '/../models/NewsPageSettings.php';

class AdminNewsPageController {
    public static function handle(): void {
        $msg = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = NewsPageSettings::updateSettings([
                'subhero_lead' => trim((string)($_POST['subhero_lead'] ?? '')),
                'announcement' => trim((string)($_POST['announcement'] ?? '')),
            ]);
            $msg = $ok ? 'News page settings updated.' : 'Failed to update settings.';
        }
        $settings = NewsPageSettings::getSettings();
        require __DIR__ . '/../views/admin/news_page_form.php';
    }
}
