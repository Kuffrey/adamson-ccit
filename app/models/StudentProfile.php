<?php
// app/models/StudentProfile.php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

class StudentProfile extends Model {
    
    public static function getByUserId(int $userId): ?array {
        try {
            $db = parent::db();
            $stmt = $db->prepare("
                SELECT sp.*, u.username, u.role 
                FROM student_profiles sp 
                JOIN users u ON u.id = sp.user_id 
                WHERE sp.user_id = ?
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    public static function create(array $data): ?int {
        try {
            $db = parent::db();
            $stmt = $db->prepare("
                INSERT INTO student_profiles 
                (user_id, student_id, first_name, last_name, middle_name, email, phone, 
                 address, birth_date, program, year_level, section, gpa, bio, skills, 
                 interests, linkedin_url, github_url, portfolio_url, profile_image, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['user_id'],
                $data['student_id'] ?? null,
                $data['first_name'] ?? null,
                $data['last_name'] ?? null,
                $data['middle_name'] ?? null,
                $data['email'] ?? null,
                $data['phone'] ?? null,
                $data['address'] ?? null,
                $data['birth_date'] ?? null,
                $data['program'] ?? null,
                $data['year_level'] ?? null,
                $data['section'] ?? null,
                $data['gpa'] ?? null,
                $data['bio'] ?? null,
                $data['skills'] ?? null,
                $data['interests'] ?? null,
                $data['linkedin_url'] ?? null,
                $data['github_url'] ?? null,
                $data['portfolio_url'] ?? null,
                $data['profile_image'] ?? null,
                $data['status'] ?? 'active'
            ]);
            
            return (int)$db->lastInsertId();
        } catch (Exception $e) {
            return null;
        }
    }
    
    public static function update(int $userId, array $data): bool {
        try {
            $db = parent::db();
            $setParts = [];
            $values = [];
            
            $allowedFields = [
                'student_id', 'first_name', 'last_name', 'middle_name', 'email', 'phone',
                'address', 'birth_date', 'program', 'year_level', 'section', 'gpa', 'bio',
                'skills', 'interests', 'linkedin_url', 'github_url', 'portfolio_url',
                'profile_image', 'status'
            ];
            
            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $setParts[] = "$field = ?";
                    $values[] = $data[$field];
                }
            }
            
            if (empty($setParts)) return false;
            
            $values[] = $userId;
            $sql = "UPDATE student_profiles SET " . implode(', ', $setParts) . " WHERE user_id = ?";
            
            $stmt = $db->prepare($sql);
            return $stmt->execute($values);
        } catch (Exception $e) {
            return false;
        }
    }
    
    public static function getCertificationStats(int $userId): array {
        try {
            $db = parent::db();
            $stmt = $db->prepare("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN expires = 0 OR (expire_year > YEAR(NOW()) OR (expire_year = YEAR(NOW()) AND expire_month > MONTH(NOW()))) THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN expires = 1 AND (expire_year < YEAR(NOW()) OR (expire_year = YEAR(NOW()) AND expire_month < MONTH(NOW()))) THEN 1 ELSE 0 END) as expired
                FROM student_licenses 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total' => (int)($result['total'] ?? 0),
                'active' => (int)($result['active'] ?? 0),
                'expired' => (int)($result['expired'] ?? 0)
            ];
        } catch (Exception $e) {
            return ['total' => 0, 'active' => 0, 'expired' => 0];
        }
    }
    
    public static function getFullName(?array $profile): string {
        if (!$profile) return 'Student';
        
        $parts = array_filter([
            $profile['first_name'] ?? '',
            $profile['middle_name'] ?? '',
            $profile['last_name'] ?? ''
        ]);
        
        return !empty($parts) ? implode(' ', $parts) : ($profile['username'] ?? 'Student');
    }
    
    public static function getAvatarInitials(?array $profile): string {
        if (!$profile) return 'S';
        
        $firstName = $profile['first_name'] ?? '';
        $lastName = $profile['last_name'] ?? '';
        
        if ($firstName && $lastName) {
            return strtoupper($firstName[0] . $lastName[0]);
        } elseif ($firstName) {
            return strtoupper($firstName[0]);
        } elseif (!empty($profile['username'])) {
            return strtoupper($profile['username'][0]);
        }
        
        return 'S';
    }
}
?>