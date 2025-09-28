-- faculty_management_comprehensive.sql
-- Comprehensive Faculty Management System Database Schema

-- 1. Faculty Submissions Table (for research, certifications, news)
CREATE TABLE IF NOT EXISTS faculty_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    submission_type ENUM('research', 'certification', 'news') NOT NULL,
    title VARCHAR(500) NOT NULL,
    description TEXT,
    content LONGTEXT,
    category VARCHAR(100),
    file_path VARCHAR(500),
    status ENUM('draft', 'submitted', 'under_review', 'approved', 'rejected', 'published') DEFAULT 'draft',
    submitted_at TIMESTAMP NULL,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    review_notes TEXT,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    tags JSON,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_faculty_id (faculty_id),
    INDEX idx_submission_type (submission_type),
    INDEX idx_status (status),
    INDEX idx_submitted_at (submitted_at),
    
    FOREIGN KEY (faculty_id) REFERENCES faculty_profile(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 2. Faculty Research Table (detailed research tracking)
CREATE TABLE IF NOT EXISTS faculty_research (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT,
    faculty_id INT NOT NULL,
    title VARCHAR(500) NOT NULL,
    abstract TEXT,
    research_area VARCHAR(200),
    funding_source VARCHAR(300),
    funding_amount DECIMAL(15,2),
    start_date DATE,
    end_date DATE,
    status ENUM('planning', 'ongoing', 'completed', 'published', 'archived') DEFAULT 'planning',
    collaborators JSON,
    keywords JSON,
    publications JSON,
    impact_factor DECIMAL(5,2),
    citations_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_faculty_id (faculty_id),
    INDEX idx_research_area (research_area),
    INDEX idx_status (status),
    
    FOREIGN KEY (submission_id) REFERENCES faculty_submissions(id) ON DELETE SET NULL,
    FOREIGN KEY (faculty_id) REFERENCES faculty_profile(id) ON DELETE CASCADE
);

-- 3. Faculty Certifications Table (detailed certification tracking)
CREATE TABLE IF NOT EXISTS faculty_certifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT,
    faculty_id INT NOT NULL,
    certification_name VARCHAR(300) NOT NULL,
    issuing_organization VARCHAR(300) NOT NULL,
    certification_type ENUM('professional', 'academic', 'technical', 'language', 'other') DEFAULT 'professional',
    issue_date DATE,
    expiry_date DATE,
    credential_id VARCHAR(200),
    verification_url VARCHAR(500),
    certificate_file_path VARCHAR(500),
    status ENUM('valid', 'expired', 'revoked', 'pending_verification') DEFAULT 'valid',
    verification_status ENUM('verified', 'unverified', 'in_progress', 'failed') DEFAULT 'unverified',
    skill_level ENUM('beginner', 'intermediate', 'advanced', 'expert') DEFAULT 'intermediate',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_faculty_id (faculty_id),
    INDEX idx_certification_type (certification_type),
    INDEX idx_status (status),
    INDEX idx_expiry_date (expiry_date),
    
    FOREIGN KEY (submission_id) REFERENCES faculty_submissions(id) ON DELETE SET NULL,
    FOREIGN KEY (faculty_id) REFERENCES faculty_profile(id) ON DELETE CASCADE
);

-- 4. Faculty News Table (faculty-submitted news)
CREATE TABLE IF NOT EXISTS faculty_news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT,
    faculty_id INT NOT NULL,
    title VARCHAR(500) NOT NULL,
    content LONGTEXT,
    category ENUM('research', 'achievement', 'event', 'announcement', 'academic') DEFAULT 'announcement',
    featured_image VARCHAR(500),
    publish_date DATE,
    status ENUM('draft', 'submitted', 'approved', 'published', 'archived') DEFAULT 'draft',
    visibility ENUM('public', 'internal', 'department') DEFAULT 'public',
    author_byline VARCHAR(200),
    tags JSON,
    seo_meta JSON,
    social_media_ready BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL,
    
    INDEX idx_faculty_id (faculty_id),
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_publish_date (publish_date),
    
    FOREIGN KEY (submission_id) REFERENCES faculty_submissions(id) ON DELETE SET NULL,
    FOREIGN KEY (faculty_id) REFERENCES faculty_profile(id) ON DELETE CASCADE
);

-- 5. Faculty Activity Log (track all actions)
CREATE TABLE IF NOT EXISTS faculty_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    activity_type ENUM('profile_update', 'submission_create', 'submission_update', 'login', 'other') NOT NULL,
    description VARCHAR(500),
    related_id INT NULL,
    related_type VARCHAR(50) NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_faculty_id (faculty_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (faculty_id) REFERENCES faculty_profile(id) ON DELETE CASCADE
);

-- 6. Faculty Notifications Table
CREATE TABLE IF NOT EXISTS faculty_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    title VARCHAR(300) NOT NULL,
    message TEXT,
    type ENUM('info', 'success', 'warning', 'error', 'submission_update') DEFAULT 'info',
    related_id INT NULL,
    related_type VARCHAR(50) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    action_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    
    INDEX idx_faculty_id (faculty_id),
    INDEX idx_is_read (is_read),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (faculty_id) REFERENCES faculty_profile(id) ON DELETE CASCADE
);

-- 7. Approval Workflow Table
CREATE TABLE IF NOT EXISTS approval_workflow (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    step_number INT NOT NULL,
    reviewer_role ENUM('dean', 'admin', 'department_head') NOT NULL,
    reviewer_id INT NULL,
    status ENUM('pending', 'approved', 'rejected', 'skipped') DEFAULT 'pending',
    comments TEXT,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_submission_id (submission_id),
    INDEX idx_reviewer_role (reviewer_role),
    INDEX idx_status (status),
    
    FOREIGN KEY (submission_id) REFERENCES faculty_submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_submission_step (submission_id, step_number)
);

-- 8. Insert sample data
INSERT INTO faculty_submissions (faculty_id, submission_type, title, description, status, submitted_at) VALUES
(1, 'research', 'AI in Education: A Comprehensive Study', 'Research on implementing artificial intelligence in educational systems', 'submitted', NOW() - INTERVAL 2 DAY),
(1, 'certification', 'AWS Solutions Architect Professional', 'Professional certification in cloud architecture', 'under_review', NOW() - INTERVAL 1 DAY),
(2, 'news', 'Computer Science Department Wins Innovation Award', 'Announcement about recent departmental achievement', 'submitted', NOW() - INTERVAL 3 DAY),
(2, 'research', 'Cybersecurity Framework for SMEs', 'Developing security frameworks for small and medium enterprises', 'approved', NOW() - INTERVAL 5 DAY);

INSERT INTO faculty_research (faculty_id, title, abstract, research_area, status) VALUES
(1, 'AI in Education: A Comprehensive Study', 'This research explores the integration of artificial intelligence technologies in modern educational systems...', 'Artificial Intelligence', 'ongoing'),
(2, 'Cybersecurity Framework for SMEs', 'Development of comprehensive cybersecurity frameworks tailored for small and medium enterprises...', 'Cybersecurity', 'completed');

INSERT INTO faculty_certifications (faculty_id, certification_name, issuing_organization, certification_type, issue_date) VALUES
(1, 'AWS Solutions Architect Professional', 'Amazon Web Services', 'professional', '2024-08-15'),
(2, 'Certified Ethical Hacker (CEH)', 'EC-Council', 'professional', '2024-07-20');

INSERT INTO faculty_news (faculty_id, title, content, category, status) VALUES
(2, 'Computer Science Department Wins Innovation Award', 'The Computer Science Department has been recognized with the Innovation in Education Award...', 'achievement', 'submitted');

-- Create views for easy data retrieval
CREATE OR REPLACE VIEW faculty_dashboard_summary AS
SELECT 
    fp.id,
    fp.name,
    fp.dept,
    fp.role,
    COUNT(DISTINCT fs.id) as total_submissions,
    COUNT(DISTINCT CASE WHEN fs.status = 'submitted' THEN fs.id END) as pending_submissions,
    COUNT(DISTINCT CASE WHEN fs.status = 'approved' THEN fs.id END) as approved_submissions,
    COUNT(DISTINCT fr.id) as research_count,
    COUNT(DISTINCT fc.id) as certification_count,
    COUNT(DISTINCT fn.id) as news_count,
    MAX(fs.submitted_at) as last_submission
FROM faculty_profile fp
LEFT JOIN faculty_submissions fs ON fp.id = fs.faculty_id
LEFT JOIN faculty_research fr ON fp.id = fr.faculty_id
LEFT JOIN faculty_certifications fc ON fp.id = fc.faculty_id
LEFT JOIN faculty_news fn ON fp.id = fn.faculty_id
GROUP BY fp.id, fp.name, fp.dept, fp.role;