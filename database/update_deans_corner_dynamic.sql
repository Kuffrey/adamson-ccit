-- Add 'subhero_image', 'cta_title', 'cta_body', and 'cta_btn_label' columns to deans_corner table
ALTER TABLE deans_corner 
  ADD COLUMN subhero_image VARCHAR(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg' AFTER photo,
  ADD COLUMN cta_title VARCHAR(255) DEFAULT 'Connect with the Dean' AFTER message,
  ADD COLUMN cta_body TEXT DEFAULT 'Have questions or want to learn more about CCIT? Reach out to our office for more information.' AFTER cta_title,
  ADD COLUMN cta_btn_label VARCHAR(64) DEFAULT 'Email the Dean' AFTER cta_body;

-- Example: Update values
UPDATE deans_corner SET 
  subhero_image = '/adamson-ccit/public/assets/images/hero-campus.jpg',
  cta_title = 'Connect with the Dean',
  cta_body = 'Have questions or want to learn more about CCIT? Reach out to our office for more information.',
  cta_btn_label = 'Email the Dean'
WHERE id = 1;