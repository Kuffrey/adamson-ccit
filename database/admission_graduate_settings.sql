-- admission_graduate_settings.sql
-- Table for Graduate School & JD Admission page content (for CMS)

CREATE TABLE IF NOT EXISTS admission_graduate_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subhero_lead VARCHAR(255) NOT NULL DEFAULT '',
    how_to_apply TEXT DEFAULT NULL,
    initial_uploads TEXT DEFAULT NULL,
    qualifications_masters TEXT DEFAULT NULL,
    qualifications_doctoral TEXT DEFAULT NULL,
    qualifications_jd TEXT DEFAULT NULL,
    requirements_enrollment TEXT DEFAULT NULL,
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
INSERT INTO admission_graduate_settings (
    subhero_lead, how_to_apply, initial_uploads, qualifications_masters, qualifications_doctoral, qualifications_jd, requirements_enrollment, enrollment_procedure, sidebar_office, sidebar_links, sidebar_image_url, sidebar_image_caption, cta_title, cta_description, cta_action_label, cta_action_url
) VALUES (
    'Advance your career through Master’s, Doctoral, and JD programs at Adamson University.',
    '<h2>How to Apply</h2><p>Apply online through the official Adamson portal and upload the required files for initial evaluation.</p>',
    '<ul><li>Transcript of Records (TOR)</li><li>Résumé</li><li>Two (2) pcs 2×2 picture, white background</li><li><em>International students only:</em> Passport bio page & Vaccination certificate</li></ul>',
    '<ul><li>Bachelor’s degree from a recognized institution of higher learning</li><li>Intellectual capacity and aptitude for advanced studies and research</li><li>Language proficiency</li><li>Fulfillment of University requirements (e.g., health clearance) and any additional unit/Graduate School Office requirements</li></ul>',
    '<ul><li>Master’s degree (or equivalent) from a recognized institution of higher learning</li><li>Intellectual capacity and aptitude for advanced studies and research</li><li>Language proficiency</li><li>Fulfillment of University requirements and any additional College/Graduate Office/Committee requirements</li></ul>',
    '<ul><li>Bachelor’s degree in the Arts or Sciences (or higher) from an authorized and recognized institution</li><li>English language proficiency</li><li>Demonstrated critical thinking skills and sound judgment</li></ul>',
    '<ul><li>Transcript of Records (TOR)</li><li>Original Certificate of Good Moral Character</li><li>Original Transfer Credentials / Honorable Dismissal</li><li>Clear copy of PSA/NSO Birth Certificate</li><li>Two (2) pcs 2×2 ID picture (white background)</li></ul>',
    '<ol><li>Submit all original credentials to the Admissions Office.</li><li>Pay the non-refundable down payment of Php 5,000.</li><li>Get your Certificate of Registration (COR).</li><li>Proceed to the ID Section for ID processing.</li></ol><p class="disclaimer">Information may change without prior notice. Please verify via the official Adamson admissions portal.</p>',
    '<h3>Admissions &amp; Student Recruitment Office</h3><ul><li><strong>Hours:</strong> 8:00 AM – 12:00 NN; 1:00 – 5:00 PM</li><li><strong>Direct Line:</strong> <a class="link" href="tel:+63283549267">(02) 8354-9267</a></li><li><strong>Trunkline:</strong> <a class="link" href="tel:+63285242011">(02) 8524-2011</a> <small>loc. 102</small></li><li><strong>Email:</strong> <a class="link" href="mailto:admission@adamson.edu.ph">admission@adamson.edu.ph</a></li></ul>',
    '<ul><li><a class="link" href="https://www.adamson.edu.ph/cfe" target="_blank" rel="noopener">Admissions Portal</a></li><li><a class="link" href="/adamson-ccit/public/index.php?page=programs_graduate_studies">CCIT Graduate Studies</a></li><li><a class="link" href="/adamson-ccit/public/index.php?page=programs_undergraduate">Undergraduate Programs</a></li><li><a class="link" href="/adamson-ccit/public/index.php?page=student_scholarships">Scholarships</a></li></ul>',
    '/adamson-ccit/public/assets/images/programs/grad.jpg',
    'Welcome to advanced studies—Graduate School & Juris Doctor at Adamson.',
    'Ready to take the next step?',
    'Apply online and begin your graduate or JD journey with Adamson University.',
    'Apply Now',
    'https://www.adamson.edu.ph/cfe'
);
