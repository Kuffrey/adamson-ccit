<?php
declare(strict_types=1);

$base = __DIR__ . '/Model.php';
if (is_file($base)) { require_once $base; }

final class News extends Model
{
    private static array $cols = [];
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
    
    public static function getBodyColumnName(): string { return self::bodyCol(); }

    /** Prefer `date` if exists, else `published_at`, else `created_at` */
    private static function dateExpr(): string {
        if (self::has('date')) return 'date';
        return 'COALESCE(published_at, created_at)';
    }

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
            // never show announcements in News
            $where[] = "LOWER(category) <> 'announcement'";

            $whereSql = $where ? 'WHERE '.implode(' AND ',$where) : '';

            $bcol    = self::bodyCol();
            $bodyExpr = "COALESCE($bcol, '')";
            $imgExpr  = self::has('image_url') ? 'image_url' : 'NULL AS image_url';
            $authExpr = self::has('author') ? 'author' : 'NULL AS author';
            $dateExpr = self::dateExpr() . ' AS display_date'; // <-- new alias

            $sql = "
                SELECT id, title,
                       COALESCE(excerpt, SUBSTRING($bodyExpr, 1, 160)) AS excerpt,
                       $bodyExpr AS body,
                       category, status, $imgExpr, $authExpr,
                       $dateExpr,
                       published_at, created_at, updated_at
                FROM news
                $whereSql
                ORDER BY ".self::dateExpr()." DESC, id DESC";
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

        // base columns
        $cols = [
            'title'    => $title,
            $bcol      => $content,
            'status'   => $status,
            'category' => $category,
        ];

        if (self::has('image_url') && $imageUrl) {
            $cols['image_url'] = $imageUrl;
        }

        // 👇 KEY FIX: if initially created as "published", stamp published_at and date (if present)
        if ($status === 'published') {
            if (self::has('published_at')) $cols['published_at'] = date('Y-m-d H:i:s');
            if (self::has('date'))         $cols['date']         = date('Y-m-d H:i:s');
        }

        // build INSERT
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

        // When publishing, ensure both published_at and date are set the first time
        $sql = ($status === 'published')
            ? "UPDATE news
            SET status=:s,
                published_at = COALESCE(published_at, NOW()),
                /* the line below ensures front-end uses a real date immediately */
                date = COALESCE(date, NOW()),
                updated_at=NOW()
            WHERE id=:id"
            : "UPDATE news
            SET status=:s, updated_at=NOW()
            WHERE id=:id";

        $st = parent::db()->prepare($sql);
        return $st->execute([':s'=>$status, ':id'=>$id]);
    }

    public static function update(int $id, array $data): bool {
        try {
            $db = parent::db();
            $cols = self::cols();
            $setClauses = [];
            $params = [':id' => $id];
            
            // Build SET clauses for valid columns
            foreach ($data as $field => $value) {
                if (in_array($field, $cols) && $field !== 'id') {
                    $setClauses[] = "$field = :$field";
                    $params[":$field"] = $value;
                }
            }
            
            if (empty($setClauses)) {
                return false; // No valid fields to update
            }
            
            // Add updated_at if it exists
            if (self::has('updated_at')) {
                $setClauses[] = "updated_at = NOW()";
            }
            
            $sql = "UPDATE news SET " . implode(', ', $setClauses) . " WHERE id = :id";
            $stmt = $db->prepare($sql);
            return $stmt->execute($params);
            
        } catch (\Throwable $e) {
            error_log("News::update error: " . $e->getMessage());
            return false;
        }
    }

    public static function delete(int|string $id): bool {
        $st = parent::db()->prepare("DELETE FROM news WHERE id=:id");
        return $st->execute([':id'=>(int)$id]);
    }

    public static function findById(int $id): ?array {
        try {
            $db = parent::db();
            $bodyCol = self::bodyCol();
            $imgExpr = self::has('image_url') ? 'image_url' : 'NULL AS image_url';
            
            $sql = "SELECT id, title, excerpt, COALESCE($bodyCol,'') AS content, 
                           category, status, author, $imgExpr,
                           " . self::dateExpr() . " AS display_date,
                           created_at, updated_at
                    FROM news 
                    WHERE id = :id";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $result ?: null;
        } catch (\Throwable $e) {
            error_log("News::findById error: " . $e->getMessage());
            return null;
        }
    }

    /** For small admin lists */
    public static function latest(int $limit = 6): array {
        try {
            $db = parent::db();
            $sql = "
                SELECT id, title, status,
                       ".self::dateExpr()." AS display_date,
                       author, category
                FROM news
                WHERE LOWER(category) <> 'announcement'
                ORDER BY ".self::dateExpr()." DESC, id DESC
                LIMIT :lim";
            $st = $db->prepare($sql);
            $st->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $st->execute();
            return $st->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable) {
            return [];
        }
    }

    /** Public listing for /news page (published only) */
    public static function searchPublished(array $f, array $pg = []): array {
        $db   = parent::db();
        $cat  = $f['cat']  ?? null;
        $year = $f['year'] ?? null;
        $q    = $f['q']    ?? null;

        $page = max(1, (int)($pg['page'] ?? 1));
        $per  = max(1, min(48, (int)($pg['perPage'] ?? 9)));
        $off  = ($page - 1) * $per;

        $where = ["LOWER(status)='published'","LOWER(category) <> 'announcement'"];
        $bind  = [];
        if ($cat && $cat!=='announcement') { $where[] = 'LOWER(category) = :cat'; $bind[':cat'] = strtolower((string)$cat); }
        if ($year) { $where[] = "YEAR(".self::dateExpr().") = :yr"; $bind[':yr'] = (int)$year; }
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
                       ".self::dateExpr()." AS date, author
                FROM news
                $whereSql
                ORDER BY ".self::dateExpr()." DESC, id DESC
                LIMIT :off,:per";
        $stmt = $db->prepare($sql);
        foreach ($bind as $k=>$v) $stmt->bindValue($k,$v);
        $stmt->bindValue(':off', $off, \PDO::PARAM_INT);
        $stmt->bindValue(':per', $per, \PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $ys = $db->query("
            SELECT DISTINCT YEAR(".self::dateExpr().") y
            FROM news
            WHERE LOWER(status)='published' AND LOWER(category) <> 'announcement'
            ORDER BY y DESC
        ")->fetchAll(\PDO::FETCH_COLUMN);

        return ['items'=>$items,'total'=>$total,'years'=>$ys ?: []];
    }
}
