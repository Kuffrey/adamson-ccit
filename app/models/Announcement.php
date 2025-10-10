<?php
declare(strict_types=1);

// Ensure base Model is loaded
$base1 = __DIR__ . '/Model.php';
if (is_file($base1)) require_once $base1;

$base2 = __DIR__ . '/../core/Model.php';
if (is_file($base2)) require_once $base2;

final class Announcement extends Model
{
    private static array $cols = [];
    private static array $allowedStatus = ['draft','published','archived'];
    private static array $allowedCats   = ['general','advisory','deadline','policy','alert'];

    /** Column discovery so we can feature-detect optional fields safely. */
    private static function cols(): array {
        if (!self::$cols) {
            try {
                $rows = parent::db()->query("SHOW COLUMNS FROM announcements")->fetchAll();
                self::$cols = array_map(fn($r)=>$r['Field'] ?? $r['field'] ?? '', $rows);
            } catch (\Throwable) { self::$cols = []; }
        }
        return self::$cols;
    }
    private static function has(string $c): bool { return in_array($c, self::cols(), true); }

    /** Prefer the first available body column. */
    private static function bodyCol(): string {
        foreach (['body','content','text','details'] as $c) if (self::has($c)) return $c;
        return 'body'; // will error only if neither exists; schema should have at least one
    }

    /** A unified "date" expression for selects (what your views expect). */
    private static function dateExpr(): string {
        // Prefer explicit `date`, then `published_at`, then `created_at`
        $parts = [];
        if (self::has('date'))         $parts[] = 'date';
        if (self::has('published_at')) $parts[] = 'published_at';
        $parts[] = 'created_at';
        return 'COALESCE('.implode(',', $parts).')';
    }

    /** Status counts for tabs. */
    public static function statusCounts(): array {
        try {
            $db   = parent::db();
            $rows = $db->query("SELECT LOWER(status) s, COUNT(*) c FROM announcements GROUP BY LOWER(status)")
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

    /** Admin list by status (null/'all' = all). */
    public static function list(?string $status=null): array {
        try {
            $db = parent::db();
            $where=''; $bind=[];
            if ($status && $status!=='all') { $where='WHERE LOWER(status)=:s'; $bind[':s']=strtolower($status); }

            $body = self::bodyCol();
            $img  = self::has('image_url') ? 'image_url' : 'NULL AS image_url';
            $date = self::dateExpr();
            $author = self::has('author') ? 'author' : 'NULL AS author';

            // excerpt: use column if present, else substring from body
            $excerptExpr = self::has('excerpt')
                ? 'excerpt'
                : "NULLIF(TRIM(SUBSTRING($body,1,160)),'') AS excerpt";

            $sql = "SELECT id, title, $excerptExpr,
                           $body AS body, category, status, $img, $author,
                           $date AS date, created_at
                    FROM announcements
                    $where
                    ORDER BY $date DESC";
            $st=$db->prepare($sql); foreach($bind as $k=>$v)$st->bindValue($k,$v);
            $st->execute(); return $st->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable) { return []; }
    }

    /** Single row (useful for edit screens). */
    public static function get(int $id): ?array {
        try {
            $db = parent::db();
            $body = self::bodyCol();
            $img  = self::has('image_url') ? 'image_url' : 'NULL AS image_url';
            $date = self::dateExpr();
            $author = self::has('author') ? 'author' : 'NULL AS author';

            $st=$db->prepare("
                SELECT id, title, ".(self::has('excerpt')?'excerpt':'NULL AS excerpt').",
                       $body AS body, category, status, $img, $author,
                       $date AS date, created_at, updated_at
                FROM announcements
                WHERE id=:id
                LIMIT 1
            ");
            $st->execute([':id'=>$id]);
            $row = $st->fetch(\PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\Throwable) { return null; }
    }

    /**
     * Create (AdminController calls: create($title,$content,$status,$category,$date,$imageUrl))
     * $date is optional; if your table has `date` or `published_at`, we’ll use it.
     */
    public static function create(
        string $title,
        string $content,
        string $status='draft',
        ?string $category='general',
        ?string $date=null,
        ?string $imageUrl=null,
        ?string $excerpt=null
    ): int {
        $db = parent::db();

        $bcol     = self::bodyCol();
        $status   = in_array(strtolower($status), self::$allowedStatus, true) ? strtolower($status) : 'draft';
        $category = in_array(strtolower((string)$category), self::$allowedCats, true) ? strtolower((string)$category) : 'general';

        $cols = [
            'title'    => $title,
            $bcol      => $content,
            'status'   => $status,
            'category' => $category,
        ];

        if (self::has('excerpt') && $excerpt) {
            $cols['excerpt'] = $excerpt;
        }

        if (self::has('image_url') && $imageUrl) {
            $cols['image_url'] = $imageUrl;
        }

        // If you have an explicit `date` column, use it; otherwise if publishing and `published_at` exists, use that.
        if ($date) {
            // Normalize date to DATETIME if it looks like a date only.
            $norm = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? ($date.' 00:00:00') : $date;
            if (self::has('date')) {
                $cols['date'] = $norm;
            } elseif ($status === 'published' && self::has('published_at')) {
                $cols['published_at'] = $norm;
            }
        }

        // Build dynamic insert
        $fields = array_keys($cols);
        $ph     = array_map(fn($f)=>':'.$f, $fields);

        // Always set created_at
        $fields[] = 'created_at';
        $values   = implode(',', $ph) . ', NOW()';

        $sql = "INSERT INTO announcements (".implode(',', $fields).") VALUES ($values)";
        $st  = $db->prepare($sql);
        foreach ($cols as $f=>$v) $st->bindValue(':'.$f, $v);
        $st->execute();

        return (int)$db->lastInsertId();
    }

    /** Update an announcement with the given data. */
    public static function update(int $id, array $data): bool {
        $id = (int)$id;
        if ($id <= 0) return false;

        $db = parent::db();
        $bcol = self::bodyCol();
        
        $sets = [];
        $vals = [':id' => $id];

        // Map input data to database columns
        $map = [
            'title'    => 'title',
            'content'  => $bcol,
            'excerpt'  => 'excerpt',
            'category' => 'category',
            'status'   => 'status',
        ];

        foreach ($map as $k => $col) {
            if (array_key_exists($k, $data)) {
                // Only set if column exists in database
                if ($k === 'excerpt' && !self::has('excerpt')) continue;
                
                $sets[] = "$col = :$k";
                $vals[":$k"] = $k === 'category' ? strtolower((string)$data[$k]) :
                               ($k === 'status'   ? strtolower((string)$data[$k]) : $data[$k]);
            }
        }

        // Handle image_url if column exists
        if (self::has('image_url') && array_key_exists('image_url', $data)) {
            $sets[] = "image_url = :image_url";
            $vals[':image_url'] = $data['image_url'];
        }

        // Handle date
        if (array_key_exists('date', $data) && $data['date']) {
            $norm = preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date']) ? ($data['date'].' 00:00:00') : $data['date'];
            if (self::has('date')) {
                $sets[] = "date = :date";
                $vals[':date'] = $norm;
            } elseif (isset($data['status']) && strtolower($data['status']) === 'published' && self::has('published_at')) {
                $sets[] = "published_at = :published_at";
                $vals[':published_at'] = $norm;
            }
        }

        if (!$sets) return true; // nothing to update

        $sets[] = "updated_at = NOW()";
        $sql = "UPDATE announcements SET ".implode(', ', $sets)." WHERE id = :id";
        $st = $db->prepare($sql);
        return $st->execute($vals);
    }

    /** Update status (sets published_at on first publish if column exists). */
    public static function updateStatus(int $id, string $status): bool {
        $status=strtolower($status);
        if (!in_array($status,self::$allowedStatus,true)) return false;

        $hasPubAt = self::has('published_at');
        if ($status==='published' && $hasPubAt) {
            $sql = "UPDATE announcements
                    SET status=:s, published_at=COALESCE(published_at,NOW()), updated_at=NOW()
                    WHERE id=:id";
        } else {
            $sql = "UPDATE announcements
                    SET status=:s, updated_at=NOW()
                    WHERE id=:id";
        }
        $st = parent::db()->prepare($sql);
        return $st->execute([':s'=>$status, ':id'=>$id]);
    }

    public static function delete(int|string $id): bool {
        $st = parent::db()->prepare("DELETE FROM announcements WHERE id=:id");
        return $st->execute([':id'=>(int)$id]);
    }

    /** Public listing with filters (published only). */
    public static function searchPublished(array $f, array $pg=[]): array {
        $db   = parent::db();
        $cat  = $f['cat']  ?? null;
        $year = $f['year'] ?? null;
        $q    = $f['q']    ?? null;

        $page = max(1,(int)($pg['page'] ?? 1));
        $per  = max(1,min(48,(int)($pg['perPage'] ?? 9)));
        $off  = ($page-1)*$per;

        $where = ["LOWER(status)='published'"];
        $bind  = [];

        if ($cat)  { $where[]='LOWER(category)=:cat'; $bind[':cat']=strtolower($cat); }

        $date = self::dateExpr();
        if ($year) { $where[]="YEAR($date)=:y"; $bind[':y']=(int)$year; }

        $body = self::bodyCol();
        if ($q)    { $where[]="(title LIKE :q OR ".(self::has('excerpt')?'excerpt':'').(self::has('excerpt')?' LIKE :q OR ':'')."$body LIKE :q)"; $bind[':q']='%'.$q.'%'; }

        $ws = 'WHERE '.implode(' AND ',$where);

        // Total
        $cnt = $db->prepare("SELECT COUNT(*) FROM announcements $ws");
        foreach($bind as $k=>$v) $cnt->bindValue($k,$v);
        $cnt->execute();
        $total = (int)$cnt->fetchColumn();

        // Items
        $img  = self::has('image_url') ? 'image_url' : 'NULL AS image_url';
        $author = self::has('author') ? 'author' : 'NULL AS author';

        $sql="SELECT id, title, ".(self::has('excerpt')?'excerpt':'NULL AS excerpt').",
                     $body AS body, category, $img, $author,
                     $date AS date
              FROM announcements
              $ws
              ORDER BY $date DESC
              LIMIT :off,:per";
        $st=$db->prepare($sql);
        foreach($bind as $k=>$v) $st->bindValue($k,$v);
        $st->bindValue(':off',$off,\PDO::PARAM_INT);
        $st->bindValue(':per',$per,\PDO::PARAM_INT);
        $st->execute();
        $items=$st->fetchAll(\PDO::FETCH_ASSOC);

        // Year facets
        $ys=$db->query("
            SELECT DISTINCT YEAR($date) y
            FROM announcements
            WHERE LOWER(status)='published'
            ORDER BY y DESC
        ")->fetchAll(\PDO::FETCH_COLUMN);

        return ['items'=>$items,'total'=>$total,'years'=>$ys?:[]];
    }
}
