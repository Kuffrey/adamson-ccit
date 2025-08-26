<?php
declare(strict_types=1);

final class Certification extends Model {
    public function create(array $d): int {
        $sql="INSERT INTO certifications (title, issuer, owner_user_id, department_id, issued_at, expires_at, status)
              VALUES (:title,:issuer,:owner,:dept,:issued,:expires,:status)";
        $st=$this->db->prepare($sql);
        $st->execute([
            ':title'=>trim($d['title']),
            ':issuer'=>$d['issuer'] ?? null,
            ':owner'=>(int)$d['owner_user_id'],
            ':dept'=>(int)($d['department_id'] ?? 0),
            ':issued'=>$d['issued_at'],
            ':expires'=>$d['expires_at'] ?? null,
            ':status'=>$d['status'] ?? 'pending', // pending|approved|rejected
        ]);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, array $d): bool {
        $sql="UPDATE certifications SET title=:title, issuer=:issuer, department_id=:dept, issued_at=:issued, expires_at=:expires, status=:status WHERE id=:id";
        $st=$this->db->prepare($sql);
        return $st->execute([
            ':title'=>trim($d['title']),
            ':issuer'=>$d['issuer'] ?? null,
            ':dept'=>(int)($d['department_id'] ?? 0),
            ':issued'=>$d['issued_at'],
            ':expires'=>$d['expires_at'] ?? null,
            ':status'=>$d['status'] ?? 'pending',
            ':id'=>$id
        ]);
    }
    public function listByOwner(int $ownerId, int $page=1, int $per=12): array {
        $sql="SELECT * FROM certifications WHERE owner_user_id=:uid ORDER BY issued_at DESC";
        return $this->paginate($sql, [':uid'=>$ownerId], $page, $per);
    }
    public function expiringInDays(int $days=60): array {
        $st=$this->db->prepare("SELECT * FROM certifications WHERE expires_at BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :d DAY)");
        $st->bindValue(':d',$days,PDO::PARAM_INT); $st->execute(); return $st->fetchAll();
    }
    public function delete(int $id): bool {
        $st = $this->db->prepare("DELETE FROM certifications WHERE id = :id");
        return $st->execute([':id' => $id]);
    }
}
