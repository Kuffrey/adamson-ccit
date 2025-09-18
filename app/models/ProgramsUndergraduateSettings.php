<?php
// app/models/ProgramsUndergraduateSettings.php
require_once __DIR__ . '/Model.php';

class ProgramsUndergraduateSettings extends Model
{
    protected static $table = 'programs_undergraduate_settings';
    protected const CARDS_TABLE = 'programs_undergraduate';

    /** Always return latest row (your table stores a single record). */
    public static function getSettings(): array
    {
        $db  = self::db();
        
        try {
            // Check if table exists first
            $checkSql = "SHOW TABLES LIKE '" . self::$table . "'";
            $tableExists = $db->query($checkSql)->rowCount() > 0;
            
            if (!$tableExists) {
                // Create the table if it doesn't exist
                $createSql = "CREATE TABLE IF NOT EXISTS " . self::$table . " (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    subhero_image_url VARCHAR(255) DEFAULT '/adamson-ccit/public/assets/images/programs/undergrad.jpg',
                    subhero_lead TEXT DEFAULT NULL,
                    programs_grid TEXT DEFAULT NULL,
                    cta_title VARCHAR(255) DEFAULT NULL,
                    cta_description TEXT DEFAULT NULL,
                    cta_action_url VARCHAR(255) DEFAULT NULL,
                    cta_action_label VARCHAR(100) DEFAULT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                )";
                $db->exec($createSql);
                
                // Insert default row
                $db->exec(
                    "INSERT INTO " . self::$table . " (subhero_image_url, subhero_lead) 
                     VALUES ('/adamson-ccit/public/assets/images/programs/undergrad.jpg', 'Undergraduate Programs at Adamson University CCIT')"
                );
            }
            
            $sql = 'SELECT * FROM ' . self::$table . ' ORDER BY id DESC LIMIT 1';
            $stmt = $db->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
            
        } catch (\Throwable $e) {
            // Log the error
            error_log("Error in getSettings: " . $e->getMessage());
            // Return empty array with default values
            return [
                'subhero_image_url' => '/adamson-ccit/public/assets/images/programs/undergrad.jpg',
                'subhero_lead' => 'Undergraduate Programs at Adamson University CCIT',
                'programs_grid' => '',
                'cta_title' => '',
                'cta_description' => '',
                'cta_action_url' => '',
                'cta_action_label' => ''
            ];
        }
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
        
        try {
            error_log('Starting updateSettings with data: ' . json_encode($settings));
            
            // Check if table exists
            $checkSql = "SHOW TABLES LIKE '" . self::$table . "'";
            $tableExists = $db->query($checkSql)->rowCount() > 0;
            
            if (!$tableExists) {
                error_log('Table ' . self::$table . ' does not exist. Creating...');
                // Create the table if it doesn't exist
                $createSql = "CREATE TABLE IF NOT EXISTS " . self::$table . " (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    subhero_image_url VARCHAR(255) DEFAULT '/adamson-ccit/public/assets/images/programs/undergrad.jpg',
                    subhero_lead TEXT DEFAULT NULL,
                    programs_grid TEXT DEFAULT NULL,
                    cta_title VARCHAR(255) DEFAULT NULL,
                    cta_description TEXT DEFAULT NULL,
                    cta_action_url VARCHAR(255) DEFAULT NULL,
                    cta_action_label VARCHAR(100) DEFAULT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                )";
                $db->exec($createSql);
            }

            // Ensure there is at least one settings row
            $countSql = "SELECT COUNT(*) FROM " . self::$table;
            $count = (int)$db->query($countSql)->fetchColumn();
            
            if ($count === 0) {
                error_log('No records in ' . self::$table . '. Inserting default row.');
                $insertSql = "INSERT INTO " . self::$table . " (subhero_image_url, subhero_lead) VALUES ('/adamson-ccit/public/assets/images/programs/undergrad.jpg', 'Undergraduate Programs at Adamson University CCIT')";
                $db->exec($insertSql);
            }

            // --- Allowlist & update settings ---
            $allow = [
                'subhero_image_url','subhero_lead','programs_grid',
                'cta_title','cta_description','cta_action_url','cta_action_label',
            ];

            $setParts = [];
            foreach ($allow as $col) { 
                $setParts[] = "$col = :$col"; 
            }
            
            $sql = 'UPDATE ' . self::$table . '
                    SET ' . implode(', ', $setParts) . '
                    WHERE id = (SELECT id FROM (SELECT id FROM ' . self::$table . ' ORDER BY id DESC LIMIT 1) AS temp)';
                    
            error_log('Update SQL: ' . $sql);
            
            $stmt = $db->prepare($sql);
            foreach ($allow as $col) {
                $value = array_key_exists($col, $settings) ? $settings[$col] : null;
                $stmt->bindValue(':' . $col, $value);
                error_log("Binding $col: " . (is_null($value) ? "NULL" : $value));
            }
            
            $result = $stmt->execute();
            error_log('Update result: ' . ($result ? 'success' : 'failed'));
        } catch (\Throwable $e) {
            error_log('Error in updateSettings: ' . $e->getMessage());
            throw $e; // Rethrow to allow caller to handle
        }

        // --- Optional: add ONE new dynamic card into ug_cards ---
        if (is_array($addCard)) {
            self::insertOneCard($db, $addCard);
        }
    }

    /**
     * Get field mapping for programs_undergraduate table
     */
    private static function getFieldMapping(): array
    {
        static $mapping = null;
        
        if ($mapping === null) {
            $db = self::db();
            
            // Get actual column names
            $stmt = $db->query("DESCRIBE " . self::CARDS_TABLE);
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $actualFields = array_column($columns, 'Field');
            
            $mapping = [];
            
            // Map title field
            $titleFields = ['name', 'title', 'program_name', 'program_title'];
            foreach ($titleFields as $field) {
                if (in_array($field, $actualFields)) {
                    $mapping['title'] = $field;
                    break;
                }
            }
            
            // Map description field
            $descFields = ['description', 'desc', 'program_description'];
            foreach ($descFields as $field) {
                if (in_array($field, $actualFields)) {
                    $mapping['description'] = $field;
                    break;
                }
            }
            
            // Map badge field
            $badgeFields = ['degree_type', 'type', 'program_type', 'badge'];
            foreach ($badgeFields as $field) {
                if (in_array($field, $actualFields)) {
                    $mapping['badge'] = $field;
                    break;
                }
            }
            
            // Set defaults if not found
            if (!isset($mapping['title'])) $mapping['title'] = 'id';
            if (!isset($mapping['description'])) $mapping['description'] = 'id';
            if (!isset($mapping['badge'])) $mapping['badge'] = 'id';
            
            $mapping['available_fields'] = $actualFields;
        }
        
        return $mapping;
    }

    /**
     * Get cards - for frontend (active only) or admin (all cards)
     * @param array $settings - unused, kept for compatibility
     * @param bool $adminMode - if true, return all cards; if false, return active only
     */
    public static function getCards(array $settings = [], bool $adminMode = false): array
    {
        $db = self::db();
        
        try {
            $mapping = self::getFieldMapping();
            $fields = $mapping['available_fields'];
            
            if ($adminMode) {
                // Admin mode: return all programs with mapping to card structure
                $sql = 'SELECT id';
                if ($mapping['title'] !== 'id') $sql .= ', `' . $mapping['title'] . '` as title';
                if ($mapping['description'] !== 'id') $sql .= ', `' . $mapping['description'] . '` as description';
                if ($mapping['badge'] !== 'id') $sql .= ', `' . $mapping['badge'] . '` as badge';
                if (in_array('slug', $fields)) $sql .= ', slug';
                if (in_array('title_muted', $fields)) $sql .= ', title_muted';
                if (in_array('summary', $fields)) $sql .= ', summary';
                if (in_array('pillbox_title', $fields)) $sql .= ', pillbox_title';
                if (in_array('pills', $fields)) $sql .= ', pills';
                if (in_array('learn_more_url', $fields)) $sql .= ', learn_more_url';
                if (in_array('learn_more_external', $fields)) $sql .= ', learn_more_external';
                if (in_array('curriculum_url', $fields)) $sql .= ', curriculum_url';
                if (in_array('curriculum_external', $fields)) $sql .= ', curriculum_external';
                if (in_array('apply_url', $fields)) $sql .= ', apply_url';
                if (in_array('position', $fields)) $sql .= ', position';
                if (in_array('is_active', $fields)) $sql .= ', is_active';
                if (in_array('created_at', $fields)) $sql .= ', created_at';
                if (in_array('updated_at', $fields)) $sql .= ', updated_at';
                $sql .= ' FROM ' . self::CARDS_TABLE . ' ORDER BY position ASC, id ASC';
                
                $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
                
                // Convert to card format for admin interface
                $cards = [];
                foreach ($rows as $row) {
                    $cards[] = [
                        'id' => $row['id'],
                        'is_active' => $row['is_active'] ?? 1,
                        'position' => $row['position'] ?? $row['id'],
                        'slug' => $row['slug'] ?? '',
                        'badge' => $row['badge'] ?? '',
                        'title' => $row['title'] ?? 'Program ' . $row['id'],
                        'title_muted' => $row['title_muted'] ?? '',
                        'description' => $row['description'] ?? '', // Keep for backward compatibility
                        'summary' => $row['summary'] ?? '',
                        'pillbox_title' => $row['pillbox_title'] ?? '',
                        'pills' => $row['pills'] ?? '',
                        'learn_more_url' => $row['learn_more_url'] ?? '',
                        'learn_more_external' => $row['learn_more_external'] ?? 0,
                        'curriculum_url' => $row['curriculum_url'] ?? '',
                        'curriculum_external' => $row['curriculum_external'] ?? 0,
                        'apply_url' => $row['apply_url'] ?? '',
                        'link_url' => '', // Legacy field
                        'icon' => '', // Legacy field
                        'created_at' => $row['created_at'] ?? '',
                        'updated_at' => $row['updated_at'] ?? ''
                    ];
                }
                return $cards;
            } else {
                // Frontend mode: return all active programs with full details
                $sql = 'SELECT id';
                if ($mapping['title'] !== 'id') $sql .= ', `' . $mapping['title'] . '` as title';
                if (in_array('summary', $fields)) $sql .= ', summary';
                if ($mapping['badge'] !== 'id') $sql .= ', `' . $mapping['badge'] . '` as badge';
                if (in_array('slug', $fields)) $sql .= ', slug';
                if (in_array('title_muted', $fields)) $sql .= ', title_muted as muted';
                if (in_array('pillbox_title', $fields)) $sql .= ', pillbox_title as pill_t';
                if (in_array('pills', $fields)) $sql .= ', pills';
                if (in_array('learn_more_url', $fields)) $sql .= ', learn_more_url as lm_url';
                if (in_array('learn_more_external', $fields)) $sql .= ', learn_more_external as lm_ext';
                if (in_array('curriculum_url', $fields)) $sql .= ', curriculum_url as cur_url';
                if (in_array('curriculum_external', $fields)) $sql .= ', curriculum_external as cur_ext';
                if (in_array('apply_url', $fields)) $sql .= ', apply_url as apply';
                if (in_array('is_active', $fields)) {
                    $sql .= ', is_active FROM ' . self::CARDS_TABLE . ' WHERE is_active = 1 ORDER BY position ASC, id ASC';
                } else {
                    $sql .= ' FROM ' . self::CARDS_TABLE . ' ORDER BY id ASC';
                }
                
                $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];

                $cards = [];
                foreach ($rows as $row) {
                    // Process pills field - convert newline-separated string to array
                    $pillsArray = [];
                    if (!empty($row['pills'])) {
                        $pillsArray = array_filter(array_map('trim', explode("\n", $row['pills'])));
                    }
                    
                    $cards[] = [
                        'id' => $row['id'],
                        'is_active' => $row['is_active'] ?? 1,
                        'position' => $row['id'],
                        'slug' => $row['slug'] ?? '',
                        'badge' => $row['badge'] ?? '',
                        'title' => $row['title'] ?? 'Program ' . $row['id'],
                        'muted' => $row['muted'] ?? '',
                        'summary' => $row['summary'] ?? '',
                        'pill_t' => $row['pill_t'] ?? '',
                        'pills' => $pillsArray,
                        'lm_url' => $row['lm_url'] ?? '',
                        'lm_ext' => $row['lm_ext'] ?? 0,
                        'cur_url' => $row['cur_url'] ?? '',
                        'cur_ext' => $row['cur_ext'] ?? 0,
                        'apply' => $row['apply'] ?? '',
                        'link_url' => '',
                        'icon' => ''
                    ];
                }
                return $cards;
            }
        } catch (Exception $e) {
            error_log("Error in getCards: " . $e->getMessage());
            return [];
        }
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
        try {
            // If nothing meaningful provided, skip
            $title = trim((string)($in['title'] ?? ''));
            $badge = trim((string)($in['badge'] ?? ''));
            if ($title === '' && $badge === '') {
                error_log("Skipping card insertion: no title or badge provided");
                return;
            }
            
            error_log("Inserting new card with title: $title, badge: $badge");

            // Normalize inputs
            $slugIn   = trim((string)($in['slug'] ?? ''));
            $slug     = $slugIn !== '' ? self::slugify($slugIn) : self::slugify($title ?: $badge);
            if ($slug === '') $slug = 'program-' . time();
            $slug     = self::uniqueSlug(self::db(), $slug);
            
            error_log("Generated slug for new card: $slug");

            $summary  = self::nullIfEmpty($in['summary'] ?? null);
            $muted    = self::nullIfEmpty($in['title_muted'] ?? null);
            $pill_t   = self::nullIfEmpty($in['pillbox_title'] ?? null);
            $pillsRaw = self::normalizePills($in['pills'] ?? null);

            $lm_url   = self::nullIfEmpty($in['learn_more_url'] ?? null);
            $lm_ext   = self::bool01($in['learn_more_external'] ?? 1);
            $cur_url  = self::nullIfEmpty($in['curriculum_url'] ?? null);
            $cur_ext  = self::bool01($in['curriculum_external'] ?? 1);
            $apply    = self::nullIfEmpty($in['apply_url'] ?? null);

            // Check if table exists first
            $checkSql = "SHOW TABLES LIKE '" . self::CARDS_TABLE . "'";
            $tableExists = $db->query($checkSql)->rowCount() > 0;
            
            if (!$tableExists) {
                error_log("Table " . self::CARDS_TABLE . " doesn't exist - creating it");
                // Create the table if it doesn't exist
                $createSql = "CREATE TABLE IF NOT EXISTS " . self::CARDS_TABLE . " (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    is_active TINYINT(1) NOT NULL DEFAULT '1',
                    position INT(11) NOT NULL DEFAULT '999',
                    slug VARCHAR(100) NOT NULL,
                    badge VARCHAR(50) DEFAULT NULL,
                    title VARCHAR(100) NOT NULL,
                    title_muted VARCHAR(100) DEFAULT NULL,
                    summary TEXT DEFAULT NULL,
                    pillbox_title VARCHAR(100) DEFAULT NULL,
                    pills TEXT DEFAULT NULL,
                    learn_more_url VARCHAR(255) DEFAULT NULL,
                    learn_more_external TINYINT(1) DEFAULT '1',
                    curriculum_url VARCHAR(255) DEFAULT NULL,
                    curriculum_external TINYINT(1) DEFAULT '1',
                    apply_url VARCHAR(255) DEFAULT NULL,
                    PRIMARY KEY (id),
                    UNIQUE KEY slug (slug)
                )";
                $db->exec($createSql);
                error_log("Table created successfully");
            }

            // Next position
            $position = (int)$db->query('SELECT IFNULL(MAX(position),0)+1 AS p FROM ' . self::CARDS_TABLE)->fetchColumn();
            if ($position < 1) $position = 1;
            error_log("New card position will be: $position");

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
                        
            error_log("Preparing SQL: $sql");
            
            $params = [
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
            ];
            
            error_log("Params for insert: " . json_encode($params));
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            $insertId = $db->lastInsertId();
            error_log("Card inserted successfully. New ID: $insertId");
        } catch (PDOException $e) {
            error_log("Database error in insertOneCard: " . $e->getMessage());
            throw $e;
        } catch (\Throwable $e) {
            error_log("General error in insertOneCard: " . $e->getMessage());
            throw $e;
        }
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
        if (empty($v)) {
            return 'program-' . time();
        }
        
        // Remove any non-ASCII characters and convert to lowercase
        $s = strtolower(trim($v));
        
        // Replace non-alphanumeric characters with hyphens
        $s = preg_replace('~[^a-z0-9-]+~', '-', $s);
        
        // Remove duplicate hyphens
        $s = preg_replace('~-+~', '-', $s);
        
        // Remove leading and trailing hyphens
        $s = trim($s, '-');
        
        // If slug is empty after cleaning, use a fallback
        if (empty($s)) {
            return 'program-' . time();
        }
        
        // Ensure slug is no longer than 80 characters
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
    try {
        $db = self::db();
        
        // Check if table exists first
        $checkSql = "SHOW TABLES LIKE '" . self::CARDS_TABLE . "'";
        $tableExists = $db->query($checkSql)->rowCount() > 0;
        
        if (!$tableExists) {
            // Create the table if it doesn't exist, matching the existing structure from db
            $createSql = "CREATE TABLE IF NOT EXISTS " . self::CARDS_TABLE . " (
                id INT(11) NOT NULL AUTO_INCREMENT,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                position INT(11) NOT NULL DEFAULT 100,
                slug VARCHAR(80) DEFAULT NULL,
                badge VARCHAR(60) DEFAULT NULL,
                title VARCHAR(255) NOT NULL,
                title_muted VARCHAR(120) DEFAULT NULL,
                summary TEXT DEFAULT NULL,
                pillbox_title VARCHAR(120) DEFAULT NULL,
                pills TEXT DEFAULT NULL,
                learn_more_url VARCHAR(512) DEFAULT NULL,
                learn_more_external TINYINT(1) NOT NULL DEFAULT 1,
                curriculum_url VARCHAR(512) DEFAULT NULL,
                curriculum_external TINYINT(1) NOT NULL DEFAULT 1,
                apply_url VARCHAR(512) DEFAULT NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uq_ug_cards_slug (slug),
                KEY idx_active_pos (is_active,position),
                KEY idx_pos (position)
            )";
            $db->exec($createSql);
        }
        
        $sql = 'SELECT id, is_active, position, slug, badge, title, title_muted, summary,
                       pillbox_title, pills, learn_more_url, learn_more_external,
                       curriculum_url, curriculum_external, apply_url
                  FROM ' . self::CARDS_TABLE . '
              ORDER BY position ASC, id ASC';
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (\Throwable $e) {
        // Log the error
        error_log("Error in getAllCards: " . $e->getMessage());
        // Return empty array
        return [];
    }
}

/** Admin: update one card by id (normalizes fields, keeps slug unique).
 * @return array Returns information about the update operation
 */
public static function updateCardById(int $id, array $in): array
{
    $result = [
        'success' => false,
        'rows_affected' => 0,
        'message' => '',
        'card_id' => $id,
        'updated_data' => null
    ];
    
    if ($id <= 0) {
        error_log("Invalid card ID: $id");
        $result['message'] = "Invalid card ID: $id";
        return $result;
    }
    
    try {
        // Get database connection
        $db = self::db();
        
        error_log("Starting update for card ID $id with data: " . json_encode($in));
        
        // First check if the card exists
        $checkStmt = $db->prepare("SELECT * FROM " . self::CARDS_TABLE . " WHERE id = ?");
        $checkStmt->execute([$id]);
        $row = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            error_log("Card with ID $id not found in database");
            $result['message'] = "Card with ID $id not found in database";
            return $result;
        }
        
        error_log("Found existing card: " . json_encode($row));
        
        // Process the input data
        // Always use the existing value as fallback if not provided in input
        $position = isset($in['position']) ? (int)$in['position'] : (int)$row['position'];
        $is_active = isset($in['is_active']) ? ($in['is_active'] ? 1 : 0) : (int)$row['is_active'];
        
        $badge = isset($in['badge']) ? $in['badge'] : $row['badge'];
        $badge = $badge === '' ? null : $badge;
        
        // Title is required - never allow empty
        $title = isset($in['title']) && $in['title'] !== '' ? $in['title'] : $row['title'];
        if (empty($title)) {
            $title = $badge ?? 'Program ' . $id;
        }
        
        $title_muted = isset($in['title_muted']) ? $in['title_muted'] : $row['title_muted'];
        $title_muted = $title_muted === '' ? null : $title_muted;
        
        // Handle slug - generate from title if empty
        $slugInput = isset($in['slug']) ? trim($in['slug']) : $row['slug'];
        if (empty($slugInput)) {
            $slugInput = self::slugify($title);
            error_log("Generated slug from title: $slugInput");
        }
        
        // Ensure slug is unique
        $slugBase = self::slugify($slugInput);
        $slug = $slugBase;
        
        // Only check uniqueness if slug changed
        if ($slug !== $row['slug']) {
            $checkSlugStmt = $db->prepare("SELECT 1 FROM " . self::CARDS_TABLE . " WHERE slug = ? AND id <> ? LIMIT 1");
            $counter = 2;
            
            while (true) {
                $checkSlugStmt->execute([$slug, $id]);
                if (!$checkSlugStmt->fetchColumn()) {
                    break; // Slug is unique
                }
                
                // Slug exists, add counter
                $slug = $slugBase . "-" . $counter++;
                if ($counter > 100) {
                    $slug = $slugBase . "-" . uniqid();
                    break;
                }
            }
            
            error_log("Using slug: $slug (original input: $slugInput)");
        }
        
        // Process text fields
        $summary = isset($in['summary']) ? $in['summary'] : $row['summary'];
        $summary = $summary === '' ? null : $summary;
        
        $pillbox_title = isset($in['pillbox_title']) ? $in['pillbox_title'] : $row['pillbox_title'];
        $pillbox_title = $pillbox_title === '' ? null : $pillbox_title;
        
        $pills = isset($in['pills']) ? self::normalizePills($in['pills']) : $row['pills'];
        
        // Process URL fields
        $learn_more_url = isset($in['learn_more_url']) ? $in['learn_more_url'] : $row['learn_more_url'];
        $learn_more_url = $learn_more_url === '' ? null : $learn_more_url;
        
        $learn_more_external = isset($in['learn_more_external']) ? ($in['learn_more_external'] ? 1 : 0) : (int)$row['learn_more_external'];
        
        $curriculum_url = isset($in['curriculum_url']) ? $in['curriculum_url'] : $row['curriculum_url'];
        $curriculum_url = $curriculum_url === '' ? null : $curriculum_url;
        
        $curriculum_external = isset($in['curriculum_external']) ? ($in['curriculum_external'] ? 1 : 0) : (int)$row['curriculum_external'];
        
        $apply_url = isset($in['apply_url']) ? $in['apply_url'] : $row['apply_url'];
        $apply_url = $apply_url === '' ? null : $apply_url;
        
        // Check if force update is requested
        $force_update = !empty($in['_force_update']);
        
        // Prepare new data for response
        $updatedData = [
            'id' => $id,
            'is_active' => $is_active,
            'position' => $position,
            'slug' => $slug,
            'badge' => $badge,
            'title' => $title,
            'title_muted' => $title_muted,
            'summary' => $summary,
            'pillbox_title' => $pillbox_title,
            'pills' => $pills,
            'learn_more_url' => $learn_more_url,
            'learn_more_external' => $learn_more_external,
            'curriculum_url' => $curriculum_url,
            'curriculum_external' => $curriculum_external,
            'apply_url' => $apply_url
        ];
        
        // Try a proper prepared statement approach first
        try {
            error_log("Executing UPDATE SQL: UPDATE " . self::CARDS_TABLE . "
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
             WHERE id = :id");
                
            $stmt = $db->prepare("UPDATE " . self::CARDS_TABLE . " SET 
                is_active = :act, 
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
                apply_url = :apply,
                updated_at = NOW()
                WHERE id = :id");
                
            // Bind parameters directly, not using arrays to avoid reference issues
            $stmt->bindValue(':act', $is_active ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(':pos', $position, PDO::PARAM_INT);
            $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
            $stmt->bindValue(':badge', $badge, $badge === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':title', $title, PDO::PARAM_STR);
            $stmt->bindValue(':muted', $title_muted, $title_muted === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':summary', $summary, $summary === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':pill_t', $pillbox_title, $pillbox_title === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':pills', $pills, $pills === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':lm_url', $learn_more_url, $learn_more_url === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':lm_ext', $learn_more_external ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(':cur_url', $curriculum_url, $curriculum_url === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':cur_ext', $curriculum_external ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(':apply', $apply_url, $apply_url === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            // Execute the statement
            $stmt->execute();
            $updateResult = $stmt->rowCount();
            error_log("Prepared statement update result: $updateResult rows affected");
            $result['rows_affected'] = $updateResult;
            
        } catch (\PDOException $preparedStmtError) {
            // If the prepared statement fails, fall back to direct SQL
            error_log("Prepared statement failed: " . $preparedStmtError->getMessage());
            error_log("Falling back to direct SQL execution");
            
            // Build direct update query with literals to avoid PDO binding issues
            $updateSql = "UPDATE " . self::CARDS_TABLE . " SET 
                is_active = " . ($is_active ? "1" : "0") . ", 
                position = " . $position . ", 
                slug = " . ($slug === null ? "NULL" : $db->quote($slug)) . ", 
                badge = " . ($badge === null ? "NULL" : $db->quote($badge)) . ", 
                title = " . $db->quote($title) . ",
                title_muted = " . ($title_muted === null ? "NULL" : $db->quote($title_muted)) . ", 
                summary = " . ($summary === null ? "NULL" : $db->quote($summary)) . ", 
                pillbox_title = " . ($pillbox_title === null ? "NULL" : $db->quote($pillbox_title)) . ", 
                pills = " . ($pills === null ? "NULL" : $db->quote($pills)) . ", 
                learn_more_url = " . ($learn_more_url === null ? "NULL" : $db->quote($learn_more_url)) . ", 
                learn_more_external = " . $learn_more_external . ", 
                curriculum_url = " . ($curriculum_url === null ? "NULL" : $db->quote($curriculum_url)) . ", 
                curriculum_external = " . $curriculum_external . ", 
                apply_url = " . ($apply_url === null ? "NULL" : $db->quote($apply_url)) . ",
                updated_at = NOW()
                WHERE id = " . $id;
            
            error_log("Executing direct UPDATE SQL: " . $updateSql);
            $updateResult = $db->exec($updateSql);
            $result['rows_affected'] = $updateResult;
            error_log("Direct SQL update result: $updateResult rows affected");
        }
        
        // We now update the timestamp directly in the main update query
        error_log("Timestamp updated for card ID $id within the main query");
        
        if ($updateResult === false) {
            $errorInfo = $db->errorInfo();
            $errorMessage = "Database error: " . implode(" | ", $errorInfo);
            error_log($errorMessage);
            $result['message'] = $errorMessage;
            return $result;
        }
        
        if ($updateResult === 0) {
            error_log("No rows were affected. This could mean no changes were made or there was an issue.");
            
            // Make one more attempt to verify the record exists
            $verifyStmt = $db->prepare("SELECT id FROM " . self::CARDS_TABLE . " WHERE id = ?");
            $verifyStmt->execute([$id]);
            
            if (!$verifyStmt->fetch()) {
                $errorMessage = "ERROR: Card ID $id not found after update attempt!";
                error_log($errorMessage);
                $result['message'] = $errorMessage;
                return $result;
            } else {
                error_log("Card exists but no changes were made - data might be identical");
                
                // We already forced the timestamp update above, so just report success
                $result['success'] = true;
                $result['message'] = "Card data updated successfully";
                $result['updated_data'] = $updatedData;
            }
        } else {
            error_log("Successfully updated card ID $id");
            $result['success'] = true;
            $result['message'] = "Successfully updated card ID $id";
            $result['updated_data'] = $updatedData;
        }
        
        // Fetch the updated record to return
        $getUpdatedStmt = $db->prepare("SELECT * FROM " . self::CARDS_TABLE . " WHERE id = ?");
        $getUpdatedStmt->execute([$id]);
        $updatedRow = $getUpdatedStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($updatedRow) {
            $result['updated_record'] = $updatedRow;
        }
        
        return $result;
        
    } catch (\PDOException $e) {
        $errorMessage = "PDO Exception in updateCardById: " . $e->getMessage();
        error_log($errorMessage);
        $result['message'] = $errorMessage;
        return $result;
    } catch (\Throwable $e) {
        $errorMessage = "Error in updateCardById: " . $e->getMessage();
        error_log($errorMessage);
        $result['message'] = $errorMessage;
        return $result;
    }
}

/** Admin: delete one card by id. */
public static function deleteCardById(int $id): void
{
    if ($id <= 0) {
        error_log("Invalid ID for deletion: $id");
        return;
    }
    
    try {
        $db = self::db();
        $stmt = $db->prepare('DELETE FROM ' . self::CARDS_TABLE . ' WHERE id = ?');
        $stmt->execute([$id]);
        
        $rowCount = $stmt->rowCount();
        error_log("Delete executed for ID $id. Rows affected: $rowCount");
        
        if ($rowCount === 0) {
            error_log("Warning: No rows were deleted for ID $id");
        }
    } catch (PDOException $e) {
        error_log("Database error in deleteCardById: " . $e->getMessage());
        throw $e;
    }
}

    /**
     * Alias method for admin interface compatibility
     */
    public static function saveSettings(array $settings, array $addCard = []): void 
    {
        self::updateSettings($settings, $addCard);
    }

    /**
     * Create a new card (alias for insertOneCard) - Fixed for programs_undergraduate table
     */
    public static function createCard(array $cardData): void 
    {
        $db = self::db();
        
        try {
            $mapping = self::getFieldMapping();
            $fields = $mapping['available_fields'];
            
            $insertFields = [];
            $placeholders = [];
            $values = [];
            
            // Map title field
            if (isset($cardData['title']) && $mapping['title'] !== 'id') {
                $insertFields[] = '`' . $mapping['title'] . '`';
                $placeholders[] = '?';
                $values[] = $cardData['title'];
            }
            
            // Map description field (for backward compatibility)
            if (isset($cardData['description']) && $mapping['description'] !== 'id') {
                $insertFields[] = '`' . $mapping['description'] . '`';
                $placeholders[] = '?';
                $values[] = $cardData['description'];
            }
            
            // Map badge field
            if (isset($cardData['badge']) && $mapping['badge'] !== 'id') {
                $insertFields[] = '`' . $mapping['badge'] . '`';
                $placeholders[] = '?';
                $values[] = $cardData['badge'];
            }
            
            // Handle all the comprehensive fields
            $fieldMapping = [
                'title_muted' => ['title_muted'],
                'summary' => ['summary'],
                'pillbox_title' => ['pillbox_title'],
                'pills' => ['pills'],
                'learn_more_url' => ['learn_more_url'],
                'learn_more_external' => ['learn_more_external'],
                'curriculum_url' => ['curriculum_url'],
                'curriculum_external' => ['curriculum_external'],
                'apply_url' => ['apply_url'],
                'position' => ['position'],
                'is_active' => ['is_active']
            ];
            
            foreach ($fieldMapping as $dataField => $possibleColumns) {
                if (isset($cardData[$dataField])) {
                    foreach ($possibleColumns as $column) {
                        if (in_array($column, $fields)) {
                            $insertFields[] = $column;
                            $placeholders[] = '?';
                            
                            if ($dataField === 'is_active' || $dataField === 'learn_more_external' || $dataField === 'curriculum_external') {
                                // Handle checkbox values
                                $values[] = !empty($cardData[$dataField]) ? 1 : 0;
                            } elseif ($dataField === 'position') {
                                $values[] = max(1, (int)($cardData[$dataField] ?? 999)); // Default to end
                            } else {
                                $values[] = $cardData[$dataField];
                            }
                            break; // Use first matching column
                        }
                    }
                }
            }
            
            // Generate slug if possible
            if (in_array('slug', $fields)) {
                $title = $cardData['title'] ?? 'New Program';
                $slug = self::slugify($title);
                $counter = 1;
                $originalSlug = $slug;
                while (true) {
                    $checkSlug = $db->prepare("SELECT id FROM " . self::CARDS_TABLE . " WHERE slug = ?");
                    $checkSlug->execute([$slug]);
                    if (!$checkSlug->fetch()) break;
                    $counter++;
                    $slug = $originalSlug . '-' . $counter;
                }
                $insertFields[] = 'slug';
                $placeholders[] = '?';
                $values[] = $slug;
            }
            
            // Set default active status if not provided
            if (!isset($cardData['is_active']) && in_array('is_active', $fields)) {
                $insertFields[] = 'is_active';
                $placeholders[] = '?';
                $values[] = 1; // Default to active
            }
            
            // Add timestamps if available
            if (in_array('created_at', $fields)) {
                $insertFields[] = 'created_at';
                $placeholders[] = 'NOW()';
            }
            
            if (in_array('updated_at', $fields)) {
                $insertFields[] = 'updated_at';
                $placeholders[] = 'NOW()';
            }
            
            if (empty($insertFields)) {
                throw new Exception("No valid fields to insert");
            }
            
            $sql = 'INSERT INTO ' . self::CARDS_TABLE . ' (' . implode(', ', $insertFields) . ') VALUES (' . implode(', ', $placeholders) . ')';
            
            $stmt = $db->prepare($sql);
            $stmt->execute($values);
            
        } catch (Exception $e) {
            error_log("Error in createCard: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a card (alias for deleteCardById)
     */
    public static function deleteCard(int $id): void 
    {
        self::deleteCardById($id);
    }

    /**
     * Update a card (alias for updateCardById) - Fixed for programs_undergraduate table
     */
    public static function updateCard(int $id, array $cardData): void 
    {
        $db = self::db();
        
        try {
            $mapping = self::getFieldMapping();
            $fields = $mapping['available_fields'];
            
            $updateFields = [];
            $values = [];
            
            // Map title field
            if (isset($cardData['title']) && $mapping['title'] !== 'id') {
                $updateFields[] = '`' . $mapping['title'] . '` = ?';
                $values[] = $cardData['title'];
            }
            
            // Map description field (for backward compatibility)
            if (isset($cardData['description']) && $mapping['description'] !== 'id') {
                $updateFields[] = '`' . $mapping['description'] . '` = ?';
                $values[] = $cardData['description'];
            }
            
            // Map badge field
            if (isset($cardData['badge']) && $mapping['badge'] !== 'id') {
                $updateFields[] = '`' . $mapping['badge'] . '` = ?';
                $values[] = $cardData['badge'];
            }
            
            // Handle all the comprehensive fields
            $fieldMapping = [
                'title_muted' => ['title_muted'],
                'summary' => ['summary'],
                'pillbox_title' => ['pillbox_title'],
                'pills' => ['pills'],
                'learn_more_url' => ['learn_more_url'],
                'learn_more_external' => ['learn_more_external'],
                'curriculum_url' => ['curriculum_url'],
                'curriculum_external' => ['curriculum_external'],
                'apply_url' => ['apply_url'],
                'position' => ['position'],
                'is_active' => ['is_active']
            ];
            
            foreach ($fieldMapping as $dataField => $possibleColumns) {
                if (isset($cardData[$dataField])) {
                    foreach ($possibleColumns as $column) {
                        if (in_array($column, $fields)) {
                            if ($dataField === 'is_active' || $dataField === 'learn_more_external' || $dataField === 'curriculum_external') {
                                // Handle checkbox values
                                $updateFields[] = "$column = ?";
                                $values[] = !empty($cardData[$dataField]) ? 1 : 0;
                            } elseif ($dataField === 'position') {
                                $updateFields[] = "$column = ?";
                                $values[] = max(1, (int)$cardData[$dataField]);
                            } else {
                                $updateFields[] = "$column = ?";
                                $values[] = $cardData[$dataField];
                            }
                            break; // Use first matching column
                        }
                    }
                }
            }
            
            // Generate slug if title changed and slug column exists
            if (isset($cardData['title']) && in_array('slug', $fields)) {
                $slug = self::slugify($cardData['title']);
                // Ensure slug uniqueness
                $counter = 1;
                $originalSlug = $slug;
                while (true) {
                    $checkSlug = $db->prepare("SELECT id FROM " . self::CARDS_TABLE . " WHERE slug = ? AND id != ?");
                    $checkSlug->execute([$slug, $id]);
                    if (!$checkSlug->fetch()) break;
                    $counter++;
                    $slug = $originalSlug . '-' . $counter;
                }
                $updateFields[] = "slug = ?";
                $values[] = $slug;
            }
            
            if (empty($updateFields)) {
                throw new Exception("No valid fields to update");
            }
            
            // Add updated timestamp if available
            if (in_array('updated_at', $fields)) {
                $updateFields[] = "updated_at = NOW()";
            }
            
            $sql = "UPDATE " . self::CARDS_TABLE . " SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $values[] = $id;
            
            $stmt = $db->prepare($sql);
            $stmt->execute($values);
            
        } catch (Exception $e) {
            error_log("Error in updateCard: " . $e->getMessage());
            throw $e;
        }
    }

}
