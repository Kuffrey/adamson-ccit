-- student_organizations_settings.sql

-- New normalized structure: one row per org, plus a settings table for subhero
CREATE TABLE IF NOT EXISTS student_organizations_page_settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  subhero_image_url VARCHAR(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  subhero_lead TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS student_organization (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  type ENUM('Academic','Co-Academic') NOT NULL,
  logo_url VARCHAR(255) NOT NULL,
  summary TEXT,
  audience VARCHAR(255),
  facebook_url VARCHAR(255),
  instagram_url VARCHAR(255),
  x_url VARCHAR(255),
  learn_more_url VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Page settings sample
INSERT INTO student_organizations_page_settings (subhero_image_url, subhero_lead) VALUES (
  '/adamson-ccit/public/assets/images/hero-campus.jpg',
  'Official academic and co-academic organizations for CCIT students.'
);

-- Sample orgs
INSERT INTO student_organization (name, type, logo_url, summary, audience, facebook_url, instagram_url, x_url, learn_more_url) VALUES
('AdU IT&IS Society', 'Academic', '/adamson-ccit/public/assets/images/orgs/aduitis.jpg', 'The Adamson University Information Technology & Information Systems Society is a recognized academic, non-profit organization embodied by the BSIT & BSIS students of Adamson University.', 'BSIT, BSIS', 'https://www.facebook.com/AdU.IT.and.IS.Society/', 'https://www.instagram.com/officialaduitissociety/', 'https://x.com/aduitissociety', 'https://www.adamson.edu.ph/v1/?page=organization&org=22'),
('ACOMSS', 'Academic', '/adamson-ccit/public/assets/images/orgs/acomss.png', 'The Adamson Computer Science Society is a recognized academic student organization composed of Computer Science students of Adamson University.', 'BSCS', 'https://www.facebook.com/ACOMSSofficial/', 'https://www.instagram.com/acomss_official/', NULL, 'https://www.adamson.edu.ph/v1/?page=organization&org=5'),
('AdU GAME', 'Co-Academic', '/adamson-ccit/public/assets/images/orgs/adugame.jpg', 'The Adamson University Guild of Animation Makers and Esports is a co-academic organization that empowers computer animation enthusiasts and esports players in AdU.', 'Animation & Esports', 'https://www.facebook.com/AdUGAMEOfficial/', NULL, NULL, 'https://www.adamson.edu.ph/v1/?page=organization&org=83');
