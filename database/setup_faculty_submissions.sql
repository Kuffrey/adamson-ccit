-- Faculty Submissions and Dean Logs Tables Setup

-- Create faculty_submissions table
CREATE TABLE IF NOT EXISTS faculty_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    submission_type ENUM('news', 'research', 'event', 'announcement', 'certification') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content LONGTEXT,
    category VARCHAR(100),
    status ENUM('submitted', 'under_review', 'approved', 'rejected', 'published') DEFAULT 'submitted',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    review_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_faculty_id (faculty_id),
    INDEX idx_submission_type (submission_type),
    INDEX idx_status (status),
    INDEX idx_submitted_at (submitted_at),
    FOREIGN KEY (faculty_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create dean_logs table
CREATE TABLE IF NOT EXISTS dean_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action ENUM('CREATE', 'UPDATE', 'DELETE', 'APPROVE', 'REJECT', 'PUBLISH') NOT NULL,
    table_name VARCHAR(100) NOT NULL,
    record_id INT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_table_name (table_name),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert some sample data if tables are empty
INSERT IGNORE INTO faculty_submissions (faculty_id, submission_type, title, description, content, category, status) 
SELECT 
    u.id,
    'news',
    'Sample News Submission',
    'This is a sample news submission for testing purposes.',
    'This is the full content of the sample news submission. It contains more detailed information about the news item.',
    'general',
    'submitted'
FROM users u 
WHERE u.role = 'faculty' 
LIMIT 1;

INSERT IGNORE INTO faculty_submissions (faculty_id, submission_type, title, description, content, category, status) 
SELECT 
    u.id,
    'research',
    'Sample Research Submission',
    'This is a sample research submission for testing purposes.',
    '{"type":"journal","year":"2024","authors":"Faculty Member","venue":"Test Journal","dept":"cs"}',
    'journal',
    'submitted'
FROM users u 
WHERE u.role = 'faculty' 
LIMIT 1;
