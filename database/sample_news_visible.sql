-- Corrected sample news article for visibility on the News page
INSERT INTO news (title, excerpt, body, category, image_url, date, author, status, created_at, updated_at)
VALUES (
  'AI in Academy: Promoting Its Responsible and Effective Use in Education',
  'Adamson University underscored its commitment to ethical innovation in higher education with a full-day AI training for faculty.',
  'Adamson University underscored its commitment to ethical innovation in higher education with AI in Academy: Promoting Its Responsible and Effective Use in Education, a full-day training spearheaded by Center for Innovative Learning (CIL) held on June 2 at the Co Po Ty Hall in the Carlos Tiu Building.  \nThe event opened the university’s first-ever 2025 Mid-Year Inset and gathered faculty members across all colleges for an intensive program on integrating artificial intelligence (AI) into the academic landscape — guided not only by innovation but by values of ethics, inclusion, and responsibility.',
  'news',
  '/adamson-ccit/public/assets/images/news/sample1.jpg',
  '2025-06-02',
  'CCIT Communications',
  'published',
  NOW(),
  NOW()
);
