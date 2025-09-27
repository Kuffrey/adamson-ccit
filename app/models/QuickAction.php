<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

final class QuickAction extends Model
{
    /**
     * Get all active quick actions ordered by display_order
     */
    public static function getAllActive(): array
    {
        $stmt = self::db()->prepare("
            SELECT id, label, icon, url, display_order, is_active
            FROM homepage_quick_actions
            WHERE is_active = 1
            ORDER BY display_order ASC, id ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get all quick actions (for admin management)
     */
    public static function getAll(): array
    {
        $stmt = self::db()->prepare("
            SELECT id, label, icon, url, display_order, is_active, created_at, updated_at
            FROM homepage_quick_actions
            ORDER BY display_order ASC, id ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get a single quick action by ID
     */
    public static function getById(int $id): ?array
    {
        $stmt = self::db()->prepare("
            SELECT id, label, icon, url, display_order, is_active
            FROM homepage_quick_actions
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Create a new quick action
     */
    public static function create(array $data): bool
    {
        $stmt = self::db()->prepare("
            INSERT INTO homepage_quick_actions (label, icon, url, display_order, is_active)
            VALUES (:label, :icon, :url, :display_order, :is_active)
        ");
        
        return $stmt->execute([
            ':label' => $data['label'] ?? '',
            ':icon' => $data['icon'] ?? '',
            ':url' => $data['url'] ?? '',
            ':display_order' => (int)($data['display_order'] ?? 0),
            ':is_active' => !empty($data['is_active']) ? 1 : 0,
        ]);
    }

    /**
     * Update a quick action
     */
    public static function update(int $id, array $data): bool
    {
        $stmt = self::db()->prepare("
            UPDATE homepage_quick_actions
            SET label = :label, icon = :icon, url = :url, 
                display_order = :display_order, is_active = :is_active
            WHERE id = :id
        ");
        
        return $stmt->execute([
            ':id' => $id,
            ':label' => $data['label'] ?? '',
            ':icon' => $data['icon'] ?? '',
            ':url' => $data['url'] ?? '',
            ':display_order' => (int)($data['display_order'] ?? 0),
            ':is_active' => !empty($data['is_active']) ? 1 : 0,
        ]);
    }

    /**
     * Delete a quick action
     */
    public static function delete(int $id): bool
    {
        $stmt = self::db()->prepare("DELETE FROM homepage_quick_actions WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Toggle active status
     */
    public static function toggleActive(int $id): bool
    {
        $stmt = self::db()->prepare("
            UPDATE homepage_quick_actions
            SET is_active = NOT is_active
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Reorder quick actions
     */
    public static function updateOrder(array $orderData): bool
    {
        $db = self::db();
        $db->beginTransaction();
        
        try {
            $stmt = $db->prepare("UPDATE homepage_quick_actions SET display_order = :order WHERE id = :id");
            
            foreach ($orderData as $order => $id) {
                $stmt->execute([
                    ':id' => (int)$id,
                    ':order' => (int)$order + 1
                ]);
            }
            
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }
}