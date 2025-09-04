-- student_scholarships_settings.sql

CREATE TABLE IF NOT EXISTS student_scholarships_page_settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  subhero_image_url VARCHAR(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  subhero_lead TEXT,
  note TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS student_scholarship (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(150) NOT NULL,
  type ENUM('Freshmen','University','External') NOT NULL,
  summary TEXT,
  conditions TEXT,
  requirements TEXT,
  examples TEXT,
  learn_more_url VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Page settings sample
INSERT INTO student_scholarships_page_settings (subhero_image_url, subhero_lead, note) VALUES (
  '/adamson-ccit/public/assets/images/hero-campus.jpg',
  'Financial aid options for incoming and current Adamson students.',
  'For new calls, slots, and deadlines, follow OSAS: <a class="ext" href="https://www.facebook.com/AdamsonU.osas/" target="_blank" rel="noopener">Facebook</a>.'
);

-- Sample scholarships
INSERT INTO student_scholarship (name, type, summary, conditions, requirements, examples, learn_more_url) VALUES
('Scholarships for Freshmen Students', 'Freshmen', 'Rank&nbsp;1: <strong>100% tuition</strong> (1st &amp; 2nd sem). Rank&nbsp;2: <strong>50% tuition</strong> (1st &amp; 2nd sem). Maintain no grade below 2.5 and pass NSTP in 1st sem.', '<ul class="bullets"><li>Graduate of a government-recognized school.</li><li>School with <strong>≥100 graduates</strong> (else Registrar evaluation; good for one sem only).</li><li><strong>Certificate of Honor</strong> with dry seal &amp; total number of graduates.</li></ul>', NULL, NULL, 'https://www.adamson.edu.ph/v1/?page=freshmen-scholarship'),
('Academic Scholarship Program (ASP)', 'University', 'Tuition coverage for <strong>regular load only</strong>. Highly selective; outstanding GWA and clean academic record required.', '<ul class="bullets"><li>Failing (5.0) or Dropped (130)</li><li>Not Attending (120) or No Grade OBE (140)</li><li>Special Consideration (150) or Unofficial Withdrawal (0.0)</li></ul>', NULL, NULL, 'https://www.adamson.edu.ph/v1/?page=academic-scholarship-program'),
('Corporate / Foundation / Individual Sponsorships', 'External', 'Full (100%) or partial (50%) support in tuition/misc. Maintain sponsor-required GWA; <strong>no dropped/failed/incomplete</strong>. Join at least <strong>2 OSAS/college activities</strong> per semester.', '<ul class="bullets"><li>Letter of intent to the VP for Student Affairs.</li><li>Required course; good moral character.</li><li>Form 138 (GWA ≥88%) or sem GWA ≤<strong>1.75</strong>, no drops/fails/incompletes.</li></ul>', NULL, '<ul class="bullets"><li>CHED UniFAST, GBF, Megaworld, Petron Foundation</li><li>San Miguel Foundation, LCCK, Rotary Club of Manila Bay</li><li>NROTC tuition discounts (25–100% by rank)</li></ul>', 'https://www.adamson.edu.ph/v1/?page=corporate-foundation-individual-sponsorships');
