<?php
// app/models/FacultyProfile.php
require_once __DIR__ . '/Model.php';

class FacultyProfile extends Model {
    /**
     * Create a faculty profile from a student profile array
     * Sets role to 'faculty' and maps relevant fields
     */
    public static function createFromStudentProfile(array $studentProfile): bool {
        $db = self::db();
        $sql = 'INSERT INTO ' . self::$table . ' (name, dept, role, title, avatar_url, avatar_initials, badges, ordering) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $name = trim(($studentProfile['first_name'] ?? '') . ' ' . ($studentProfile['last_name'] ?? ''));
        $dept = $studentProfile['program'] ?? '';
        $role = 'faculty';
        $title = $studentProfile['title'] ?? '';
        $avatar_url = $studentProfile['profile_image'] ?? '';
        $avatar_initials = strtoupper(substr($studentProfile['first_name'] ?? '', 0, 1) . substr($studentProfile['last_name'] ?? '', 0, 1));
        $badges = '';
        $ordering = 0;
        return $db->prepare($sql)->execute([
            $name,
            $dept,
            $role,
            $title,
            $avatar_url,
            $avatar_initials,
            $badges,
            $ordering
        ]);
    }
    protected static $table = 'faculty_profile';
    
    public static function getAll() {
        $db = self::db();
        $sql = 'SELECT id, name, prefix, first_name, middle_initial, surname, suffix, dept, role, role_order, title, avatar_url, avatar_initials, badges, ordering, created_at, updated_at
                FROM ' . self::$table . ' 
                ORDER BY role_order ASC, surname ASC, first_name ASC, id ASC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getDepartments() {
        $db = self::db();
        $sql = 'SELECT DISTINCT dept FROM ' . self::$table . ' ORDER BY dept ASC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public static function getRoles() {
        $db = self::db();
        $sql = 'SELECT DISTINCT role FROM ' . self::$table . ' ORDER BY role ASC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getById($id) {
        $db = self::db();
        $sql = 'SELECT id, name, prefix, first_name, middle_initial, surname, suffix, dept, role, role_order, title, avatar_url, avatar_initials, badges, ordering 
                FROM ' . self::$table . ' WHERE id = ?';
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = self::db();
        
        // Parse the full name if provided
        if (!empty($data['name']) && (empty($data['prefix']) || empty($data['surname']))) {
            $parsedName = self::parseName($data['name']);
            $data['prefix'] = $data['prefix'] ?? $parsedName['prefix'];
            $data['first_name'] = $data['first_name'] ?? $parsedName['first_name'];
            $data['surname'] = $data['surname'] ?? $parsedName['surname'];
        }
        
        // Build the full name from parts if components are provided
        if (!empty($data['prefix']) || !empty($data['first_name']) || !empty($data['middle_initial']) || !empty($data['surname']) || !empty($data['suffix'])) {
            $data['name'] = self::buildFullName($data);
        }
        
        // Auto-generate avatar initials if not provided
        if (empty($data['avatar_initials']) && (!empty($data['first_name']) || !empty($data['surname']))) {
            $data['avatar_initials'] = self::generateAvatarInitials($data['first_name'] ?? '', $data['surname'] ?? '');
        }
        
        // Set role_order based on role
        $data['role_order'] = self::getRoleOrder($data['role'] ?? '');
        
        $sql = 'INSERT INTO ' . self::$table . ' 
                (name, prefix, first_name, middle_initial, surname, suffix, dept, role, role_order, title, avatar_url, avatar_initials, badges, ordering) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['name'] ?? '',
            $data['prefix'] ?? '',
            $data['first_name'] ?? '',
            $data['middle_initial'] ?? '',
            $data['surname'] ?? '',
            $data['suffix'] ?? '',
            $data['dept'] ?? '',
            $data['role'] ?? '',
            $data['role_order'] ?? 999,
            $data['title'] ?? '',
            $data['avatar_url'] ?? '',
            $data['avatar_initials'] ?? '',
            $data['badges'] ?? '',
            $data['ordering'] ?? 0
        ]);
    }

    public static function update($id, $data) {
        $db = self::db();
        
        // Parse the full name if provided
        if (!empty($data['name']) && (empty($data['prefix']) || empty($data['surname']))) {
            $parsedName = self::parseName($data['name']);
            $data['prefix'] = $data['prefix'] ?? $parsedName['prefix'];
            $data['first_name'] = $data['first_name'] ?? $parsedName['first_name'];
            $data['surname'] = $data['surname'] ?? $parsedName['surname'];
        }
        
        // Build the full name from parts if components are provided
        if (!empty($data['prefix']) || !empty($data['first_name']) || !empty($data['middle_initial']) || !empty($data['surname']) || !empty($data['suffix'])) {
            $data['name'] = self::buildFullName($data);
        }
        
        // Auto-generate avatar initials if not provided
        if (empty($data['avatar_initials']) && (!empty($data['first_name']) || !empty($data['surname']))) {
            $data['avatar_initials'] = self::generateAvatarInitials($data['first_name'] ?? '', $data['surname'] ?? '');
        }
        
        // Set role_order based on role
        $data['role_order'] = self::getRoleOrder($data['role'] ?? '');
        
        $sql = 'UPDATE ' . self::$table . ' SET 
                name = ?, prefix = ?, first_name = ?, middle_initial = ?, surname = ?, suffix = ?, dept = ?, role = ?, role_order = ?, title = ?, avatar_url = ?, 
                avatar_initials = ?, badges = ?, ordering = ? 
                WHERE id = ?';
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['name'] ?? '',
            $data['prefix'] ?? '',
            $data['first_name'] ?? '',
            $data['middle_initial'] ?? '',
            $data['surname'] ?? '',
            $data['suffix'] ?? '',
            $data['dept'] ?? '',
            $data['role'] ?? '',
            $data['role_order'] ?? 999,
            $data['title'] ?? '',
            $data['avatar_url'] ?? '',
            $data['avatar_initials'] ?? '',
            $data['badges'] ?? '',
            $data['ordering'] ?? 0,
            $id
        ]);
    }

    public static function delete($id) {
        $db = self::db();
        $sql = 'DELETE FROM ' . self::$table . ' WHERE id = ?';
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Parse a full name into prefix, first name, and surname components
     */
    private static function parseName($fullName) {
        $fullName = trim($fullName);
        
        // Extract prefix (Dr., Mrs., Mr., Ms., Prof., etc.)
        $prefix = '';
        $nameWithoutPrefix = $fullName;
        
        $prefixes = ['Dr.', 'Prof.', 'Mrs.', 'Mr.', 'Ms.', 'Miss.', 'Sir.', 'Ma\'am.', 'Prof.Dr.', 'Rev.'];
        foreach ($prefixes as $p) {
            if (stripos($fullName, $p) === 0) {
                $prefix = $p;
                $nameWithoutPrefix = trim(substr($fullName, strlen($p)));
                break;
            }
        }
        
        // Split remaining name into parts
        $nameParts = explode(' ', $nameWithoutPrefix);
        $nameParts = array_filter(array_map('trim', $nameParts)); // Remove empty parts
        
        if (count($nameParts) >= 2) {
            // Last part is surname, everything else is first name
            $surname = array_pop($nameParts);
            $firstName = implode(' ', $nameParts);
        } else {
            // Only one name part, treat as surname
            $firstName = '';
            $surname = $nameParts[0] ?? '';
        }
        
        return [
            'prefix' => $prefix ?: null,
            'first_name' => $firstName ?: null,
            'surname' => $surname
        ];
    }
    
    /**
     * Get role order for hierarchical sorting
     * Dean = 1, Chair = 2, Full = 3, Part = 4, Lecturer = 5
     */
    private static function getRoleOrder($role) {
        $roleOrders = [
            'dean' => 1,
            'chair' => 2,
            'full' => 3,
            'part' => 4,
            'lecturer' => 5
        ];
        
        return $roleOrders[$role] ?? 999;
    }
    
    /**
     * Build full name with proper comma rules
     * Academic titles (PhD, MSIT, etc.) get commas after surname
     * Generational suffixes (Jr., Sr., III, etc.) don't get commas
     */
    public static function buildFullName($data) {
        $nameParts = [];
        
        // Add prefix
        if (!empty($data['prefix'])) {
            $nameParts[] = $data['prefix'];
        }
        
        // Add first name
        if (!empty($data['first_name'])) {
            $nameParts[] = $data['first_name'];
        }
        
        // Add middle initial
        if (!empty($data['middle_initial'])) {
            $nameParts[] = $data['middle_initial'];
        }
        
        // Add surname
        if (!empty($data['surname'])) {
            $nameParts[] = $data['surname'];
        }
        
        $fullName = implode(' ', array_filter($nameParts));
        
        // Handle suffix with proper comma rules
        if (!empty($data['suffix'])) {
            $suffix = trim($data['suffix']);
            
            // Generational suffixes that don't need commas
            $generationalSuffixes = ['Jr.', 'Jr', 'Sr.', 'Sr', 'II', 'III', 'IV', 'V', '2nd', '3rd', '4th', '5th'];
            
            if (in_array($suffix, $generationalSuffixes)) {
                $fullName .= ' ' . $suffix;
            } else {
                // Academic titles and other suffixes get commas
                $fullName .= ', ' . $suffix;
            }
        }
        
        return $fullName;
    }
    
    /**
     * Generate avatar initials from first and last name only
     */
    public static function generateAvatarInitials($firstName, $surname) {
        $firstInitial = !empty($firstName) ? strtoupper(substr(trim($firstName), 0, 1)) : '';
        $lastInitial = !empty($surname) ? strtoupper(substr(trim($surname), 0, 1)) : '';
        
        return $firstInitial . $lastInitial;
    }
}
