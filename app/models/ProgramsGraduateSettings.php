<?php
// app/models/ProgramsGraduateSettings.php
require_once __DIR__ . '/Model.php';

class ProgramsGraduateSettings extends Model {
    protected static $table = 'programs_graduate_settings';
    
    // Use programs_graduate table for dynamic cards, similar to undergraduate
    const CARDS_TABLE = 'programs_graduate';

    public static function getSettings(): array {
        $db = self::db();
        
        // Ensure the settings table exists
        $createSql = "CREATE TABLE IF NOT EXISTS " . self::$table . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            subhero_image_url VARCHAR(255) DEFAULT '/adamson-ccit/public/assets/images/programs/graduate.jpg',
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
        
        // Ensure there is at least one settings row
        $countSql = "SELECT COUNT(*) FROM " . self::$table;
        $count = (int)$db->query($countSql)->fetchColumn();
        
        if ($count === 0) {
            $insertSql = "INSERT INTO " . self::$table . " (subhero_image_url, subhero_lead) VALUES ('/adamson-ccit/public/assets/images/programs/graduate.jpg', 'Graduate Programs at Adamson University CCIT')";
            $db->exec($insertSql);
        }
        
        $sql = 'SELECT * FROM ' . self::$table . ' ORDER BY id DESC LIMIT 1';
        $stmt = $db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public static function saveSettings(array $settings, array $addCard = null): void
    {
        $db = self::db();
        
        try {
            // Ensure tables exist
            self::getSettings();
            
            // Ensure programs_graduate table exists with comprehensive structure
            $createCardsSql = "CREATE TABLE IF NOT EXISTS " . self::CARDS_TABLE . " (
                id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                is_active TINYINT(1) DEFAULT 1,
                position INT(11) DEFAULT 1,
                slug VARCHAR(255) UNIQUE,
                badge VARCHAR(50),
                title VARCHAR(255) NOT NULL,
                title_muted VARCHAR(255),
                summary TEXT,
                pillbox_title VARCHAR(255),
                pills TEXT,
                learn_more_url VARCHAR(500),
                learn_more_external TINYINT(1) DEFAULT 0,
                curriculum_url VARCHAR(500),
                curriculum_external TINYINT(1) DEFAULT 0,
                apply_url VARCHAR(500),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $db->exec($createCardsSql);

            // Update settings
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
            
            $stmt = $db->prepare($sql);
            foreach ($allow as $col) {
                $value = array_key_exists($col, $settings) ? $settings[$col] : null;
                $stmt->bindValue(':' . $col, $value);
            }
            
            $stmt->execute();
        } catch (\Throwable $e) {
            error_log('Error in saveSettings: ' . $e->getMessage());
            throw $e;
        }

        // Add new card if provided
        if (is_array($addCard)) {
            self::createCard($addCard);
        }
    }

    /**
     * Get field mapping for programs_graduate table
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
            if (!isset($mapping['title'])) $mapping['title'] = 'title';
            if (!isset($mapping['description'])) $mapping['description'] = 'summary';
            if (!isset($mapping['badge'])) $mapping['badge'] = 'badge';
            
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
                // Admin mode: return all programs with comprehensive data
                $sql = 'SELECT id';
                if ($mapping['title'] !== 'id') $sql .= ', `' . $mapping['title'] . '` as title';
                if (in_array('summary', $fields)) $sql .= ', summary';
                if ($mapping['badge'] !== 'id') $sql .= ', `' . $mapping['badge'] . '` as badge';
                if (in_array('slug', $fields)) $sql .= ', slug';
                if (in_array('title_muted', $fields)) $sql .= ', title_muted';
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
                        'description' => $row['summary'] ?? '', // Keep for backward compatibility
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
     * Create a new card
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
            
            // Handle all the comprehensive fields
            $fieldMapping = [
                'title' => ['title'],
                'summary' => ['summary'],
                'badge' => ['badge'],
                'title_muted' => ['title_muted'],
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
                                $values[] = !empty($cardData[$dataField]) ? 1 : 0;
                            } elseif ($dataField === 'position') {
                                $values[] = max(1, (int)($cardData[$dataField] ?? 999));
                            } else {
                                $values[] = $cardData[$dataField];
                            }
                            break;
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
                $values[] = 1;
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
     * Update a card
     */
    public static function updateCard(int $id, array $cardData): void 
    {
        $db = self::db();
        
        try {
            $mapping = self::getFieldMapping();
            $fields = $mapping['available_fields'];
            
            $updateFields = [];
            $values = [];
            
            // Handle all the comprehensive fields
            $fieldMapping = [
                'title' => ['title'],
                'summary' => ['summary'],
                'badge' => ['badge'],
                'title_muted' => ['title_muted'],
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
                                $updateFields[] = "$column = ?";
                                $values[] = !empty($cardData[$dataField]) ? 1 : 0;
                            } elseif ($dataField === 'position') {
                                $updateFields[] = "$column = ?";
                                $values[] = max(1, (int)$cardData[$dataField]);
                            } else {
                                $updateFields[] = "$column = ?";
                                $values[] = $cardData[$dataField];
                            }
                            break;
                        }
                    }
                }
            }
            
            // Generate slug if title changed
            if (isset($cardData['title']) && in_array('slug', $fields)) {
                $slug = self::slugify($cardData['title']);
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
            
            // Add updated timestamp
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

    /**
     * Delete a card
     */
    public static function deleteCard(int $id): void 
    {
        $db = self::db();
        $stmt = $db->prepare("DELETE FROM " . self::CARDS_TABLE . " WHERE id = ?");
        $stmt->execute([$id]);
    }

    /**
     * Create URL-friendly slug from title
     */
    private static function slugify(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }
}
