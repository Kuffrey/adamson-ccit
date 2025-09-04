-- faculty_research_settings.sql

CREATE TABLE IF NOT EXISTS faculty_research_page_settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  subhero_image_url VARCHAR(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  subhero_lead TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS faculty_research (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  authors TEXT,
  dept VARCHAR(20), -- itis, cs
  type VARCHAR(20), -- journal, conference, chapter, patent, other
  year VARCHAR(10),
  venue VARCHAR(255),
  pdf_url VARCHAR(255),
  view_url VARCHAR(255),
  image_url VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Page settings sample
INSERT INTO faculty_research_page_settings (subhero_image_url, subhero_lead) VALUES (
  '/adamson-ccit/public/assets/images/hero-campus.jpg',
  'Peer-reviewed publications, presentations, and other scholarly work by CCIT faculty.'
);

-- Sample research entries
INSERT INTO faculty_research (title, authors, dept, type, year, venue, pdf_url, view_url, image_url) VALUES
('A Study on Secure IoT Protocols', 'Alejandro, L.; Santiago, A.', 'itis', 'journal', '2025', 'International Journal of IoT Security', '/adamson-ccit/public/assets/papers/iot-secure.pdf', '#', '/adamson-ccit/public/assets/images/placeholder-16x9.jpg'),
('AI in Education: A Philippine Perspective', 'Navarro, C.; Gonzales-Vergara, C.', 'cs', 'conference', '2024', 'Proceedings of EduTech Asia', '/adamson-ccit/public/assets/papers/ai-edu.pdf', '#', '/adamson-ccit/public/assets/images/placeholder-16x9.jpg');
