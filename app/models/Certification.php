<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

final class Certification extends Model {
    public function create(array $d): int {
        $db = self::db();
        $sql="INSERT INTO certifications (title, issuer, owner_user_id, department_id, issued_at, expires_at, status)
              VALUES (:title,:issuer,:owner,:dept,:issued,:expires,:status)";
        $st=$db->prepare($sql);
        $success = $st->execute([
            ':title'=>trim($d['title']),
            ':issuer'=>$d['issuer'] ?? null,
            ':owner'=>(int)$d['owner_user_id'],
            ':dept'=>(int)($d['department_id'] ?? 0),
            ':issued'=>$d['issued_at'],
            ':expires'=>$d['expires_at'] ?? null,
            ':status'=>$d['status'] ?? 'pending', // pending|approved|rejected
        ]);
        return $success ? (int)$db->lastInsertId() : 0;
    }
    
    public function update(int $id, array $d): bool {
        $db = self::db();
        $sql="UPDATE certifications SET title=:title, issuer=:issuer, department_id=:dept, issued_at=:issued, expires_at=:expires, status=:status WHERE id=:id";
        $st=$db->prepare($sql);
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
    
    public function listByOwner(int $ownerId): array {
        $db = self::db();
        $sql="SELECT * FROM certifications WHERE owner_user_id=:uid ORDER BY issued_at DESC";
        $st = $db->prepare($sql);
        $st->execute([':uid' => $ownerId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function expiringInDays(int $days=60): array {
        $db = self::db();
        $st=$db->prepare("SELECT * FROM certifications WHERE expires_at BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :d DAY)");
        $st->bindValue(':d',$days,PDO::PARAM_INT); 
        $st->execute(); 
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function delete(int $id): bool {
        $db = self::db();
        $st = $db->prepare("DELETE FROM certifications WHERE id = :id");
        return $st->execute([':id' => $id]);
    }
    
    public static function getAllPending(): array {
        $db = self::db();
        $sql = "SELECT c.*, u.username as faculty_name FROM certifications c 
                LEFT JOIN users u ON c.owner_user_id = u.id 
                WHERE c.status = 'pending' ORDER BY c.issued_at DESC";
        $st = $db->query($sql);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function updateStatus(int $id, string $status): bool {
        $db = self::db();
        $sql = "UPDATE certifications SET status = :status WHERE id = :id";
        $st = $db->prepare($sql);
        return $st->execute([':status' => $status, ':id' => $id]);
    }
}
