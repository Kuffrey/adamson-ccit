-- Sample news article for news table
INSERT INTO news (title, excerpt, body, category, image_url, date, author, status, created_at, updated_at)
VALUES (
  'CCIT Launches New AI Research Lab',
  'Adamson CCIT opens a state-of-the-art AI research facility to foster innovation and student research.',
  '<p>The College of Computer Science, Information Technology, and Engineering (CCIT) at Adamson University has inaugurated a new AI Research Lab. The facility is equipped with the latest hardware and software to support advanced research in artificial intelligence, machine learning, and data science. Faculty and students are encouraged to collaborate on projects that address real-world problems and contribute to the growing field of AI.</p><p>The launch event was attended by university officials, industry partners, and student leaders. The Dean of CCIT emphasized the importance of research and innovation in preparing students for the future of technology.</p>',
  'research',
  '/adamson-ccit/public/assets/images/news/sample1.jpg',
  '2025-08-15',
  'CCIT Communications',
  'published',
  NOW(),
  NOW()
);
