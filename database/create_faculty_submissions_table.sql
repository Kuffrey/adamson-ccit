-- Create faculty_submissions table for the dean approval system
USE adamson_ccit;

CREATE TABLE IF NOT EXISTS faculty_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    submission_type ENUM('research', 'news', 'publication', 'certification') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content LONGTEXT,
    category VARCHAR(100),
    related_item_id INT NULL COMMENT 'ID of the related research or certification record',
    status ENUM('draft', 'submitted', 'under_review', 'approved', 'rejected') DEFAULT 'submitted',
    submitted_at TIMESTAMP NULL,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    review_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_faculty_id (faculty_id),
    INDEX idx_status (status),
    INDEX idx_submission_type (submission_type),
    INDEX idx_submitted_at (submitted_at),
    INDEX idx_related_item_id (related_item_id),
    
    FOREIGN KEY (faculty_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);
