<?php
// app/models/ProgramsUndergraduateSettings.php
require_once __DIR__ . '/Model.php';

class ProgramsUndergraduateSettings extends Model
{
    protected static $table = 'programs_undergraduate_settings';
    protected const CARDS_TABLE = 'ug_cards';

    /** Always return latest row (your table stores a single record). */
    public static function getSettings(): array
    {
        $db  = self::db();
        $sql = 'SELECT * FROM ' . self::$table . ' ORDER BY id DESC LIMIT 1';
        $stmt = $db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Update settings row; optionally insert ONE new dynamic card.
     *
     * Usage from admin:
     *   ProgramsUndergraduateSettings::updateSettings($_POST['ugs'] ?? [], $_POST['add_card'] ?? null);
     */
    public static function updateSettings(array $settings, ?array $addCard = null): void
    {
        $db = self::db();

        // Ensure there is at least one settings row
        $db->exec(
            'INSERT INTO ' . self::$table . ' (subhero_image_url)
             SELECT \'/adamson-ccit/public/assets/images/programs/undergrad.jpg\'
             WHERE NOT EXISTS (SELECT 1 FROM ' . self::$table . ')'
        );

        // --- Allowlist & update settings ---
        $allow = [
            'subhero_image_url','subhero_lead','programs_grid',
            'cta_title','cta_description','cta_action_url','cta_action_label',
        ];

        $setParts = [];
        foreach ($allow as $col) { $setParts[] = "$col = :$col"; }
        $sql = 'UPDATE ' . self::$table . '
                   SET ' . implode(', ', $setParts) . ', updated_at = CURRENT_TIMESTAMP
                 WHERE id = (SELECT id FROM ' . self::$table . ' ORDER BY id DESC LIMIT 1)';
        $stmt = $db->prepare($sql);
        foreach ($allow as $col) {
            $stmt->bindValue(':' . $col, array_key_exists($col, $settings) ? $settings[$col] : null);
        }
        $stmt->execute();

        // --- Optional: add ONE new dynamic card into ug_cards ---
        if (is_array($addCard)) {
            self::insertOneCard($db, $addCard);
        }
    }

    /**
     * Frontend renderer: return ACTIVE cards only.
     * (position, slug, badge, title, muted, summary, pill_t, pills[], lm_url, lm_ext, cur_url, cur_ext, apply)
     */
    public static function getCards(array $settings = []): array
    {
        $db = self::db();
        $sql = 'SELECT id, is_active, position, slug, badge, title, title_muted, summary,
                       pillbox_title, pills, learn_more_url, learn_more_external,
                       curriculum_url, curriculum_external, apply_url
                  FROM ' . self::CARDS_TABLE . '
                 WHERE is_active = 1
              ORDER BY position ASC, id ASC';
        $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $cards = [];
        foreach ($rows as $r) {
            $cards[] = self::rowToCard($r);
        }
        return $cards;
    }

    /**
     * Admin: fetch ALL cards (active + inactive), ordered by position then id.
     */
    public static function fetchAllCards(): array
    {
        $db = self::db();
        $sql = 'SELECT id, is_active, position, slug, badge, title, title_muted, summary,
                       pillbox_title, pills, learn_more_url, learn_more_external,
                       curriculum_url, curriculum_external, apply_url, created_at, updated_at
                  FROM ' . self::CARDS_TABLE . '
              ORDER BY position ASC, id ASC';
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Admin: bulk update cards + optional delete list.
     * $cards shape: [id => [fields...], ...]
     * $deleteIds: [id, id, ...]
     */
    public static function updateCardsBulk(array $cards, array $deleteIds = []): void
    {
        $db = self::db();
        $db->beginTransaction();
        try {

            // Delete marked cards
            if (!empty($deleteIds)) {
                $ids = array_values(array_filter(array_map('intval', $deleteIds), fn($x) => $x > 0));
                if ($ids) {
                    $in = implode(',', array_fill(0, count($ids), '?'));
                    $db->prepare('DELETE FROM ' . self::CARDS_TABLE . ' WHERE id IN (' . $in . ')')->execute($ids);
                }
            }

            // Update existing
            if (!empty($cards)) {
                $sql = 'UPDATE ' . self::CARDS_TABLE . ' SET
                            is_active = :is_active,
                            position = :position,
                            slug = :slug,
                            badge = :badge,
                            title = :title,
                            title_muted = :title_muted,
                            summary = :summary,
                            pillbox_title = :pillbox_title,
                            pills = :pills,
                            learn_more_url = :learn_more_url,
                            learn_more_external = :learn_more_external,
                            curriculum_url = :curriculum_url,
                            curriculum_external = :curriculum_external,
                            apply_url = :apply_url,
                            updated_at = CURRENT_TIMESTAMP
                        WHERE id = :id';
                $stmt = $db->prepare($sql);

                foreach ($cards as $id => $in) {
                    $id = (int)$id;
                    if ($id <= 0) continue;

                    // Normalize
                    $is_active = self::bool01($in['is_active'] ?? 0);
                    $position  = max(1, (int)($in['position'] ?? 1));

                    $badge   = self::nullIfEmpty($in['badge'] ?? null);
                    $title   = trim((string)($in['title'] ?? ''));
                    if ($title === '') $title = (string)($badge ?? 'Program');

                    $muted   = self::nullIfEmpty($in['title_muted'] ?? null);
                    $summary = self::nullIfEmpty($in['summary'] ?? null);

                    $pill_t  = self::nullIfEmpty($in['pillbox_title'] ?? null);
                    $pills   = self::normalizePills($in['pills'] ?? null);

                    $lm_url  = self::nullIfEmpty($in['learn_more_url'] ?? null);
                    $lm_ext  = self::bool01($in['learn_more_external'] ?? 0);
                    $cur_url = self::nullIfEmpty($in['curriculum_url'] ?? null);
                    $cur_ext = self::bool01($in['curriculum_external'] ?? 0);
                    $apply   = self::nullIfEmpty($in['apply_url'] ?? null);

                    // Slug
                    $slugIn = trim((string)($in['slug'] ?? ''));
                    $slug   = $slugIn !== '' ? self::slugify($slugIn)
                                             : self::slugify($title ?: ($badge ?? 'program'));
                    $slug   = self::uniqueSlugExcept($db, $slug, $id);

                    $stmt->execute([
                        ':id'        => $id,
                        ':is_active' => $is_active,
                        ':position'  => $position,
                        ':slug'      => $slug,
                        ':badge'     => $badge,
                        ':title'     => $title,
                        ':title_muted' => $muted,
                        ':summary'   => $summary,
                        ':pillbox_title' => $pill_t,
                        ':pills'     => $pills,
                        ':learn_more_url' => $lm_url,
                        ':learn_more_external' => $lm_ext,
                        ':curriculum_url' => $cur_url,
                        ':curriculum_external' => $cur_ext,
                        ':apply_url' => $apply,
                    ]);
                }
            }

            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /* ======================== Private helpers ======================== */

    private static function insertOneCard(PDO $db, array $in): void
    {
        // If nothing meaningful provided, skip
        $title = trim((string)($in['title'] ?? ''));
        $badge = trim((string)($in['badge'] ?? ''));
        if ($title === '' && $badge === '') return;

        // Normalize inputs
        $slugIn   = trim((string)($in['slug'] ?? ''));
        $slug     = $slugIn !== '' ? self::slugify($slugIn) : self::slugify($title ?: $badge);
        if ($slug === '') $slug = 'program-' . time();
        $slug     = self::uniqueSlug(self::db(), $slug);

        $summary  = self::nullIfEmpty($in['summary'] ?? null);
        $muted    = self::nullIfEmpty($in['title_muted'] ?? null);
        $pill_t   = self::nullIfEmpty($in['pillbox_title'] ?? null);
        $pillsRaw = self::normalizePills($in['pills'] ?? null);

        $lm_url   = self::nullIfEmpty($in['learn_more_url'] ?? null);
        $lm_ext   = self::bool01($in['learn_more_external'] ?? 1);
        $cur_url  = self::nullIfEmpty($in['curriculum_url'] ?? null);
        $cur_ext  = self::bool01($in['curriculum_external'] ?? 1);
        $apply    = self::nullIfEmpty($in['apply_url'] ?? null);

        // Next position
        $position = (int)$db->query('SELECT IFNULL(MAX(position),0)+1 AS p FROM ' . self::CARDS_TABLE)->fetchColumn();
        if ($position < 1) $position = 1;

        // Insert
        $sql = 'INSERT INTO ' . self::CARDS_TABLE . ' 
                   (is_active, position, slug, badge, title, title_muted, summary,
                    pillbox_title, pills,
                    learn_more_url, learn_more_external,
                    curriculum_url, curriculum_external,
                    apply_url)
                VALUES
                   (1, :position, :slug, :badge, :title, :muted, :summary,
                    :pill_t, :pills,
                    :lm_url, :lm_ext,
                    :cur_url, :cur_ext,
                    :apply)';
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':position' => $position,
            ':slug'     => $slug,
            ':badge'    => self::nullIfEmpty($badge),
            ':title'    => $title ?: $badge,
            ':muted'    => $muted,
            ':summary'  => $summary,
            ':pill_t'   => $pill_t,
            ':pills'    => $pillsRaw,
            ':lm_url'   => $lm_url,
            ':lm_ext'   => $lm_ext,
            ':cur_url'  => $cur_url,
            ':cur_ext'  => $cur_ext,
            ':apply'    => $apply,
        ]);
    }

    private static function rowToCard(array $r): array
    {
        return [
            'position' => (int)($r['position'] ?? 0),
            'slug'     => (string)($r['slug'] ?? ''),
            'badge'    => (string)($r['badge'] ?? ''),
            'title'    => (string)($r['title'] ?? ''),
            'muted'    => (string)($r['title_muted'] ?? ''),
            'summary'  => (string)($r['summary'] ?? ''),
            'pill_t'   => (string)($r['pillbox_title'] ?? ''),
            'pills'    => self::pillsToArray($r['pills'] ?? ''),
            'lm_url'   => (string)($r['learn_more_url'] ?? ''),
            'lm_ext'   => !empty($r['learn_more_external']),
            'cur_url'  => (string)($r['curriculum_url'] ?? ''),
            'cur_ext'  => !empty($r['curriculum_external']),
            'apply'    => (string)($r['apply_url'] ?? ''),
        ];
    }

    private static function slugify(string $v): string
    {
        $s = strtolower(trim($v));
        $s = preg_replace('~[^a-z0-9-]+~', '-', $s);
        $s = preg_replace('~-+~', '-', $s);
        $s = trim($s, '-');
        return mb_substr($s, 0, 80);
    }

    private static function uniqueSlug(PDO $db, string $base): string
    {
        $slug = $base;
        $i = 2;
        $check = $db->prepare('SELECT 1 FROM ' . self::CARDS_TABLE . ' WHERE slug = ? LIMIT 1');
        while (true) {
            $check->execute([$slug]);
            if (!$check->fetchColumn()) return $slug;
            $slug = $base . '-' . $i;
            $i++;
            if ($i > 200) return $base . '-' . uniqid();
        }
    }

    private static function uniqueSlugExcept(PDO $db, string $base, int $exceptId): string
    {
        $slug = $base;
        $i = 2;
        $check = $db->prepare('SELECT 1 FROM ' . self::CARDS_TABLE . ' WHERE slug = ? AND id <> ? LIMIT 1');
        while (true) {
            $check->execute([$slug, $exceptId]);
            if (!$check->fetchColumn()) return $slug;
            $slug = $base . '-' . $i;
            $i++;
            if ($i > 200) return $base . '-' . uniqid();
        }
    }

    private static function normalizePills(?string $v): ?string
    {
        if ($v === null) return null;
        $t = str_replace(["\r\n", "\r"], "\n", (string)$v);
        $lines = array_filter(array_map('trim', explode("\n", $t)), fn($x) => $x !== '');
        return $lines ? implode("\n", $lines) : null;
    }

    private static function pillsToArray(?string $v): array
    {
        if (!$v) return [];
        $lines = preg_split("/\r\n|\n|\r/", (string)$v);
        return array_values(array_filter(array_map('trim', $lines), fn($x)=>$x!==''));
    }

    private static function bool01($v): int { return !empty($v) ? 1 : 0; }

    private static function nullIfEmpty($v)
    {
        if (!isset($v)) return null;
        $s = trim((string)$v);
        return $s === '' ? null : $s;
    }

    // ... inside class ProgramsUndergraduateSettings extends Model

/** Admin: fetch raw rows for editing. */
public static function getAllCards(): array
{
    $db = self::db();
    $sql = 'SELECT id, is_active, position, slug, badge, title, title_muted, summary,
                   pillbox_title, pills, learn_more_url, learn_more_external,
                   curriculum_url, curriculum_external, apply_url
              FROM ' . self::CARDS_TABLE . '
          ORDER BY position ASC, id ASC';
    return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/** Admin: update one card by id (normalizes fields, keeps slug unique). */
public static function updateCardById(int $id, array $in): void
{
    if ($id <= 0) return;
    $db = self::db();

    // Load existing (needed for slug fallback)
    $cur = $db->prepare('SELECT * FROM ' . self::CARDS_TABLE . ' WHERE id = ?');
    $cur->execute([$id]);
    $row = $cur->fetch(PDO::FETCH_ASSOC);
    if (!$row) return;

    // Normalize
    $pos   = isset($in['position']) ? max(1, (int)$in['position']) : (int)$row['position'];
    $act   = !empty($in['is_active']) ? 1 : 0;
    $badge = self::nullIfEmpty($in['badge'] ?? null);
    $title = trim((string)($in['title'] ?? ''));
    if ($title === '') $title = (string)($row['title'] ?? ($badge ?? 'Program'));

    $muted = self::nullIfEmpty($in['title_muted'] ?? null);

    // Slug
    $slugIn = trim((string)($in['slug'] ?? ''));
    $slug   = $slugIn !== '' ? self::slugify($slugIn) : ($row['slug'] ?: self::slugify($title));
    // ensure uniqueness excluding current row
    $check = $db->prepare('SELECT 1 FROM ' . self::CARDS_TABLE . ' WHERE slug = ? AND id <> ? LIMIT 1');
    $base  = $slug;
    $i     = 2;
    while (true) {
        $check->execute([$slug, $id]);
        if (!$check->fetchColumn()) break;
        $slug = $base . '-' . $i++;
        if ($i > 200) { $slug = $base . '-' . uniqid(); break; }
    }

    // Text blocks
    $summary = self::nullIfEmpty($in['summary'] ?? null);
    $pill_t  = self::nullIfEmpty($in['pillbox_title'] ?? null);
    $pills   = self::normalizePills($in['pills'] ?? null);

    // Links
    $lm_url  = self::nullIfEmpty($in['learn_more_url'] ?? null);
    $lm_ext  = self::bool01($in['learn_more_external'] ?? 0);
    $cur_url = self::nullIfEmpty($in['curriculum_url'] ?? null);
    $cur_ext = self::bool01($in['curriculum_external'] ?? 0);
    $apply   = self::nullIfEmpty($in['apply_url'] ?? null);

    $sql = 'UPDATE ' . self::CARDS_TABLE . '
               SET is_active = :act,
                   position = :pos,
                   slug = :slug,
                   badge = :badge,
                   title = :title,
                   title_muted = :muted,
                   summary = :summary,
                   pillbox_title = :pill_t,
                   pills = :pills,
                   learn_more_url = :lm_url,
                   learn_more_external = :lm_ext,
                   curriculum_url = :cur_url,
                   curriculum_external = :cur_ext,
                   apply_url = :apply
             WHERE id = :id';
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':act'   => $act,
        ':pos'   => $pos,
        ':slug'  => $slug,
        ':badge' => $badge,
        ':title' => $title,
        ':muted' => $muted,
        ':summary' => $summary,
        ':pill_t'  => $pill_t,
        ':pills'   => $pills,
        ':lm_url'  => $lm_url,
        ':lm_ext'  => $lm_ext,
        ':cur_url' => $cur_url,
        ':cur_ext' => $cur_ext,
        ':apply'   => $apply,
        ':id'    => $id,
    ]);
}

/** Admin: delete one card by id. */
public static function deleteCardById(int $id): void
{
    if ($id <= 0) return;
    $db = self::db();
    $stmt = $db->prepare('DELETE FROM ' . self::CARDS_TABLE . ' WHERE id = ?');
    $stmt->execute([$id]);
}

}
