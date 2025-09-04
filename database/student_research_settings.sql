-- student_research_settings.sql

CREATE TABLE IF NOT EXISTS student_research_page_settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  subhero_image_url VARCHAR(255) DEFAULT '/adamson-ccit/public/assets/images/hero-research.jpg',
  subhero_lead TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS student_research (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  category ENUM('publication','project','award') NOT NULL,
  year YEAR NOT NULL,
  image_url VARCHAR(255),
  link_url VARCHAR(255),
  link_label VARCHAR(100),
  meta TEXT,
  authors TEXT,
  description TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Page settings sample
INSERT INTO student_research_page_settings (subhero_image_url, subhero_lead) VALUES (
  '/adamson-ccit/public/assets/images/hero-research.jpg',
  'Publications and conference papers by our students and faculty mentors.'
);

-- Sample research
INSERT INTO student_research (title, category, year, image_url, link_url, link_label, meta, authors, description) VALUES
('Species Classification and Counter for Mixed-Species Bird Flock Using ResNet9 and YOLOv5', 'publication', 2024, '/adamson-ccit/public/assets/images/research/bird-flock.jpg', 'https://doi.org/10.1109/ICBIR61386.2024.10875872', 'Read on IEEE Xplore', 'ICBIR 2024 — Bangkok, Thailand • DOI: 10.1109/ICBIR61386.2024.10875872', NULL, NULL),
('Development of Employment Tracking System with File Routing for HR Management (AdU)', 'publication', 2024, '/adamson-ccit/public/assets/images/research/ets-hrm.jpg', 'https://doi.org/10.1109/ICBIR61386.2024.10875844', 'Read on IEEE Xplore', 'ICBIR 2024 — Bangkok, Thailand • DOI: 10.1109/ICBIR61386.2024.10875844', NULL, NULL),
('Unified NN Framework for Real-time Detection of Early Longitudinal Melanonychia', 'publication', 2025, '/adamson-ccit/public/assets/images/research/melanonychia.jpg', 'https://doi.org/10.1109/AIIT63112.2025.11082862', 'Read on IEEE Xplore', 'AIIT 2025 — University of Jeddah • DOI: 10.1109/AIIT63112.2025.11082862', NULL, NULL);
