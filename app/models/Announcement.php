<?php
declare(strict_types=1);
// Ensure base Model is loaded
$base = __DIR__ . '/Model.php';
if (is_file($base)) { require_once $base; }

$base = __DIR__ . '/../core/Model.php';
if (is_file($base)) { require_once $base; }
else if (!class_exists('Model')) {
}

final class Announcement extends Model
{
    private static array $cols = [];
    private static array $allowedStatus = ['draft','published','archived'];
    private static array $allowedCats   = ['general','advisory','deadline','policy','alert'];

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
    private static function bodyCol(): string { return self::has('body') ? 'body' : (self::has('content') ? 'content' : 'body'); }

    public static function statusCounts(): array {
        try {
            $db   = parent::db();
            $rows = $db->query("SELECT LOWER(status) s, COUNT(*) c FROM announcements GROUP BY LOWER(status)")
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

    public static function list(?string $status=null): array {
        try {
            $db = parent::db();
            $where=''; $bind=[];
            if ($status && $status!=='all') { $where='WHERE LOWER(status)=:s'; $bind[':s']=strtolower($status); }

            $b = 'COALESCE(body, content, \'\')';
            $img = self::has('image_url') ? 'image_url' : 'NULL AS image_url';

            $sql = "SELECT id, title, COALESCE(excerpt, SUBSTRING($b,1,160)) excerpt,
                           $b AS body, category, status, $img,
                           COALESCE(published_at, created_at) created_at
                    FROM announcements
                    $where
                    ORDER BY COALESCE(published_at, created_at) DESC";
            $st=$db->prepare($sql); foreach($bind as $k=>$v)$st->bindValue($k,$v);
            $st->execute(); return $st->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable) { return []; }
    }

    public static function create(string $title, string $content, string $status='draft', ?string $category='general', ?string $imageUrl=null): int {
        $db   = parent::db();
        $bcol = self::bodyCol();
        $status   = in_array(strtolower($status), self::$allowedStatus, true) ? strtolower($status) : 'draft';
        $category = in_array(strtolower((string)$category), self::$allowedCats, true) ? strtolower((string)$category) : 'general';

        $cols = ['title'=>$title, $bcol=>$content, 'status'=>$status, 'category'=>$category];
        if (self::has('image_url') && $imageUrl) { $cols['image_url'] = $imageUrl; }

        $fields = array_keys($cols); $ph = array_map(fn($f)=>':'.$f, $fields);
        $sql = "INSERT INTO announcements (".implode(',', $fields).", created_at) VALUES (".implode(',', $ph).", NOW())";
        $st  = $db->prepare($sql); foreach ($cols as $f=>$v) $st->bindValue(':'.$f, $v);
        $st->execute(); return (int)$db->lastInsertId();
    }

    public static function updateStatus(int $id, string $status): bool {
        $status=strtolower($status); if (!in_array($status,self::$allowedStatus,true)) return false;
        $sql = ($status==='published')
            ? "UPDATE announcements SET status=:s, published_at=COALESCE(published_at,NOW()), updated_at=NOW() WHERE id=:id"
            : "UPDATE announcements SET status=:s, updated_at=NOW() WHERE id=:id";
        $st = parent::db()->prepare($sql);
        return $st->execute([':s'=>$status, ':id'=>$id]);
    }

    public static function delete(int|string $id): bool {
        $st = parent::db()->prepare("DELETE FROM announcements WHERE id=:id");
        return $st->execute([':id'=>(int)$id]);
    }

    // For public listing
    public static function searchPublished(array $f, array $pg=[]): array {
        $db   = parent::db();
        $cat  = $f['cat']  ?? null;
        $year = $f['year'] ?? null;
        $q    = $f['q']    ?? null;

        $page = max(1,(int)($pg['page'] ?? 1));
        $per  = max(1,min(48,(int)($pg['perPage'] ?? 9)));
        $off  = ($page-1)*$per;

        $w=["LOWER(status)='published'"]; $b=[];
        if ($cat)  { $w[]='LOWER(category)=:cat'; $b[':cat']=strtolower($cat); }
        if ($year) { $w[]='YEAR(COALESCE(published_at, created_at))=:y'; $b[':y']=(int)$year; }
        if ($q)    { $bc=self::bodyCol(); $w[]="(title LIKE :q OR excerpt LIKE :q OR $bc LIKE :q)"; $b[':q']='%'.$q.'%'; }
        $ws='WHERE '.implode(' AND ',$w);

        $cnt=$db->prepare("SELECT COUNT(*) FROM announcements $ws"); foreach($b as $k=>$v)$cnt->bindValue($k,$v); $cnt->execute();
        $total=(int)$cnt->fetchColumn();

        $img=self::has('image_url')?'image_url':'NULL AS image_url';
        $bc=self::bodyCol();
        $sql="SELECT id,title,excerpt,COALESCE($bc,'') body,category,$img,
                     COALESCE(published_at,created_at) date,author
              FROM announcements
              $ws
              ORDER BY COALESCE(published_at,created_at) DESC
              LIMIT :off,:per";
        $st=$db->prepare($sql); foreach($b as $k=>$v)$st->bindValue($k,$v);
        $st->bindValue(':off',$off,\PDO::PARAM_INT); $st->bindValue(':per',$per,\PDO::PARAM_INT); $st->execute();
        $items=$st->fetchAll(\PDO::FETCH_ASSOC);

        $ys=$db->query("SELECT DISTINCT YEAR(COALESCE(published_at,created_at)) y
                        FROM announcements WHERE LOWER(status)='published' ORDER BY y DESC")->fetchAll(\PDO::FETCH_COLUMN);

        return ['items'=>$items,'total'=>$total,'years'=>$ys?:[]];
    }
}
