-- Table for Events Page Settings (subhero, announcement)
DROP TABLE IF EXISTS events_page_settings;
CREATE TABLE events_page_settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  subhero_lead TEXT,
  announcement TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default row (run only once)
INSERT INTO events_page_settings (subhero_lead, announcement)
VALUES (
  'Career fairs, forums, workshops, and student showcases happening at CCIT.',
  NULL
);
