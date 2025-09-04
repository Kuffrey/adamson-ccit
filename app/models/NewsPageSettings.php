<?php
// app/models/NewsPageSettings.php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

class NewsPageSettings extends Model {
    protected static $table = 'news_page_settings';

    public static function getSettings(): array {
        $db = self::db();
        $stmt = $db->query('SELECT * FROM news_page_settings ORDER BY id DESC LIMIT 1');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: [
            'subhero_lead' => '',
            'announcement' => null,
        ];
    }

    public static function updateSettings(array $data): bool {
        $db = self::db();
        $stmt = $db->prepare('UPDATE news_page_settings SET subhero_lead=?, announcement=?, updated_at=NOW() WHERE id=(SELECT id FROM (SELECT id FROM news_page_settings ORDER BY id DESC LIMIT 1) AS t)');
        return $stmt->execute([
            $data['subhero_lead'] ?? '',
            $data['announcement'] ?? null,
        ]);
    }
}
