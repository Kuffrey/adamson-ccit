-- student_certifications_settings.sql

CREATE TABLE IF NOT EXISTS student_certifications_page_settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  subhero_image_url VARCHAR(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  subhero_lead TEXT,
  cstat_note TEXT,
  certs_note TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS student_certification_stat (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  rate VARCHAR(20) NOT NULL,
  tag VARCHAR(50),
  meta VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS student_certification (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(150) NOT NULL,
  badge_url VARCHAR(255),
  issuer VARCHAR(100),
  description TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Page settings sample
INSERT INTO student_certifications_page_settings (subhero_image_url, subhero_lead, cstat_note, certs_note) VALUES (
  '/adamson-ccit/public/assets/images/hero-campus.jpg',
  'Industry badges aligned with CCIT courses and labs.',
  'If an exam isn’t listed here, its passing rate will be posted when available.',
  'Certification windows & registration are announced by the department through official CCIT channels and your instructors. Posts include dates, fees (if any), seat counts, and step-by-step registration.'
);

-- Sample passing rates
INSERT INTO student_certification_stat (title, rate, tag, meta) VALUES
('IT Specialist — Cybersecurity', '100%', 'Passing rate', 'BS Computer Science'),
('IT Specialist — Network Security', '99.53%', 'Passing rate', 'BS Information Technology & BS Information Systems'),
('IT Specialist — Networking', '99.02%', 'Passing rate', 'BS Computer Science'),
('IT Specialist — Databases', '96.11%', 'Passing rate', 'BS Information Technology & BS Information Systems'),
('IT Specialist — Databases', '95.62%', 'Passing rate', 'BS Computer Science & BSCS–BSIE (Dual)');

-- Sample certifications
INSERT INTO student_certification (name, badge_url, issuer, description) VALUES
('IT Specialist – Networking', '/adamson-ccit/public/assets/images/certs/its-networking.png', 'Certiport', 'Foundational networking knowledge and skills: TCP/IP, networking services, topologies, and troubleshooting for wired and wireless environments.'),
('IT Specialist – Network Security', '/adamson-ccit/public/assets/images/certs/its-network-security.png', 'Certiport', 'Core security principles; OS, network, and device security; secure computing practices.'),
('IT Specialist – Cybersecurity', '/adamson-ccit/public/assets/images/certs/its-cybersecurity.png', 'Certiport', 'Baseline cybersecurity skills including threats, vulnerabilities, controls, and basic incident response.'),
('IT Specialist – Databases', '/adamson-ccit/public/assets/images/certs/its-databases.png', 'Certiport', 'Designing and querying relational databases (e.g., MySQL, Microsoft SQL Server, Oracle).');
