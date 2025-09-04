-- admission_freshman_settings.sql
-- Table for Freshman Admission page content (for CMS)

CREATE TABLE IF NOT EXISTS admission_freshman_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subhero_lead VARCHAR(255) NOT NULL DEFAULT '',
    how_to_apply TEXT DEFAULT NULL,
    initial_uploads TEXT DEFAULT NULL,
    requirements_shs TEXT DEFAULT NULL,
    requirements_als TEXT DEFAULT NULL,
    requirements_abroad TEXT DEFAULT NULL,
    enrollment_procedure TEXT DEFAULT NULL,
    sidebar_office TEXT DEFAULT NULL,
    sidebar_links TEXT DEFAULT NULL,
    sidebar_image_url VARCHAR(255) DEFAULT NULL,
    sidebar_image_caption VARCHAR(255) DEFAULT NULL,
    cta_title VARCHAR(255) DEFAULT NULL,
    cta_description VARCHAR(255) DEFAULT NULL,
    cta_action_label VARCHAR(255) DEFAULT NULL,
    cta_action_url VARCHAR(255) DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sample content
INSERT INTO admission_freshman_settings (
    subhero_lead, how_to_apply, initial_uploads, requirements_shs, requirements_als, requirements_abroad, enrollment_procedure, sidebar_office, sidebar_links, sidebar_image_url, sidebar_image_caption, cta_title, cta_description, cta_action_label, cta_action_url
) VALUES (
    'Start your CCIT journey with industry-aligned learning and strong student support.',
    '<h2>How to Apply</h2><p>Submit an online application through the official Adamson portal and upload the initial evaluation files.</p>',
    '<ul><li>Scanned back-to-back (JPEG or PDF) Grade 12 Report Card (DepEd F-138) or Grade 12 1st Quarter grade; or Certificate of Rating for ALS/PEPT passers.</li><li>Latest 2×2 picture, white background.</li></ul>',
    '<ul><li>Original Grade 12 Report Card (Form 138) with eligibility for college admission, signed by the school principal.</li><li>Original Certificate of Good Moral Character (dated not earlier than February of the graduation year; with school seal).</li><li>Clear copy of PSA Birth Certificate.</li><li>Two (2) pcs 2×2 ID picture.</li></ul>',
    '<ul><li>Certificate of Rating (passing marks in all subjects).</li><li>Clear copy of PSA Birth Certificate.</li><li>Original Certificate of Good Moral Character (with school seal).</li></ul>',
    '<div><strong>Graduates from abroad (International Curriculum):</strong> All documents must be authenticated / <em>apostille-stamped</em> by the Philippine Embassy or Consulate in the country where the school is located.</div>',
    '<ol><li>Submit all original credentials to the Admissions Office.</li><li>Pay the non-refundable down payment of Php 5,000.</li><li>Get your Certificate of Registration (COR).</li><li>Proceed to the ID Section for ID processing.</li><li>Proceed to the University Store for school uniform purchase.</li><li>Attend the Freshmen/Transferee Orientation scheduled by the Office for Student Affairs.</li></ol><p class="disclaimer">Information may change without prior notice. Please verify via the official Adamson admissions portal.</p>',
    '<h3>Admissions &amp; Student Recruitment Office</h3><ul><li><strong>Hours:</strong> 8:00 AM – 12:00 NN; 1:00 – 5:00 PM</li><li><strong>Direct Line:</strong> <a class="link" href="tel:+63283549267">(02) 8354-9267</a></li><li><strong>Trunkline:</strong> <a class="link" href="tel:+63285242011">(02) 8524-2011</a> <small>loc. 102</small></li><li><strong>Email:</strong> <a class="link" href="mailto:admission@adamson.edu.ph">admission@adamson.edu.ph</a></li></ul>',
    '<ul><li><a class="link" href="https://www.adamson.edu.ph/cfe" target="_blank" rel="noopener">Admissions Portal</a></li><li><a class="link" href="/adamson-ccit/public/index.php?page=programs_undergraduate">CCIT Undergraduate Programs</a></li><li><a class="link" href="/adamson-ccit/public/index.php?page=student_scholarships">Scholarships</a></li><li><a class="link" href="/adamson-ccit/public/index.php?page=student_organizations">Student Life</a></li></ul>',
    '/adamson-ccit/public/assets/images/admissions/freshman.jpg',
    'Welcome, future Falcons—start your CCIT journey at Adamson.',
    'Explore CCIT Programs',
    'Match your interests to a pathway in Computing and IT.',
    'See Programs',
    '/adamson-ccit/public/index.php?page=programs_undergraduate'
);
