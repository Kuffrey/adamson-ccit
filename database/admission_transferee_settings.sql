-- admission_transferee_settings.sql
-- Table for Transferee Admission page content (for CMS)

CREATE TABLE IF NOT EXISTS admission_transferee_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subhero_lead VARCHAR(255) NOT NULL DEFAULT '',
    how_to_apply TEXT DEFAULT NULL,
    requirements TEXT DEFAULT NULL,
    enrollment_procedure TEXT DEFAULT NULL,
    enrollment_note TEXT DEFAULT NULL,
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
INSERT INTO admission_transferee_settings (
    subhero_lead, how_to_apply, requirements, enrollment_procedure, enrollment_note, sidebar_office, sidebar_links, sidebar_image_url, sidebar_image_caption, cta_title, cta_description, cta_action_label, cta_action_url
) VALUES (
    'For students transferring from other colleges or universities.',
    '<h2>How to Apply</h2><p>Apply online and prepare the required documents for evaluation and interview.</p>',
    '<ul><li>Original True Copy of Grades</li><li>Original Certificate of Good Moral Character</li><li>Original Transfer Credentials / Honorable Dismissal</li><li>Letter of Application</li><li>Clear copy of PSA/NSO Birth Certificate</li><li>Two (2) pieces 2×2 ID photo (white background)</li></ul>',
    '<ol><li>Submit all original credentials to the Admissions Office, including the approved accreditation of courses.</li><li>Pay the non-refundable down payment of <strong>Php 5,000</strong>.</li><li>Using your student number (as <em>USERNAME</em>) and your assigned temporary password, access your eLearning account at <a class="link" href="https://learn.adamson.edu.ph" target="_blank" rel="noopener">learn.adamson.edu.ph</a>.</li><li>Go to <strong>Subject Enlistment</strong> &rarr; <strong>Proceed to Subject Enlistment</strong>.</li><li>From <strong>Pre-Advised Subjects</strong>, choose schedules/sections for each subject. Add, edit, or delete as needed, then <strong>Save</strong>.</li><li>Print your <strong>Certificate of Enrollment</strong> (with Assessment of Fees).</li><li>Proceed to the <strong>ID Section</strong> for ID processing.</li><li>Proceed to the <strong>University Store</strong> to purchase the school uniform.</li><li>Attend the <strong>Freshmen/Transferee Orientation</strong> scheduled by the Office for Student Affairs.</li></ol>',
    '<div class="note"><strong>Note:</strong> Subject enlistment is on a <em>first-come, first-serve</em> basis. “Pre-Advised Subjects” are those recommended for your next term/semester.</div>',
    '<h3>Admissions &amp; Student Recruitment Office</h3><ul><li><strong>Hours:</strong> 8:00 AM – 12:00 NN; 1:00 – 5:00 PM</li><li><strong>Direct Line:</strong> <a class="link" href="tel:+63283549267">(02) 8354-9267</a></li><li><strong>Trunkline:</strong> <a class="link" href="tel:+63285242011">(02) 8524-2011</a> <small>loc. 102</small></li><li><strong>Email:</strong> <a class="link" href="mailto:admission@adamson.edu.ph">admission@adamson.edu.ph</a></li></ul>',
    '<ul><li><a class="link" href="/adamson-ccit/public/index.php?page=programs_undergraduate">CCIT Programs</a></li><li><a class="link" href="/adamson-ccit/public/index.php?page=student_scholarships">Scholarships</a></li><li><a class="link" href="/adamson-ccit/public/index.php?page=student_organizations">Student Life</a></li></ul>',
    '/adamson-ccit/public/assets/images/admissions/transferee.jpg',
    'Welcome, future Falcons—transfer your journey to CCIT.',
    'Ready to transfer to CCIT?',
    'Apply online and we’ll guide you through evaluation, interview, and enlistment.',
    'Apply Now',
    'https://www.adamson.edu.ph/cfe'
);
