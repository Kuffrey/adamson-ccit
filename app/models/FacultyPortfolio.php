<?php
require_once __DIR__ . '/Model.php';

class FacultyPortfolio extends Model
{
    public static function migrateFacultyPortfolioTables(): void
    {
        $db = static::db();

        // Check and create faculty_profile table
        $db->exec("
            CREATE TABLE IF NOT EXISTS faculty_profile (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                full_name VARCHAR(255),
                first_name VARCHAR(255),
                last_name VARCHAR(255),
                employee_id VARCHAR(32),
                work_email VARCHAR(128),
                position VARCHAR(128),
                department VARCHAR(128),
                employment_type VARCHAR(64),
                date_hired DATE,
                office_location VARCHAR(128),
                profile_photo VARCHAR(255),
                specializations TEXT,
                languages TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");

        // Check and create faculty_certifications table
        $db->exec("
            CREATE TABLE IF NOT EXISTS faculty_certifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                name VARCHAR(255),
                company_name VARCHAR(255),
                issue_year INT,
                expire_year INT,
                credential_id VARCHAR(128),
                credential_url VARCHAR(255),
                visibility ENUM('public', 'private') DEFAULT 'public',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");

        // Check and create faculty_experience table
        $db->exec("
            CREATE TABLE IF NOT EXISTS faculty_experience (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                position VARCHAR(255),
                department VARCHAR(255),
                period VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");

        // Check and create faculty_education table
        $db->exec("
            CREATE TABLE IF NOT EXISTS faculty_education (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                degree VARCHAR(255),
                institution VARCHAR(255),
                year INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");

        // Check and create faculty_personal table
        $db->exec("
            CREATE TABLE IF NOT EXISTS faculty_personal (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                birthday DATE,
                gender VARCHAR(32),
                marital_status VARCHAR(32),
                nationality VARCHAR(64),
                address TEXT,
                contact_number VARCHAR(64),
                emergency_contact_name VARCHAR(255),
                emergency_contact_number VARCHAR(64),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");

        // Check and create faculty_research table
        $db->exec("
            CREATE TABLE IF NOT EXISTS faculty_research (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255),
                journal VARCHAR(255),
                year INT,
                type VARCHAR(64),
                authors TEXT,
                doi_url VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");

        // Check and create faculty_trainings table
        $db->exec("
            CREATE TABLE IF NOT EXISTS faculty_trainings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255),
                provider VARCHAR(255),
                year INT,
                certificate_url VARCHAR(255),
                status ENUM('active', 'expired') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");

        // Check and create faculty_performance table
        $db->exec("
            CREATE TABLE IF NOT EXISTS faculty_performance (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255),
                year INT,
                rating VARCHAR(64),
                remarks TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");

        // Check and create faculty_awards table
        $db->exec("
            CREATE TABLE IF NOT EXISTS faculty_awards (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255),
                year INT,
                issuer VARCHAR(255),
                description TEXT,
                certificate_url VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
    }

    // Profile methods
    public static function getProfile($userId) {
        $stmt = static::db()->prepare("SELECT * FROM faculty_profile WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function updateProfile($userId, $data) {
        if (!$userId || empty($data['full_name']) || empty($data['employee_id']) || empty($data['work_email']) || empty($data['position']) || empty($data['department'])) {
            error_log("FacultyPortfolio: Missing mandatory profile fields");
            return false;
        }
        try {
            $db = self::db();
            $columns = [
                'full_name', 'employee_id', 'work_email', 'position', 'department',
                'employment_type', 'date_hired', 'office_location',
                'specializations', 'languages'
            ];
            $values = [];
            foreach ($columns as $col) {
                $values[$col] = $data[$col] ?? '';
            }
            if (isset($data['profile_photo'])) {
                $columns[] = 'profile_photo';
                $values['profile_photo'] = $data['profile_photo'];
            }

            $setClause = implode(', ', array_map(function($col) { return "$col = ?"; }, $columns));
            $stmt = $db->prepare("UPDATE faculty_profile SET $setClause WHERE user_id = ?");
            $params = array_values($values);
            $params[] = $userId;
            $stmt->execute($params);

            if ($stmt->rowCount() === 0) {
                $insertCols = implode(', ', array_merge(['user_id'], $columns));
                $insertPlaceholders = implode(', ', array_fill(0, count($columns) + 1, '?'));
                $insertStmt = $db->prepare("INSERT INTO faculty_profile ($insertCols) VALUES ($insertPlaceholders)");
                $insertParams = array_merge([$userId], array_values($values));
                $insertStmt->execute($insertParams);
            }
            return true;
        } catch (\Exception $e) {
            error_log("FacultyPortfolio: updateProfile error - " . $e->getMessage());
            return false;
        }
    }

    // Certification methods
    public static function getCertifications($userId) {
        $stmt = static::db()->prepare("SELECT * FROM faculty_certifications WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createCertification($userId, $data) {
        if (!$userId || empty($data['name']) || empty($data['company_name'])) {
            error_log("FacultyPortfolio: Missing mandatory certification fields");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("INSERT INTO faculty_certifications (user_id, name, company_name, issue_year, expire_year, credential_id, credential_url, visibility) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
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
            error_log("FacultyPortfolio: createCertification error - " . $e->getMessage());
            return false;
        }
    }

    public static function updateCertification($id, $userId, $data) {
        if (!$id || !$userId || empty($data['name']) || empty($data['company_name'])) {
            error_log("FacultyPortfolio: Missing mandatory certification fields for update");
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("UPDATE faculty_certifications SET name = ?, company_name = ?, issue_year = ?, expire_year = ?, credential_id = ?, credential_url = ?, visibility = ? WHERE id = ? AND user_id = ?");
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
            error_log("FacultyPortfolio: updateCertification error - " . $e->getMessage());
            return false;
        }
    }

    public static function deleteCertification($id, $userId) {
        try {
            $db = self::db();
            $stmt = $db->prepare("DELETE FROM faculty_certifications WHERE id = ? AND user_id = ?");
            return $stmt->execute([$id, $userId]);
        } catch (\Exception $e) {
            error_log("FacultyPortfolio: deleteCertification error - " . $e->getMessage());
            return false;
        }
    }

    // Experience methods
    public static function getExperience($userId) {
        $stmt = static::db()->prepare("SELECT * FROM faculty_experience WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createExperience($userId, $data) {
        if (!$userId || empty($data['position']) || empty($data['department']) || empty($data['period'])) {
            return false;
        }
        try {
            $db = self::db();
            $stmt = $db->prepare("INSERT INTO faculty_experience (user_id, position, department, period) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$userId, $data['position'], $data['department'], $data['period']]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateExperience($id, $userId, $data) {
        try {
            $db = self::db();
            $stmt = $db->prepare("UPDATE faculty_experience SET position = ?, department = ?, period = ? WHERE id = ? AND user_id = ?");
            return $stmt->execute([$data['position'], $data['department'], $data['period'], $id, $userId]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteExperience($id, $userId) {
        try {
            $db = self::db();
            $stmt = $db->prepare("DELETE FROM faculty_experience WHERE id = ? AND user_id = ?");
            return $stmt->execute([$id, $userId]);
        } catch (\Exception $e) {
            return false;
        }
    }

    // Education methods
    public static function getEducation($userId) {
        $stmt = static::db()->prepare("SELECT * FROM faculty_education WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createEducation($userId, $data) {
        try {
            $db = self::db();
            $stmt = $db->prepare("INSERT INTO faculty_education (user_id, degree, institution, year) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$userId, $data['degree'], $data['institution'], $data['year']]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateEducation($id, $userId, $data) {
        try {
            $db = self::db();
            $stmt = $db->prepare("UPDATE faculty_education SET degree = ?, institution = ?, year = ? WHERE id = ? AND user_id = ?");
            return $stmt->execute([$data['degree'], $data['institution'], $data['year'], $id, $userId]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteEducation($id, $userId) {
        try {
            $db = self::db();
            $stmt = $db->prepare("DELETE FROM faculty_education WHERE id = ? AND user_id = ?");
            return $stmt->execute([$id, $userId]);
        } catch (\Exception $e) {
            return false;
        }
    }

    // Personal methods
    public static function getPersonal($userId) {
        $stmt = static::db()->prepare("SELECT * FROM faculty_personal WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function updatePersonal($userId, $data) {
        try {
            $db = self::db();
            $stmt = $db->prepare("SELECT id FROM faculty_personal WHERE user_id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                $stmt = $db->prepare("UPDATE faculty_personal SET birthday = ?, gender = ?, marital_status = ?, nationality = ?, address = ?, emergency_contact_name = ?, emergency_contact_number = ?, contact_number = ? WHERE user_id = ?");
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
                $stmt = $db->prepare("INSERT INTO faculty_personal (user_id, birthday, gender, marital_status, nationality, address, emergency_contact_name, emergency_contact_number, contact_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
            return false;
        }
    }

    // Research methods
    public static function getResearch($userId) {
        $stmt = static::db()->prepare("SELECT * FROM faculty_research WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createResearch($userId, $data) {
        try {
            $db = self::db();
            $stmt = $db->prepare("INSERT INTO faculty_research (user_id, title, journal, year, type, authors, doi_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
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
            return false;
        }
    }

    public static function updateResearch($id, $userId, $data) {
        try {
            $db = self::db();
            $stmt = $db->prepare("UPDATE faculty_research SET title = ?, journal = ?, year = ?, type = ?, authors = ?, doi_url = ? WHERE id = ? AND user_id = ?");
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
            return false;
        }
    }

    public static function deleteResearch($id, $userId) {
        try {
            $db = self::db();
            $stmt = $db->prepare("DELETE FROM faculty_research WHERE id = ? AND user_id = ?");
            return $stmt->execute([$id, $userId]);
        } catch (\Exception $e) {
            return false;
        }
    }

    // Training methods
    public static function getTrainings($userId) {
        $stmt = static::db()->prepare("SELECT * FROM faculty_trainings WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createTraining($userId, $data) {
        try {
            $db = self::db();
            $stmt = $db->prepare("INSERT INTO faculty_trainings (user_id, title, provider, year, certificate_url, status) VALUES (?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $userId,
                $data['title'],
                $data['provider'] ?? '',
                $data['year'] ?? null,
                $data['certificate_url'] ?? '',
                $data['status'] ?? 'active'
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateTraining($id, $userId, $data) {
        try {
            $db = self::db();
            $stmt = $db->prepare("UPDATE faculty_trainings SET title=?, provider=?, year=?, certificate_url=?, status=? WHERE id=? AND user_id=?");
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
            return false;
        }
    }

    public static function deleteTraining($id, $userId) {
        try {
            $db = self::db();
            $stmt = $db->prepare("DELETE FROM faculty_trainings WHERE id=? AND user_id=?");
            return $stmt->execute([$id, $userId]);
        } catch (\Exception $e) {
            return false;
        }
    }

    // Performance methods
    public static function getPerformance($userId) {
        $stmt = static::db()->prepare("SELECT * FROM faculty_performance WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createPerformance($userId, $data) {
        try {
            $db = self::db();
            $stmt = $db->prepare("INSERT INTO faculty_performance (user_id, title, year, rating, remarks) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([
                $userId,
                $data['title'],
                $data['year'],
                $data['rating'] ?? '',
                $data['remarks'] ?? ''
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updatePerformance($id, $userId, $data) {
        try {
            $db = self::db();
            $stmt = $db->prepare("UPDATE faculty_performance SET title=?, year=?, rating=?, remarks=? WHERE id=? AND user_id=?");
            return $stmt->execute([
                $data['title'],
                $data['year'],
                $data['rating'] ?? '',
                $data['remarks'] ?? '',
                $id,
                $userId
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deletePerformance($id, $userId) {
        try {
            $db = self::db();
            $stmt = $db->prepare("DELETE FROM faculty_performance WHERE id = ? AND user_id = ?");
            return $stmt->execute([$id, $userId]);
        } catch (\Exception $e) {
            return false;
        }
    }

    // Awards methods
    public static function getAwards($userId) {
        $stmt = static::db()->prepare("SELECT * FROM faculty_awards WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createAward($userId, $data) {
        try {
            $db = self::db();
            $stmt = $db->prepare("INSERT INTO faculty_awards (user_id, title, issuer, year, description, certificate_url) VALUES (?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $userId,
                $data['title'],
                $data['issuer'] ?? '',
                $data['year'] ?? null,
                $data['description'] ?? '',
                $data['certificate_url'] ?? ''
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateAward($id, $userId, $data) {
        try {
            $db = self::db();
            $stmt = $db->prepare("UPDATE faculty_awards SET title=?, issuer=?, year=?, description=?, certificate_url=? WHERE id=? AND user_id=?");
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
            return false;
        }
    }

    public static function deleteAward($id, $userId) {
        try {
            $db = self::db();
            $stmt = $db->prepare("DELETE FROM faculty_awards WHERE id = ? AND user_id = ?");
            return $stmt->execute([$id, $userId]);
        } catch (\Exception $e) {
            return false;
        }
    }

    // Helper methods for compatibility
    public static function getByUserId(int $facultyId): array
    {
        return self::getCertifications($facultyId);
    }

    public static function getStats(int $facultyId): array
    {
        $certifications = self::getCertifications($facultyId);
        $total = count($certifications);
        $active = 0;
        $expired = 0;
        
        foreach ($certifications as $cert) {
            if (!empty($cert['expire_year']) && (int)$cert['expire_year'] < (int)date('Y')) {
                $expired++;
            } else {
                $active++;
            }
        }
        
        return ['total' => $total, 'active' => $active, 'expired' => $expired];
    }
}
?>