

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS faculty_certification_award;
DROP TABLE IF EXISTS certification;
DROP TABLE IF EXISTS faculty;
DROP TABLE IF EXISTS faculty_certifications_page_settings;
SET FOREIGN_KEY_CHECKS = 1;



-- Clean, minimal, correct schema for a fresh database
CREATE TABLE faculty_certifications_page_settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  subhero_image_url VARCHAR(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  subhero_lead TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE faculty (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  dept VARCHAR(20), -- itis, cs
  profile TEXT,
  photo_url VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE certification (
  id INT PRIMARY KEY AUTO_INCREMENT,
  cert_title VARCHAR(255) NOT NULL,
  issuer VARCHAR(50),
  issuer_key VARCHAR(32), -- for filtering (e.g. aws, cisco, etc)
  badge_url VARCHAR(255),
  cert_url VARCHAR(255),
  verify_url VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE faculty_certification_award (
  id INT PRIMARY KEY AUTO_INCREMENT,
  faculty_id INT NOT NULL,
  certification_id INT NOT NULL,
  year_earned VARCHAR(10),
  year_expiry VARCHAR(10),
  status VARCHAR(20), -- Active, Expired, etc
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
  FOREIGN KEY (certification_id) REFERENCES certification(id) ON DELETE CASCADE
);


-- Page settings sample
INSERT INTO faculty_certifications_page_settings (subhero_image_url, subhero_lead) VALUES (
  '/adamson-ccit/public/assets/images/hero-campus.jpg',
  'Professional badges, licenses, and industry certifications held by CCIT faculty.'
);

INSERT INTO faculty (name, dept, profile, photo_url) VALUES
('Dr. Jane Q. Faculty', 'itis', 'Expert in cloud computing and distributed systems.', '/adamson-ccit/public/assets/images/faculty/jane-faculty.jpg'),
('Mr. John D. Instructor', 'cs', 'Specialist in networking and security.', '/adamson-ccit/public/assets/images/faculty/john-instructor.jpg');

-- Sample certifications
INSERT INTO certification (cert_title, issuer, issuer_key, badge_url, cert_url, verify_url) VALUES
('AWS Certified Solutions Architect – Associate', 'AWS', 'aws', '/adamson-ccit/public/assets/images/certs/aws-solutions-arch.png', '#', '#'),
('Cisco Certified Network Associate (CCNA)', 'Cisco', 'cisco', '/adamson-ccit/public/assets/images/certs/cisco-ccna.png', '#', '#');

-- Sample awarded certifications
INSERT INTO faculty_certification_award (faculty_id, certification_id, year_earned, year_expiry, status) VALUES
(1, 1, '2024', '2027', 'Active'),
(2, 2, '2023', '2026', 'Active');
