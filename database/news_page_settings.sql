-- news_page_settings.sql
-- Table for News page subhero lead and announcement (for CMS)

CREATE TABLE IF NOT EXISTS news_page_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subhero_lead VARCHAR(255) NOT NULL DEFAULT '',
    announcement TEXT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Example default row
INSERT INTO news_page_settings (subhero_lead, announcement) VALUES
('Stories from CCIT—research, achievements, announcements, and student life.', NULL)
ON DUPLICATE KEY UPDATE subhero_lead=VALUES(subhero_lead), announcement=VALUES(announcement);
