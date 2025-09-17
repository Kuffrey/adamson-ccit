<?php
declare(strict_types=1);

// Ensure base Model is loaded
$base = __DIR__ . '/../core/Model.php';
if (is_file($base)) require_once $base;

final class User extends Model {
    public function find(int $id): ?array {
        $st = parent::db()->prepare("SELECT id,username,role,department_id,is_active FROM users WHERE id=:id");
        $st->execute([':id'=>$id]);
        $r = $st->fetch();
        return $r ?: null;
    }

    public function findByUsername(string $email): ?array {
        $st = parent::db()->prepare("SELECT * FROM users WHERE username=:email OR email=:email LIMIT 1");
        $st->execute([':email'=>$email]);
        $r = $st->fetch();
        return $r ?: null;
    }

    /** Simple paginator (no external paginate() dependency) */
    public function listByRole(string $role, int $page=1, int $per=20): array {
        $page = max(1, $page);
        $per  = max(1, min(100, $per));
        $off  = ($page - 1) * $per;

        $db = parent::db();

        $st = $db->prepare(
            "SELECT SQL_CALC_FOUND_ROWS id,username,role,department_id,is_active
             FROM users
             WHERE role=:role
             ORDER BY username
             LIMIT :off,:per"
        );
        $st->bindValue(':role', $role);
        $st->bindValue(':off',  $off, \PDO::PARAM_INT);
        $st->bindValue(':per',  $per, \PDO::PARAM_INT);
        $st->execute();
        $items = $st->fetchAll();

        $total = (int)$db->query("SELECT FOUND_ROWS()")->fetchColumn();

        return ['items'=>$items, 'total'=>$total, 'page'=>$page, 'per'=>$per];
    }
}
