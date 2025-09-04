<?php
// app/models/AdmissionGraduateSettings.php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class AdmissionGraduateSettings extends Model {
    protected static $table = 'admission_graduate_settings';
    public static function getSettings(): array {
        $db = self::db();
        $stmt = $db->query('SELECT * FROM admission_graduate_settings ORDER BY id DESC LIMIT 1');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: [];
    }
    public static function updateSettings(array $data): bool {
        $db = self::db();
        $stmt = $db->prepare('UPDATE admission_graduate_settings SET subhero_lead=?, how_to_apply=?, initial_uploads=?, qualifications_masters=?, qualifications_doctoral=?, qualifications_jd=?, requirements_enrollment=?, enrollment_procedure=?, sidebar_office=?, sidebar_links=?, sidebar_image_url=?, sidebar_image_caption=?, cta_title=?, cta_description=?, cta_action_label=?, cta_action_url=?, updated_at=NOW() WHERE id=(SELECT id FROM (SELECT id FROM admission_graduate_settings ORDER BY id DESC LIMIT 1) AS t)');
        return $stmt->execute([
            $data['subhero_lead'] ?? '',
            $data['how_to_apply'] ?? '',
            $data['initial_uploads'] ?? '',
            $data['qualifications_masters'] ?? '',
            $data['qualifications_doctoral'] ?? '',
            $data['qualifications_jd'] ?? '',
            $data['requirements_enrollment'] ?? '',
            $data['enrollment_procedure'] ?? '',
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
