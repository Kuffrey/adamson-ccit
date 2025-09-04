<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

// Load base Model safely
$base = __DIR__ . '/../core/Model.php';
if (is_file($base)) {
    require_once $base;
} else {
    if (!class_exists('Model')) {
    }
}

final class Event extends Model
{
    private static array $cols = [];
    private static array $allowedStatus = ['draft','published','archived'];
    private static array $allowedCats   = ['career','forum','workshop','competition','community'];

    private static function cols(): array {
        if (!self::$cols) {
            try {
                $rows = parent::db()->query("SHOW COLUMNS FROM events")->fetchAll();
                self::$cols = array_map(fn($r)=>$r['Field'] ?? $r['field'] ?? '', $rows);
            } catch (\Throwable) { self::$cols = []; }
        }
        return self::$cols;
    }
    private static function has(string $col): bool { return in_array($col, self::cols(), true); }

    /** Status counts for tabs */
    public static function statusCounts(): array {
        try {
            $db   = parent::db();
            $rows = $db->query("SELECT LOWER(status) s, COUNT(*) c FROM events GROUP BY LOWER(status)")
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

    /** List by status (null/'all' = all). Upcoming first (earliest future), then past (most recent). */
    public static function list(?string $status = null): array {
        try {
            $db = parent::db();
            $where = '';
            $bind  = [];
            if ($status && $status !== 'all') {
                $where = 'WHERE LOWER(status) = :s';
                $bind[':s'] = strtolower($status);
            }

            $imgExpr = self::has('image_url') ? 'image_url' : 'NULL AS image_url';

            // Sort rule: future first ASC, then past DESC
            $sql = "
                SELECT id, title, description, location, category, status,
                       start_at, end_at, $imgExpr,
                       COALESCE(published_at, created_at) AS created_at
                FROM events
                $where
                ORDER BY (start_at >= NOW()) DESC, start_at ASC, created_at DESC";
            $st = $db->prepare($sql);
            foreach ($bind as $k=>$v) $st->bindValue($k,$v);
            $st->execute();
            return $st->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable) {
            return [];
        }
    }

    public static function all(): array { return self::list(null); }

    /** Create */
    public static function create(array $data): int {
        $db = parent::db();

        $title    = trim((string)($data['title']    ?? ''));
        $desc     = trim((string)($data['description'] ?? ''));
        $location = trim((string)($data['location'] ?? ''));
        $category = strtolower((string)($data['category'] ?? 'career'));
        $status   = strtolower((string)($data['status']   ?? 'draft'));
        $startAt  = (string)($data['start_at'] ?? null);
        $endAt    = (string)($data['end_at']   ?? null);
        $imageUrl = $data['image_url'] ?? null;

        if (!in_array($category, self::$allowedCats, true))  $category = 'career';
        if (!in_array($status,   self::$allowedStatus, true)) $status   = 'draft';

        $fields = ['title','description','location','category','status','start_at','end_at','created_at'];
        $ph     = [':title',':description',':location',':category',':status',':start_at',':end_at','NOW()'];
        $vals   = [
            ':title'       => $title,
            ':description' => $desc,
            ':location'    => $location,
            ':category'    => $category,
            ':status'      => $status,
            ':start_at'    => $startAt,
            ':end_at'      => $endAt,
        ];

        if (self::has('image_url') && $imageUrl) {
            array_splice($fields, 5, 0, 'image_url'); // insert before start_at
            array_splice($ph,     5, 0, ':image_url');
            $vals[':image_url'] = $imageUrl;
        }

        $sql = "INSERT INTO events (".implode(',', $fields).") VALUES (".implode(',', $ph).")";
        $st  = $db->prepare($sql);
        $st->execute($vals);
        return (int)$db->lastInsertId();
    }

    /** Update status (sets published_at when publishing) */
    public static function updateStatus(int $id, string $status): bool {
        $status = strtolower($status);
        if (!in_array($status, self::$allowedStatus, true)) return false;

        if ($status === 'published') {
            $sql = "UPDATE events SET status=:s, published_at = COALESCE(published_at, NOW()), updated_at=NOW() WHERE id=:id";
        } else {
            $sql = "UPDATE events SET status=:s, updated_at=NOW() WHERE id=:id";
        }
        $st = parent::db()->prepare($sql);
        return $st->execute([':s'=>$status, ':id'=>$id]);
    }

    public static function delete(int|string $id): bool {
        $st = parent::db()->prepare("DELETE FROM events WHERE id=:id");
        return $st->execute([':id'=>(int)$id]);
    }

    /** Dashboard widget (upcoming published) */
    public static function latestUpcoming(int $limit = 6): array {
        try {
            $db = parent::db();
            $st = $db->prepare("
                SELECT id, title, start_at, end_at, location
                FROM events
                WHERE LOWER(status)='published' AND start_at >= NOW()
                ORDER BY start_at ASC
                LIMIT :lim");
            $st->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $st->execute();
            return $st->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable) { return []; }
    }

    /** Public listing (published only), with filters + upcoming-first ordering */
    public static function searchPublished(array $f, array $pg = []): array {
        $db   = parent::db();
        $cat  = $f['cat']  ?? null;
        $year = $f['year'] ?? null;  // based on YEAR(start_at)
        $q    = $f['q']    ?? null;

        $page = max(1, (int)($pg['page'] ?? 1));
        $per  = max(1, min(48, (int)($pg['perPage'] ?? 9)));
        $off  = ($page - 1) * $per;

        $where = ["LOWER(status) = 'published'"];
        $bind  = [];
        if ($cat)  { $where[] = 'LOWER(category) = :cat'; $bind[':cat'] = strtolower((string)$cat); }
        if ($year) { $where[] = 'YEAR(start_at) = :yr';   $bind[':yr']  = (int)$year; }
        if ($q)    { $where[] = '(title LIKE :q OR description LIKE :q OR location LIKE :q)'; $bind[':q'] = '%'.$q.'%'; }
        $whereSql = 'WHERE '.implode(' AND ', $where);

        $c = $db->prepare("SELECT COUNT(*) FROM events $whereSql");
        foreach ($bind as $k=>$v) $c->bindValue($k,$v);
        $c->execute();
        $total = (int)$c->fetchColumn();

        $imgExpr = self::has('image_url') ? 'image_url' : 'NULL AS image_url';

        $sql = "SELECT id, title, description, location, category, $imgExpr, start_at, end_at
                FROM events
                $whereSql
                ORDER BY (start_at >= NOW()) DESC, start_at ASC
                LIMIT :off,:per";
        $stmt = $db->prepare($sql);
        foreach ($bind as $k=>$v) $stmt->bindValue($k,$v);
        $stmt->bindValue(':off', $off, \PDO::PARAM_INT);
        $stmt->bindValue(':per', $per, \PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $ys = $db->query("SELECT DISTINCT YEAR(start_at) y
                          FROM events WHERE LOWER(status)='published' ORDER BY y DESC")->fetchAll(\PDO::FETCH_COLUMN);

        return ['items'=>$items,'total'=>$total,'years'=>$ys ?: []];
    }
}
