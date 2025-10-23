<?php
// app/models/AboutHistory.php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

final class AboutHistory extends Model
{
    private string $table = 'about_history';

    /** Create table if it doesn't exist (fields used by your view). */
    public function ensureTable(): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS {$this->table} (
          id INT AUTO_INCREMENT PRIMARY KEY,
          subhero_lead TEXT NULL,
          intro_lead MEDIUMTEXT NULL,
          origins_body MEDIUMTEXT NULL,
          milestones JSON NULL,
          leaders_list MEDIUMTEXT NULL,
          academic_leads_list MEDIUMTEXT NULL,
          fact_title VARCHAR(255) NULL,
          fact_1 VARCHAR(255) NULL,
          fact_2 VARCHAR(255) NULL,
          fact_3 VARCHAR(255) NULL,
          identity_title VARCHAR(255) NULL,
          identity_items MEDIUMTEXT NULL,
          photo_url VARCHAR(255) NULL,
          photo_caption VARCHAR(255) NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        SQL;
        self::db()->exec($sql);
    }

    /** Fetch latest row (or an empty array if none). Decodes milestones JSON. */
    public function get(): array
    {
        $this->ensureTable();

        $stmt = self::db()->query("SELECT * FROM {$this->table} ORDER BY id DESC LIMIT 1");
        $row  = $stmt->fetch();
        if (!$row) return [];

        // Decode milestones safely
        if (!empty($row['milestones'])) {
            $decoded = json_decode((string)$row['milestones'], true);
            $row['milestones'] = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
        } else {
            $row['milestones'] = [];
        }
        return $row;
    }

    /**
     * Upsert: if table has no rows, INSERT; otherwise UPDATE the newest row.
     * Accepts only columns that actually exist (extra keys are ignored).
     */
    public function update(array $d): bool
    {
        $this->ensureTable();

        // Normalize milestones -> JSON
        if (array_key_exists('milestones', $d)) {
            $d['milestones'] = is_array($d['milestones'])
                ? json_encode($d['milestones'], JSON_UNESCAPED_UNICODE)
                : (is_string($d['milestones']) ? $d['milestones'] : json_encode([], JSON_UNESCAPED_UNICODE));
        }

        $cols = [
            'subhero_lead','intro_lead','origins_body','milestones',
            'leaders_list','academic_leads_list',
            'fact_title','fact_1','fact_2','fact_3',
            'identity_title','identity_items',
            'photo_url','photo_caption'
        ];

        // Filter payload to known columns
        $payload = [];
        foreach ($cols as $c) {
            if (array_key_exists($c, $d)) {
                $payload[$c] = $d[$c];
            }
        }

        // If nothing to write, consider it "ok"
        if (!$payload) return true;

        // Do we have at least one row?
        $hasRow = (int) self::db()->query("SELECT COUNT(*) AS c FROM {$this->table}")
                                  ->fetch()['c'] > 0;

        if (!$hasRow) {
            // INSERT first row
            $colList = implode(',', array_keys($payload));
            $placeholders = implode(',', array_fill(0, count($payload), '?'));
            $sql = "INSERT INTO {$this->table} ({$colList}) VALUES ({$placeholders})";
            $st  = self::db()->prepare($sql);
            return $st->execute(array_values($payload));
        }

        // UPDATE the newest row
        $set = implode(',', array_map(fn($c) => "{$c} = ?", array_keys($payload)));
        $sql = "UPDATE {$this->table} SET {$set} ORDER BY id DESC LIMIT 1";
        $st  = self::db()->prepare($sql);
        return $st->execute(array_values($payload));
    }
}
