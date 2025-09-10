<?php
// app/models/ProgramsUndergraduateSettings.php
require_once __DIR__ . '/Model.php';

class ProgramsUndergraduateSettings extends Model {
    protected static $table = 'programs_undergraduate_settings';

    /** Always return latest row (your table already stores a single record). */
    public static function getSettings(): array {
        $db  = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' ORDER BY id DESC LIMIT 1';
        $stmt = $db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Update latest row with flat fields (including per-card fields).
     * - preserves programs_grid as a fallback
     * - normalizes checkboxes to 0/1
     * - normalizes newline for pills
     * - lightly sanitizes slugs
     */
    public static function updateSettings(array $data): void {
        $db = self::db();

        // Ensure we have a row to update (no-op if one exists)
        $db->exec('INSERT INTO ' . self::$table . ' (subhero_image_url) 
                   SELECT \'/adamson-ccit/public/assets/images/programs/undergrad.jpg\' 
                   WHERE NOT EXISTS (SELECT 1 FROM ' . self::$table . ')');

        // --- normalize helpers ---
        $b = function($v){ return (int)!empty($v); };
        $normSlug = function($v){
            $s = strtolower(trim((string)$v));
            $s = preg_replace('~[^a-z0-9-]+~', '-', $s);
            $s = preg_replace('~-+~', '-', $s);
            $s = trim($s, '-');
            return $s ?: null;
        };
        $normPills = function($v){
            $t = str_replace(["\r\n", "\r"], "\n", (string)$v);
            // trim each line, drop empties, join with single \n
            $lines = array_filter(array_map('trim', explode("\n", $t)), fn($x) => $x !== '');
            return $lines ? implode("\n", $lines) : null;
        };

        // normalize booleans, slugs, pills for each card
        for ($i=1; $i<=4; $i++) {
            $data["card{$i}_active"]                = $b($data["card{$i}_active"] ?? 0);
            $data["card{$i}_learn_more_external"]   = $b($data["card{$i}_learn_more_external"] ?? 1);
            $data["card{$i}_curriculum_external"]   = $b($data["card{$i}_curriculum_external"] ?? 1);

            if (isset($data["card{$i}_position"])) {
                $pos = (int)$data["card{$i}_position"];
                $data["card{$i}_position"] = $pos > 0 ? $pos : $i;
            }

            if (array_key_exists("card{$i}_slug", $data)) {
                $data["card{$i}_slug"] = $normSlug($data["card{$i}_slug"]);
            }

            if (array_key_exists("card{$i}_pills", $data)) {
                $data["card{$i}_pills"] = $normPills($data["card{$i}_pills"]);
            }
        }

        // --- allowlist of columns to update (includes existing + per-card + CTA) ---
        $fields = [
            'subhero_image_url','subhero_lead','programs_grid',

            // Card 1
            'card1_active','card1_position','card1_slug','card1_badge','card1_title','card1_title_muted',
            'card1_summary','card1_pillbox_title','card1_pills',
            'card1_learn_more_url','card1_learn_more_external','card1_curriculum_url','card1_curriculum_external','card1_apply_url',

            // Card 2
            'card2_active','card2_position','card2_slug','card2_badge','card2_title','card2_title_muted',
            'card2_summary','card2_pillbox_title','card2_pills',
            'card2_learn_more_url','card2_learn_more_external','card2_curriculum_url','card2_curriculum_external','card2_apply_url',

            // Card 3
            'card3_active','card3_position','card3_slug','card3_badge','card3_title','card3_title_muted',
            'card3_summary','card3_pillbox_title','card3_pills',
            'card3_learn_more_url','card3_learn_more_external','card3_curriculum_url','card3_curriculum_external','card3_apply_url',

            // Card 4
            'card4_active','card4_position','card4_slug','card4_badge','card4_title','card4_title_muted',
            'card4_summary','card4_pillbox_title','card4_pills',
            'card4_learn_more_url','card4_learn_more_external','card4_curriculum_url','card4_curriculum_external','card4_apply_url',

            // CTA
            'cta_title','cta_description','cta_action_url','cta_action_label',
        ];

        $set = implode(', ', array_map(fn($f) => "$f = :$f", $fields));
        $sql = 'UPDATE ' . self::$table . ' 
                   SET ' . $set . ', updated_at = CURRENT_TIMESTAMP
                 WHERE id = (SELECT id FROM ' . self::$table . ' ORDER BY id DESC LIMIT 1)';
        $stmt = $db->prepare($sql);

        foreach ($fields as $f) {
            $stmt->bindValue(":$f", array_key_exists($f, $data) ? $data[$f] : null);
        }
        $stmt->execute();
    }

    /**
     * Helper: turn flat $settings into structured cards array for rendering.
     * Returns only active cards, sorted by position.
     */
    public static function getCards(array $settings): array {
        $cards = [];
        for ($i=1; $i<=4; $i++) {
            if (!empty($settings["card{$i}_active"])) {
                $pills = [];
                if (!empty($settings["card{$i}_pills"])) {
                    $pills = preg_split("/\r\n|\n|\r/", (string)$settings["card{$i}_pills"]);
                    $pills = array_values(array_filter(array_map('trim', $pills), fn($x)=>$x!==''));
                }
                $cards[] = [
                    'position' => (int)($settings["card{$i}_position"] ?? $i),
                    'slug'     => $settings["card{$i}_slug"] ?? '',
                    'badge'    => $settings["card{$i}_badge"] ?? '',
                    'title'    => $settings["card{$i}_title"] ?? '',
                    'muted'    => $settings["card{$i}_title_muted"] ?? '',
                    'summary'  => $settings["card{$i}_summary"] ?? '',
                    'pill_t'   => $settings["card{$i}_pillbox_title"] ?? '',
                    'pills'    => $pills,
                    'lm_url'   => $settings["card{$i}_learn_more_url"] ?? '',
                    'lm_ext'   => !empty($settings["card{$i}_learn_more_external"]),
                    'cur_url'  => $settings["card{$i}_curriculum_url"] ?? '',
                    'cur_ext'  => !empty($settings["card{$i}_curriculum_external"]),
                    'apply'    => $settings["card{$i}_apply_url"] ?? '',
                ];
            }
        }
        usort($cards, fn($a,$b) => ($a['position'] <=> $b['position']));
        return $cards;
    }
}
