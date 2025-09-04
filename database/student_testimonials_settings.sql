-- student_testimonials_settings.sql

CREATE TABLE IF NOT EXISTS student_testimonials_page_settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  subhero_image_url VARCHAR(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  subhero_lead TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS student_testimonial (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  program VARCHAR(20) NOT NULL, -- bscs, bsit, bsis, grad
  grad_year VARCHAR(10) NOT NULL,
  role VARCHAR(255),
  quote TEXT,
  avatar_url VARCHAR(255),
  avatar_initials VARCHAR(4),
  is_alumni TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Page settings sample
INSERT INTO student_testimonials_page_settings (subhero_image_url, subhero_lead) VALUES (
  '/adamson-ccit/public/assets/images/hero-campus.jpg',
  'Stories from CCIT students and alumni—internships, certifications, and early careers.'
);

-- Sample testimonials
INSERT INTO student_testimonial (name, program, grad_year, role, quote, avatar_url, avatar_initials, is_alumni) VALUES
('Jane Dela Cruz', 'bsit', '2025', 'Software Engineering Intern — FinTechPH', 'Hands-on labs prepared me well. I shipped features in my second week and passed IT Specialist — Networking on my first try.', NULL, 'JD', 0),
('Mark Santos', 'bscs', '2024', 'Junior Developer — DevWorks', 'Our capstone and algorithms track gave me the confidence to tackle production code. The culture pushed me to keep learning.', NULL, 'MS', 1),
('Bea Lim', 'bsis', '2025', 'Business Analyst Intern — RetailHub', 'The analytics focus and casework translated directly to my internship. I also cleared the IT Specialist — Databases exam.', NULL, 'BL', 0),
('Ramon Alvarez', 'grad', '2024', 'IT Manager — HealthTech', 'The graduate coursework sharpened my leadership and security foundations. It’s been a big step for my team and career.', NULL, 'RA', 1);
