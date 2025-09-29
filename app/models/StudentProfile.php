<?php
// app/models/StudentProfile.php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

class StudentProfile extends Model {
    
    public static function getByUserId(int $userId): ?array {
        try {
            $db = parent::db();
            // Get user data from users table (primary source) and merge with student_profiles if exists
            $stmt = $db->prepare("
                SELECT u.*, sp.bio, sp.skills, sp.linkedin_url, sp.github_url, sp.portfolio_url
                FROM users u 
                LEFT JOIN student_profiles sp ON u.id = sp.user_id 
                WHERE u.id = ?
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return null;
            }
            
            // Return unified profile data from both tables
            return [
                'id' => $result['id'],
                'user_id' => $result['id'],
                'first_name' => $result['first_name'] ?? null,
                'last_name' => $result['last_name'] ?? null,
                'full_name' => trim(($result['first_name'] ?? '') . ' ' . ($result['last_name'] ?? '')),
                'email' => $result['email'] ?? null,
                'phone' => $result['phone'] ?? null,
                'birthday' => $result['birthday'] ?? null,
                'birth_date' => $result['birthday'] ?? null, // Alias for compatibility
                'program' => $result['program'] ?? null,
                'year_level' => $result['year_level'] ?? null,
                'student_number' => $result['student_number'] ?? null,
                'bio' => $result['bio'] ?? null,
                'skills' => $result['skills'] ?? null,
                'linkedin_url' => $result['linkedin_url'] ?? null,
                'github_url' => $result['github_url'] ?? null,
                'website_url' => $result['portfolio_url'] ?? null, // Map portfolio_url to website_url
                'portfolio_url' => $result['portfolio_url'] ?? null,
                'location' => $result['address'] ?? null, // Map address to location from users table
                'profile_image' => $result['profile_image'] ?? null,
                'created_at' => $result['created_at'] ?? null,
                'updated_at' => $result['updated_at'] ?? null,
                'username' => $result['username'] ?? null,
                'role' => $result['role'] ?? null
            ];
        } catch (Exception $e) {
            error_log("StudentProfile::getByUserId error: " . $e->getMessage());
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