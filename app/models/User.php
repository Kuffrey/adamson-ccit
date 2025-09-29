<?php
declare(strict_types=1);

// Ensure base Model is loaded
$base = __DIR__ . '/Model.php';
if (is_file($base)) require_once $base;

final class User extends Model {
    
    // Static methods for admin management
    public static function getAll(): array {
        $db = parent::db();
        $stmt = $db->query("
            SELECT id, username, first_name, last_name, email, 
                   student_number, program, year_level, status, 
                   role, department_id, created_at 
            FROM users 
            ORDER BY role, username
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public static function getById(int $id): ?array {
        $db = parent::db();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
    
    public static function create(array $data): bool {
        $db = parent::db();
        $stmt = $db->prepare("
            INSERT INTO users (
                username, password, role, first_name, last_name, email,
                student_number, program, year_level, status, department_id
            ) VALUES (
                :username, :password, :role, :first_name, :last_name, :email,
                :student_number, :program, :year_level, :status, :department_id
            )
        ");
        
        return $stmt->execute([
            ':username' => $data['username'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role' => $data['role'],
            ':first_name' => $data['first_name'] ?? null,
            ':last_name' => $data['last_name'] ?? null,
            ':email' => $data['email'] ?? null,
            ':student_number' => $data['student_number'] ?? null,
            ':program' => $data['program'] ?? null,
            ':year_level' => $data['year_level'] ?? null,
            ':status' => $data['status'] ?? 'active',
            ':department_id' => $data['department_id'] ?? null
        ]);
    }
    
    public static function update(int $id, array $data): bool {
        $db = parent::db();
        
        $fields = [];
        $params = [':id' => $id];
        
        if (isset($data['username'])) {
            $fields[] = 'username = :username';
            $params[':username'] = $data['username'];
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            $fields[] = 'password = :password';
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (isset($data['role'])) {
            $fields[] = 'role = :role';
            $params[':role'] = $data['role'];
        }
        
        if (isset($data['first_name'])) {
            $fields[] = 'first_name = :first_name';
            $params[':first_name'] = $data['first_name'];
        }
        
        if (isset($data['last_name'])) {
            $fields[] = 'last_name = :last_name';
            $params[':last_name'] = $data['last_name'];
        }
        
        if (isset($data['email'])) {
            $fields[] = 'email = :email';
            $params[':email'] = $data['email'];
        }
        
        if (isset($data['student_number'])) {
            $fields[] = 'student_number = :student_number';
            $params[':student_number'] = $data['student_number'];
        }
        
        if (isset($data['program'])) {
            $fields[] = 'program = :program';
            $params[':program'] = $data['program'];
        }
        
        if (isset($data['year_level'])) {
            $fields[] = 'year_level = :year_level';
            $params[':year_level'] = $data['year_level'];
        }
        
        if (isset($data['status'])) {
            $fields[] = 'status = :status';
            $params[':status'] = $data['status'];
        }
        
        if (isset($data['department_id'])) {
            $fields[] = 'department_id = :department_id';
            $params[':department_id'] = $data['department_id'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $db->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    public static function delete(int $id): bool {
        $db = parent::db();
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    public static function authenticate(string $username, string $password): ?array {
        $db = parent::db();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return null;
    }
    
    public static function usernameExists(string $username, ?int $excludeId = null): bool {
        $db = parent::db();
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $params = [':username' => $username];
        
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    // Original instance methods for backward compatibility
    public function find(int $id): ?array {
        $st = parent::db()->prepare("SELECT id,username,role,department_id FROM users WHERE id=:id");
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

    public function listByRole(string $role, int $page=1, int $per=20): array {
        $page = max(1, $page);
        $per  = max(1, min(100, $per));
        $off  = ($page - 1) * $per;

        $db = parent::db();

        $st = $db->prepare(
            "SELECT SQL_CALC_FOUND_ROWS id,username,role,department_id
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