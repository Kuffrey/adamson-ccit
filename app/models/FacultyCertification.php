<?php
// app/models/FacultyCertification.php
require_once __DIR__ . '/Model.php';
class FacultyCertification extends Model {
    // Grouped by year > issuer > certification > faculty
    public static function getGrouped() {
        $db = self::db();
        $sql = 'SELECT fca.year_earned, c.issuer, c.issuer_key, c.cert_title, c.badge_url, c.cert_url, c.verify_url, f.name AS faculty_name
                FROM faculty_certification_award fca
                JOIN faculty f ON fca.faculty_id = f.id
                JOIN certification c ON fca.certification_id = c.id
                ORDER BY fca.year_earned DESC, c.issuer, c.cert_title, f.name';
        $stmt = $db->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $grouped = [];
        foreach ($rows as $row) {
            $year = $row['year_earned'];
            $issuer = $row['issuer'];
            $cert = $row['cert_title'];
            if (!isset($grouped[$year])) $grouped[$year] = [];
            if (!isset($grouped[$year][$issuer])) $grouped[$year][$issuer] = [];
            if (!isset($grouped[$year][$issuer][$cert])) {
                $grouped[$year][$issuer][$cert] = [
                    'badge_url' => $row['badge_url'],
                    'cert_url' => $row['cert_url'],
                    'verify_url' => $row['verify_url'],
                    'faculty' => []
                ];
            }
            $grouped[$year][$issuer][$cert]['faculty'][] = $row['faculty_name'];
        }
        return $grouped;
    }
}
