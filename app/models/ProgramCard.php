<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

final class ProgramCard extends Model
{
    // Table: program_cards
    // columns (suggested):
    // id, level ENUM('undergraduate','graduate'), slug, position INT,
    // badge, title, title_muted, summary, pillbox_title, pills TEXT,
    // learn_more_url, learn_more_external TINYINT(1),
    // curriculum_url, curriculum_external TINYINT(1),
    // apply_url, thumb_url,
    // status ENUM('draft','published','archived'),
    // created_at, updated_at

    private static array $allowedStatus = ['draft','published','archived'];
    private static array $allowedLevel  = ['undergraduate','graduate'];

    private static function clean(array $d): array {
        $x = [];
        $x['level']   = in_array(($d['level'] ?? ''), self::$allowedLevel, true) ? $d['level'] : 'undergraduate';
        $x['status']  = in_array(($d['status'] ?? 'draft'), self::$allowedStatus, true) ? $d['status'] : 'draft';
        $x['slug']    = trim((string)($d['slug'] ?? ''));
        $x['position']= (int)($d['position'] ?? 999);

        $x['badge']   = trim((string)($d['badge'] ?? ''));
        $x['title']   = trim((string)($d['title'] ?? ''));
        $x['title_muted'] = trim((string)($d['title_muted'] ?? ''));
        $x['summary'] = trim((string)($d['summary'] ?? ''));
        $x['pillbox_title'] = trim((string)($d['pillbox_title'] ?? ''));
        // store pills as newline-joined text
        if (!empty($d['pills']) && is_array($d['pills'])) {
            $x['pills'] = implode("\n", array_values(array_filter(array_map('trim',$d['pills']))));
        } else {
            $x['pills'] = trim((string)($d['pills'] ?? ''));
        }

        $x['learn_more_url']      = trim((string)($d['learn_more_url'] ?? ''));
        $x['learn_more_external'] = !empty($d['learn_more_external']) ? 1 : 0;
        $x['curriculum_url']      = trim((string)($d['curriculum_url'] ?? ''));
        $x['curriculum_external'] = !empty($d['curriculum_external']) ? 1 : 0;
        $x['apply_url']           = trim((string)($d['apply_url'] ?? ''));
        $x['thumb_url']           = trim((string)($d['thumb_url'] ?? ''));
        return $x;
    }

    public static function create(array $d): int {
        $d = self::clean($d);
        $cols = array_keys($d);
        $ph   = array_map(fn($f)=>":$f",$cols);
        $sql  = "INSERT INTO program_cards (".implode(',',$cols).", created_at, updated_at)
                 VALUES (".implode(',',$ph).", NOW(), NOW())";
        $st = parent::db()->prepare($sql);
        foreach ($d as $k=>$v) $st->bindValue(":$k",$v);
        $st->execute();
        return (int)parent::db()->lastInsertId();
    }

    public static function update(int $id, array $d): bool {
        $d = self::clean($d);
        $set = implode(', ', array_map(fn($f)=>"$f=:$f", array_keys($d)));
        $sql = "UPDATE program_cards SET $set, updated_at=NOW() WHERE id=:id";
        $st  = parent::db()->prepare($sql);
        foreach ($d as $k=>$v) $st->bindValue(":$k",$v);
        $st->bindValue(':id',$id,\PDO::PARAM_INT);
        return $st->execute();
    }

    public static function updateStatus(int $id, string $status): bool {
        $status = strtolower($status);
        if (!in_array($status, self::$allowedStatus, true)) $status = 'draft';
        $st = parent::db()->prepare("UPDATE program_cards SET status=:s, updated_at=NOW() WHERE id=:id");
        return $st->execute([':s'=>$status, ':id'=>$id]);
    }

    public static function delete(int $id): bool {
        $st = parent::db()->prepare("DELETE FROM program_cards WHERE id=:id");
        return $st->execute([':id'=>$id]);
    }

    public static function list(string $level, string $status='all'): array {
        $level = in_array($level, self::$allowedLevel, true) ? $level : 'undergraduate';
        $where = ['level=:lv'];
        $bind  = [':lv'=>$level];
        if ($status !== 'all') { $where[]='status=:s'; $bind[':s']=strtolower($status); }
        $sql = "SELECT * FROM program_cards
                WHERE ".implode(' AND ',$where)."
                ORDER BY position ASC, id ASC";
        $st = parent::db()->prepare($sql);
        foreach ($bind as $k=>$v) $st->bindValue($k,$v);
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function listPublished(string $level): array {
        return self::list($level, 'published');
    }

    public static function statusCounts(string $level): array {
        $st = parent::db()->prepare("SELECT LOWER(status) s, COUNT(*) c FROM program_cards WHERE level=:lv GROUP BY LOWER(status)");
        $st->execute([':lv'=>$level]);
        $rows = $st->fetchAll(\PDO::FETCH_KEY_PAIR);
        $all  = array_sum($rows) ?: 0;
        return [
            'all'=>$all,
            'draft'=>(int)($rows['draft']??0),
            'published'=>(int)($rows['published']??0),
            'archived'=>(int)($rows['archived']??0),
        ];
    }
}
