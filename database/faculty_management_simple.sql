-- faculty_management_simple.sql
-- Simplified Faculty Management System Database Schema for adamson_ccit

USE adamson_ccit;

-- 1. Faculty Submissions Table (for research, certifications, news)
CREATE TABLE IF NOT EXISTS faculty_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_profile_id INT NOT NULL,
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_faculty_profile_id (faculty_profile_id),
    INDEX idx_submission_type (submission_type),
    INDEX idx_status (status),
    INDEX idx_submitted_at (submitted_at)
);

-- 2. Faculty Activity Log (track all actions)
CREATE TABLE IF NOT EXISTS faculty_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_profile_id INT NOT NULL,
    activity_type ENUM('profile_update', 'submission_create', 'submission_update', 'login', 'other') NOT NULL,
    description VARCHAR(500),
    related_id INT NULL,
    related_type VARCHAR(50) NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_faculty_profile_id (faculty_profile_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_created_at (created_at)
);

-- 3. Faculty Notifications Table
CREATE TABLE IF NOT EXISTS faculty_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_profile_id INT NOT NULL,
    title VARCHAR(300) NOT NULL,
    message TEXT,
    type ENUM('info', 'success', 'warning', 'error', 'submission_update') DEFAULT 'info',
    related_id INT NULL,
    related_type VARCHAR(50) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    action_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    
    INDEX idx_faculty_profile_id (faculty_profile_id),
    INDEX idx_is_read (is_read),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at)
);

-- Insert sample data for testing
INSERT INTO faculty_submissions (faculty_profile_id, submission_type, title, description, status, submitted_at) VALUES
(1, 'research', 'AI in Education: A Comprehensive Study', 'Research on implementing artificial intelligence in educational systems to improve learning outcomes and personalized instruction.', 'submitted', NOW() - INTERVAL 2 DAY),
(1, 'certification', 'AWS Solutions Architect Professional', 'Professional certification in cloud architecture and solutions design for enterprise systems.', 'under_review', NOW() - INTERVAL 1 DAY),
(2, 'news', 'Computer Science Department Wins Innovation Award', 'Announcement about recent departmental achievement in innovative teaching methodologies.', 'submitted', NOW() - INTERVAL 3 DAY),
(2, 'research', 'Cybersecurity Framework for SMEs', 'Developing comprehensive security frameworks tailored for small and medium enterprises.', 'approved', NOW() - INTERVAL 5 DAY),
(3, 'certification', 'Google Cloud Professional Data Engineer', 'Professional certification in data engineering and analytics on Google Cloud Platform.', 'submitted', NOW() - INTERVAL 1 DAY),
(3, 'news', 'Faculty Excellence in Research Recognition', 'Recognition received for outstanding research contributions in machine learning.', 'approved', NOW() - INTERVAL 4 DAY),
(4, 'research', 'Mobile App Security in Healthcare', 'Research on security vulnerabilities and protection mechanisms in healthcare mobile applications.', 'submitted', NOW() - INTERVAL 3 DAY),
(4, 'certification', 'Certified Information Systems Security Professional', 'CISSP certification for information security management and governance.', 'under_review', NOW() - INTERVAL 2 DAY);

-- Insert sample activity log data
INSERT INTO faculty_activity_log (faculty_profile_id, activity_type, description, created_at) VALUES
(1, 'submission_create', 'Submitted research: AI in Education Study', NOW() - INTERVAL 2 DAY),
(1, 'submission_create', 'Submitted certification: AWS Solutions Architect', NOW() - INTERVAL 1 DAY),
(2, 'submission_create', 'Submitted news: Department Innovation Award', NOW() - INTERVAL 3 DAY),
(2, 'profile_update', 'Updated profile information', NOW() - INTERVAL 4 DAY),
(3, 'submission_create', 'Submitted certification: Google Cloud Professional', NOW() - INTERVAL 1 DAY),
(3, 'submission_create', 'Submitted news: Research Recognition', NOW() - INTERVAL 4 DAY),
(4, 'submission_create', 'Submitted research: Mobile App Security', NOW() - INTERVAL 3 DAY),
(4, 'submission_create', 'Submitted certification: CISSP', NOW() - INTERVAL 2 DAY);

-- Insert sample notifications
INSERT INTO faculty_notifications (faculty_profile_id, title, message, type, created_at) VALUES
(1, 'Submission Under Review', 'Your AWS certification submission is being reviewed by the dean.', 'info', NOW() - INTERVAL 1 DAY),
(2, 'Submission Approved', 'Your cybersecurity research has been approved and will be published.', 'success', NOW() - INTERVAL 5 DAY),
(3, 'New Submission Required', 'Please submit your quarterly research progress report.', 'warning', NOW() - INTERVAL 2 DAY),
(4, 'Certification Verification', 'Your CISSP certification is being verified with the issuing organization.', 'info', NOW() - INTERVAL 2 DAY);

-- Create view for easy dashboard data
CREATE OR REPLACE VIEW faculty_dashboard_summary AS
SELECT 
    fp.id,
    fp.name,
    fp.dept,
    fp.role,
    COUNT(DISTINCT fs.id) as total_submissions,
    COUNT(DISTINCT CASE WHEN fs.status IN ('submitted', 'under_review') THEN fs.id END) as pending_submissions,
    COUNT(DISTINCT CASE WHEN fs.status = 'approved' THEN fs.id END) as approved_submissions,
    COUNT(DISTINCT CASE WHEN fs.submission_type = 'research' AND fs.status IN ('submitted', 'under_review') THEN fs.id END) as pending_research,
    COUNT(DISTINCT CASE WHEN fs.submission_type = 'certification' AND fs.status IN ('submitted', 'under_review') THEN fs.id END) as pending_certifications,
    COUNT(DISTINCT CASE WHEN fs.submission_type = 'news' AND fs.status IN ('submitted', 'under_review') THEN fs.id END) as pending_news,
    MAX(fs.submitted_at) as last_submission,
    MAX(fal.created_at) as last_activity
FROM faculty_profile fp
LEFT JOIN faculty_submissions fs ON fp.id = fs.faculty_profile_id
LEFT JOIN faculty_activity_log fal ON fp.id = fal.faculty_profile_id
GROUP BY fp.id, fp.name, fp.dept, fp.role;

-- Show table creation results
SHOW TABLES LIKE 'faculty_%';
SELECT COUNT(*) as submission_count FROM faculty_submissions;
SELECT COUNT(*) as activity_count FROM faculty_activity_log;
SELECT COUNT(*) as notification_count FROM faculty_notifications;