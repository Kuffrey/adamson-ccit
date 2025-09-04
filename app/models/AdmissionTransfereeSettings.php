<?php
// app/models/AdmissionTransfereeSettings.php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class AdmissionTransfereeSettings extends Model {
    protected static $table = 'admission_transferee_settings';
    public static function getSettings(): array {
        $db = self::db();
        $stmt = $db->query('SELECT * FROM admission_transferee_settings ORDER BY id DESC LIMIT 1');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: [];
    }
    public static function updateSettings(array $data): bool {
        $db = self::db();
        $stmt = $db->prepare('UPDATE admission_transferee_settings SET subhero_lead=?, how_to_apply=?, requirements=?, enrollment_procedure=?, enrollment_note=?, sidebar_office=?, sidebar_links=?, sidebar_image_url=?, sidebar_image_caption=?, cta_title=?, cta_description=?, cta_action_label=?, cta_action_url=?, updated_at=NOW() WHERE id=(SELECT id FROM (SELECT id FROM admission_transferee_settings ORDER BY id DESC LIMIT 1) AS t)');
        return $stmt->execute([
            $data['subhero_lead'] ?? '',
            $data['how_to_apply'] ?? '',
            $data['requirements'] ?? '',
            $data['enrollment_procedure'] ?? '',
            $data['enrollment_note'] ?? '',
            $data['sidebar_office'] ?? '',
            $data['sidebar_links'] ?? '',
            $data['sidebar_image_url'] ?? '',
            $data['sidebar_image_caption'] ?? '',
            $data['cta_title'] ?? '',
            $data['cta_description'] ?? '',
            $data['cta_action_label'] ?? '',
            $data['cta_action_url'] ?? '',
        ]);
    }
}
