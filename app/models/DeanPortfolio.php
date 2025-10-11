<?php
require_once __DIR__ . '/Model.php';

class DeanPortfolio extends Model {

    // All methods including getCertifications must be inside this class block
    public static function migrateDeanPortfolioTables() {
        $pdo = self::db();
        
        // Profile table
        $pdo->exec("CREATE TABLE IF NOT EXISTS dean_profile (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            full_name VARCHAR(255),
            first_name VARCHAR(128),
            last_name VARCHAR(128),
            position VARCHAR(128),
            department VARCHAR(128),
            work_email VARCHAR(128),
            office_location VARCHAR(128),
            employee_id VARCHAR(32),
            employment_type VARCHAR(64),
            date_hired DATE,
            profile_photo VARCHAR(255),
            specializations TEXT,
            languages VARCHAR(128),
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX(user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // Certifications table
        $pdo->exec("CREATE TABLE IF NOT EXISTS dean_certifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            company_name VARCHAR(255),
            issue_year INT,
            expire_year INT,
            credential_id VARCHAR(128),
            credential_url VARCHAR(255),
            status VARCHAR(32) DEFAULT 'active',
            visibility ENUM('public','private') DEFAULT 'public',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX(user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // Experience table
        $pdo->exec("CREATE TABLE IF NOT EXISTS dean_experience (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            position VARCHAR(128),
            department VARCHAR(128),
            period VARCHAR(64),
            INDEX(user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // Education table
        $pdo->exec("CREATE TABLE IF NOT EXISTS dean_education (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            degree VARCHAR(128),
            institution VARCHAR(128),
            year VARCHAR(8),
            INDEX(user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // Personal table
        $pdo->exec("CREATE TABLE IF NOT EXISTS dean_personal (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            birthday DATE,
            gender VARCHAR(16),
            marital_status VARCHAR(32),
            nationality VARCHAR(64),
            address TEXT,
            emergency_contact_name VARCHAR(128),
            emergency_contact_number VARCHAR(32),
            contact_number VARCHAR(32),
            INDEX(user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        // Remove phone_number column if exists
        try { $pdo->exec("ALTER TABLE dean_personal DROP COLUMN phone_number"); } catch (\PDOException $e) {}
        
        // Research table
        $pdo->exec("CREATE TABLE IF NOT EXISTS dean_research (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255),
            journal VARCHAR(255),
            year INT,
            type VARCHAR(64),
            authors TEXT,
            doi_url VARCHAR(255),
            INDEX(user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // Trainings table: ensure columns exist
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS dean_trainings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255),
                provider VARCHAR(255),
                year INT,
                certificate_url VARCHAR(255),
                status VARCHAR(30) DEFAULT 'active'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        // Ensure columns exist even if table already exists
        try { $pdo->exec("ALTER TABLE dean_trainings ADD COLUMN title VARCHAR(255)"); } catch (PDOException $e) {}
        try { $pdo->exec("ALTER TABLE dean_trainings ADD COLUMN provider VARCHAR(255)"); } catch (PDOException $e) {}
        try { $pdo->exec("ALTER TABLE dean_trainings ADD COLUMN certificate_url VARCHAR(255)"); } catch (PDOException $e) {}
        
        // Performance table
        $pdo->exec("CREATE TABLE IF NOT EXISTS dean_performance (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255),
            year INT,
            rating VARCHAR(32),
            remarks TEXT,
            INDEX(user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // Awards table
        $pdo->exec("CREATE TABLE IF NOT EXISTS dean_awards (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255),
            issuer VARCHAR(255),
            year INT,
            description TEXT,
            certificate_url VARCHAR(255),
            INDEX(user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // Ensure missing columns exist in tables (for legacy DBs)
        // These ALTER TABLEs will only add columns if missing, and will be ignored if already present.
        $alterStatements = [
            "ALTER TABLE dean_personal ADD COLUMN gender VARCHAR(16)",
            "ALTER TABLE dean_personal ADD COLUMN marital_status VARCHAR(32)",
            "ALTER TABLE dean_personal ADD COLUMN nationality VARCHAR(64)",
            "ALTER TABLE dean_personal ADD COLUMN emergency_contact_name VARCHAR(128)",
            "ALTER TABLE dean_personal ADD COLUMN emergency_contact_number VARCHAR(32)",
            "ALTER TABLE dean_personal ADD COLUMN contact_number VARCHAR(32)",
            "ALTER TABLE dean_profile ADD COLUMN status VARCHAR(32)",
            "ALTER TABLE dean_experience ADD COLUMN title VARCHAR(128)",
            "ALTER TABLE dean_trainings ADD COLUMN certificate_url VARCHAR(255)",
            "ALTER TABLE dean_trainings ADD COLUMN status VARCHAR(30) DEFAULT 'active'",
            "ALTER TABLE dean_performance ADD COLUMN title VARCHAR(255)",
            "ALTER TABLE dean_awards ADD COLUMN certificate_url VARCHAR(255)",
            "ALTER TABLE dean_research ADD COLUMN journal VARCHAR(255)",
            "ALTER TABLE dean_research ADD COLUMN type VARCHAR(64)",
            "ALTER TABLE dean_research ADD COLUMN authors TEXT",
            "ALTER TABLE dean_research ADD COLUMN doi_url VARCHAR(255)"
        ];
        foreach ($alterStatements as $sql) {
            try { $pdo->exec($sql); } catch (\PDOException $e) {}
        }
    }

    // Profile methods
    // Mandatory: userId, full_name, employee_id, work_email, position, department
    // Optional: employment_type, date_hired, office_location, status, bio, specializations, languages
    public static function getProfile($userId) {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM dean_profile WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public static function updateProfile($userId, $data) {
        if (!$userId || empty($data['full_name']) || empty($data['employee_id']) || empty($data['work_email']) || empty($data['position']) || empty($data['department'])) {
            error_log("DeanPortfolio: Missing mandatory profile fields");
            return false;
        }
        try {
            $db = self::db();
            // Add profile_photo to the query if present
            $columns = [
                'full_name', 'employee_id', 'work_email', 'position', 'department',
                'employment_type', 'date_hired', 'office_location',
                'specializations', 'languages'
            ];
            $values = [];
            foreach ($columns as $col) {
                $values[$col] = $data[$col] ?? '';
            }
            // Handle profile_photo
            if (isset($data['profile_photo'])) {
                $columns[] = 'profile_photo';
                $values['profile_photo'] = $data['profile_photo'];
            }

            // Build SET clause for update
            $setClause = implode(', ', array_map(function($col) { return "$col = ?"; }, $columns));

            // Try update first
            $stmt = $db->prepare("UPDATE dean_profile SET $setClause WHERE user_id = ?");
            $params = array_values($values);
            $params[] = $userId;
            $stmt->execute($params);

            // If no row updated, insert
            if ($stmt->rowCount() === 0) {
                $insertCols = implode(', ', array_merge(['user_id'], $columns));
                $insertPlaceholders = implode(', ', array_fill(0, count($columns) + 1, '?'));
                $insertStmt = $db->prepare("INSERT INTO dean_profile ($insertCols) VALUES ($insertPlaceholders)");
                $insertParams = array_merge([$userId], array_values($values));
                $insertStmt->execute($insertParams);
            }
            return true;
        } catch (\Exception $e) {
            error_log("DeanPortfolio: updateProfile error - " . $e->getMessage());
            return false;
        }
    }

    // Certification methods
    // Mandatory: userId, name, company_name
    // Optional: issue_year, expire_year, credential_id, credential_url, visibility
    public static function getCertifications($userId) {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM dean_certifications WHERE user_id = ? ORDER BY issue_year DESC, id DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function createCertification($userId, $data) {
        if (!$userId || empty($data['name']) || empty($data['company_name'])) {
            error_log("DeanPortfolio: Missing mandatory certification fields");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("INSERT INTO dean_certifications (user_id, name, company_name, issue_year, expire_year, credential_id, credential_url, visibility) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $userId,
                $data['name'],
                $data['company_name'],
                $data['issue_year'] ?: null,
                $data['expire_year'] ?: null,
                $data['credential_id'] ?? '',
                $data['credential_url'] ?? '',
                $data['visibility'] ?? 'public'
            ]);
        } catch (\Exception $e) {
            error_log("DeanPortfolio: createCertification error - " . $e->getMessage());
            return false;
        }
    }

    public static function updateCertification($id, $userId, $data) {
        if (!$id || !$userId || empty($data['name']) || empty($data['company_name'])) {
            error_log("DeanPortfolio: Missing mandatory certification fields for update");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("UPDATE dean_certifications SET name = ?, company_name = ?, issue_year = ?, expire_year = ?, credential_id = ?, credential_url = ?, visibility = ? WHERE id = ? AND user_id = ?");
            return $stmt->execute([
                $data['name'],
                $data['company_name'],
                $data['issue_year'] ?: null,
                $data['expire_year'] ?: null,
                $data['credential_id'] ?? '',
                $data['credential_url'] ?? '',
                $data['visibility'] ?? 'public',
                $id,
                $userId
            ]);
        } catch (\Exception $e) {
            error_log("DeanPortfolio: updateCertification error - " . $e->getMessage());
            return false;
        }
    }

    public static function deleteCertification($id, $userId) {
        $db = self::db();
        $stmt = $db->prepare("DELETE FROM dean_certifications WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    // Experience methods
    // Mandatory: userId, position, department, period
    public static function getExperience($userId) {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM dean_experience WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function createExperience($userId, $data) {
        if (!$userId || empty($data['position']) || empty($data['department']) || empty($data['period'])) {
            error_log("DeanPortfolio: Missing mandatory experience fields");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("INSERT INTO dean_experience (user_id, position, department, period) VALUES (?, ?, ?, ?)");
            return $stmt->execute([
                $userId,
                $data['position'],
                $data['department'],
                $data['period']
            ]);
        } catch (\Exception $e) {
            error_log("DeanPortfolio: createExperience error - " . $e->getMessage());
            return false;
        }
    }

    public static function updateExperience($id, $userId, $data) {
        if (!$id || !$userId || empty($data['position']) || empty($data['department']) || empty($data['period'])) {
            error_log("DeanPortfolio: Missing mandatory experience fields for update");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("UPDATE dean_experience SET position = ?, department = ?, period = ? WHERE id = ? AND user_id = ?");
            return $stmt->execute([
                $data['position'],
                $data['department'],
                $data['period'],
                $id,
                $userId
            ]);
        } catch (\Exception $e) {
            error_log("DeanPortfolio: updateExperience error - " . $e->getMessage());
            return false;
        }
    }

    public static function deleteExperience($id, $userId) {
        $db = self::db();
        $stmt = $db->prepare("DELETE FROM dean_experience WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    // Education methods
    // Mandatory: userId, degree, institution, year
    public static function getEducation($userId) {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM dean_education WHERE user_id = ? ORDER BY year DESC, id DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function createEducation($userId, $data) {
        if (!$userId || empty($data['degree']) || empty($data['institution']) || empty($data['year']) || !is_numeric($data['year']) || $data['year'] < 1900 || $data['year'] > 2100) {
            error_log("DeanPortfolio: Invalid or missing education year");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("INSERT INTO dean_education (user_id, degree, institution, year) VALUES (?, ?, ?, ?)");
            return $stmt->execute([
                $userId,
                $data['degree'],
                $data['institution'],
                $data['year']
            ]);
        } catch (\Exception $e) {
            error_log("DeanPortfolio: createEducation error - " . $e->getMessage());
            return false;
        }
    }

    public static function updateEducation($id, $userId, $data) {
        if (!$id || !$userId || empty($data['degree']) || empty($data['institution']) || empty($data['year'])) {
            error_log("DeanPortfolio: Missing mandatory education fields for update");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("UPDATE dean_education SET degree = ?, institution = ?, year = ? WHERE id = ? AND user_id = ?");
            return $stmt->execute([
                $data['degree'],
                $data['institution'],
                $data['year'],
                $id,
                $userId
            ]);
        } catch (\Exception $e) {
            error_log("DeanPortfolio: updateEducation error - " . $e->getMessage());
            return false;
        }
    }

    public static function deleteEducation($id, $userId) {
        $db = self::db();
        $stmt = $db->prepare("DELETE FROM dean_education WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    // Personal methods
    // Mandatory: userId
    // Optional: birthday, contact_number, address
    public static function getPersonal($userId) {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM dean_personal WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public static function updatePersonal($userId, $data) {
        if (!$userId) {
            error_log("DeanPortfolio: Missing userId for personal info");
            return false;
        }
        try {
            $db = self::db();
            // Check if record exists
            $stmt = $db->prepare("SELECT id FROM dean_personal WHERE user_id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                // Update existing record
                $stmt = $db->prepare("UPDATE dean_personal SET birthday = ?, gender = ?, marital_status = ?, nationality = ?, address = ?, emergency_contact_name = ?, emergency_contact_number = ?, contact_number = ? WHERE user_id = ?");
                return $stmt->execute([
                    $data['birthday'] ?: null,
                    $data['gender'] ?? '',
                    $data['marital_status'] ?? '',
                    $data['nationality'] ?? '',
                    $data['address'] ?? '',
                    $data['emergency_contact_name'] ?? '',
                    $data['emergency_contact_number'] ?? '',
                    $data['contact_number'] ?? '',
                    $userId
                ]);
            } else {
                // Insert new record
                $stmt = $db->prepare("INSERT INTO dean_personal (user_id, birthday, gender, marital_status, nationality, address, emergency_contact_name, emergency_contact_number, contact_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                return $stmt->execute([
                    $userId,
                    $data['birthday'] ?: null,
                    $data['gender'] ?? '',
                    $data['marital_status'] ?? '',
                    $data['nationality'] ?? '',
                    $data['address'] ?? '',
                    $data['emergency_contact_name'] ?? '',
                    $data['emergency_contact_number'] ?? '',
                    $data['contact_number'] ?? ''
                ]);
            }
        } catch (\Exception $e) {
            error_log("DeanPortfolio: updatePersonal error - " . $e->getMessage());
            return false;
        }
    }

    // Research methods
    // Mandatory: userId, title, year
    // Optional: journal, type, authors, doi_url
    public static function getResearch($userId) {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM dean_research WHERE user_id = ? ORDER BY year DESC, id DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function createResearch($userId, $data) {
        if (!$userId || empty($data['title']) || empty($data['year']) || !is_numeric($data['year']) || $data['year'] < 1900 || $data['year'] > 2100) {
            error_log("DeanPortfolio: Invalid or missing research year");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("INSERT INTO dean_research (user_id, title, journal, year, type, authors, doi_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $userId,
                $data['title'],
                $data['journal'] ?? '',
                $data['year'],
                $data['type'] ?? '',
                $data['authors'] ?? '',
                $data['doi_url'] ?? ''
            ]);
        } catch (\Exception $e) {
            error_log("DeanPortfolio: createResearch error - " . $e->getMessage());
            return false;
        }
    }

    public static function updateResearch($id, $userId, $data) {
        if (!$id || !$userId || empty($data['title']) || empty($data['year'])) {
            error_log("DeanPortfolio: Missing mandatory research fields for update");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("UPDATE dean_research SET title = ?, journal = ?, year = ?, type = ?, authors = ?, doi_url = ? WHERE id = ? AND user_id = ?");
            return $stmt->execute([
                $data['title'],
                $data['journal'] ?? '',
                $data['year'],
                $data['type'] ?? '',
                $data['authors'] ?? '',
                $data['doi_url'] ?? '',
                $id,
                $userId
            ]);
        } catch (\Exception $e) {
            error_log("DeanPortfolio: updateResearch error - " . $e->getMessage());
            return false;
        }
    }

    public static function deleteResearch($id, $userId) {
        $db = self::db();
        $stmt = $db->prepare("DELETE FROM dean_research WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    // Trainings CRUD
    // Mandatory: userId, title
    // Optional: provider, year, certificate_url, status
    public static function getTrainings($userId) {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM dean_trainings WHERE user_id = ? ORDER BY year DESC, id DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    public static function createTraining($userId, $data) {
        if (!$userId || empty($data['title'])) {
            error_log("DeanPortfolio: Missing mandatory training fields");
            return false;
        }
        if (!empty($data['year']) && (!is_numeric($data['year']) || $data['year'] < 1900 || $data['year'] > 2100)) {
            error_log("DeanPortfolio: Invalid training year");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("INSERT INTO dean_trainings (user_id, title, provider, year, certificate_url, status) VALUES (?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $userId,
                $data['title'],
                $data['provider'] ?? '',
                $data['year'] ?? null,
                $data['certificate_url'] ?? '',
                $data['status'] ?? 'active'
            ]);
        } catch (\Exception $e) {
            error_log("DeanPortfolio: createTraining error - " . $e->getMessage());
            return false;
        }
    }
    public static function updateTraining($id, $userId, $data) {
        if (!$id || !$userId || empty($data['title'])) {
            error_log("DeanPortfolio: Missing mandatory training fields for update");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("UPDATE dean_trainings SET title=?, provider=?, year=?, certificate_url=?, status=? WHERE id=? AND user_id=?");
            return $stmt->execute([
                $data['title'],
                $data['provider'] ?? '',
                $data['year'] ?? null,
                $data['certificate_url'] ?? '',
                $data['status'] ?? 'active',
                $id,
                $userId
            ]);
        } catch (\Exception $e) {
            error_log("DeanPortfolio: updateTraining error - " . $e->getMessage());
            return false;
        }
    }
    public static function deleteTraining($id, $userId) {
        $db = self::db();
        $stmt = $db->prepare("DELETE FROM dean_trainings WHERE id=? AND user_id=?");
        return $stmt->execute([$id, $userId]);
    }

    // Performance CRUD
    // Mandatory: userId, title, year
    // Optional: rating, remarks
    public static function getPerformance($userId) {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM dean_performance WHERE user_id = ? ORDER BY year DESC, id DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    public static function createPerformance($userId, $data) {
        if (!$userId || empty($data['title']) || empty($data['year']) || !is_numeric($data['year']) || $data['year'] < 1900 || $data['year'] > 2100) {
            error_log("DeanPortfolio: Invalid or missing performance year");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("INSERT INTO dean_performance (user_id, title, year, rating, remarks) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([
                $userId,
                $data['title'],
                $data['year'],
                $data['rating'] ?? '',
                $data['remarks'] ?? ''
            ]);
        } catch (\Exception $e) {
            error_log("DeanPortfolio: createPerformance error - " . $e->getMessage());
            return false;
        }
    }

    public static function updatePerformance($id, $userId, $data) {
        if (!$id || !$userId || empty($data['title']) || empty($data['year'])) {
            error_log("DeanPortfolio: Missing mandatory performance fields for update");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("UPDATE dean_performance SET title=?, year=?, rating=?, remarks=? WHERE id=? AND user_id=?");
            return $stmt->execute([
                $data['title'],
                $data['year'],
                $data['rating'] ?? '',
                $data['remarks'] ?? '',
                $id,
                $userId
            ]);
        } catch (\Exception $e) {
            error_log("DeanPortfolio: updatePerformance error - " . $e->getMessage());
            return false;
        }
    }

    // Awards CRUD
    // Mandatory: userId, title
    // Optional: issuer, year, description, certificate_url
    public static function getAwards($userId) {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM dean_awards WHERE user_id = ? ORDER BY year DESC, id DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    public static function createAward($userId, $data) {
        if (!$userId || empty($data['title'])) {
            error_log("DeanPortfolio: Missing mandatory award fields");
            return false;
        }
        if (!empty($data['year']) && (!is_numeric($data['year']) || $data['year'] < 1900 || $data['year'] > 2100)) {
            error_log("DeanPortfolio: Invalid award year");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("INSERT INTO dean_awards (user_id, title, issuer, year, description, certificate_url) VALUES (?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $userId,
                $data['title'],
                $data['issuer'] ?? '',
                $data['year'] ?? null,
                $data['description'] ?? '',
                $data['certificate_url'] ?? ''
            ]);
        } catch (\Exception $e) {
            error_log("DeanPortfolio: createAward error - " . $e->getMessage());
            return false;
        }
    }

    public static function updateAward($id, $userId, $data) {
        if (!$id || !$userId || empty($data['title'])) {
            error_log("DeanPortfolio: Missing mandatory award fields for update");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("UPDATE dean_awards SET title=?, issuer=?, year=?, description=?, certificate_url=? WHERE id=? AND user_id=?");
            return $stmt->execute([
                $data['title'],
                $data['issuer'] ?? '',
                $data['year'] ?? null,
                $data['description'] ?? '',
                $data['certificate_url'] ?? '',
                $id,
                $userId
            ]);
        } catch (\Exception $e) {
            error_log("DeanPortfolio: updateAward error - " . $e->getMessage());
            return false;
        }
    }

    public static function deletePerformance($id, $userId) {
        $db = self::db();
        $stmt = $db->prepare("DELETE FROM dean_performance WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    public static function deleteAward($id, $userId) {
        $db = self::db();
        $stmt = $db->prepare("DELETE FROM dean_awards WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
}

/*
When displaying fields, use a placeholder if missing
Example usage in your views:
<?= esc($row['contact_number'] ?? 'N/A') ?>
<?= esc($row['status'] ?? 'N/A') ?>
<?= esc($row['title'] ?? 'N/A') ?>
<?= esc($row['certificate_url'] ?? 'N/A') ?>
<?= esc($row['journal'] ?? 'N/A') ?>
*/

