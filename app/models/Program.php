<?php
require_once __DIR__ . '/Model.php';
class Program extends Model {

    public static function all() {
        $stmt = self::db()->query('SELECT * FROM programs ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public static function create($name, $description) {
        $stmt = self::db()->prepare('INSERT INTO programs (name, description) VALUES (?, ?)');
        return $stmt->execute([$name, $description]);
    }

    // New: create with all columns (optional)
    public static function createFull(array $data) {
        // Auto-generate slug if not provided
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = self::generateSlug($data['name']);
        }
        
        $sql = 'INSERT INTO programs (slug, name, description, image, image_alt, url, is_active)
                VALUES (:slug, :name, :description, :image, :image_alt, :url, :is_active)';
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([
            ':slug'       => trim($data['slug'] ?? ''),
            ':name'       => trim($data['name'] ?? ''),
            ':description'=> trim($data['description'] ?? ''),
            ':image'      => trim($data['image'] ?? ''),
            ':image_alt'  => trim($data['image_alt'] ?? ''),
            ':url'        => trim($data['url'] ?? ''),
            ':is_active'  => !empty($data['is_active']) ? 1 : 0,
        ]);
    }

    // Helper: generate URL-safe slug from name
    private static function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    // New: update any subset of columns (safe allowlist)
    public static function update($id, array $data) {
        $id = (int)$id;
        $allow = ['slug','name','description','image','image_alt','url','is_active'];
        $set = [];
        $params = [];
        
        // Auto-generate slug if name is being updated but slug is not provided
        if (array_key_exists('name', $data) && !array_key_exists('slug', $data)) {
            $data['slug'] = self::generateSlug($data['name']);
        }
        
        foreach ($allow as $col) {
            if (array_key_exists($col, $data)) {
                $set[] = "$col = :$col";
                if ($col === 'is_active') {
                    $params[":$col"] = !empty($data[$col]) ? 1 : 0;
                } else {
                    $params[":$col"] = trim((string)$data[$col]);
                }
            }
        }
        if (!$set) return true; // nothing to update
        $params[':id'] = $id;
        $sql = 'UPDATE programs SET '.implode(', ', $set).' WHERE id = :id';
        $stmt = self::db()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($id) {
        $stmt = self::db()->prepare('DELETE FROM programs WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Get the latest published programs, limited by $limit
     * @param int $limit
     * @return array
     */
    public static function latest($limit = 4) {
        $stmt = self::db()->prepare('SELECT * FROM programs ORDER BY id DESC LIMIT ?');
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
