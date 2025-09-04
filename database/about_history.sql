-- Table for About > History page
CREATE TABLE IF NOT EXISTS about_history (
  id INT PRIMARY KEY AUTO_INCREMENT,
  subhero_lead TEXT,
  intro_title VARCHAR(255),
  intro_lead TEXT,
  fact_title VARCHAR(255),
  fact_1 VARCHAR(255),
  fact_2 VARCHAR(255),
  fact_3 VARCHAR(255),
  origins_title VARCHAR(255),
  origins_body TEXT,
  milestones JSON,
  leaders_title VARCHAR(255),
  leaders_list TEXT,
  academic_leads_title VARCHAR(255),
  academic_leads_list TEXT,
  identity_title VARCHAR(255),
  identity_items TEXT,
  cta_title VARCHAR(255),
  cta_body TEXT,
  cta_btn_label VARCHAR(255),
  cta_btn_url VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default row (run only once)
INSERT INTO about_history (
  subhero_lead, intro_title, intro_lead, fact_title, fact_1, fact_2, fact_3, origins_title, origins_body, milestones, leaders_title, leaders_list, academic_leads_title, academic_leads_list, identity_title, identity_items, cta_title, cta_body, cta_btn_label, cta_btn_url
) VALUES (
  'From our roots in the College of Science to the founding of the College of Computing and Information Technology.',
  'Who We Are',
  'The College of Computing and Information Technology (CCIT) is dedicated to cultivating the next generation of technology leaders. We offer cutting-edge programs designed to equip students with the skills necessary to thrive in an increasingly digital world. With a commitment to excellence in Vincentian education, research, innovation, and internationalization, we prepare graduates for in-demand roles and empower them to create their own professional identities.',
  'At a Glance',
  'Primary Colors: CCIT Green, Adamson Blue',
  'Strengths: Computing, Information Systems, Research & Industry Linkages',
  'Student Support: Scholarships, Internships, Career Pathways',
  'Origins & Purpose',
  'Before its establishment as a separate college, Adamson University’s Information Technology, Information Systems, and Computer Science students were part of the College of Science—alongside programs in Biology, Chemistry, and Psychology. As computing disciplines grew in scope and industry relevance, the University recognized the need for a dedicated academic home focused on digital competencies, research, and partnerships. This vision culminated in the launch of the College of Computing and Information Technology (CCIT), aligning Adamson’s Vincentian mission with the demands of the digital economy and expanding opportunities for learners through specialized curricula and industry collaboration.',
  '[{"date":"2024-09-16T12:00:00+08:00","label":"Thanksgiving Mass","meta":"ST Chapel","desc":"A community celebration marking the beginning of CCIT’s journey as a new academic unit."},{"date":"2024-09-17T09:00:00+08:00","label":"Official Launch of CCIT","meta":"@ CO PO TY Hall, Dr. Carlos Tiu Building","desc":"The College of Computing and Information Technology is formally introduced to the Adamson community."},{"date":"2024-09-17","label":"Strategic Partnerships","meta":"","desc":"MOA signings with technology, esports, outreach, sustainability, and research publication partners—expanding internships, mentorships, and applied research."}]',
  'Leadership at Launch',
  '<li><strong>Fr. Daniel Franklin E. Pilario, C.M.</strong> — University President</li><li><strong>Dr. Rosula S. J. Reyes</strong> — Vice President for Academic Affairs</li><li><strong>Dr. Venusmar C. Quevedo</strong> — Vice President for Administration</li><li><strong>Dr. Leonard L. Alejandro</strong> — Dean, CCIT</li>',
  'Academic Leads',
  '<li><strong>Ms. Ma. Christina Navarro</strong> — Chairperson, Computer Science Department</li><li><strong>Mr. Archie G. Santiago</strong> — Chairperson, IT & IS Department</li>',
  'Identity & Values',
  '<li><h4>Vincentian Education</h4><p>Service-oriented formation that integrates ethics, leadership, and social responsibility in technology.</p></li><li><h4>Research & Innovation</h4><p>Applied projects and labs that connect classroom learning with real community and industry needs.</p></li><li><h4>Internationalization</h4><p>Global outlook through partnerships, exchanges, and exposure to international best practices.</p></li><li><h4>Industry Linkages</h4><p>Internships, mentorships, and co-developed initiatives that prepare learners for in-demand roles.</p></li>',
  'Explore CCIT’s Journey Further',
  'Learn about our programs, research, and industry partnerships that continue to shape our story.',
  'See our Programs',
  '/adamson-ccit/public/index.php?page=programs_undergraduate'
);
