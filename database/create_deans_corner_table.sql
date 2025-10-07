-- Table for Dean's Corner info
CREATE TABLE IF NOT EXISTS deans_corner (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  title VARCHAR(150) NOT NULL,
  photo VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Example initial data
INSERT INTO deans_corner (name, title, photo, message) VALUES (
  'Dr. John Doe',
  'Dean, College of Computing and Information Technology',
  '/adamson-ccit/public/assets/images/dean-john-doe.jpg',
  'Welcome to the College of Computing and Information Technology at Adamson University! Our commitment is to provide world-class education, foster innovation, and prepare our students for successful careers in IT and computing. We invite you to explore our programs, research, and vibrant community.'
);
