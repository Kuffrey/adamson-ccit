<?php
declare(strict_types=1);

// Keep it consistent with your other models
require_once __DIR__ . '/Model.php';

// If your base Model actually lives in /core/Model.php this will be a no-op if it's already loaded.
$maybeCore = __DIR__ . '/../core/Model.php';
if (is_file($maybeCore)) {
    require_once $maybeCore;
}

final class Event extends Model
{
    private static array $cols = [];
    private static array $allowedStatus = ['draft','published','archived'];
    private static array $allowedCats   = ['career','forum','workshop','competition','community'];

    /** Cache table columns so we can feature-detect optional fields safely. */
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

    /** Counts for dashboard tabs. */
    public static function statusCounts(): array {
        try {
            $db   = parent::db();
            $rows = $db->query("SELECT LOWER(status) s, COUNT(*) c FROM events GROUP BY LOWER(status)")
                       ->fetchAll(\PDO::FETCH_KEY_PAIR);
            $all  = (int)array_sum($rows);
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

    /**
     * List events by status (null/'all' = all).
     * Sort: upcoming first (earliest start ASC), then past (most recent first).
     */
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

            $sql = "
                SELECT id, title, description, location, category, status,
                       start_at, end_at, $imgExpr, created_at
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

    /** Single row fetch (handy for future edit screens). */
    public static function get(int $id): ?array {
        try {
            $db = parent::db();
            $imgExpr = self::has('image_url') ? 'image_url' : 'NULL AS image_url';
            $st = $db->prepare("
                SELECT id, title, description, location, category, status,
                       start_at, end_at, $imgExpr, created_at, updated_at
                FROM events
                WHERE id = :id
                LIMIT 1
            ");
            $st->execute([':id'=>$id]);
            $row = $st->fetch(\PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\Throwable) { return null; }
    }

    /** Create a new event (used by Admin). */
    public static function create(array $data): int {
        $db = parent::db();

        $title    = trim((string)($data['title']       ?? ''));
        $desc     = trim((string)($data['description'] ?? ''));
        $location = trim((string)($data['location']    ?? ''));
        $category = strtolower((string)($data['category'] ?? 'career'));
        $status   = strtolower((string)($data['status']   ?? 'draft'));
        $startAt  = (string)($data['start_at'] ?? null);
        $endAt    = (string)($data['end_at']   ?? null);
        $imageUrl = $data['image_url'] ?? null;

        if (!in_array($category, self::$allowedCats, true))  $category = 'career';
        if (!in_array($status,   self::$allowedStatus, true)) $status   = 'draft';

        // Base required columns
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

        // Optional image_url
        if (self::has('image_url') && $imageUrl) {
            array_splice($fields, 5, 0, 'image_url'); // insert before start_at
            array_splice($ph,     5, 0, ':image_url');
            $vals[':image_url'] = $imageUrl;
        }

        // If your table has a generic link column (e.g., url / link_url / registration_url), support it:
        foreach (['url','link_url','registration_url'] as $col) {
            if (self::has($col) && isset($data[$col]) && $data[$col] !== '') {
                $fields[] = $col;
                $ph[]     = ':'.$col;
                $vals[':'.$col] = (string)$data[$col];
                break; // only one of them
            }
        }

        $sql = "INSERT INTO events (".implode(',', $fields).") VALUES (".implode(',', $ph).")";
        $st  = $db->prepare($sql);
        $st->execute($vals);
        return (int)$db->lastInsertId();
    }

    /** Update an existing event (optional but useful). */
    public static function update(int $id, array $data): bool {
        $id = (int)$id;
        if ($id <= 0) return false;

        $sets = [];
        $vals = [':id'=>$id];

        $map = [
            'title'       => 'title',
            'description' => 'description',
            'location'    => 'location',
            'category'    => 'category',
            'status'      => 'status',
            'start_at'    => 'start_at',
            'end_at'      => 'end_at',
        ];
        foreach ($map as $k=>$col) {
            if (array_key_exists($k, $data)) {
                $sets[] = "$col = :$k";
                $vals[":$k"] = $k === 'category' ? strtolower((string)$data[$k]) :
                               ($k === 'status'   ? strtolower((string)$data[$k]) : $data[$k]);
            }
        }

        if (self::has('image_url') && array_key_exists('image_url', $data)) {
            $sets[] = "image_url = :image_url";
            $vals[':image_url'] = $data['image_url'];
        }

        foreach (['url','link_url','registration_url'] as $col) {
            if (self::has($col) && array_key_exists($col, $data)) {
                $sets[] = "$col = :$col";
                $vals[":$col"] = $data[$col];
                break;
            }
        }

        if (!$sets) return true; // nothing to update

        $sets[] = "updated_at = NOW()";
        $sql = "UPDATE events SET ".implode(', ', $sets)." WHERE id=:id";
        $st  = parent::db()->prepare($sql);
        return $st->execute($vals);
    }

    /**
     * Update status; if the table has `published_at`, set it on first publish.
     */
    public static function updateStatus(int $id, string $status): bool {
        $status = strtolower($status);
        if (!in_array($status, self::$allowedStatus, true)) return false;

        $hasPubAt = self::has('published_at');

        if ($status === 'published' && $hasPubAt) {
            $sql = "UPDATE events
                    SET status=:s,
                        published_at = COALESCE(published_at, NOW()),
                        updated_at = NOW()
                    WHERE id=:id";
        } else {
            $sql = "UPDATE events
                    SET status=:s,
                        updated_at = NOW()
                    WHERE id=:id";
        }
        $st = parent::db()->prepare($sql);
        return $st->execute([':s'=>$status, ':id'=>$id]);
    }

    public static function delete(int|string $id): bool {
        $st = parent::db()->prepare("DELETE FROM events WHERE id=:id");
        return $st->execute([':id'=>(int)$id]);
    }

    /** Dashboard widget (upcoming published only). */
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

    /**
     * Public listing (published only), with filters and upcoming-first ordering.
     * Returns: ['items'=>[], 'total'=>int, 'years'=>[YYYY,...]]
     */
    public static function searchPublished(array $f, array $pg = []): array {
        $db   = parent::db();
        $cat  = $f['cat']  ?? null;
        $year = $f['year'] ?? null;  // YEAR(start_at)
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

        // total
        $c = $db->prepare("SELECT COUNT(*) FROM events $whereSql");
        foreach ($bind as $k=>$v) $c->bindValue($k,$v);
        $c->execute();
        $total = (int)$c->fetchColumn();

        $imgExpr = self::has('image_url') ? 'image_url' : 'NULL AS image_url';

        // items
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

        // year facets
        $ys = $db->query("SELECT DISTINCT YEAR(start_at) y
                          FROM events
                          WHERE LOWER(status)='published'
                          ORDER BY y DESC")->fetchAll(\PDO::FETCH_COLUMN);

        return ['items'=>$items,'total'=>$total,'years'=>$ys ?: []];
    }
}
