<?php
declare(strict_types=1);

// Load base Model safely
$base = __DIR__ . '/../core/Model.php';
if (is_file($base)) { require_once $base; }
else if (!class_exists('Model')) {
    abstract class Model { protected static function db(){ throw new \RuntimeException('DB not configured'); } }
}

final class News extends Model
{
    private static array $cols = [];
    // ⬇️ Announcements removed here
    private static array $allowedStatus = ['draft','published','archived'];
    private static array $allowedCats   = ['news','research','achievement','student'];

    private static function cols(): array {
        if (!self::$cols) {
            try {
                $rows = parent::db()->query("SHOW COLUMNS FROM news")->fetchAll();
                self::$cols = array_map(fn($r)=>$r['Field'] ?? $r['field'] ?? '', $rows);
            } catch (\Throwable) { self::$cols = []; }
        }
        return self::$cols;
    }
    private static function has(string $col): bool { return in_array($col, self::cols(), true); }
    private static function bodyCol(): string { return self::has('body') ? 'body' : (self::has('content') ? 'content' : 'body'); }

    public static function statusCounts(): array {
        try {
            $db   = parent::db();
            $rows = $db->query("SELECT LOWER(status) s, COUNT(*) c FROM news GROUP BY LOWER(status)")
                       ->fetchAll(\PDO::FETCH_KEY_PAIR);
            $all  = array_sum($rows) ?: 0;
            return [
                'all'       => $all,
                'draft'     => (int)($rows['draft']     ?? 0),
                'published' => (int)($rows['published'] ?? 0),
                'archived'  => (int)($rows['archived']  ?? 0),
            ];
        } catch (\Throwable) {
            return ['all'=>0,'draft'=>0,'published'=>0,'archived'=>0];
        }
    }

    public static function list(?string $status = null): array {
        try {
            $db = parent::db();
            $where = [];
            $bind  = [];

            if ($status && $status !== 'all') {
                $where[] = 'LOWER(status) = :s';
                $bind[':s'] = strtolower($status);
            }
            // prevent announcements leaking into News if old rows still exist
            $where[] = "LOWER(category) <> 'announcement'";

            $whereSql = $where ? 'WHERE '.implode(' AND ',$where) : '';

            $bodyExpr = 'COALESCE(body, content, \'\')';
            $imgExpr  = self::has('image_url') ? 'image_url' : 'NULL AS image_url';

            $sql = "
                SELECT id, title,
                       COALESCE(excerpt, SUBSTRING($bodyExpr, 1, 160)) AS excerpt,
                       $bodyExpr AS body,
                       category, status, $imgExpr,
                       COALESCE(published_at, created_at) AS created_at
                FROM news
                $whereSql
                ORDER BY COALESCE(published_at, created_at) DESC";
            $st = $db->prepare($sql);
            foreach ($bind as $k=>$v) $st->bindValue($k,$v);
            $st->execute();
            return $st->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable) {
            return [];
        }
    }

    public static function all(): array { return self::list(null); }

    public static function create(string $title, string $content, string $status='draft', ?string $category='news', ?string $imageUrl=null): int {
        $db   = parent::db();
        $bcol = self::bodyCol();

        $status   = in_array(strtolower($status), self::$allowedStatus, true) ? strtolower($status) : 'draft';
        $category = in_array(strtolower((string)$category), self::$allowedCats, true) ? strtolower((string)$category) : 'news';

        $cols = ['title'=>$title, $bcol=>$content, 'status'=>$status, 'category'=>$category];
        if (self::has('image_url') && $imageUrl) { $cols['image_url'] = $imageUrl; }

        $fields = array_keys($cols);
        $ph     = array_map(fn($f)=>':'.$f, $fields);

        $sql = "INSERT INTO news (".implode(',', $fields).", created_at) VALUES (".implode(',', $ph).", NOW())";
        $st  = $db->prepare($sql);
        foreach ($cols as $f=>$v) { $st->bindValue(':'.$f, $v); }
        $st->execute();
        return (int)$db->lastInsertId();
    }

    public static function updateStatus(int $id, string $status): bool {
        $status = strtolower($status);
        if (!in_array($status, self::$allowedStatus, true)) return false;

        $sql = ($status === 'published')
            ? "UPDATE news SET status=:s, published_at = COALESCE(published_at, NOW()), updated_at=NOW() WHERE id=:id"
            : "UPDATE news SET status=:s, updated_at=NOW() WHERE id=:id";

        $st = parent::db()->prepare($sql);
        return $st->execute([':s'=>$status, ':id'=>$id]);
    }

    public static function delete(int|string $id): bool {
        $st = parent::db()->prepare("DELETE FROM news WHERE id=:id");
        return $st->execute([':id'=>(int)$id]);
    }

    public static function latest(int $limit = 6): array {
        try {
            $db = parent::db();
            $st = $db->prepare("
                SELECT id, title, status, COALESCE(published_at, created_at) AS date
                FROM news
                WHERE LOWER(status)='published' AND LOWER(category) <> 'announcement'
                ORDER BY date DESC
                LIMIT :lim");
            $st->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $st->execute();
            return $st->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable) {
            return [];
        }
    }

    public static function searchPublished(array $f, array $pg = []): array {
        $db   = parent::db();
        $cat  = $f['cat']  ?? null;
        $year = $f['year'] ?? null;
        $q    = $f['q']    ?? null;

        $page = max(1, (int)($pg['page'] ?? 1));
        $per  = max(1, min(48, (int)($pg['perPage'] ?? 9)));
        $off  = ($page - 1) * $per;

        // never include announcements here
        $where = ["LOWER(status)='published'","LOWER(category) <> 'announcement'"];
        $bind  = [];
        if ($cat && $cat!=='announcement') { $where[] = 'LOWER(category) = :cat'; $bind[':cat'] = strtolower((string)$cat); }
        if ($year) { $where[] = 'YEAR(COALESCE(published_at, created_at)) = :yr'; $bind[':yr'] = (int)$year; }

        if ($q) {
            $bcol = self::bodyCol();
            $where[] = "(title LIKE :q OR excerpt LIKE :q OR $bcol LIKE :q)";
            $bind[':q'] = '%'.$q.'%';
        }
        $whereSql = 'WHERE '.implode(' AND ', $where);

        $c = $db->prepare("SELECT COUNT(*) FROM news $whereSql");
        foreach ($bind as $k=>$v) $c->bindValue($k,$v);
        $c->execute();
        $total = (int)$c->fetchColumn();

        $imgExpr = self::has('image_url') ? 'image_url' : 'NULL AS image_url';
        $bcol    = self::bodyCol();

        $sql = "SELECT id, title, excerpt, COALESCE($bcol,'') AS body, category, $imgExpr,
                       COALESCE(published_at, created_at) AS date, author
                FROM news
                $whereSql
                ORDER BY COALESCE(published_at, created_at) DESC
                LIMIT :off,:per";
        $stmt = $db->prepare($sql);
        foreach ($bind as $k=>$v) $stmt->bindValue($k,$v);
        $stmt->bindValue(':off', $off, \PDO::PARAM_INT);
        $stmt->bindValue(':per', $per, \PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $ys = $db->query("
            SELECT DISTINCT YEAR(COALESCE(published_at, created_at)) y
            FROM news
            WHERE LOWER(status)='published' AND LOWER(category) <> 'announcement'
            ORDER BY y DESC
        ")->fetchAll(\PDO::FETCH_COLUMN);

        return ['items'=>$items,'total'=>$total,'years'=>$ys ?: []];
    }
}
