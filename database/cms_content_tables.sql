-- CMS Content Tables for Adamson CCIT

-- Homepage Content
CREATE TABLE IF NOT EXISTS homepage_content (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hero_title VARCHAR(255),
  hero_subtitle VARCHAR(255),
  btn_primary_text VARCHAR(100),
  btn_primary_url VARCHAR(255),
  why_title VARCHAR(255),
  why_content TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- About Page Content
CREATE TABLE IF NOT EXISTS about_content (
  id INT AUTO_INCREMENT PRIMARY KEY,
  about_title VARCHAR(255),
  about_body TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- News
CREATE TABLE IF NOT EXISTS news (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  category VARCHAR(100),
  body TEXT,
  published_at DATETIME,
  status ENUM('draft','published') DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Events
CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  event_date DATE,
  location VARCHAR(255),
  description TEXT,
  status ENUM('draft','published') DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Announcements
CREATE TABLE IF NOT EXISTS announcements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  body TEXT,
  published_at DATETIME,
  status ENUM('draft','published') DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Programs
CREATE TABLE IF NOT EXISTS programs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255),
  description TEXT,
  curriculum_url VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Student Testimonials
CREATE TABLE IF NOT EXISTS student_testimonials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_name VARCHAR(255),
  testimonial TEXT,
  photo_url VARCHAR(255),
  status ENUM('draft','published') DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Student Research
CREATE TABLE IF NOT EXISTS student_research (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  authors VARCHAR(255),
  abstract TEXT,
  pdf_url VARCHAR(255),
  status ENUM('draft','published') DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Faculty Profile
CREATE TABLE IF NOT EXISTS faculty_profile (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255),
  position VARCHAR(255),
  bio TEXT,
  photo_url VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Faculty Research
CREATE TABLE IF NOT EXISTS faculty_research (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  authors VARCHAR(255),
  abstract TEXT,
  pdf_url VARCHAR(255),
  status ENUM('draft','published') DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Faculty Certifications
CREATE TABLE IF NOT EXISTS faculty_certifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  faculty_id INT,
  certification_name VARCHAR(255),
  issuer VARCHAR(255),
  year YEAR,
  description TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
