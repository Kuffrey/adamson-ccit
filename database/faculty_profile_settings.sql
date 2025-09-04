-- faculty_profile_settings.sql

CREATE TABLE IF NOT EXISTS faculty_profile_page_settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  subhero_image_url VARCHAR(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  subhero_lead TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS faculty_profile (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  dept VARCHAR(20) NOT NULL, -- admin, itis, cs
  role VARCHAR(20) NOT NULL, -- dean, chair, full, part, lecturer
  title VARCHAR(255),
  avatar_url VARCHAR(255),
  avatar_initials VARCHAR(4),
  badges TEXT, -- comma-separated badges
  ordering INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Page settings sample
INSERT INTO faculty_profile_page_settings (subhero_image_url, subhero_lead) VALUES (
  '/adamson-ccit/public/assets/images/hero-campus.jpg',
  'College of Computing & Information Technology — administration and faculty roster.'
);

-- Sample faculty (abbreviated for brevity)
INSERT INTO faculty_profile (name, dept, role, title, avatar_url, avatar_initials, badges, ordering) VALUES
('Dr. Leonard L. Alejandro', 'admin', 'dean', 'College Dean', NULL, 'LA', 'Administration,Dean', 1),
('Mr. Archie G. Santiago, MSIT', 'admin', 'chair', 'Chairperson, IT&IS Department', NULL, 'AS', 'Administration,Chairperson', 2),
('Ms. Ma. Christina R. Navarro', 'admin', 'chair', 'Chairperson, CS Department', NULL, 'CN', 'Administration,Chairperson', 3),
('Mrs. Charlene I. Gonzales-Vergara', 'itis', 'full', 'Full-Time Faculty, IT&IS Department', NULL, 'CV', 'IT&IS,Full-Time', 4),
('Jerome Alvez', 'cs', 'full', 'Full-Time Faculty, CS Department', NULL, 'JA', 'CS,Full-Time', 5),
('Jessie Alamil', 'cs', 'part', 'Part-Time Faculty, CS Department', NULL, 'JA', 'CS,Part-Time', 6),
('Davood Pour Yousefian Barfeh', 'cs', 'lecturer', 'Special Lecturer, CS Department', NULL, 'DB', 'CS,Special Lecturer', 7);
