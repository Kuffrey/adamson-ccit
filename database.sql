-- Adamson CCIT Website Database Schema
CREATE DATABASE IF NOT EXISTS adamson_ccit;
USE adamson_ccit;

-- News Table
CREATE TABLE IF NOT EXISTS news (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Programs Table
CREATE TABLE IF NOT EXISTS programs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT NOT NULL
);

-- Faculty Table
CREATE TABLE IF NOT EXISTS faculty (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  profile TEXT NOT NULL
);

-- Research Table
CREATE TABLE IF NOT EXISTS research (
  id INT AUTO_INCREMENT PRIMARY KEY,
  faculty_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE
);

-- Certifications Table
CREATE TABLE IF NOT EXISTS certifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  faculty_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  issuer VARCHAR(255) NOT NULL,
  FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','faculty','student') NOT NULL
);

-- Admin Logs Table
CREATE TABLE IF NOT EXISTS admin_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id INT NOT NULL,
  action VARCHAR(255) NOT NULL,
  details TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Student Organizations Table
CREATE TABLE IF NOT EXISTS student_organizations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT NOT NULL
);

-- Student Scholarships Table
CREATE TABLE IF NOT EXISTS student_scholarships (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT NOT NULL
);

-- Student Testimonials Table
CREATE TABLE IF NOT EXISTS student_testimonials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_name VARCHAR(255) NOT NULL,
  testimonial TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Student Research Table
CREATE TABLE IF NOT EXISTS student_research (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_name VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL
);

-- Student Certifications Table
CREATE TABLE IF NOT EXISTS student_certifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_name VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  issuer VARCHAR(255) NOT NULL
);

-- Industry Partners Table
CREATE TABLE IF NOT EXISTS industry_partners (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  logo VARCHAR(255)
);

-- Career Pathway Generator Logs Table
CREATE TABLE IF NOT EXISTS career_pathway_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  answers TEXT NOT NULL,
  recommended_programs TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
