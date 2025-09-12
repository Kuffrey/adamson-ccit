<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

final class Research extends Model {
    public function create(array $d): int {
        $sql="INSERT INTO research (title, abstract, owner_user_id, department_id, status, requires_dean_approval, submitted_at, published_at)
              VALUES (:title,:abstract,:owner,:dept,:status,:requires, NOW(), NULL)";
        $st=$this->db->prepare($sql);
        $st->execute([
            ':title'=>trim($d['title']),
            ':abstract'=>$d['abstract'] ?? null,
            ':owner'=>(int)$d['owner_user_id'],
            ':dept'=>(int)($d['department_id'] ?? 0),
            ':status'=>$d['status'] ?? 'draft',   // draft|review|approved|published|rejected
            ':requires'=>!empty($d['requires_dean_approval'])?1:0,
        ]);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, array $d): bool {
        $sql="UPDATE research SET title=:title, abstract=:abstract, department_id=:dept, status=:status, requires_dean_approval=:requires WHERE id=:id";
        $st=$this->db->prepare($sql);
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
        $st=$this->db->prepare("UPDATE research SET status='review', submitted_at=NOW() WHERE id=:id");
        return $st->execute([':id'=>$id]);
    }
    public function approve(int $id, int $approverId, bool $publish=false): bool {
        if ($publish) {
            $sql="UPDATE research SET status='published', approved_by=:uid, published_at=NOW() WHERE id=:id";
        } else {
            $sql="UPDATE research SET status='approved', approved_by=:uid WHERE id=:id";
        }
        $st=$this->db->prepare($sql);
        return $st->execute([':uid'=>$approverId, ':id'=>$id]);
    }
    public function listByOwner(int $ownerId, int $page=1, int $per=10): array {
        $sql="SELECT * FROM research WHERE owner_user_id=:uid ORDER BY created_at DESC";
        return $this->paginate($sql, [':uid'=>$ownerId], $page, $per);
    }
    public function listForDeanQueue(int $page=1,int $per=20): array {
        $sql="SELECT * FROM research WHERE requires_dean_approval=1 AND status='review' ORDER BY submitted_at DESC";
        return $this->paginate($sql, [], $page, $per);
    }
    public function delete(int $id): bool {
        $st = $this->db->prepare("DELETE FROM research WHERE id = :id");
        return $st->execute([':id' => $id]);
    }
}
