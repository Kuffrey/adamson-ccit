<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

final class Research extends Model {
    public function create(array $d): int {
        $db = self::db();
        $sql="INSERT INTO research (title, abstract, owner_user_id, department_id, status, requires_dean_approval, submitted_at, published_at)
              VALUES (:title,:abstract,:owner,:dept,:status,:requires, NOW(), NULL)";
        $st=$db->prepare($sql);
        $st->execute([
            ':title'=>trim($d['title']),
            ':abstract'=>$d['abstract'] ?? null,
            ':owner'=>(int)$d['owner_user_id'],
            ':dept'=>(int)($d['department_id'] ?? 0),
            ':status'=>$d['status'] ?? 'draft',   // draft|review|approved|published|rejected
            ':requires'=>!empty($d['requires_dean_approval'])?1:0,
        ]);
        return (int)$db->lastInsertId();
    }

        /**
         * List research by status (for dean dashboard)
         */
        public function listByStatus(string $status): array {
            $db = self::db();
            $sql = "SELECT r.*, u.username as faculty_name FROM research r LEFT JOIN users u ON r.owner_user_id = u.id WHERE r.status = :status ORDER BY r.submitted_at DESC";
            $st = $db->prepare($sql);
            $st->execute([':status' => $status]);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        }
    
    public function update(int $id, array $d): bool {
        $db = self::db();
        $sql="UPDATE research SET title=:title, abstract=:abstract, department_id=:dept, status=:status, requires_dean_approval=:requires WHERE id=:id";
        $st=$db->prepare($sql);
        return $st->execute([
            ':title'=>trim($d['title']),
            ':abstract'=>$d['abstract'] ?? null,
            ':dept'=>(int)($d['department_id'] ?? 0),
            ':status'=>$d['status'] ?? 'draft',
            ':requires'=>!empty($d['requires_dean_approval'])?1:0,
            ':id'=>$id
        ]);
    }
    
    public function submitForReview(int $id): bool {
        $db = self::db();
        $st=$db->prepare("UPDATE research SET status='review', submitted_at=NOW() WHERE id=:id");
        return $st->execute([':id'=>$id]);
    }
    
    public function approve(int $id, int $approverId, bool $publish=false): bool {
        $db = self::db();
        if ($publish) {
            $sql="UPDATE research SET status='published', approved_by=:uid, published_at=NOW() WHERE id=:id";
        } else {
            $sql="UPDATE research SET status='approved', approved_by=:uid WHERE id=:id";
        }
        $st=$db->prepare($sql);
        return $st->execute([':uid'=>$approverId, ':id'=>$id]);
    }
    
    public function listByOwner(int $ownerId): array {
        $db = self::db();
        $sql="SELECT * FROM research WHERE owner_user_id=:uid ORDER BY created_at DESC";
        $st = $db->prepare($sql);
        $st->execute([':uid' => $ownerId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getAllPending(): array {
        $db = self::db();
        $sql="SELECT r.*, u.username as faculty_name FROM research r
              LEFT JOIN users u ON r.owner_user_id = u.id
              WHERE r.requires_dean_approval=1 AND r.status='review' ORDER BY r.submitted_at DESC";
        $st = $db->query($sql);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function updateStatus(int $id, string $status, int $approverId = null): bool {
        $db = self::db();
        if ($approverId) {
            $sql = "UPDATE research SET status = :status, approved_by = :approver WHERE id = :id";
            $st = $db->prepare($sql);
            return $st->execute([':status' => $status, ':approver' => $approverId, ':id' => $id]);
        } else {
            $sql = "UPDATE research SET status = :status WHERE id = :id";
            $st = $db->prepare($sql);
            return $st->execute([':status' => $status, ':id' => $id]);
        }
    }
    
    public function delete(int $id): bool {
        $db = self::db();
        $st = $db->prepare("DELETE FROM research WHERE id = :id");
        return $st->execute([':id' => $id]);
    }
}
