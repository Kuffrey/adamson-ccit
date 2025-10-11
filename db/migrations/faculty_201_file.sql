CREATE TABLE IF NOT EXISTS faculty_201_file (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    company_name VARCHAR(255) DEFAULT NULL,
    issue_month INT DEFAULT NULL,
    issue_year INT DEFAULT NULL,
    expires TINYINT(1) DEFAULT 1,
    expire_month INT DEFAULT NULL,
    expire_year INT DEFAULT NULL,
    credential_id VARCHAR(128) DEFAULT NULL,
    credential_url VARCHAR(255) DEFAULT NULL,
    visibility ENUM('public','private') DEFAULT 'public',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
