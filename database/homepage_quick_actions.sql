-- Quick Actions Table for Homepage
CREATE TABLE IF NOT EXISTS homepage_quick_actions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(100) NOT NULL,
  icon TEXT NOT NULL COMMENT 'SVG icon code',
  url VARCHAR(255) NOT NULL,
  display_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default quick actions data
INSERT INTO homepage_quick_actions (label, icon, url, display_order, is_active) VALUES
('Admissions', '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M5 4h14a1 1 0 0 1 1 1v13l-3-2-3 2-3-2-3 2-3-2V5a1 1 0 0 1 1-1z"/></svg>', '/adamson-ccit/public/index.php?page=admission_freshman', 1, 1),
('Programs', '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>', '/adamson-ccit/public/index.php?page=programs_undergraduate', 2, 1),
('Scholarships', '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M12 2a7 7 0 1 1-4.95 2.05A7 7 0 0 1 12 2zm-1 8h2v6h-2zm0 8h2v2h-2z"/></svg>', '/adamson-ccit/public/index.php?page=student_scholarships', 3, 1),
('Student Life', '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M12 2a5 5 0 1 1-5 5 5 5 0 0 1 5-5Zm8 18v-2H4v-2a6 6 0 0 1 8-5.29A6 6 0 0 1 20 20Z"/></svg>', '/adamson-ccit/public/index.php?page=student_organizations', 4, 1),
('Faculty', '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-7 9v-2a7 7 0 0 1 14 0v2Z"/></svg>', '/adamson-ccit/public/index.php?page=faculty_profile', 5, 1),
('News', '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M4 4h16v2H4zm0 4h10v2H4zm0 4h16v2H4zm0 4h10v2H4z"/></svg>', '/adamson-ccit/public/index.php?page=news', 6, 1);