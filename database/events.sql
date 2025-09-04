-- Drop and recreate the events table for a clean, modern CMS structure
DROP TABLE IF EXISTS events;

CREATE TABLE events (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  location VARCHAR(255),
  category VARCHAR(64),
  image_url VARCHAR(255),
  start_at DATETIME,
  end_at DATETIME,
  url VARCHAR(255),
  status ENUM('draft','published','archived') DEFAULT 'published',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Example insert for your LLM event
INSERT INTO events (title, description, location, category, image_url, start_at, end_at, url, status)
VALUES (
  'A Practical Approach to Large Language Models (LLMs) & Retrieval Augmented Generation (RAG)',
  'Adamson Center for Executive Studies, in partnership with Straits Interactive, invites you to an exclusive info session for our newest course offering. This course is for non-developers and professionals with no prior AI background who want to learn how to build custom, no-code AI applications.\n\nCourse Preview Session (via Zoom)\nDate: September 3, 2025 (Wednesday)\nTime: 5:00 PM – 6:00 PM (PHT)\nRegister: https://us06web.zoom.us/.../register/CY0CzBVoQlWRuQ7Zqu3qsg\nLearn more: https://outreach.straitsinteractive.com/courses-philippines\n#AdamsonUniversity',
  'Online (Zoom)',
  'workshop',
  NULL,
  '2025-09-03 17:00:00',
  '2025-09-03 18:00:00',
  'https://us06web.zoom.us/.../register/CY0CzBVoQlWRuQ7Zqu3qsg',
  'published'
);
