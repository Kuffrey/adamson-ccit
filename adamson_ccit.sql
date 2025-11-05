-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2025 at 08:17 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET SESSION sql_mode = '';
START TRANSACTION;
SET time_zone = "+00:00";

-- Drop all tables in reverse dependency order
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `user_remember_tokens`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `ug_cards`;
DROP TABLE IF EXISTS `student_testimonials_page_settings`;
DROP TABLE IF EXISTS `student_testimonials`;
DROP TABLE IF EXISTS `student_testimonial`;
DROP TABLE IF EXISTS `student_scholarships_page_settings`;
DROP TABLE IF EXISTS `student_scholarship`;
DROP TABLE IF EXISTS `student_research_page_settings`;
DROP TABLE IF EXISTS `student_research`;
DROP TABLE IF EXISTS `student_profiles`;
DROP TABLE IF EXISTS `student_organizations_page_settings`;
DROP TABLE IF EXISTS `student_organization`;
DROP TABLE IF EXISTS `student_licenses`;
DROP TABLE IF EXISTS `student_certification_stat`;
DROP TABLE IF EXISTS `student_certifications_page_settings`;
DROP TABLE IF EXISTS `student_certifications`;
DROP TABLE IF EXISTS `student_certification`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `secretary_logs`;
DROP TABLE IF EXISTS `secretary_appointments`;
DROP TABLE IF EXISTS `research`;
DROP TABLE IF EXISTS `remember_tokens`;
DROP TABLE IF EXISTS `program_group_settings`;
DROP TABLE IF EXISTS `program_group_content_blocks`;
DROP TABLE IF EXISTS `program_cards`;
DROP TABLE IF EXISTS `programs_undergraduate_settings`;
DROP TABLE IF EXISTS `programs_undergraduate`;
DROP TABLE IF EXISTS `programs_graduate_settings`;
DROP TABLE IF EXISTS `programs_graduate`;
DROP TABLE IF EXISTS `programs`;
DROP TABLE IF EXISTS `partners`;
DROP TABLE IF EXISTS `news_page_settings`;
DROP TABLE IF EXISTS `news`;
DROP TABLE IF EXISTS `homepage_settings`;
DROP TABLE IF EXISTS `homepage_quick_actions`;
DROP TABLE IF EXISTS `header_utility_links`;
DROP TABLE IF EXISTS `header_settings`;
DROP TABLE IF EXISTS `header_menu_items`;
DROP TABLE IF EXISTS `header_menu`;
DROP TABLE IF EXISTS `footer_socials`;
DROP TABLE IF EXISTS `footer_settings`;
DROP TABLE IF EXISTS `footer_links`;
DROP TABLE IF EXISTS `faculty_trainings`;
DROP TABLE IF EXISTS `faculty_submissions`;
DROP TABLE IF EXISTS `faculty_research_page_settings`;
DROP TABLE IF EXISTS `faculty_research`;
DROP TABLE IF EXISTS `faculty_profile_page_settings`;
DROP TABLE IF EXISTS `faculty_profiles`;
DROP TABLE IF EXISTS `faculty_profile`;
DROP TABLE IF EXISTS `faculty_portfolio`;
DROP TABLE IF EXISTS `faculty_personal`;
DROP TABLE IF EXISTS `faculty_performance`;
DROP TABLE IF EXISTS `faculty_notifications`;
DROP TABLE IF EXISTS `faculty_news`;
DROP TABLE IF EXISTS `faculty_experience`;
DROP TABLE IF EXISTS `faculty_education`;
DROP TABLE IF EXISTS `faculty_certification_award`;
DROP TABLE IF EXISTS `faculty_certifications_page_settings`;
DROP TABLE IF EXISTS `faculty_certifications`;
DROP TABLE IF EXISTS `faculty_awards`;
DROP TABLE IF EXISTS `faculty_activity_log`;
DROP TABLE IF EXISTS `faculty`;
DROP TABLE IF EXISTS `events_page_settings`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `departments`;
DROP TABLE IF EXISTS `dean_trainings`;
DROP TABLE IF EXISTS `dean_research`;
DROP TABLE IF EXISTS `dean_profile`;
DROP TABLE IF EXISTS `dean_personal`;
DROP TABLE IF EXISTS `dean_performance`;
DROP TABLE IF EXISTS `dean_manage_news`;
DROP TABLE IF EXISTS `dean_manage_events`;
DROP TABLE IF EXISTS `dean_manage_announcements`;
DROP TABLE IF EXISTS `dean_logs`;
DROP TABLE IF EXISTS `dean_experience`;
DROP TABLE IF EXISTS `dean_education`;
DROP TABLE IF EXISTS `dean_certifications`;
DROP TABLE IF EXISTS `dean_awards`;
DROP TABLE IF EXISTS `deans_corner`;
DROP TABLE IF EXISTS `companies`;
DROP TABLE IF EXISTS `chairperson_trainings`;
DROP TABLE IF EXISTS `chairperson_research`;
DROP TABLE IF EXISTS `chairperson_profile`;
DROP TABLE IF EXISTS `chairperson_personal`;
DROP TABLE IF EXISTS `chairperson_performance`;
DROP TABLE IF EXISTS `chairperson_experience`;
DROP TABLE IF EXISTS `chairperson_education`;
DROP TABLE IF EXISTS `chairperson_certifications`;
DROP TABLE IF EXISTS `chairperson_awards`;
DROP TABLE IF EXISTS `certifications`;
DROP TABLE IF EXISTS `certification`;
DROP TABLE IF EXISTS `career_pathway_logs`;
DROP TABLE IF EXISTS `approval_workflow`;
DROP TABLE IF EXISTS `announcements`;
DROP TABLE IF EXISTS `admission_transferee_settings`;
DROP TABLE IF EXISTS `admission_graduate_settings`;
DROP TABLE IF EXISTS `admission_freshman_settings`;
DROP TABLE IF EXISTS `admin_logs`;
DROP TABLE IF EXISTS `about_vision_mission`;
DROP TABLE IF EXISTS `about_history`;

SET FOREIGN_KEY_CHECKS = 1;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `adamson_ccit`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_history`
--

CREATE TABLE `about_history` (
  `id` int(11) NOT NULL,
  `subhero_lead` text DEFAULT NULL,
  `intro_title` varchar(255) DEFAULT NULL,
  `intro_lead` text DEFAULT NULL,
  `fact_title` varchar(255) DEFAULT NULL,
  `fact_1` varchar(255) DEFAULT NULL,
  `fact_2` varchar(255) DEFAULT NULL,
  `fact_3` varchar(255) DEFAULT NULL,
  `origins_title` varchar(255) DEFAULT NULL,
  `origins_body` text DEFAULT NULL,
  `milestones` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`milestones`)),
  `leaders_title` varchar(255) DEFAULT NULL,
  `leaders_list` text DEFAULT NULL,
  `academic_leads_title` varchar(255) DEFAULT NULL,
  `academic_leads_list` text DEFAULT NULL,
  `identity_title` varchar(255) DEFAULT NULL,
  `identity_items` text DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `photo_caption` varchar(255) DEFAULT NULL,
  `cta_title` varchar(255) DEFAULT NULL,
  `cta_body` text DEFAULT NULL,
  `cta_btn_label` varchar(255) DEFAULT NULL,
  `cta_btn_url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_history`
--

INSERT INTO `about_history` (`id`, `subhero_lead`, `intro_title`, `intro_lead`, `fact_title`, `fact_1`, `fact_2`, `fact_3`, `origins_title`, `origins_body`, `milestones`, `leaders_title`, `leaders_list`, `academic_leads_title`, `academic_leads_list`, `identity_title`, `identity_items`, `photo_url`, `photo_caption`, `cta_title`, `cta_body`, `cta_btn_label`, `cta_btn_url`, `updated_at`) VALUES
(1, 'From our roots in the College of Science to the founding of the College of Computing and Information Technology.', 'Who We Are', 'The College of Computing and Information Technology (CCIT) is dedicated to cultivating the next generation of technology leaders. We offer cutting-edge programs designed to equip students with the skills necessary to thrive in an increasingly digital world. With a commitment to excellence in Vincentian education, research, innovation, and internationalization, we prepare graduates for in-demand roles and empower them to create their own professional identities.', 'At a Glance', 'Primary Colors: CCIT Green, Adamson Blue', 'Strengths: Computing, Information Systems, Research & Industry Linkages', 'Student Support: Scholarships, Internships, Career Pathways', 'Origins & Purpose', 'Before its establishment as a separate college, Adamson University’s Information Technology, Information Systems, and Computer Science students were part of the College of Science—alongside programs in Biology, Chemistry, and Psychology. As computing disciplines grew in scope and industry relevance, the University recognized the need for a dedicated academic home focused on digital competencies, research, and partnerships. This vision culminated in the launch of the College of Computing and Information Technology (CCIT), aligning Adamson’s Vincentian mission with the demands of the digital economy and expanding opportunities for learners through specialized curricula and industry collaboration.', '[{\"date\":\"2024-01-01\",\"label\":\"Thanksgiving Mass\",\"desc\":\"A community celebration marking the beginning of CCIT’s journey as a new academic unit.\"},{\"date\":\"2024-02-01\",\"label\":\"Official Launch of CCIT\",\"desc\":\"The College of Computing and Information Technology is formally introduced to the Adamson community.\"},{\"date\":\"2024-03-01\",\"label\":\"Strategic Partnerships\",\"desc\":\"MOA signings with technology, esports, outreach, sustainability, and research publication partners—expanding internships, mentorships, and applied research.\"}]', 'Leadership at Launch', '<li><strong>Fr. Daniel Franklin E. Pilario, C.M.</strong> — University President</li><li><strong>Dr. Rosula S. J. Reyes</strong> — Vice President for Academic Affairs</li><li><strong>Dr. Venusmar C. Quevedo</strong> — Vice President for Administration</li><li><strong>Dr. Leonard L. Alejandro</strong> — Dean, CCIT</li>', 'Academic Leads', '<li><strong>Ms. Ma. Christina Navarro</strong> — Chairperson, Computer Science Department</li><li><strong>Mr. Archie G. Santiago</strong> — Chairperson, IT & IS Department</li>', 'Identity & Values', '<li><h4>Vincentian Education</h4><p>Service-oriented formation that integrates ethics, leadership, and social responsibility in technology.</p></li><li><h4>Research & Innovation</h4><p>Applied projects and labs that connect classroom learning with real community and industry needs.</p></li><li><h4>Internationalization</h4><p>Global outlook through partnerships, exchanges, and exposure to international best practices.</p></li><li><h4>Industry Linkages</h4><p>Internships, mentorships, and co-developed initiatives that prepare learners for in-demand roles.</p></li>', '/adamson-ccit/public/assets/images/history/ccit-launch.jpg', 'CCIT continues to grow and evolve.', 'Explore CCIT’s Journey Further', 'Learn about our programs, research, and industry partnerships that continue to shape our story.', 'See our Programs', 'http://localhost/adamson-ccit/public/index.php?page=programs_undergraduate', '2025-11-02 22:19:43');

-- --------------------------------------------------------

--
-- Table structure for table `about_vision_mission`
--

CREATE TABLE `about_vision_mission` (
  `id` int(11) NOT NULL,
  `main_vision` text DEFAULT NULL,
  `main_mission` text DEFAULT NULL,
  `main_intro` text DEFAULT NULL,
  `dept1_title` varchar(255) DEFAULT NULL,
  `dept1_vision` text DEFAULT NULL,
  `dept1_mission` text DEFAULT NULL,
  `dept1_objectives` text DEFAULT NULL,
  `dept2_title` varchar(255) DEFAULT NULL,
  `dept2_vision` text DEFAULT NULL,
  `dept2_mission` text DEFAULT NULL,
  `dept2_objectives` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_vision_mission`
--

INSERT INTO `about_vision_mission` (`id`, `main_vision`, `main_mission`, `main_intro`, `dept1_title`, `dept1_vision`, `dept1_mission`, `dept1_objectives`, `dept2_title`, `dept2_vision`, `dept2_mission`, `dept2_objectives`, `updated_at`) VALUES
(1, 'We are recognized globally for pioneering education in computing and information technologies, fostering innovation, and producing graduates who are leaders in the IT industry, capable of addressing the challenges and opportunities of a technology-focused society.', 'As an academic unit, we provide an inclusive and dynamic learning environment that delivers specialized, industry-relevant curriculum led by highly qualified faculty.', 'Our purpose, our promise, and the departmental directions that guide CCIT.', 'Information Technology & Information Systems', 'A College dedicated in developing Christian professionals with solid foundation in the fields of Chemistry, Computer Science, Information Management, Information Technology, Mathematics, Natural Science, Psychology and Physics.', 'To provide graduates with adequate knowledge and skills in their major field of specialization; To develop quality graduates who will be globally competitive in their chosen field; To prepare graduates for entry to industry, research and entrepreneurship.', 'To offer courses which will provide solid foundation in the Sciences particularly in Mathematics, Chemistry, Natural Sciences, Psychology, Computer Science, Information Management and Information Technology; To provide adequate coverage of major fields of specialization; To qualify a student for career in his chosen field of specialization as well as entry into advance studies in Sciences.', 'Computer Science', 'To be a nationally and regionally recognized center for excellence in Computer Science education and research, developing innovators who create impactful computing solutions for society.', 'To deliver a rigorous, research-informed curriculum grounded in algorithms, systems, and data, empowering students to design, build, and evaluate trustworthy software and intelligent systems with ethical and social responsibility.', 'Prepare students to be computer professionals and researchers; Develop proficiency in designing and developing robust computing solutions.', '2025-11-02 22:22:18');

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admission_freshman_settings`
--

CREATE TABLE `admission_freshman_settings` (
  `id` int(11) NOT NULL,
  `subhero_lead` varchar(255) NOT NULL DEFAULT '',
  `how_to_apply` text DEFAULT NULL,
  `initial_uploads` text DEFAULT NULL,
  `requirements_shs` text DEFAULT NULL,
  `requirements_als` text DEFAULT NULL,
  `requirements_abroad` text DEFAULT NULL,
  `enrollment_procedure` text DEFAULT NULL,
  `sidebar_office` text DEFAULT NULL,
  `sidebar_links` text DEFAULT NULL,
  `sidebar_image_url` varchar(255) DEFAULT NULL,
  `sidebar_image_caption` varchar(255) DEFAULT NULL,
  `cta_title` varchar(255) DEFAULT NULL,
  `cta_description` varchar(255) DEFAULT NULL,
  `cta_action_label` varchar(255) DEFAULT NULL,
  `cta_action_url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admission_freshman_settings`
--

INSERT INTO `admission_freshman_settings` (`id`, `subhero_lead`, `how_to_apply`, `initial_uploads`, `requirements_shs`, `requirements_als`, `requirements_abroad`, `enrollment_procedure`, `sidebar_office`, `sidebar_links`, `sidebar_image_url`, `sidebar_image_caption`, `cta_title`, `cta_description`, `cta_action_label`, `cta_action_url`, `updated_at`) VALUES
(1, 'Start your CCIT journey with industry-aligned learning and strong student support.', '<h2>How to Apply</h2><p>Submit an online application through the official Adamson portal and upload the initial evaluation files.</p>', '<ul><li>Scanned back-to-back (JPEG or PDF) Grade 12 Report Card (DepEd F-138) or Grade 12 1st Quarter grade; or Certificate of Rating for ALS/PEPT passers.</li><li>Latest 2×2 picture, white background.</li></ul>', '<ul><li>Original Grade 12 Report Card (Form 138) with eligibility for college admission, signed by the school principal.</li><li>Original Certificate of Good Moral Character (dated not earlier than February of the graduation year; with school seal).</li><li>Clear copy of PSA Birth Certificate.</li><li>Two (2) pcs 2×2 ID picture.</li></ul>', '<ul><li>Certificate of Rating (passing marks in all subjects).</li><li>Clear copy of PSA Birth Certificate.</li><li>Original Certificate of Good Moral Character (with school seal).</li></ul>', '<div><strong>Graduates from abroad (International Curriculum):</strong> All documents must be authenticated / <em>apostille-stamped</em> by the Philippine Embassy or Consulate in the country where the school is located.</div>', '<ol><li>Submit all original credentials to the Admissions Office.</li><li>Pay the non-refundable down payment of Php 5,000.</li><li>Get your Certificate of Registration (COR).</li><li>Proceed to the ID Section for ID processing.</li><li>Proceed to the University Store for school uniform purchase.</li><li>Attend the Freshmen/Transferee Orientation scheduled by the Office for Student Affairs.</li></ol><p class=\"disclaimer\">Information may change without prior notice. Please verify via the official Adamson admissions portal.</p>', '<h3>Admissions &amp; Student Recruitment Office</h3><ul><li><strong>Hours:</strong> 8:00 AM – 12:00 NN; 1:00 – 5:00 PM</li><li><strong>Direct Line:</strong> <a class=\"link\" href=\"tel:+63283549267\">(02) 8354-9267</a></li><li><strong>Trunkline:</strong> <a class=\"link\" href=\"tel:+63285242011\">(02) 8524-2011</a> <small>loc. 102</small></li><li><strong>Email:</strong> <a class=\"link\" href=\"mailto:admission@adamson.edu.ph\">admission@adamson.edu.ph</a></li></ul>', '<ul><li><a class=\"link\" href=\"https://www.adamson.edu.ph/cfe\" target=\"_blank\" rel=\"noopener\">Admissions Portal</a></li><li><a class=\"link\" href=\"/adamson-ccit/public/index.php?page=programs_undergraduate\">CCIT Undergraduate Programs</a></li><li><a class=\"link\" href=\"/adamson-ccit/public/index.php?page=student_scholarships\">Scholarships</a></li><li><a class=\"link\" href=\"/adamson-ccit/public/index.php?page=student_organizations\">Student Life</a></li></ul>', '/adamson-ccit/public/assets/images/admissions/freshman.jpg', 'Welcome, future Falcons—start your CCIT journey at Adamson.', 'Explore CCIT Programs', 'Match your interests to a pathway in Computing and IT.', 'See Programs', '/adamson-ccit/public/index.php?page=programs_undergraduate', '2025-09-07 15:47:17');

-- --------------------------------------------------------

--
-- Table structure for table `admission_graduate_settings`
--

CREATE TABLE `admission_graduate_settings` (
  `id` int(11) NOT NULL,
  `subhero_lead` varchar(255) NOT NULL DEFAULT '',
  `how_to_apply` text DEFAULT NULL,
  `initial_uploads` text DEFAULT NULL,
  `qualifications_masters` text DEFAULT NULL,
  `qualifications_doctoral` text DEFAULT NULL,
  `qualifications_jd` text DEFAULT NULL,
  `requirements_enrollment` text DEFAULT NULL,
  `enrollment_procedure` text DEFAULT NULL,
  `sidebar_office` text DEFAULT NULL,
  `sidebar_links` text DEFAULT NULL,
  `sidebar_image_url` varchar(255) DEFAULT NULL,
  `sidebar_image_caption` varchar(255) DEFAULT NULL,
  `cta_title` varchar(255) DEFAULT NULL,
  `cta_description` varchar(255) DEFAULT NULL,
  `cta_action_label` varchar(255) DEFAULT NULL,
  `cta_action_url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admission_graduate_settings`
--

INSERT INTO `admission_graduate_settings` (`id`, `subhero_lead`, `how_to_apply`, `initial_uploads`, `qualifications_masters`, `qualifications_doctoral`, `qualifications_jd`, `requirements_enrollment`, `enrollment_procedure`, `sidebar_office`, `sidebar_links`, `sidebar_image_url`, `sidebar_image_caption`, `cta_title`, `cta_description`, `cta_action_label`, `cta_action_url`, `updated_at`) VALUES
(1, 'Advance your career through Master’s, Doctoral, and JD programs at Adamson University.', '<h2>How to Apply</h2><p>Apply online through the official Adamson portal and upload the required files for initial evaluation.</p>', '<ul><li>Transcript of Records (TOR)</li><li>Résumé</li><li>Two (2) pcs 2×2 picture, white background</li><li><em>International students only:</em> Passport bio page & Vaccination certificate</li></ul>', '<ul><li>Bachelor’s degree from a recognized institution of higher learning</li><li>Intellectual capacity and aptitude for advanced studies and research</li><li>Language proficiency</li><li>Fulfillment of University requirements (e.g., health clearance) and any additional unit/Graduate School Office requirements</li></ul>', '<ul><li>Master’s degree (or equivalent) from a recognized institution of higher learning</li><li>Intellectual capacity and aptitude for advanced studies and research</li><li>Language proficiency</li><li>Fulfillment of University requirements and any additional College/Graduate Office/Committee requirements</li></ul>', '<ul><li>Bachelor’s degree in the Arts or Sciences (or higher) from an authorized and recognized institution</li><li>English language proficiency</li><li>Demonstrated critical thinking skills and sound judgment</li></ul>', '<ul><li>Transcript of Records (TOR)</li><li>Original Certificate of Good Moral Character</li><li>Original Transfer Credentials / Honorable Dismissal</li><li>Clear copy of PSA/NSO Birth Certificate</li><li>Two (2) pcs 2×2 ID picture (white background)</li></ul>', '<ol><li>Submit all original credentials to the Admissions Office.</li><li>Pay the non-refundable down payment of Php 5,000.</li><li>Get your Certificate of Registration (COR).</li><li>Proceed to the ID Section for ID processing.</li></ol><p class=\"disclaimer\">Information may change without prior notice. Please verify via the official Adamson admissions portal.</p>', '<h3>Admissions &amp; Student Recruitment Office</h3><ul><li><strong>Hours:</strong> 8:00 AM – 12:00 NN; 1:00 – 5:00 PM</li><li><strong>Direct Line:</strong> <a class=\"link\" href=\"tel:+63283549267\">(02) 8354-9267</a></li><li><strong>Trunkline:</strong> <a class=\"link\" href=\"tel:+63285242011\">(02) 8524-2011</a> <small>loc. 102</small></li><li><strong>Email:</strong> <a class=\"link\" href=\"mailto:admission@adamson.edu.ph\">admission@adamson.edu.ph</a></li></ul>', '<ul><li><a class=\"link\" href=\"https://www.adamson.edu.ph/cfe\" target=\"_blank\" rel=\"noopener\">Admissions Portal</a></li><li><a class=\"link\" href=\"/adamson-ccit/public/index.php?page=programs_graduate_studies\">CCIT Graduate Studies</a></li><li><a class=\"link\" href=\"/adamson-ccit/public/index.php?page=programs_undergraduate\">Undergraduate Programs</a></li><li><a class=\"link\" href=\"/adamson-ccit/public/index.php?page=student_scholarships\">Scholarships</a></li></ul>', '/adamson-ccit/public/assets/images/programs/grad.jpg', 'Welcome to advanced studies—Graduate School & Juris Doctor at Adamson.', 'Ready to take the next step?', 'Apply online and begin your graduate or JD journey with Adamson University.', 'Apply Now', 'https://www.adamson.edu.ph/cfe', '2025-09-01 20:00:05');

-- --------------------------------------------------------

--
-- Table structure for table `admission_transferee_settings`
--

CREATE TABLE `admission_transferee_settings` (
  `id` int(11) NOT NULL,
  `subhero_lead` varchar(255) NOT NULL DEFAULT '',
  `how_to_apply` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `enrollment_procedure` text DEFAULT NULL,
  `enrollment_note` text DEFAULT NULL,
  `sidebar_office` text DEFAULT NULL,
  `sidebar_links` text DEFAULT NULL,
  `sidebar_image_url` varchar(255) DEFAULT NULL,
  `sidebar_image_caption` varchar(255) DEFAULT NULL,
  `cta_title` varchar(255) DEFAULT NULL,
  `cta_description` varchar(255) DEFAULT NULL,
  `cta_action_label` varchar(255) DEFAULT NULL,
  `cta_action_url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admission_transferee_settings`
--

INSERT INTO `admission_transferee_settings` (`id`, `subhero_lead`, `how_to_apply`, `requirements`, `enrollment_procedure`, `enrollment_note`, `sidebar_office`, `sidebar_links`, `sidebar_image_url`, `sidebar_image_caption`, `cta_title`, `cta_description`, `cta_action_label`, `cta_action_url`, `updated_at`) VALUES
(1, 'For students transferring from other colleges or universities.', '<h2>How to Apply</h2><p>Apply online and prepare the required documents for evaluation and interview.</p>', '<ul><li>Original True Copy of Grades</li><li>Original Certificate of Good Moral Character</li><li>Original Transfer Credentials / Honorable Dismissal</li><li>Letter of Application</li><li>Clear copy of PSA/NSO Birth Certificate</li><li>Two (2) pieces 2×2 ID photo (white background)</li></ul>', '<ol><li>Submit all original credentials to the Admissions Office, including the approved accreditation of courses.</li><li>Pay the non-refundable down payment of <strong>Php 5,000</strong>.</li><li>Using your student number (as <em>USERNAME</em>) and your assigned temporary password, access your eLearning account at <a class=\"link\" href=\"https://learn.adamson.edu.ph\" target=\"_blank\" rel=\"noopener\">learn.adamson.edu.ph</a>.</li><li>Go to <strong>Subject Enlistment</strong> &rarr; <strong>Proceed to Subject Enlistment</strong>.</li><li>From <strong>Pre-Advised Subjects</strong>, choose schedules/sections for each subject. Add, edit, or delete as needed, then <strong>Save</strong>.</li><li>Print your <strong>Certificate of Enrollment</strong> (with Assessment of Fees).</li><li>Proceed to the <strong>ID Section</strong> for ID processing.</li><li>Proceed to the <strong>University Store</strong> to purchase the school uniform.</li><li>Attend the <strong>Freshmen/Transferee Orientation</strong> scheduled by the Office for Student Affairs.</li></ol>', '<div class=\"note\"><strong>Note:</strong> Subject enlistment is on a <em>first-come, first-serve</em> basis. “Pre-Advised Subjects” are those recommended for your next term/semester.</div>', '<h3>Admissions &amp; Student Recruitment Office</h3><ul><li><strong>Hours:</strong> 8:00 AM – 12:00 NN; 1:00 – 5:00 PM</li><li><strong>Direct Line:</strong> <a class=\"link\" href=\"tel:+63283549267\">(02) 8354-9267</a></li><li><strong>Trunkline:</strong> <a class=\"link\" href=\"tel:+63285242011\">(02) 8524-2011</a> <small>loc. 102</small></li><li><strong>Email:</strong> <a class=\"link\" href=\"mailto:admission@adamson.edu.ph\">admission@adamson.edu.ph</a></li></ul>', '<ul><li><a class=\"link\" href=\"/adamson-ccit/public/index.php?page=programs_undergraduate\">CCIT Programs</a></li><li><a class=\"link\" href=\"/adamson-ccit/public/index.php?page=student_scholarships\">Scholarships</a></li><li><a class=\"link\" href=\"/adamson-ccit/public/index.php?page=student_organizations\">Student Life</a></li></ul>', '/adamson-ccit/public/assets/images/admissions/transferee.jpg', 'Welcome, future Falcons—transfer your journey to CCIT.', 'Ready to transfer to CCIT?', 'Apply online and we’ll guide you through evaluation, interview, and enlistment.', 'Apply Now', 'https://www.adamson.edu.ph/cfe', '2025-09-01 19:55:55');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `excerpt` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `category` varchar(64) DEFAULT 'general',
  `image_url` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `author` varchar(128) DEFAULT NULL,
  `status` varchar(32) DEFAULT 'published',
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `excerpt`, `body`, `category`, `image_url`, `date`, `author`, `status`, `published_at`, `created_at`, `updated_at`) VALUES
(18, 'CCIT Alumni Basketball League', NULL, 'Gather your team and sign up now! Spots are limited and registration is on a first-come, first-served basis. Don’t forget to check out the posted mechanics before registering.\r\n\r\nRegister here: https://forms.gle/QXHhG9rTBVNXoiQj8\r\nOr scan the QR code to join.\r\n\r\nSee you on the court, CCIT Klasmeyts!🏀', 'general', '/adamson-ccit/public/assets/images/announcements/announcement_1762336757_5590.jpg', '0000-00-00', NULL, 'archived', NULL, '2025-11-05 09:58:25', '2025-11-05 10:08:06');

--
-- Triggers `announcements`
--
DELIMITER $$
CREATE TRIGGER `announcements_updated_at` BEFORE UPDATE ON `announcements` FOR EACH ROW SET NEW.updated_at = CURRENT_TIMESTAMP
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `approval_workflow`
--

CREATE TABLE `approval_workflow` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `step_number` int(11) NOT NULL,
  `reviewer_role` enum('dean','admin','department_head') NOT NULL,
  `reviewer_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected','skipped') DEFAULT 'pending',
  `comments` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `career_pathway_logs`
--

CREATE TABLE `career_pathway_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `answers` text NOT NULL,
  `recommended_programs` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certification`
--

CREATE TABLE `certification` (
  `id` int(11) NOT NULL,
  `cert_title` varchar(255) NOT NULL,
  `issuer` varchar(50) DEFAULT NULL,
  `issuer_key` varchar(32) DEFAULT NULL,
  `badge_url` varchar(255) DEFAULT NULL,
  `cert_url` varchar(255) DEFAULT NULL,
  `verify_url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certification`
--

INSERT INTO `certification` (`id`, `cert_title`, `issuer`, `issuer_key`, `badge_url`, `cert_url`, `verify_url`, `updated_at`) VALUES
(1, 'HTML & CSS', 'Certiport', 'certiport', '/adamson-ccit/public/assets/images/certs/html-css.png', '#', '#', '2025-09-04 13:30:54'),
(2, 'Software Development', 'Certiport', 'certiport', '/adamson-ccit/public/assets/images/certs/software-dev.png', '#', '#', '2025-09-04 13:30:54'),
(3, 'Python', 'Certiport', 'certiport', '/adamson-ccit/public/assets/images/certs/python.png', '#', '#', '2025-09-04 13:30:54'),
(4, 'Networking', 'Certiport', 'certiport', '/adamson-ccit/public/assets/images/certs/networking.png', '#', '#', '2025-09-04 13:30:54'),
(13, 'Device Configuration and Management', 'Certiport', 'certiport', '', '', '', '2025-10-01 13:10:18'),
(14, 'Databases', 'Certiport', 'certiport', '', '', '', '2025-10-01 13:10:26');

-- --------------------------------------------------------

--
-- Table structure for table `certifications`
--

CREATE TABLE `certifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `issuer` varchar(255) NOT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT 0,
  `issued_at` date DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certifications`
--

INSERT INTO `certifications` (`id`, `title`, `issuer`, `owner_user_id`, `department_id`, `issued_at`, `expires_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 'fdfsf', 'fsdfs', 1, 1, '2025-09-27', NULL, 'approved', '2025-09-27 20:56:52', '2025-10-01 11:56:03');

-- --------------------------------------------------------

--
-- Table structure for table `chairperson_awards`
--

CREATE TABLE `chairperson_awards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `issuer` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `certificate_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chairperson_certifications`
--

CREATE TABLE `chairperson_certifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `issue_year` int(11) DEFAULT NULL,
  `expire_year` int(11) DEFAULT NULL,
  `credential_id` varchar(128) DEFAULT NULL,
  `credential_url` varchar(255) DEFAULT NULL,
  `status` varchar(32) DEFAULT 'active',
  `visibility` enum('public','private') DEFAULT 'public',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chairperson_education`
--

CREATE TABLE `chairperson_education` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `degree` varchar(128) DEFAULT NULL,
  `institution` varchar(128) DEFAULT NULL,
  `year` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chairperson_experience`
--

CREATE TABLE `chairperson_experience` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `position` varchar(128) DEFAULT NULL,
  `department` varchar(128) DEFAULT NULL,
  `period` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chairperson_performance`
--

CREATE TABLE `chairperson_performance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `rating` varchar(32) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chairperson_personal`
--

CREATE TABLE `chairperson_personal` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `birthday` date DEFAULT NULL,
  `gender` varchar(16) DEFAULT NULL,
  `marital_status` varchar(32) DEFAULT NULL,
  `nationality` varchar(64) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_contact_name` varchar(128) DEFAULT NULL,
  `emergency_contact_number` varchar(32) DEFAULT NULL,
  `contact_number` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chairperson_profile`
--

CREATE TABLE `chairperson_profile` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(128) DEFAULT NULL,
  `last_name` varchar(128) DEFAULT NULL,
  `position` varchar(128) DEFAULT NULL,
  `department` varchar(128) DEFAULT NULL,
  `work_email` varchar(128) DEFAULT NULL,
  `office_location` varchar(128) DEFAULT NULL,
  `employee_id` varchar(32) DEFAULT NULL,
  `employment_type` varchar(64) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `specializations` text DEFAULT NULL,
  `languages` varchar(128) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chairperson_profile`
--

INSERT INTO `chairperson_profile` (`id`, `user_id`, `full_name`, `first_name`, `last_name`, `position`, `department`, `work_email`, `office_location`, `employee_id`, `employment_type`, `date_hired`, `profile_photo`, `specializations`, `languages`, `updated_at`, `status`) VALUES
(1, 24, 'Archie Santiago', NULL, NULL, 'Chairperson', 'Information Technology & Information Systems', 'archie.santiago@adamson.edu.ph', '', '2273628376', '', '0000-00-00', '/adamson-ccit/public/assets/images/profile_24_1762360914.png', '', '', '2025-11-06 00:41:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chairperson_research`
--

CREATE TABLE `chairperson_research` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `journal` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `type` varchar(64) DEFAULT NULL,
  `authors` text DEFAULT NULL,
  `doi_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chairperson_trainings`
--

CREATE TABLE `chairperson_trainings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `certificate_url` varchar(255) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `status`, `created_at`) VALUES
(1, 'Amazon Web Services', 'active', '2025-09-09 18:45:36'),
(2, 'Microsoft', 'active', '2025-09-09 18:45:36'),
(3, 'Cisco', 'active', '2025-09-09 18:45:36'),
(4, 'Databricks', 'active', '2025-09-09 18:45:36'),
(5, 'Google', 'active', '2025-09-09 18:45:36'),
(6, 'IBM', 'active', '2025-09-09 18:45:36'),
(7, 'Oracle', 'active', '2025-09-09 18:45:36'),
(8, 'Salesforce', 'active', '2025-09-09 18:45:36'),
(9, 'SAP', 'active', '2025-09-09 18:45:36'),
(10, 'Cisco Systems', 'active', '2025-09-09 18:45:36'),
(11, 'VMware', 'active', '2025-09-09 18:45:36'),
(12, 'Adobe', 'active', '2025-09-09 18:45:36'),
(13, 'Intel', 'active', '2025-09-09 18:45:36'),
(14, 'NVIDIA', 'active', '2025-09-09 18:45:36'),
(15, 'Accenture Philippines', 'active', '2025-09-09 18:45:36'),
(16, 'Pointwest Technologies', 'active', '2025-09-09 18:45:36'),
(17, 'Exist Software Labs', 'active', '2025-09-09 18:45:36'),
(18, 'Stratpoint Technologies', 'active', '2025-09-09 18:45:36'),
(19, 'Yondu', 'active', '2025-09-09 18:45:36'),
(20, 'Stratbase', 'active', '2025-09-09 18:45:36'),
(21, 'Novare Technologies', 'active', '2025-09-09 18:45:36'),
(22, 'Magellan Solutions', 'active', '2025-09-09 18:45:36'),
(23, 'Acquire BPO', 'active', '2025-09-09 18:45:36'),
(24, 'TaskUs', 'active', '2025-09-09 18:45:36'),
(25, 'Cloudstaff', 'active', '2025-09-09 18:45:36'),
(26, 'KMC Solutions', 'active', '2025-09-09 18:45:36'),
(27, 'Xurpas', 'active', '2025-09-09 18:45:36'),
(28, 'Genpact Philippines', 'active', '2025-09-09 18:45:36'),
(29, 'NEC Philippines', 'active', '2025-09-09 18:45:36'),
(30, 'm360, Inc.', 'active', '2025-09-09 18:45:36'),
(31, 'Cognizant', 'active', '2025-09-09 18:45:36'),
(32, 'Capgemini', 'active', '2025-09-09 18:45:36'),
(33, 'Wipro', 'active', '2025-09-09 18:45:36'),
(34, 'Infosys', 'active', '2025-09-09 18:45:36'),
(35, 'DXC Technology', 'active', '2025-09-09 18:45:36'),
(36, 'Amazon.com Inc.', 'active', '2025-09-09 18:45:36'),
(37, 'Alibaba Group Holding Limited', 'active', '2025-09-09 18:45:36'),
(38, 'AT&T Inc', 'active', '2025-09-09 18:45:36'),
(39, 'Verizon Communications Inc', 'active', '2025-09-09 18:45:36'),
(40, 'Interxion', 'active', '2025-09-09 18:45:36'),
(41, 'Reksoft', 'active', '2025-09-09 18:45:36'),
(42, 'Velocity Software Solutions', 'active', '2025-09-09 18:45:36'),
(43, 'E2Logy Software Solutions', 'active', '2025-09-09 18:45:36'),
(44, 'Integra Global Solutions', 'active', '2025-09-09 18:45:36'),
(45, 'itCraft', 'active', '2025-09-09 18:45:36'),
(46, 'Proton AG', 'active', '2025-09-09 18:45:36'),
(47, 'Certiport - A Pearson VUE Business', 'active', '2025-09-09 18:45:36');

-- --------------------------------------------------------

--
-- Table structure for table `deans_corner`
--

CREATE TABLE `deans_corner` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(150) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `subhero_image` varchar(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  `email` varchar(128) DEFAULT 'dean@adamson.edu.ph',
  `message` text NOT NULL,
  `cta_title` varchar(255) DEFAULT 'Connect with the Dean',
  `cta_body` text DEFAULT 'Have questions or want to learn more about CCIT? Reach out to our office for more information.',
  `cta_btn_label` varchar(64) DEFAULT 'Email the Dean',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deans_corner`
--

INSERT INTO `deans_corner` (`id`, `name`, `title`, `photo`, `subhero_image`, `email`, `message`, `cta_title`, `cta_body`, `cta_btn_label`, `updated_at`) VALUES
(1, 'Dr. Leonard L. Alejandro', 'Dean, College of Computing and Information Technology', '/adamson-ccit/public/uploads/faculty/faculty_68d8442ecd780_1759003694.png', '/adamson-ccit/public/assets/images/hero-campus.jpg', 'leonard.alejandro@adamson.edu.ph', 'Welcome to the College of Computing and Information Technology at Adamson University! Here, we are deeply committed to providing a world-class education that empowers students to excel in the ever-evolving fields of IT and computing. Our faculty is dedicated to fostering innovation, critical thinking, and problem-solving skills that are essential for success in the digital age. With cutting-edge facilities, industry collaborations, and hands-on learning opportunities, we ensure that our students are well-prepared for a range of exciting career paths in technology.\r\n\r\nWe take pride in our dynamic community, which brings together a diverse group of passionate individuals. Our programs are designed to equip students with the technical expertise and practical experience necessary to thrive in today’s global technology landscape. At Adamson University, we not only focus on academic excellence but also nurture creativity, leadership, and collaboration—key traits that will help you stand out in your career.\r\n\r\nWhether you\'re looking to start your journey in the world of computing or take your skills to the next level, we invite you to explore our range of programs, research initiatives, and community activities. Join us in shaping the future of technology and making a meaningful impact in the world!\r\n', 'Connect with the Dean', 'Have questions or want to learn more about CCIT? Reach out to our office for more information.', 'Email the Dean', '2025-10-07 17:20:15');

-- --------------------------------------------------------

--
-- Table structure for table `dean_awards`
--

CREATE TABLE `dean_awards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `issuer` varchar(128) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `certificate_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dean_awards`
--

INSERT INTO `dean_awards` (`id`, `user_id`, `title`, `issuer`, `year`, `description`, `certificate_url`) VALUES
(1, 2, 'Outstanding Faculty', 'Adamson University', 2024, 'Test', 'https://adamson.blackboard.com/?new_loc=%2Fultra%2Fcourse');

-- --------------------------------------------------------

--
-- Table structure for table `dean_certifications`
--

CREATE TABLE `dean_certifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `issue_year` int(11) DEFAULT NULL,
  `expire_year` int(11) DEFAULT NULL,
  `credential_id` varchar(255) DEFAULT NULL,
  `credential_url` varchar(255) DEFAULT NULL,
  `status` enum('active','expired') DEFAULT 'active',
  `visibility` enum('public','private') DEFAULT 'public',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dean_certifications`
--

INSERT INTO `dean_certifications` (`id`, `user_id`, `name`, `company_name`, `issue_year`, `expire_year`, `credential_id`, `credential_url`, `status`, `visibility`, `created_at`, `updated_at`) VALUES
(3, 2, 'Python', 'Certiport', 2025, 2026, '123213', '', 'active', 'public', '2025-10-11 16:17:10', '2025-10-12 01:50:17');

-- --------------------------------------------------------

--
-- Table structure for table `dean_education`
--

CREATE TABLE `dean_education` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `degree` varchar(128) DEFAULT NULL,
  `institution` varchar(128) DEFAULT NULL,
  `year` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dean_education`
--

INSERT INTO `dean_education` (`id`, `user_id`, `degree`, `institution`, `year`) VALUES
(2, 2, 'BSIT', 'Adamson University', '2009'),
(3, 2, 'MSIT', 'Adamson University', '2013');

-- --------------------------------------------------------

--
-- Table structure for table `dean_experience`
--

CREATE TABLE `dean_experience` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `position` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `period` varchar(50) NOT NULL,
  `title` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dean_experience`
--

INSERT INTO `dean_experience` (`id`, `user_id`, `position`, `department`, `period`, `title`) VALUES
(4, 2, 'Dean', 'College of Computer and Information Technology', '2020', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dean_logs`
--

CREATE TABLE `dean_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dean_logs`
--

INSERT INTO `dean_logs` (`id`, `user_id`, `action`, `table_name`, `record_id`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'approved_submission', 'faculty_submissions', 1, 'Approved research submission: Cybersecurity Framework for SMEs', '127.0.0.1', 'Mozilla/5.0', '2025-09-28 12:14:07'),
(2, 1, 'rejected_submission', 'faculty_submissions', 2, 'Rejected news submission: Invalid content format', '127.0.0.1', 'Mozilla/5.0', '2025-09-28 12:14:07'),
(3, 1, 'viewed_faculty_profile', 'faculty_profiles', 3, 'Viewed faculty profile: Dr. Carmelita Benito', '127.0.0.1', 'Mozilla/5.0', '2025-09-28 12:14:07'),
(4, 1, 'updated_research_status', 'faculty_research', 1, 'Updated research status to published', '127.0.0.1', 'Mozilla/5.0', '2025-09-28 12:14:07'),
(5, 1, 'login', 'users', 1, 'Dean logged into system', '127.0.0.1', 'Mozilla/5.0', '2025-09-28 12:14:07'),
(11, 1, 'approve', 'faculty_submissions', 456, 'Approved research submission', '127.0.0.1', '', '2025-09-28 12:34:19'),
(12, 1, 'reject', 'faculty_submissions', 456, 'Rejected news submission', '127.0.0.1', '', '2025-09-28 12:34:19'),
(14, 2, 'approve', 'faculty_submissions', 38, 'Submission approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 08:52:22'),
(15, 2, 'delete', 'news', 18, 'Deleted news article: mar', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 08:53:43'),
(16, 2, 'delete', 'news', 15, 'Deleted news article: dsdsd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 08:53:46'),
(17, 2, 'delete', 'news', 14, 'Deleted news article: hello mar', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 08:53:50'),
(18, 2, 'approve', 'faculty_submissions', 5, 'Submission approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 08:56:05'),
(19, 2, 'approve', 'faculty_submissions', 16, 'Submission approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 08:56:08'),
(20, 2, 'approve', 'faculty_submissions', 40, 'Submission approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 09:13:03'),
(21, 2, 'approve', 'faculty_submissions', 41, 'Submission approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 09:13:07'),
(22, 2, 'approve', 'faculty_submissions', 42, 'Submission approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 09:13:10'),
(23, 2, 'update', 'news', 20, 'Updated news article: test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 07:36:52'),
(24, 2, 'update', 'news', 20, 'Updated news article: test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 07:37:01'),
(25, 2, 'delete', 'news', 19, 'Deleted news article: migel', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 12:38:39'),
(26, 2, 'delete', 'news', 20, 'Deleted news article: test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 12:38:41'),
(27, 2, 'reject', 'faculty_submissions', 62, 'Submission rejected: gfgfgfg', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-09 06:15:08'),
(28, 2, 'reject', 'faculty_submissions', 63, 'Submission rejected: hghghg', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-09 06:15:14'),
(29, 2, 'reject', 'faculty_submissions', 64, 'Submission rejected: ghghgh', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-09 06:15:19'),
(30, 2, 'reject', 'faculty_submissions', 60, 'Submission rejected: hghgh', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-09 06:15:25'),
(31, 2, 'reject', 'faculty_submissions', 65, 'Submission rejected: nbggjhgj', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-09 06:15:30'),
(32, 2, 'delete', 'news', 228, 'Deleted news article: nikky', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-10 10:05:29'),
(33, 2, 'delete', 'news', 227, 'Deleted news article: dsadada', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-10 10:05:32'),
(36, 2, 'delete', 'faculty_research', 6, 'Deleted research: fdfdfd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-10 17:33:09'),
(37, 2, 'delete', 'faculty_research', 5, 'Deleted research: fdfdfd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-10 17:33:11'),
(38, 2, 'delete', 'news', 231, 'Deleted news article: sdsd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 05:04:32'),
(39, 2, 'delete', 'news', 230, 'Deleted news article: Test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 05:04:34'),
(40, 2, 'delete', 'news', 229, 'Deleted news article: Test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 05:04:37'),
(41, 2, 'delete', 'announcements', 16, 'Deleted announcement: tets', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 18:30:42'),
(42, 2, 'delete', 'announcements', 16, 'Deleted announcement: Announcement ID 16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 18:31:04'),
(43, 2, 'delete', 'faculty_research', 16, 'Deleted research: Test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 18:56:35'),
(44, 2, 'delete', 'faculty_research', 16, 'Deleted research: Research ID 16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 18:59:16'),
(45, 2, 'delete', 'faculty_research', 17, 'Deleted research: Mar', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 19:00:54'),
(46, 2, 'delete', 'faculty_research', 2, 'Deleted research: Unified Neural Network Framework for Consistent and Efficient Real-time Object Detection of Early Longitudinal Melanonychia', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 19:36:32'),
(47, 2, 'delete', 'faculty_research', 19, 'Deleted research: Unified Neural Network Framework for Consistent and Efficient Real-time Object Detection of Early Longitudinal Melanonychia', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 19:36:36'),
(48, 2, 'delete', 'faculty_research', 19, 'Deleted research: Research ID 19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 19:37:33'),
(49, 2, 'delete', 'faculty_research', 20, 'Deleted research: rere', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 19:38:23'),
(50, 2, 'delete', 'news', 233, 'Deleted news article: taka', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 20:55:30'),
(51, 2, 'delete', 'news', 232, 'Deleted news article: hilu', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 20:55:32'),
(52, 2, 'delete', 'faculty_research', 2, 'Deleted research: Unified Neural Network Framework for Consistent and Efficient Real-time Object Detection of Early Longitudinal Melanonychia', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 08:04:23'),
(53, 2, 'delete', 'faculty_research', 2, 'Deleted research: Research ID 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 08:07:46'),
(54, 2, 'delete', 'faculty_research', 2, 'Deleted research: Research ID 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 08:07:51'),
(55, 2, 'delete', 'faculty_research', 2, 'Deleted research: Research ID 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 08:11:36'),
(56, 2, 'delete', 'faculty_research', 3, 'Deleted research: Unified Neural Network Framework for Consistent and Efficient Real-time Object Detection of Early Longitudinal Melanonychia', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 09:04:18'),
(57, 2, 'delete', 'faculty_research', 6, 'Deleted research: hWELLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 09:04:21'),
(58, 2, 'delete', 'faculty_research', 6, 'Deleted research: Research ID 6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 09:06:23'),
(59, 2, 'delete', 'faculty_research', 6, 'Deleted research: Research ID 6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 09:06:36'),
(60, 2, 'delete', 'faculty_research', 6, 'Deleted research: Research ID 6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 09:10:33'),
(61, 2, 'delete', 'faculty_research', 6, 'Deleted research: Research ID 6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 09:11:33'),
(62, 2, 'delete', 'faculty_research', 7, 'Deleted research: FGDGDFFGDGD', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 09:51:22'),
(63, 2, 'delete', 'faculty_research', 5, 'Deleted research: hWELLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 09:51:24'),
(64, 2, 'delete', 'faculty_research', 8, 'Deleted research: FGDGDFFGDGD', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 09:51:27'),
(65, 2, 'delete', 'faculty_research', 4, 'Deleted research: Unified Neural Network Framework for Consistent and Efficient Real-time Object Detection of Early Longitudinal Melanonychia', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 09:51:33'),
(66, 2, 'delete', 'faculty_research', 9, 'Deleted research: Unified Neural Network Framework for Consistent and Efficient Real-time Object Detection of Early Longitudinal Melanonychia', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 09:57:57'),
(67, 2, 'delete', 'faculty_research', 10, 'Deleted research: FDFSFS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 09:57:59'),
(68, 2, 'delete', 'faculty_research', 14, 'Deleted research: Test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 10:51:18'),
(69, 2, 'delete', 'faculty_research', 15, 'Deleted research: Test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 10:51:20'),
(70, 2, 'delete', 'faculty_research', 16, 'Deleted research: Test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 10:51:21'),
(71, 2, 'delete', 'news', 249, 'Deleted news article: Test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:03:14'),
(72, 2, 'update', 'news', 250, 'Updated news article: TEst', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:13:07'),
(73, 2, 'update', 'news', 250, 'Updated news article: TEst', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:13:09'),
(74, 2, 'update', 'news', 250, 'Updated news article: TEst', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:13:19'),
(75, 2, 'delete', 'news', 250, 'Deleted news article: TEst', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:13:47'),
(76, 2, 'delete', 'news', 251, 'Deleted news article: Testtttttttting', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:14:55'),
(77, 2, 'update', 'news', 252, 'Updated news article: dsdsd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:15:13'),
(78, 2, 'update', 'news', 252, 'Updated news article: dsdsd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:15:15'),
(79, 2, 'update', 'news', 252, 'Updated news article: dsdsd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:16:24'),
(80, 2, 'update', 'news', 252, 'Updated news article: dsdsd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:33:38'),
(81, 2, 'update', 'news', 252, 'Updated news article: dsdsd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:34:28'),
(82, 2, 'delete', 'announcements', 15, 'Deleted announcement: Test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:37:17'),
(83, 2, 'delete', 'faculty_research', 17, 'Deleted research: Test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:37:44'),
(84, 2, 'delete', 'faculty_research', 18, 'Deleted research: Test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:37:46'),
(85, 2, 'delete', 'faculty_research', 19, 'Deleted research: Test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:38:28'),
(86, 2, 'delete', 'faculty_research', 20, 'Deleted research: dsdsdsds', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:40:21'),
(87, 2, 'delete', 'faculty_research', 20, 'Deleted research: Research ID 20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:40:59'),
(88, 2, 'delete', 'faculty_research', 21, 'Deleted research: Hello', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:45:41'),
(89, 2, 'delete', 'faculty_research', 22, 'Deleted research: Hello', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:45:43'),
(90, 2, 'delete', 'faculty_research', 22, 'Deleted research: Research ID 22', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:46:34'),
(91, 2, 'delete', 'faculty_research', 23, 'Deleted research: what', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:46:53'),
(92, 2, 'delete', 'faculty_research', 24, 'Deleted research: what', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:46:55'),
(93, 2, 'delete', 'faculty_research', 25, 'Deleted research: what', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:46:57'),
(94, 2, 'delete', 'faculty_research', 26, 'Deleted research: what', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:46:59'),
(95, 2, 'delete', 'faculty_research', 27, 'Deleted research: Hello', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:47:01'),
(96, 2, 'delete', 'faculty_research', 28, 'Deleted research: Hello', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:47:02'),
(97, 2, 'delete', 'news', 252, 'Deleted news article: dsdsd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 12:49:12'),
(98, 2, 'update', 'news', 13, 'Updated news article: Ctrl + Alt + Start: A Kickoff for Tomorrow’s Innovators', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 19:11:57'),
(99, 2, 'update', 'news', 13, 'Updated news article: Ctrl + Alt + Start: A Kickoff for Tomorrow’s Innovators', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 19:13:06'),
(100, 2, 'update', 'news', 13, 'Updated news article: Ctrl + Alt + Start: A Kickoff for Tomorrow’s Innovators', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 19:13:15'),
(101, 2, 'update', 'news', 13, 'Updated news article: Ctrl + Alt + Start: A Kickoff for Tomorrow’s Innovators', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 19:13:19'),
(102, 2, 'update', 'news', 254, 'Updated news article: Test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-19 01:19:19'),
(103, 2, 'delete', 'news', 256, 'Deleted news article: Test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-30 17:22:54'),
(104, 2, 'delete', 'news', 255, 'Deleted news article: CCIT Rise', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-30 17:22:56'),
(105, 2, 'delete', 'news', 254, 'Deleted news article: Test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-30 17:22:58'),
(106, 24, 'delete', 'news', 260, 'Deleted news article: hellooooo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 17:38:09'),
(107, 24, 'delete', 'news', 259, 'Deleted news article: Hello', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 17:38:11'),
(108, 24, 'delete', 'news', 259, 'Deleted news article: ID: 259', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 17:39:11'),
(109, 24, 'delete', 'news', 261, 'Deleted news article: hello', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 18:37:00'),
(110, 2, 'delete', 'announcements', 17, 'Deleted announcement: Hello', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 09:58:12'),
(111, 2, 'delete', 'announcements', 1, 'Deleted announcement: CCIT Alumni Basketball League', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 09:59:29'),
(112, 2, 'delete', 'faculty_research', 13, 'Deleted research: ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 12:19:09'),
(113, 2, 'delete', 'faculty_research', 36, 'Deleted research: ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 12:20:00'),
(114, 2, 'delete', 'faculty_research', 38, 'Deleted research: ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 12:27:47'),
(115, 2, 'delete', 'faculty_research', 37, 'Deleted research: ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 12:27:49');

-- --------------------------------------------------------

--
-- Table structure for table `dean_manage_announcements`
--

CREATE TABLE `dean_manage_announcements` (
  `id` int(11) NOT NULL,
  `announcement_title` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dean_manage_events`
--

CREATE TABLE `dean_manage_events` (
  `id` int(11) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dean_manage_news`
--

CREATE TABLE `dean_manage_news` (
  `id` int(11) NOT NULL,
  `news_title` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dean_performance`
--

CREATE TABLE `dean_performance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `rating` varchar(32) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dean_performance`
--

INSERT INTO `dean_performance` (`id`, `user_id`, `year`, `rating`, `remarks`, `title`) VALUES
(1, 2, 2024, '100', 'Test', 'Test');

-- --------------------------------------------------------

--
-- Table structure for table `dean_personal`
--

CREATE TABLE `dean_personal` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `birthday` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `marital_status` enum('single','married','divorced','widowed') DEFAULT NULL,
  `nationality` varchar(128) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_number` varchar(32) DEFAULT NULL,
  `contact_number` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dean_personal`
--

INSERT INTO `dean_personal` (`id`, `user_id`, `birthday`, `gender`, `marital_status`, `nationality`, `address`, `emergency_contact_name`, `emergency_contact_number`, `contact_number`) VALUES
(7, 2, '1986-06-27', 'male', 'single', 'Filipino', 'Taguig', 'Nikky', '09123456789', '09123456789'),
(8, 24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dean_profile`
--

CREATE TABLE `dean_profile` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `work_email` varchar(255) NOT NULL,
  `position` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `employment_type` varchar(50) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `office_location` varchar(100) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `specializations` varchar(255) DEFAULT NULL,
  `languages` varchar(100) DEFAULT NULL,
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dean_profile`
--

INSERT INTO `dean_profile` (`id`, `user_id`, `full_name`, `employee_id`, `work_email`, `position`, `department`, `employment_type`, `date_hired`, `office_location`, `status`, `profile_photo`, `bio`, `specializations`, `languages`, `last_updated`) VALUES
(1, 2, 'Dr. Leonard Luis Alejandro', '2008060084', 'leonard.alejandro@adamson.edu.ph', 'Dean', 'College of Computer and Information Technology', 'Full-Time', '2021-02-24', 'SV Bldg., 2nd Floor, Office of the Dean', 'Active', '/adamson-ccit/public/assets/images/profile_2_1760512914.png', 'hello', 'IT Education', 'English, Filipino, Nihongo', '2025-11-05 02:31:58'),
(10, 24, 'Dr. 200813648 ', '', '200813648@adamson.edu.ph', 'Dean', 'College of Computer and Information Technology', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-05 02:28:42');

-- --------------------------------------------------------

--
-- Table structure for table `dean_research`
--

CREATE TABLE `dean_research` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `status` varchar(32) DEFAULT 'ongoing',
  `visibility` enum('public','private') DEFAULT 'public',
  `journal` varchar(255) DEFAULT NULL,
  `type` varchar(64) DEFAULT NULL,
  `authors` text DEFAULT NULL,
  `doi_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dean_research`
--

INSERT INTO `dean_research` (`id`, `user_id`, `title`, `description`, `year`, `status`, `visibility`, `journal`, `type`, `authors`, `doi_url`) VALUES
(1, 2, 'Test', NULL, 2025, 'ongoing', 'public', 'Test', 'Journal', 'Juan', 'https://adamson.blackboard.com/?new_loc=%2Fultra%2Fcourse');

-- --------------------------------------------------------

--
-- Table structure for table `dean_trainings`
--

CREATE TABLE `dean_trainings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `organizer` varchar(128) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `certificate_url` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dean_trainings`
--

INSERT INTO `dean_trainings` (`id`, `user_id`, `title`, `organizer`, `date_from`, `date_to`, `certificate_url`, `year`, `provider`, `status`) VALUES
(1, 2, 'Test', NULL, NULL, NULL, 'https://adamson.blackboard.com/?new_loc=%2Fultra%2Fcourse', 2025, 'Test', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `code`, `created_at`) VALUES
(1, 'Computer Science', 'CS', '2025-10-01 06:37:43'),
(2, 'Information Technology', 'IT', '2025-10-01 06:37:43'),
(3, 'Information Systems', 'IS', '2025-10-01 06:37:43');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `category` varchar(64) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'published',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events_page_settings`
--

CREATE TABLE `events_page_settings` (
  `id` int(11) NOT NULL,
  `subhero_lead` text DEFAULT NULL,
  `announcement` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events_page_settings`
--

INSERT INTO `events_page_settings` (`id`, `subhero_lead`, `announcement`, `updated_at`) VALUES
(1, 'Career fairs, forums, workshops, and student showcases happening at CCIT.', NULL, '2025-09-01 19:17:35');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dept` varchar(20) DEFAULT NULL,
  `profile` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `name`, `dept`, `profile`) VALUES
(1, 'Dr. Carmelita Benito', 'itis', 'HTML & CSS specialist. Congratulations for earning the ITS certification!'),
(2, 'Dr. Leonard Alejandro', 'itis', 'Software Development expert. Congratulations for earning the ITS certification!'),
(3, 'Mr. Jessie C. Alamil', 'cs', 'Python and Software Development. Congratulations for earning the ITS certification!'),
(4, 'Mr. Jerome Alvez', 'cs', 'Python and Software Development. Congratulations for earning the ITS certification!'),
(5, 'Mr. Mark Christopher Blanco', 'cs', 'Networking. Congratulations for earning the ITS certification!');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_activity_log`
--

CREATE TABLE `faculty_activity_log` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `activity_type` enum('profile_update','submission_create','submission_update','login','other') NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `related_type` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_activity_log`
--

INSERT INTO `faculty_activity_log` (`id`, `faculty_id`, `activity_type`, `description`, `related_id`, `related_type`, `ip_address`, `user_agent`, `created_at`) VALUES
(2, 2, 'submission_create', 'Submitted certification: AWS Solutions Architect', NULL, NULL, NULL, NULL, '2025-09-27 08:57:35'),
(4, 4, 'profile_update', 'Updated profile information', NULL, NULL, NULL, NULL, '2025-09-24 08:57:35');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_awards`
--

CREATE TABLE `faculty_awards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `issuer` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `certificate_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty_certifications`
--

CREATE TABLE `faculty_certifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `issue_year` int(11) DEFAULT NULL,
  `expire_year` int(11) DEFAULT NULL,
  `credential_id` varchar(128) DEFAULT NULL,
  `credential_url` varchar(255) DEFAULT NULL,
  `visibility` enum('public','private') DEFAULT 'public',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty_certifications_page_settings`
--

CREATE TABLE `faculty_certifications_page_settings` (
  `id` int(11) NOT NULL,
  `subhero_image_url` varchar(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  `subhero_lead` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_certifications_page_settings`
--

INSERT INTO `faculty_certifications_page_settings` (`id`, `subhero_image_url`, `subhero_lead`, `updated_at`) VALUES
(1, '/adamson-ccit/public/assets/images/hero-campus.jpg', 'Professional badges, licenses, and industry certifications held by CCIT faculty.', '2025-09-04 13:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_certification_award`
--

CREATE TABLE `faculty_certification_award` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `certification_id` int(11) NOT NULL,
  `year_earned` varchar(10) DEFAULT NULL,
  `year_expiry` varchar(10) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `is_archived` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_certification_award`
--

INSERT INTO `faculty_certification_award` (`id`, `faculty_id`, `certification_id`, `year_earned`, `year_expiry`, `status`, `is_archived`, `created_at`, `updated_at`) VALUES
(3, 3, 3, '2025', '2028', 'Active', 0, '2025-10-10 18:12:39', '2025-10-10 17:59:00'),
(16, 3, 14, '2022', '', 'Active', 0, '2025-10-10 18:12:39', '2025-10-10 18:26:07'),
(19, 3, 2, '2025', '', 'Active', 1, '2025-10-14 20:26:07', '2025-11-05 12:04:46'),
(21, 3, 13, '2025', '', 'Active', 0, '2025-10-14 20:44:59', '2025-10-14 20:44:59'),
(22, 3, 1, '2024', '', 'Active', 0, '2025-10-18 06:00:26', '2025-10-18 06:00:26'),
(24, 2, 3, '2023', '', 'Active', 0, '2025-10-18 06:06:00', '2025-10-18 19:13:42');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_education`
--

CREATE TABLE `faculty_education` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `degree` varchar(255) DEFAULT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty_experience`
--

CREATE TABLE `faculty_experience` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `period` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty_news`
--

CREATE TABLE `faculty_news` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) DEFAULT NULL,
  `faculty_id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `content` longtext DEFAULT NULL,
  `category` enum('research','achievement','event','announcement','academic') DEFAULT 'announcement',
  `featured_image` varchar(500) DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `status` enum('draft','submitted','approved','published','archived') DEFAULT 'draft',
  `visibility` enum('public','internal','department') DEFAULT 'public',
  `author_byline` varchar(200) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `seo_meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`seo_meta`)),
  `social_media_ready` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `published_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty_notifications`
--

CREATE TABLE `faculty_notifications` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `message` text DEFAULT NULL,
  `type` enum('info','success','warning','error','submission_update') DEFAULT 'info',
  `related_id` int(11) DEFAULT NULL,
  `related_type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `action_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty_performance`
--

CREATE TABLE `faculty_performance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `rating` varchar(64) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty_personal`
--

CREATE TABLE `faculty_personal` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `birthday` date DEFAULT NULL,
  `gender` varchar(32) DEFAULT NULL,
  `marital_status` varchar(32) DEFAULT NULL,
  `nationality` varchar(64) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_number` varchar(64) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_number` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty_portfolio`
--

CREATE TABLE `faculty_portfolio` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `credential_id` varchar(255) DEFAULT NULL,
  `credential_url` varchar(500) DEFAULT NULL,
  `issue_month` int(11) DEFAULT NULL,
  `issue_year` int(11) DEFAULT NULL,
  `expire_month` int(11) DEFAULT NULL,
  `expire_year` int(11) DEFAULT NULL,
  `expires` tinyint(1) DEFAULT 1,
  `visibility` enum('public','private') DEFAULT 'public',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_portfolio`
--

INSERT INTO `faculty_portfolio` (`id`, `user_id`, `name`, `company_id`, `credential_id`, `credential_url`, `issue_month`, `issue_year`, `expire_month`, `expire_year`, `expires`, `visibility`, `created_at`, `updated_at`) VALUES
(1, 3, 'Project Management Professional (PMP)', 37, 'CERT-428749D6', NULL, 12, 2023, NULL, NULL, 0, 'private', '2025-10-01 16:01:25', '2025-10-01 16:01:25'),
(2, 3, 'AWS Certified Solutions Architect', 23, 'CERT-49E991E0', NULL, 3, 2020, NULL, NULL, 0, 'public', '2025-10-01 16:01:25', '2025-10-01 16:01:25'),
(3, 3, 'Cisco Certified Network Associate', 12, 'CERT-2240A22B', NULL, 11, 2024, 4, 2027, 1, 'public', '2025-10-01 16:01:25', '2025-10-01 16:01:25');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_profile`
--

CREATE TABLE `faculty_profile` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `prefix` varchar(20) DEFAULT NULL,
  `surname` varchar(50) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_initial` varchar(10) DEFAULT NULL,
  `dept` varchar(20) NOT NULL,
  `role` varchar(20) NOT NULL,
  `role_order` int(11) DEFAULT 999,
  `title` varchar(255) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `avatar_initials` varchar(4) DEFAULT NULL,
  `badges` text DEFAULT NULL,
  `ordering` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(32) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `employee_id` varchar(32) DEFAULT NULL,
  `work_email` varchar(128) DEFAULT NULL,
  `position` varchar(128) DEFAULT NULL,
  `department` varchar(128) DEFAULT NULL,
  `employment_type` varchar(64) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `office_location` varchar(128) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `specializations` text DEFAULT NULL,
  `languages` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_profile`
--

INSERT INTO `faculty_profile` (`id`, `name`, `prefix`, `surname`, `suffix`, `first_name`, `middle_initial`, `dept`, `role`, `role_order`, `title`, `avatar_url`, `avatar_initials`, `badges`, `ordering`, `created_at`, `updated_at`, `status`, `user_id`, `full_name`, `last_name`, `employee_id`, `work_email`, `position`, `department`, `employment_type`, `date_hired`, `office_location`, `profile_photo`, `specializations`, `languages`) VALUES
(2, 'Mr. Archie G. Santiago, MSIT', 'Mr.', 'Santiago', 'MSIT', 'Archie', 'G.', 'itis', 'chair', 2, '', 'http://localhost/adamson-ccit/public/uploads/faculty/faculty_68d8482deb052_1759004717.png', 'AS', 'IT&IS, Chairperson', 0, '2025-09-01 20:41:32', '2025-10-18 07:02:31', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Mrs. Charlene I. Vergara-Gonzales', 'Mrs.', 'Vergara-Gonzales', '', 'Charlene', 'I.', 'itis', 'full', 3, '', '/adamson-ccit/public/uploads/faculty/faculty_68f33da125f74_1760771489.png', 'CV', 'IT&IS, Full-Time Faculty', 0, '2025-09-01 20:41:32', '2025-10-18 07:11:29', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Mr. Jessie Alamil', 'Mr.', 'Alamil', '', 'Jessie', '', 'cs', 'part', 4, '', '/adamson-ccit/public/uploads/faculty/faculty_68f33e168ac40_1760771606.png', 'JA', 'CS, Part-Time Faculty', 0, '2025-09-01 20:41:32', '2025-10-18 07:13:26', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'Dr. Carmelita H. Benito', 'Dr.', 'Benito', '', 'Carmelita H.', '', 'itis', 'full', 3, '', '/adamson-ccit/public/uploads/faculty/faculty_68f33d2e616bd_1760771374.png', 'CB', 'IT&IS, Full-Time Faculty', 0, '2025-10-08 20:39:22', '2025-10-18 07:09:34', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 'Ms. Anette G. Daligcon, MSIT', 'Ms.', 'Daligcon', 'MSIT', 'Anette', 'G.', 'itis', 'full', 3, '', '', 'AD', 'IT&IS, Full-Time Faculty', 0, '2025-10-08 21:25:28', '2025-10-18 06:43:35', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'Dr. Leonard L. Alejandro', 'Dr.', 'Alejandro', '', 'Leonard', 'L.', 'admin', 'dean', 1, 'Administration,Dean', '/adamson-ccit/public/uploads/faculty/faculty_68ec5c005d436_1760320512.png', 'LA', 'Administration,Dean', 0, '2025-10-13 01:55:12', '2025-10-13 01:55:12', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 'Mr. Adrias Q. Dominique ', 'Mr.', 'Dominique ', '', 'Adrias', 'Q.', 'itis', 'part', 4, '', '', 'AD', 'IT&IS, Part-Time Faculty', 0, '2025-10-18 06:28:00', '2025-10-18 06:43:52', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 'Mrs. Gloria R. Dela Cruz', 'Mrs.', 'Dela Cruz', '', 'Gloria', 'R.', 'itis', 'full', 3, '', '', 'GD', 'IT&IS, Full-Time Faculty', 0, '2025-10-18 06:41:12', '2025-10-18 06:43:01', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 'Mr. Jun  O. Bumagat', 'Mr.', 'Bumagat', '', 'Jun ', 'O.', 'itis', 'part', 4, '', '', 'JB', 'IT&IS, Part-Time Faculty', 0, '2025-10-18 06:44:52', '2025-10-18 06:44:52', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 'Mr. Mark Christopher  R. Blanco', 'Mr.', 'Blanco', '', 'Mark Christopher ', 'R.', 'itis', 'full', 3, '', '', 'MB', 'IT&IS, Full-Time Faculty', 0, '2025-10-18 06:45:46', '2025-10-18 06:45:46', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 'Mr. Mark Anthony J. Esmeralda', 'Mr.', 'Esmeralda', '', 'Mark Anthony', 'J.', 'itis', 'full', 3, '', '', 'ME', 'IT&IS, Full-Time Faculty', 0, '2025-10-18 06:46:23', '2025-10-18 06:46:23', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 'Ms. Latosa U. Lesliean', 'Ms.', 'Lesliean', '', 'Latosa', 'U.', 'itis', 'full', 3, '', '', 'LL', 'IT&IS, Full-Time Faculty', 0, '2025-10-18 06:46:53', '2025-10-18 06:46:53', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 'Dr. Jake M. Libed', 'Dr.', 'Libed', '', 'Jake', 'M.', 'itis', 'part', 4, '', '', 'JL', 'IT&IS, Part-Time Faculty', 0, '2025-10-18 06:47:21', '2025-10-18 06:47:21', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 'Maria Jasmin I. Villanueva', '', 'Villanueva', '', 'Maria Jasmin', 'I.', 'itis', 'full', 3, '', '', 'MV', 'IT&IS, Full-Time Faculty', 0, '2025-10-18 06:49:46', '2025-10-18 06:49:46', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 'Mrs. Quintina  M. Racal-Verceles', 'Mrs.', 'Racal-Verceles', '', 'Quintina ', 'M.', 'itis', 'full', 3, '', '', 'QR', 'IT&IS, Full-Time Faculty', 0, '2025-10-18 06:50:34', '2025-10-18 06:50:34', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 'Dr. Felnita V. Tan', 'Dr.', 'Tan', '', 'Felnita', 'V.', 'itis', 'full', 3, '', '', 'FT', 'IT&IS, Full-Time Faculty', 0, '2025-10-18 06:51:53', '2025-10-18 06:51:53', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 'Mrs. Rizalina C. Valencia', 'Mrs.', 'Valencia', '', 'Rizalina', 'C.', 'itis', 'full', 3, '', '', 'RV', 'IT&IS, Full-Time Faculty', 0, '2025-10-18 06:53:00', '2025-10-18 06:53:00', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 'Mr. Amado Sapit III', 'Mr.', 'Sapit', 'III', 'Amado', '', 'itis', 'part', 4, '', '', 'AS', 'IT&IS, Part-Time Faculty', 0, '2025-10-18 06:53:50', '2025-10-18 06:53:50', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 'Dr. Niña Ana Marie Jocelyn A. Sales', 'Dr.', 'Sales', '', 'Niña Ana Marie Jocelyn', 'A.', 'itis', 'full', 3, '', '', 'NS', 'IT&IS, Full-Time Faculty', 0, '2025-10-18 06:54:16', '2025-10-18 06:54:16', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 'Ms. Maricel G. Barrameda', 'Ms.', 'Barrameda', '', 'Maricel', 'G.', 'itis', 'full', 3, '', '', 'MB', 'IT&IS, Full-Time Faculty', 0, '2025-10-18 06:55:13', '2025-10-18 06:55:13', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 'Mrs. Ma. Carmela M. Racelis', 'Mrs.', 'Racelis', '', 'Ma. Carmela', 'M.', 'itis', 'full', 3, '', '/adamson-ccit/public/uploads/faculty/faculty_68f33d15c68ba_1760771349.png', 'MR', 'IT&IS, Full-Time Faculty', 0, '2025-10-18 06:56:08', '2025-10-18 07:09:09', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 'Mr. Jay Abaleta', 'Mr.', 'Abaleta', '', 'Jay', '', 'cs', 'full', 3, '', '/adamson-ccit/public/uploads/faculty/faculty_68f33cb860419_1760771256.png', 'JA', 'CS, Full-Time Faculty', 0, '2025-10-18 06:57:00', '2025-10-18 07:07:36', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 'Mr. Ivan Andrei Abalos ', 'Mr.', 'Abalos ', '', 'Ivan Andrei', '', 'cs', 'full', 3, '', '', 'IA', 'CS, Full-Time Faculty', 0, '2025-10-18 06:57:22', '2025-10-18 06:57:22', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 'Mr. Jerome Alvez', 'Mr.', 'Alvez', '', 'Jerome', '', 'cs', 'full', 3, '', '/adamson-ccit/public/uploads/faculty/faculty_68f33d22735d5_1760771362.png', 'JA', 'CS, Full-Time Faculty', 0, '2025-10-18 06:57:43', '2025-10-18 07:09:22', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 'Mr. Billy Jaed Angeles', 'Mr.', 'Angeles', '', 'Billy Jaed', '', 'cs', 'full', 3, '', '', 'BA', 'CS, Full-Time Faculty', 0, '2025-10-18 06:58:05', '2025-10-18 06:58:05', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 'Renato Baisa', '', 'Baisa', '', 'Renato', '', 'cs', 'full', 3, '', '/adamson-ccit/public/uploads/faculty/faculty_68f33dbbb6372_1760771515.jpg', 'RB', 'CS, Full-Time Faculty', 0, '2025-10-18 06:58:27', '2025-10-18 07:11:55', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 'Mr. Edward  Bustillos', 'Mr.', 'Bustillos', '', 'Edward ', '', 'cs', 'full', 3, '', '', 'EB', 'CS, Full-Time Faculty', 0, '2025-10-18 06:58:44', '2025-10-18 06:58:44', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 'Mr. Paul Jacob Cruz', 'Mr.', 'Cruz', '', 'Paul Jacob', '', 'cs', 'full', 3, '', '/adamson-ccit/public/uploads/faculty/faculty_68f33d3c09715_1760771388.png', 'PC', 'CS, Full-Time Faculty', 0, '2025-10-18 06:59:00', '2025-10-18 07:09:48', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 'Mr. Joel Hernandez', 'Mr.', 'Hernandez', '', 'Joel', '', 'cs', 'full', 3, '', '', 'JH', 'CS, Full-Time Faculty', 0, '2025-10-18 06:59:15', '2025-10-18 06:59:15', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 'Mr. Von Erick M. Magbitang', 'Mr.', 'Magbitang', '', 'Von Erick', 'M.', 'cs', 'full', 3, '', '', 'VM', 'CS, Full-Time Faculty', 0, '2025-10-18 06:59:40', '2025-10-18 06:59:40', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 'Ms. Ma. Christina Navarro', 'Ms.', 'Navarro', '', 'Ma. Christina', '', 'cs', 'chair', 2, '', '/adamson-ccit/public/uploads/faculty/faculty_68f33cae7950c_1760771246.png', 'MN', 'CS, Chairperson', 0, '2025-10-18 07:00:27', '2025-10-18 07:07:26', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 'Mr. Carlo Felipe Poblete', 'Mr.', 'Poblete', '', 'Carlo Felipe', '', 'cs', 'full', 3, '', '/adamson-ccit/public/uploads/faculty/faculty_68f33d715d3f2_1760771441.png', 'CP', 'CS, Full-Time Faculty', 0, '2025-10-18 07:00:54', '2025-10-18 07:10:41', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(81, 'Dr. Davood Pour Yousefian Barfeh', 'Dr.', 'Barfeh', '', 'Davood Pour Yousefian', '', 'cs', 'lecturer', 5, '', '/adamson-ccit/public/uploads/faculty/faculty_68f33e2149f1b_1760771617.png', 'DB', 'CS, Special Lecturer', 0, '2025-10-18 07:01:28', '2025-10-18 07:13:37', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(82, 'Dr. Alvin Alon', 'Dr.', 'Alon', '', 'Alvin', '', 'mit', 'full', 3, '', '', 'AA', 'MIT, Full-Time Faculty', 0, '2025-10-18 07:04:31', '2025-10-18 07:04:31', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 'Mr. Jesus D. S. Paguigan', 'Mr.', 'Paguigan', '', 'Jesus', 'D. S.', 'mit', 'part', 4, '', '', 'JP', 'MIT, Part-Time Faculty', 0, '2025-10-18 07:05:05', '2025-10-18 07:05:05', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 'Mrs. Marvi Bayrante', 'Mrs.', 'Bayrante', '', 'Marvi', '', 'itis', 'part', 4, '', '', 'MB', 'IT&IS, Part-Time Faculty', 0, '2025-10-18 10:12:02', '2025-10-18 10:15:02', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(87, '', NULL, NULL, NULL, NULL, NULL, '', '', 999, NULL, NULL, NULL, NULL, 0, '2025-11-04 18:34:49', '2025-11-04 18:34:49', NULL, 3, 'Felnita Tan', NULL, '2008060084', 'felnita.tan@adamson.edu.ph', 'Faculty', 'College of Computer and Information Technology', '', '0000-00-00', '', NULL, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_profiles`
--

CREATE TABLE `faculty_profiles` (
  `faculty_id` int(11) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `employee_id` varchar(32) DEFAULT NULL,
  `work_email` varchar(128) DEFAULT NULL,
  `position` varchar(128) DEFAULT NULL,
  `department` varchar(128) DEFAULT NULL,
  `employment_type` varchar(64) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `office_location` varchar(128) DEFAULT NULL,
  `status` varchar(64) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `specializations` text DEFAULT NULL,
  `languages` text DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `first_name` varchar(128) DEFAULT NULL,
  `last_name` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty_profile_page_settings`
--

CREATE TABLE `faculty_profile_page_settings` (
  `id` int(11) NOT NULL,
  `subhero_image_url` varchar(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  `subhero_lead` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_profile_page_settings`
--

INSERT INTO `faculty_profile_page_settings` (`id`, `subhero_image_url`, `subhero_lead`, `updated_at`) VALUES
(1, '/adamson-ccit/public/assets/images/hero-campus.jpg', 'College of Computing & Information Technology — administration and faculty roster.', '2025-09-01 20:41:32');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_research`
--

CREATE TABLE `faculty_research` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `journal` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `view_url` varchar(500) DEFAULT NULL,
  `authors` text DEFAULT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `conference` varchar(255) DEFAULT NULL,
  `doi_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_research`
--

INSERT INTO `faculty_research` (`id`, `user_id`, `title`, `journal`, `year`, `view_url`, `authors`, `doi`, `publisher`, `conference`, `doi_url`, `created_at`, `updated_at`, `status`) VALUES
(39, 0, 'AdUTrack: A Mobile Application Tracker for Facilities Maintenance and Waste Detection Using Machine Learning at Adamson University', NULL, 2024, 'https://ieeexplore.ieee.org/document/11082862', 'da', '10.1109/ICBIR65229.2025.11163172', 'IEEE', '15th International Conference on Advanced Computer Information Technologies (ACIT 2025)', NULL, '2025-11-05 12:28:04', '2025-11-05 12:28:15', 'archived');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_research_page_settings`
--

CREATE TABLE `faculty_research_page_settings` (
  `id` int(11) NOT NULL,
  `subhero_image_url` varchar(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  `subhero_lead` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_research_page_settings`
--

INSERT INTO `faculty_research_page_settings` (`id`, `subhero_image_url`, `subhero_lead`, `updated_at`) VALUES
(1, '/adamson-ccit/public/assets/images/hero-campus.jpg', 'Peer-reviewed publications, presentations, and other scholarly work by CCIT faculty.', '2025-09-01 20:44:05');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_submissions`
--

CREATE TABLE `faculty_submissions` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `submission_type` enum('research','certification','news','event','announcement') NOT NULL,
  `title` varchar(500) NOT NULL,
  `description` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `related_item_id` int(11) DEFAULT NULL COMMENT 'ID of the related research or certification record',
  `file_path` varchar(500) DEFAULT NULL,
  `status` enum('draft','submitted','under_review','approved','rejected','published') DEFAULT 'draft',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_submissions`
--

INSERT INTO `faculty_submissions` (`id`, `faculty_id`, `submission_type`, `title`, `description`, `content`, `category`, `related_item_id`, `file_path`, `status`, `submitted_at`, `reviewed_at`, `reviewed_by`, `review_notes`, `priority`, `tags`, `metadata`, `created_at`, `updated_at`) VALUES
(119, 3, 'certification', 'HTML & CSS', '', '{\"cert_title\":\"HTML & CSS\",\"issuer\":\"Certiport\",\"certification_id\":1,\"year_earned\":\"2025\",\"year_expiry\":\"\",\"credential_id\":\"\",\"verification_url\":\"\",\"description\":\"\"}', 'Professional Certification', NULL, NULL, 'approved', '2025-10-14 20:34:18', '2025-10-14 20:34:35', 2, '', 'medium', NULL, NULL, '2025-10-14 20:34:18', '2025-10-14 20:34:35'),
(120, 3, 'certification', 'Device Configuration and Management', '', '{\"cert_title\":\"Device Configuration and Management\",\"issuer\":\"Certiport\",\"certification_id\":13,\"year_earned\":\"2025\",\"year_expiry\":\"\",\"credential_id\":\"\",\"verification_url\":\"\",\"description\":\"\"}', 'Professional Certification', NULL, NULL, 'approved', '2025-10-14 20:44:47', '2025-10-14 20:44:59', 2, '', 'medium', NULL, NULL, '2025-10-14 20:44:47', '2025-10-14 20:44:59'),
(140, 3, 'certification', 'Software Development', '', '{\"cert_title\":\"Software Development\",\"issuer\":\"Certiport\",\"certification_id\":2,\"year_earned\":\"202s\",\"year_expiry\":\"\",\"credential_id\":\"\",\"verification_url\":\"\",\"description\":\"\"}', 'Professional Certification', NULL, NULL, 'approved', '2025-10-18 12:47:35', '2025-10-18 12:47:59', 2, '', 'medium', NULL, NULL, '2025-10-18 12:47:35', '2025-10-18 12:47:59'),
(141, 3, 'certification', 'Software Development', '', '{\"cert_title\":\"Software Development\",\"issuer\":\"Certiport\",\"certification_id\":2,\"year_earned\":\"202s\",\"year_expiry\":\"\",\"credential_id\":\"\",\"verification_url\":\"\",\"description\":\"\"}', 'Professional Certification', NULL, NULL, 'approved', '2025-10-18 12:48:34', '2025-10-18 12:48:55', 2, 'yes', 'medium', NULL, NULL, '2025-10-18 12:48:34', '2025-10-18 12:48:55'),
(143, 3, 'certification', 'Networking', '', '{\"cert_title\":\"Networking\",\"issuer\":\"Certiport\",\"certification_id\":4,\"year_earned\":\"2020\",\"year_expiry\":\"\",\"credential_id\":\"\",\"verification_url\":\"\",\"description\":\"\"}', 'Professional Certification', NULL, NULL, 'approved', '2025-10-18 12:49:58', '2025-10-18 12:50:22', 2, '', 'medium', NULL, NULL, '2025-10-18 12:49:58', '2025-10-18 12:50:22'),
(145, 3, 'certification', 'HTML & CSS', '', '{\"cert_title\":\"HTML & CSS\",\"issuer\":\"Certiport\",\"certification_id\":1,\"year_earned\":\"2020\",\"year_expiry\":\"\",\"credential_id\":\"\",\"verification_url\":\"\",\"description\":\"\"}', 'Professional Certification', NULL, NULL, 'approved', '2025-10-18 12:55:14', '2025-10-18 12:55:28', 2, '', 'medium', NULL, NULL, '2025-10-18 12:55:14', '2025-10-18 12:55:28'),
(146, 3, 'certification', 'HTML & CSS', '', '{\"cert_title\":\"HTML & CSS\",\"issuer\":\"Certiport\",\"certification_id\":1,\"year_earned\":\"2020\",\"year_expiry\":\"\",\"credential_id\":\"\",\"verification_url\":\"\",\"description\":\"\"}', 'Professional Certification', NULL, NULL, 'approved', '2025-10-18 12:55:33', '2025-10-18 12:56:02', 2, '', 'medium', NULL, NULL, '2025-10-18 12:55:33', '2025-10-18 12:56:02'),
(307, 3, 'news', 'Updated CCIT Tech Fair 2025', 'The 2025 Tech Fair featured new exhibits and student-led workshops.', 'The 2025 Tech Fair featured new exhibits and student-led workshops.', 'research', NULL, NULL, 'rejected', '2025-10-22 18:34:49', '2025-10-22 18:48:38', 2, 'Revise', 'medium', NULL, NULL, '2025-10-22 18:34:49', '2025-10-22 18:48:38'),
(308, 3, 'news', 'CCIT Rise', 'For testing only', 'For testing only', 'news', NULL, NULL, 'approved', '2025-10-22 18:36:09', '2025-10-22 18:46:17', 2, '', 'medium', NULL, NULL, '2025-10-22 18:36:09', '2025-10-22 18:46:17'),
(335, 3, 'news', 'Test', 'Testing only', 'Testing only', 'news', NULL, NULL, 'approved', '2025-10-24 15:42:22', '2025-10-24 15:43:43', 2, '', 'medium', NULL, NULL, '2025-10-24 15:42:22', '2025-10-24 15:43:43'),
(347, 3, 'news', 'Test', 'Hello Hello', 'Hello Hello', 'news', NULL, NULL, 'approved', '2025-10-24 16:01:12', '2025-10-24 16:01:36', 2, '', 'medium', NULL, NULL, '2025-10-24 16:01:12', '2025-10-24 16:01:36'),
(353, 3, 'news', 'Hello', 'helooooooooo', 'helooooooooo', 'news', NULL, NULL, 'approved', '2025-11-03 02:50:13', '2025-11-03 02:50:32', 2, '', 'medium', NULL, NULL, '2025-11-03 02:50:13', '2025-11-03 02:50:32'),
(354, 3, 'news', 'hello', 'helloooooooo', 'helloooooooo', 'news', NULL, NULL, 'approved', '2025-11-04 18:36:20', '2025-11-04 18:36:46', 24, '', 'medium', NULL, NULL, '2025-11-04 18:36:20', '2025-11-04 18:36:46');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_trainings`
--

CREATE TABLE `faculty_trainings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `certificate_url` varchar(255) DEFAULT NULL,
  `status` enum('active','expired') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `footer_links`
--

CREATE TABLE `footer_links` (
  `id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `url` varchar(512) NOT NULL,
  `kind` enum('quick','legal') NOT NULL DEFAULT 'quick',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_external` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `footer_links`
--

INSERT INTO `footer_links` (`id`, `label`, `url`, `kind`, `sort_order`, `is_external`, `created_at`) VALUES
(1, 'Freshman Admission', '/adamson-ccit/public/index.php?page=admission_freshman', 'quick', 1, 0, '2025-09-12 05:29:13'),
(2, 'Transferee Admission', '/adamson-ccit/public/index.php?page=admission_transfer', 'quick', 2, 0, '2025-09-12 05:29:13'),
(3, 'Foreign Admission', '/adamson-ccit/public/index.php?page=admission_foreign', 'quick', 3, 0, '2025-09-12 05:29:13'),
(4, 'Requirements', '/adamson-ccit/public/index.php?page=admission_requirements', 'quick', 4, 0, '2025-09-12 05:29:13'),
(5, 'Admission FAQ', '/adamson-ccit/public/index.php?page=admission_faq', 'quick', 5, 0, '2025-09-12 05:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `footer_settings`
--

CREATE TABLE `footer_settings` (
  `id` int(11) NOT NULL,
  `org_name` varchar(255) NOT NULL DEFAULT 'Adamson University',
  `college_name` varchar(255) NOT NULL DEFAULT 'College of Computing and Information Technology',
  `addr_line1` varchar(255) NOT NULL DEFAULT '900 San Marcelino Street, Ermita',
  `addr_line2` varchar(255) NOT NULL DEFAULT '1000 Manila, Philippines',
  `phone_display` varchar(100) NOT NULL DEFAULT '(02) 8524-20-11 loc. 324',
  `phone_tel` varchar(50) NOT NULL DEFAULT '+63285242011',
  `email` varchar(255) NOT NULL DEFAULT 'ccit@adamson.edu.ph',
  `website_label` varchar(255) NOT NULL DEFAULT 'https://www.adamson.edu.ph/cfe/',
  `website_url` varchar(255) NOT NULL DEFAULT 'https://www.adamson.edu.ph/cfe/',
  `legal_note` varchar(255) NOT NULL DEFAULT 'All Rights Reserved',
  `powered_by` varchar(255) NOT NULL DEFAULT 'Powered by Information Technology Center',
  `trademark_note` varchar(255) NOT NULL DEFAULT 'Trademark Notice',
  `sitemap_label` varchar(255) NOT NULL DEFAULT 'Site Map',
  `sitemap_url` varchar(255) NOT NULL DEFAULT '#',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `footer_settings`
--

INSERT INTO `footer_settings` (`id`, `org_name`, `college_name`, `addr_line1`, `addr_line2`, `phone_display`, `phone_tel`, `email`, `website_label`, `website_url`, `legal_note`, `powered_by`, `trademark_note`, `sitemap_label`, `sitemap_url`, `updated_at`) VALUES
(1, 'Adamson University', 'College of Computing and Information Technology', '900 San Marcelino Street, Ermita', '1000 Manila, Philippines', '(02) 8524-20-11 loc. 324', '+63285242011', 'ccit@adamson.edu.ph', 'https://www.adamson.edu.ph/cfe/', 'https://www.adamson.edu.ph/cfe/', 'All Rights Reserved', 'Powered by Information Technology Center', 'Trademark Notice', 'Site Map', '#', '2025-09-12 05:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `footer_socials`
--

CREATE TABLE `footer_socials` (
  `id` int(11) NOT NULL,
  `platform` enum('facebook','tiktok','x','youtube','instagram','linkedin','other') NOT NULL,
  `label` varchar(100) NOT NULL,
  `url` varchar(512) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `footer_socials`
--

INSERT INTO `footer_socials` (`id`, `platform`, `label`, `url`, `sort_order`, `is_enabled`, `created_at`) VALUES
(1, 'facebook', 'Facebook', 'https://www.facebook.com/p/Adamson-University-College-of-Computing-and-Information-Technology-61566114474650/', 1, 1, '2025-09-12 05:29:13'),
(2, 'tiktok', 'TikTok', 'https://www.tiktok.com/@adamsonuccit', 2, 1, '2025-09-12 05:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `header_menu`
--

CREATE TABLE `header_menu` (
  `id` int(11) NOT NULL,
  `label` varchar(120) NOT NULL,
  `url` varchar(512) DEFAULT NULL,
  `type` enum('link','dropdown') NOT NULL DEFAULT 'link',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `header_menu`
--

INSERT INTO `header_menu` (`id`, `label`, `url`, `type`, `sort_order`) VALUES
(1, 'Home', '/adamson-ccit/public/index.php', 'link', 1),
(2, 'About', NULL, 'dropdown', 2),
(3, 'News', '/adamson-ccit/public/index.php?page=news', 'link', 3),
(4, 'Admission', NULL, 'dropdown', 4),
(5, 'Programs', NULL, 'dropdown', 5),
(6, 'Student', NULL, 'dropdown', 6),
(7, 'Faculty', NULL, 'dropdown', 7);

-- --------------------------------------------------------

--
-- Table structure for table `header_menu_items`
--

CREATE TABLE `header_menu_items` (
  `id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `label` varchar(160) NOT NULL,
  `url` varchar(512) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `header_menu_items`
--

INSERT INTO `header_menu_items` (`id`, `menu_id`, `label`, `url`, `sort_order`) VALUES
(1, 2, 'History', '/adamson-ccit/public/index.php?page=about_history', 1),
(2, 2, 'Mission & Vision', '/adamson-ccit/public/index.php?page=about_vision_mission', 2),
(3, 4, 'Freshman', '/adamson-ccit/public/index.php?page=admission_freshman', 1),
(4, 4, 'Transferee', '/adamson-ccit/public/index.php?page=admission_transferee', 2),
(5, 4, 'Graduate School and Juris Doctor', '/adamson-ccit/public/index.php?page=admission_graduate_school', 3),
(6, 5, 'Undergraduate', '/adamson-ccit/public/index.php?page=programs_undergraduate', 1),
(7, 5, 'Graduate Studies', '/adamson-ccit/public/index.php?page=programs_graduate_studies', 2),
(8, 6, 'Organizations', '/adamson-ccit/public/index.php?page=student_organizations', 1),
(9, 6, 'Scholarships', '/adamson-ccit/public/index.php?page=student_scholarships', 2),
(10, 6, 'Research', '/adamson-ccit/public/index.php?page=student_research', 3),
(11, 6, 'Certifications', '/adamson-ccit/public/index.php?page=student_certifications', 4),
(12, 6, 'Testimonials', '/adamson-ccit/public/index.php?page=student_testimonials', 5),
(13, 7, 'Profile', '/adamson-ccit/public/index.php?page=faculty_profile', 1),
(14, 7, 'Research', '/adamson-ccit/public/index.php?page=faculty_research', 2),
(15, 7, 'Certifications', '/adamson-ccit/public/index.php?page=faculty_certifications', 3),
(16, 2, 'Dean\'s Corner', '/adamson-ccit/public/index.php?page=deans_corner', 3);

-- --------------------------------------------------------

--
-- Table structure for table `header_settings`
--

CREATE TABLE `header_settings` (
  `id` int(11) NOT NULL,
  `logo_url` varchar(512) NOT NULL DEFAULT '/adamson-ccit/public/assets/images/adamson-ccit-logo.png',
  `cta_label` varchar(120) NOT NULL DEFAULT 'Career Pathway Generator',
  `cta_url` varchar(512) NOT NULL DEFAULT '/adamson-ccit/public/index.php?page=career_pathway_generator',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `header_settings`
--

INSERT INTO `header_settings` (`id`, `logo_url`, `cta_label`, `cta_url`, `updated_at`) VALUES
(1, '/adamson-ccit/public/assets/images/adamson-ccit-logo.png', 'Career Pathway Generator', '/adamson-ccit/public/index.php?page=career_pathway_generator', '2025-09-12 05:39:45');

-- --------------------------------------------------------

--
-- Table structure for table `header_utility_links`
--

CREATE TABLE `header_utility_links` (
  `id` int(11) NOT NULL,
  `label` varchar(120) NOT NULL,
  `url` varchar(512) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_external` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `header_utility_links`
--

INSERT INTO `header_utility_links` (`id`, `label`, `url`, `sort_order`, `is_external`) VALUES
(1, 'AdU Website', 'https://www.adamson.edu.ph/2018/', 1, 1),
(2, 'AdU Live', 'https://live.adamson.edu.ph/login', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `homepage_quick_actions`
--

CREATE TABLE `homepage_quick_actions` (
  `id` int(11) NOT NULL,
  `label` varchar(100) NOT NULL,
  `icon` text NOT NULL COMMENT 'SVG icon code',
  `url` varchar(255) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homepage_quick_actions`
--

INSERT INTO `homepage_quick_actions` (`id`, `label`, `icon`, `url`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Admissions', '<svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\"><path fill=\"currentColor\" d=\"M5 4h14a1 1 0 0 1 1 1v13l-3-2-3 2-3-2-3 2-3-2V5a1 1 0 0 1 1-1z\"/></svg>', '/adamson-ccit/public/index.php?page=admission_freshman', 1, 1, '2025-09-26 18:36:18', '2025-11-02 16:44:36'),
(2, 'Programs', '<svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\"><path fill=\"currentColor\" d=\"M12 3 1 9l11 6 9-4.91V17h2V9L12 3z\"/></svg>', '/adamson-ccit/public/index.php?page=programs_undergraduate', 2, 1, '2025-09-26 18:36:18', '2025-09-26 18:36:18'),
(3, 'Scholarships', '<svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\"><path fill=\"currentColor\" d=\"M12 2a7 7 0 1 1-4.95 2.05A7 7 0 0 1 12 2zm-1 8h2v6h-2zm0 8h2v2h-2z\"/></svg>', '/adamson-ccit/public/index.php?page=student_scholarships', 3, 1, '2025-09-26 18:36:18', '2025-09-26 18:36:18'),
(4, 'Student Life', '<svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\"><path fill=\"currentColor\" d=\"M12 2a5 5 0 1 1-5 5 5 5 0 0 1 5-5Zm8 18v-2H4v-2a6 6 0 0 1 8-5.29A6 6 0 0 1 20 20Z\"/></svg>', '/adamson-ccit/public/index.php?page=student_organizations', 4, 1, '2025-09-26 18:36:18', '2025-09-26 18:36:18'),
(5, 'Faculty', '<svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\"><path fill=\"currentColor\" d=\"M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-7 9v-2a7 7 0 0 1 14 0v2Z\"/></svg>', '/adamson-ccit/public/index.php?page=faculty_profile', 5, 1, '2025-09-26 18:36:18', '2025-09-26 18:36:18'),
(6, 'News', '<svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\"><path fill=\"currentColor\" d=\"M4 4h16v2H4zm0 4h10v2H4zm0 4h16v2H4zm0 4h10v2H4z\"/></svg>', '/adamson-ccit/public/index.php?page=news', 6, 1, '2025-09-26 18:36:18', '2025-09-26 18:36:18');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_settings`
--

CREATE TABLE `homepage_settings` (
  `id` int(11) NOT NULL,
  `hero_eyebrow` varchar(255) DEFAULT NULL,
  `hero_title` varchar(255) DEFAULT NULL,
  `hero_subtitle` varchar(255) DEFAULT NULL,
  `hero_bg` varchar(255) DEFAULT NULL,
  `btn_primary_text` varchar(255) DEFAULT NULL,
  `btn_primary_url` varchar(255) DEFAULT NULL,
  `btn_secondary_text` varchar(255) DEFAULT NULL,
  `btn_secondary_url` varchar(255) DEFAULT NULL,
  `why_title` varchar(255) DEFAULT NULL,
  `why_subtitle` varchar(255) DEFAULT NULL,
  `why_faculty_text` varchar(255) DEFAULT NULL,
  `why_faculty_desc` text DEFAULT NULL,
  `why_faculty_link_label` varchar(255) DEFAULT NULL,
  `why_faculty_link` varchar(255) DEFAULT NULL,
  `why_facilities_text` varchar(255) DEFAULT NULL,
  `why_facilities_desc` text DEFAULT NULL,
  `why_facilities_link_label` varchar(255) DEFAULT NULL,
  `why_facilities_link` varchar(255) DEFAULT NULL,
  `why_career_text` varchar(255) DEFAULT NULL,
  `why_career_desc` text DEFAULT NULL,
  `why_career_link_label` varchar(255) DEFAULT NULL,
  `why_career_link` varchar(255) DEFAULT NULL,
  `spotlight_eyebrow` varchar(255) DEFAULT NULL,
  `spotlight_title` varchar(255) DEFAULT NULL,
  `spotlight_blurb` text DEFAULT NULL,
  `spotlight_image` varchar(255) DEFAULT NULL,
  `spotlight_image_alt` varchar(255) DEFAULT NULL,
  `spotlight_cta_text` varchar(255) DEFAULT NULL,
  `spotlight_cta_url` varchar(255) DEFAULT NULL,
  `spotlight_cta2_text` varchar(255) DEFAULT NULL,
  `spotlight_cta2_url` varchar(255) DEFAULT NULL,
  `spotlight_video_url` varchar(255) DEFAULT NULL,
  `cta_title` varchar(255) DEFAULT NULL,
  `cta_description` text DEFAULT NULL,
  `cta_action_label` varchar(255) DEFAULT NULL,
  `cta_action_url` varchar(255) DEFAULT NULL,
  `show_pinned_announcements` tinyint(1) DEFAULT 0,
  `why_faculty_image` varchar(255) DEFAULT NULL,
  `why_faculty_image_alt` varchar(255) DEFAULT NULL,
  `why_facilities_image` varchar(255) DEFAULT NULL,
  `why_facilities_image_alt` varchar(255) DEFAULT NULL,
  `why_career_image` varchar(255) DEFAULT NULL,
  `why_career_image_alt` varchar(255) DEFAULT NULL,
  `why_faculty_url` varchar(255) DEFAULT NULL,
  `why_facilities_url` varchar(255) DEFAULT NULL,
  `why_career_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homepage_settings`
--

INSERT INTO `homepage_settings` (`id`, `hero_eyebrow`, `hero_title`, `hero_subtitle`, `hero_bg`, `btn_primary_text`, `btn_primary_url`, `btn_secondary_text`, `btn_secondary_url`, `why_title`, `why_subtitle`, `why_faculty_text`, `why_faculty_desc`, `why_faculty_link_label`, `why_faculty_link`, `why_facilities_text`, `why_facilities_desc`, `why_facilities_link_label`, `why_facilities_link`, `why_career_text`, `why_career_desc`, `why_career_link_label`, `why_career_link`, `spotlight_eyebrow`, `spotlight_title`, `spotlight_blurb`, `spotlight_image`, `spotlight_image_alt`, `spotlight_cta_text`, `spotlight_cta_url`, `spotlight_cta2_text`, `spotlight_cta2_url`, `spotlight_video_url`, `cta_title`, `cta_description`, `cta_action_label`, `cta_action_url`, `show_pinned_announcements`, `why_faculty_image`, `why_faculty_image_alt`, `why_facilities_image`, `why_facilities_image_alt`, `why_career_image`, `why_career_image_alt`, `why_faculty_url`, `why_facilities_url`, `why_career_url`) VALUES
(1, 'Welcome to CCIT', 'Catalyzing Change,\r\nInnovating for Tomorrow', 'Join us as we shape the future of technology!', '', 'Enroll Now', '/adamson-ccit/public/index.php?page=admission_freshman', '360° Virtual Tour', '/adamson-ccit/public/index.php?page=virtual_tour', 'Why Choose Adamson CCIT?', 'The CCIT advantage: people, places, and pathways.', 'Experienced Faculty', 'Guidance from expert educators and industry professionals.', 'Meet Our Faculty', '/adamson-ccit/public/index.php?page=faculty_profile', 'State-of-the-Art Facilities', 'Modern labs and collaborative learning spaces.', 'Tour Our Campus', '/adamson-ccit/public/index.php?page=virtual_tour', 'Career Opportunities', 'Strong industry partnerships for internships and career opportunities.', 'View Industry Partners', '#partners', 'CCIT Spotlight', 'Where Learning Meets Innovation: Life at CCIT', 'Through labs, research, and industry partnerships, CCIT students develop practical skills, confidence, and camaraderie — a true Klasmeyt culture built on excellence and innovation.', '/adamson-ccit/public/assets/images/ccit_launch.jpg', 'Inside CCIT facilities', 'Explore Programs', '/adamson-ccit/public/index.php?page=programs_undergraduate', '', '', 'https://youtu.be/MMTTGnL2XJk', 'Ready to start your CCIT journey?', 'Find the right program with our Career Pathway Generator.', 'Launch the Tool', '/adamson-ccit/public/index.php?page=career_pathway_generator', 0, '/adamson-ccit/public/assets/images/faculty/faculty.jpg', 'CCIT Faculty Members', '/adamson-ccit/public/assets/images/facilities.jpg', 'Modern CCIT Facilities', '/adamson-ccit/public/assets/images/career-opp.jpg', 'Career Development', '/adamson-ccit/public/index.php?page=faculty_profile', '/adamson-ccit/public/index.php?page=virtual_tour', '#partners');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `excerpt` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `category` varchar(64) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `author` varchar(128) DEFAULT NULL,
  `status` varchar(32) DEFAULT 'published',
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `excerpt`, `body`, `category`, `image_url`, `date`, `author`, `status`, `published_at`, `created_at`, `updated_at`) VALUES
(11, 'CCIT Launches CATALYST: Empowering Students Through Certifications and Future-Ready Skills', NULL, 'The College of Computing and Information Technology (CCIT) proudly opened its transformative event, CCIT-CATALYST (Certification Achievements, Technical Advancement, Learning, and Your Stackable Trail), today at 8:00 AM at CoPoTy Hall. The program highlights the role of certifications, micro-credentials, and the MIT program in building a stackable, future-ready career in technology. This year, CCIT also celebrates the remarkable achievement of 867 newly certified IT Specialists, with a magazine-style photo booth available for participants to commemorate the milestone.', 'news', '/adamson-ccit/public/uploads/news/20250927_173917_ccit_catalyst.jpg', '2025-09-27', NULL, 'published', '2025-09-27 23:39:23', '2025-09-27 15:39:18', '2025-09-27 16:13:18'),
(12, 'CCIT Recognized as Certiport Authorized Testing Center, Achieves Record-Breaking Certifications', NULL, 'Adamson University’s College of Computing and Information Technology (CCIT) continues to champion digital upskilling through its strong partnership with Innovative Training Works in delivering industry-recognized certification exams. In a milestone achievement, CCIT was formally recognized as a Certiport Authorized Testing Center, with Dean Dr. Leonard L. Alejandro receiving the Certificate of Authorization at the Certiport Institutional Partners Meeting held at Jade Garden, Makati City. Last year, CCIT proudly certified 867 students and 21 faculty members as IT Specialists across multiple domains—including HTML and CSS, Databases, Python, Networking, Network Security, Software Fundamentals, and Cybersecurity—marking the college’s highest number of certifications since the program began in 2019.', 'achievement', '/adamson-ccit/public/uploads/news/20250927_174515_ccit_certiport.jpg', '2025-09-27', NULL, 'published', '2025-09-27 23:45:19', '2025-09-27 15:45:15', '2025-09-27 16:12:55'),
(13, 'Ctrl + Alt + Start: A Kickoff for Tomorrow’s Innovators', NULL, 'Last September 12, the Computer Science Department of Adamson University, in collaboration with ACOMSS, held its annual Ctrl + Alt + Start event at Co Po Ty Hall from 1:00 to 5:00 PM. Designed to welcome and guide students as they begin their college journey, the event introduced participants to the diverse career paths and vibrant community awaiting them at CCIT. This kickoff marks only the beginning of an exciting academic and professional journey for tomorrow’s innovators. 🌟', 'news', '/adamson-ccit/public/uploads/news/20250927_174909_ccit_acomss.jpg', '2025-09-27', NULL, 'published', '2025-09-27 17:49:10', '2025-09-27 15:49:10', '2025-11-02 18:49:41');

-- --------------------------------------------------------

--
-- Table structure for table `news_page_settings`
--

CREATE TABLE `news_page_settings` (
  `id` int(11) NOT NULL,
  `subhero_lead` varchar(255) NOT NULL DEFAULT '',
  `announcement` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news_page_settings`
--

INSERT INTO `news_page_settings` (`id`, `subhero_lead`, `announcement`, `updated_at`) VALUES
(1, 'Stories from CCIT—research, achievements, announcements, and student life.', '', '2025-09-07 13:48:20');

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_desc` text DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `slug` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partners`
--

INSERT INTO `partners` (`id`, `name`, `short_desc`, `logo_path`, `website_url`, `is_active`, `slug`, `sort_order`) VALUES
(1, 'Accenture', 'Global professional services company offering consulting, technology, and outsourcing.', '/adamson-ccit/public/assets/images/partners/accenture.png', 'https://www.accenture.com/', 1, NULL, 0),
(2, 'Creative Nation Academy', 'Training academy for creative & marketing disciplines; Adobe certifications and workshops.', '/adamson-ccit/public/assets/images/partners/cna.png', 'https://www.creativenation.ph/', 1, NULL, 0),
(3, 'Dark League Studios', 'Philippine esports & gaming events company partnering with schools and brands.', '/adamson-ccit/public/assets/images/partners/dls.jpg', 'https://www.facebook.com/darkleaguestudios/', 1, NULL, 0),
(4, 'Eastern Communications', 'Philippine telecom & ICT provider offering connectivity, internet, cloud, and managed services.', '/adamson-ccit/public/assets/images/partners/ec.png', 'https://www.eastern.com.ph/', 1, NULL, 0),
(5, 'ERDA Foundation, Inc.', 'NGO empowering marginalized Filipino children and youth through education programs.', '/adamson-ccit/public/assets/images/partners/erda.png', 'https://www.erda.ph/', 1, NULL, 0),
(6, 'Grundfos', 'Global pump manufacturer delivering water solutions with a focus on sustainability.', '/adamson-ccit/public/assets/images/partners/grundfos.png', 'https://www.grundfos.com/', 1, NULL, 0),
(7, 'Innovative Training Works, Inc.', 'IT education & corporate training provider in the Philippines.', '/adamson-ccit/public/assets/images/partners/itworks.png', 'https://www.itworks.com.ph/', 1, NULL, 0),
(8, 'JCI', 'Global network of young active citizens focused on leadership and community projects.', '/adamson-ccit/public/assets/images/partners/jci.png', 'https://jci.cc/', 1, NULL, 0),
(9, 'Navitaire', 'Airline and travel technology solutions powering reservation and commerce systems.', '/adamson-ccit/public/assets/images/partners/navitaire.png', 'https://www.navitaire.com/', 1, NULL, 0),
(10, 'Oracle', 'Cloud applications and database technology for enterprises and developers.', '/adamson-ccit/public/assets/images/partners/oracle.png', 'https://www.oracle.com/', 1, NULL, 0),
(11, 'Philtech', 'Local technology partner supporting internships and projects.', '/adamson-ccit/public/assets/images/partners/philtech.jpg', 'https://www.philtech.ph/', 1, NULL, 0),
(12, 'Universal Access and Systems Solutions (UAS)', 'Philippine IT systems integrator delivering end-to-end technology solutions.', '/adamson-ccit/public/assets/images/partners/uas.png', 'https://www.uas.com.ph/', 1, NULL, 0),
(13, 'Wacom', 'Digital pen tablets and creative tools for education and design.', '/adamson-ccit/public/assets/images/partners/wacom.png', 'https://www.wacom.com/', 1, NULL, 0),
(14, 'XTZ Corporation', 'IT solutions & hardware provider serving Philippine customers.', '/adamson-ccit/public/assets/images/partners/xtz.png', 'https://www.xtzcorp.com/', 1, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_alt` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `slug`, `name`, `description`, `image`, `image_alt`, `url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'undergraduate', 'Undergraduate', 'Build strong foundations in computing and IT.', '', '', '/adamson-ccit/public/index.php?page=programs_undergraduate', 1, '2025-09-07 19:16:07', '2025-10-03 14:19:54'),
(2, 'dual-degree', 'Dual Degree', 'Earn complementary credentials to stand out.', '', '', '/adamson-ccit/public/index.php?page=programs_undergraduate', 1, '2025-09-07 19:16:07', '2025-10-03 14:18:17'),
(3, 'graduate-studies', 'Graduate Studies', 'Advance your expertise through research and practice.', '', '', '/adamson-ccit/public/index.php?page=programs_graduate_studies', 1, '2025-09-07 19:16:07', '2025-10-03 14:18:17');

-- --------------------------------------------------------

--
-- Table structure for table `programs_graduate`
--

CREATE TABLE `programs_graduate` (
  `id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `position` int(11) DEFAULT 1,
  `slug` varchar(255) DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `title_muted` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `pillbox_title` varchar(255) DEFAULT NULL,
  `pills` text DEFAULT NULL,
  `learn_more_url` varchar(500) DEFAULT NULL,
  `learn_more_external` tinyint(1) DEFAULT 0,
  `curriculum_url` varchar(500) DEFAULT NULL,
  `curriculum_external` tinyint(1) DEFAULT 0,
  `apply_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs_graduate`
--

INSERT INTO `programs_graduate` (`id`, `is_active`, `position`, `slug`, `badge`, `title`, `title_muted`, `summary`, `pillbox_title`, `pills`, `learn_more_url`, `learn_more_external`, `curriculum_url`, `curriculum_external`, `apply_url`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'master-in-information-technology', 'MIT', 'Master in Information Technology', '', 'The Master in Information Technology (MIT) at Adamson University provides advanced theoretical and practical IT training to prepare students for leadership roles. It emphasizes ethical practice and social responsibility—developing professionals who drive innovation and support sustainable development.', 'Program Emphases', 'Advanced Computing Practice\r\nIT Leadership & Governance\r\nEthics & Social Responsibility\r\nInnovation & Sustainable Impact', '', 0, 'https://www.adamson.edu.ph/v1/?page=pos-course&course=j', 1, 'http://localhost/adamson-ccit/public/index.php?page=admission_graduate_school', '2025-09-18 19:03:54', '2025-09-27 16:55:12');

-- --------------------------------------------------------

--
-- Table structure for table `programs_graduate_settings`
--

CREATE TABLE `programs_graduate_settings` (
  `id` int(11) NOT NULL,
  `subhero_image_url` varchar(255) DEFAULT '/adamson-ccit/public/assets/images/programs/graduate.jpg',
  `subhero_lead` text DEFAULT NULL,
  `programs_grid` mediumtext DEFAULT NULL,
  `cta_title` varchar(255) DEFAULT NULL,
  `cta_description` text DEFAULT NULL,
  `cta_action_url` varchar(255) DEFAULT NULL,
  `cta_action_label` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `card1_active` tinyint(1) DEFAULT 1,
  `card1_position` int(11) DEFAULT 1,
  `card1_slug` varchar(80) DEFAULT NULL,
  `card1_badge` varchar(50) DEFAULT NULL,
  `card1_title` varchar(255) DEFAULT NULL,
  `card1_title_muted` varchar(120) DEFAULT NULL,
  `card1_summary` text DEFAULT NULL,
  `card1_pillbox_title` varchar(120) DEFAULT NULL,
  `card1_pills` text DEFAULT NULL,
  `card1_learn_more_url` varchar(255) DEFAULT NULL,
  `card1_learn_more_external` tinyint(1) DEFAULT 1,
  `card1_curriculum_url` varchar(255) DEFAULT NULL,
  `card1_curriculum_external` tinyint(1) DEFAULT 1,
  `card1_apply_url` varchar(255) DEFAULT NULL,
  `card2_active` tinyint(1) DEFAULT 1,
  `card2_position` int(11) DEFAULT 2,
  `card2_slug` varchar(80) DEFAULT NULL,
  `card2_badge` varchar(50) DEFAULT NULL,
  `card2_title` varchar(255) DEFAULT NULL,
  `card2_title_muted` varchar(120) DEFAULT NULL,
  `card2_summary` text DEFAULT NULL,
  `card2_pillbox_title` varchar(120) DEFAULT NULL,
  `card2_pills` text DEFAULT NULL,
  `card2_learn_more_url` varchar(255) DEFAULT NULL,
  `card2_learn_more_external` tinyint(1) DEFAULT 1,
  `card2_curriculum_url` varchar(255) DEFAULT NULL,
  `card2_curriculum_external` tinyint(1) DEFAULT 1,
  `card2_apply_url` varchar(255) DEFAULT NULL,
  `card3_active` tinyint(1) DEFAULT 1,
  `card3_position` int(11) DEFAULT 3,
  `card3_slug` varchar(80) DEFAULT NULL,
  `card3_badge` varchar(50) DEFAULT NULL,
  `card3_title` varchar(255) DEFAULT NULL,
  `card3_title_muted` varchar(120) DEFAULT NULL,
  `card3_summary` text DEFAULT NULL,
  `card3_pillbox_title` varchar(120) DEFAULT NULL,
  `card3_pills` text DEFAULT NULL,
  `card3_learn_more_url` varchar(255) DEFAULT NULL,
  `card3_learn_more_external` tinyint(1) DEFAULT 1,
  `card3_curriculum_url` varchar(255) DEFAULT NULL,
  `card3_curriculum_external` tinyint(1) DEFAULT 1,
  `card3_apply_url` varchar(255) DEFAULT NULL,
  `card4_active` tinyint(1) DEFAULT 1,
  `card4_position` int(11) DEFAULT 4,
  `card4_slug` varchar(80) DEFAULT NULL,
  `card4_badge` varchar(50) DEFAULT NULL,
  `card4_title` varchar(255) DEFAULT NULL,
  `card4_title_muted` varchar(120) DEFAULT NULL,
  `card4_summary` text DEFAULT NULL,
  `card4_pillbox_title` varchar(120) DEFAULT NULL,
  `card4_pills` text DEFAULT NULL,
  `card4_learn_more_url` varchar(255) DEFAULT NULL,
  `card4_learn_more_external` tinyint(1) DEFAULT 1,
  `card4_curriculum_url` varchar(255) DEFAULT NULL,
  `card4_curriculum_external` tinyint(1) DEFAULT 1,
  `card4_apply_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs_graduate_settings`
--

INSERT INTO `programs_graduate_settings` (`id`, `subhero_image_url`, `subhero_lead`, `programs_grid`, `cta_title`, `cta_description`, `cta_action_url`, `cta_action_label`, `updated_at`, `card1_active`, `card1_position`, `card1_slug`, `card1_badge`, `card1_title`, `card1_title_muted`, `card1_summary`, `card1_pillbox_title`, `card1_pills`, `card1_learn_more_url`, `card1_learn_more_external`, `card1_curriculum_url`, `card1_curriculum_external`, `card1_apply_url`, `card2_active`, `card2_position`, `card2_slug`, `card2_badge`, `card2_title`, `card2_title_muted`, `card2_summary`, `card2_pillbox_title`, `card2_pills`, `card2_learn_more_url`, `card2_learn_more_external`, `card2_curriculum_url`, `card2_curriculum_external`, `card2_apply_url`, `card3_active`, `card3_position`, `card3_slug`, `card3_badge`, `card3_title`, `card3_title_muted`, `card3_summary`, `card3_pillbox_title`, `card3_pills`, `card3_learn_more_url`, `card3_learn_more_external`, `card3_curriculum_url`, `card3_curriculum_external`, `card3_apply_url`, `card4_active`, `card4_position`, `card4_slug`, `card4_badge`, `card4_title`, `card4_title_muted`, `card4_summary`, `card4_pillbox_title`, `card4_pills`, `card4_learn_more_url`, `card4_learn_more_external`, `card4_curriculum_url`, `card4_curriculum_external`, `card4_apply_url`) VALUES
(1, '/adamson-ccit/public/assets/images/hero-campus.jpg', 'Advanced training for IT leaders—rigor, ethics, and impact.', NULL, 'Chart your next step.', 'Ask us about MIT schedules, requirements, and scholarships.', '/adamson-ccit/public/index.php?page=contact', 'Contact CCIT', '2025-11-02 20:12:50', 1, 1, 'mit', 'NEW', 'Master in Information Technology', '(MIT)', 'A comprehensive graduate program designed to develop IT professionals with advanced technical skills and leadership capabilities.', 'Specializations', 'Software Engineering\nCybersecurity\nData Analytics\nIT Management', '/adamson-ccit/public/index.php?page=programs_mit', 0, '/adamson-ccit/public/assets/docs/mit-curriculum.pdf', 1, '/adamson-ccit/public/index.php?page=admission_graduate_school', 1, 2, 'phd-it', '', 'Doctor of Philosophy in Information Technology', '(PhD-IT)', 'The highest level of academic achievement in IT, focusing on research, innovation, and scholarly contributions to the field.', 'Research Areas', 'Artificial Intelligence\nMachine Learning\nHuman-Computer Interaction\nSoftware Engineering\nCybersecurity', '/adamson-ccit/public/index.php?page=programs_phd', 0, '/adamson-ccit/public/assets/docs/phd-curriculum.pdf', 1, '/adamson-ccit/public/index.php?page=admission_graduate_school', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `programs_undergraduate`
--

CREATE TABLE `programs_undergraduate` (
  `id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `position` int(11) NOT NULL DEFAULT 100,
  `slug` varchar(80) DEFAULT NULL,
  `badge` varchar(60) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `title_muted` varchar(120) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `pillbox_title` varchar(120) DEFAULT NULL,
  `pills` text DEFAULT NULL,
  `learn_more_url` varchar(512) DEFAULT NULL,
  `learn_more_external` tinyint(1) NOT NULL DEFAULT 1,
  `curriculum_url` varchar(512) DEFAULT NULL,
  `curriculum_external` tinyint(1) NOT NULL DEFAULT 1,
  `apply_url` varchar(512) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs_undergraduate`
--

INSERT INTO `programs_undergraduate` (`id`, `is_active`, `position`, `slug`, `badge`, `title`, `title_muted`, `summary`, `pillbox_title`, `pills`, `learn_more_url`, `learn_more_external`, `curriculum_url`, `curriculum_external`, `apply_url`, `created_at`, `updated_at`) VALUES
(9, 1, 1, 'bs-information-technology', 'BSIT', 'BS Information Technology', '', 'Comprehensive IT program focusing on software development, systems administration, and technology implementation in business environments.', 'Tracks', 'Consumer & Enterprise Application Development\r\nGame Development\r\nNetwork Infrastructure & Data Security', 'https://www.adamson.edu.ph/v1/?page=academicsv&col=13&dept=21&link=31', 1, 'https://www.adamson.edu.ph/v1/?page=pos-course&course=3r', 1, 'http://localhost/adamson-ccit/public/index.php?page=admission_freshman', '2025-09-18 18:25:26', '2025-09-27 16:51:03'),
(10, 1, 2, 'bs-computer-science', 'BSCS', 'BS Computer Science', '', 'Rigorous computer science program emphasizing algorithmic thinking, software engineering principles, and advanced computing concepts.', 'Specializations', 'Data Science\r\nWeb Science\r\nComputer Vision', 'https://www.adamson.edu.ph/v1/?page=academicsv&col=13&dept=18', 1, 'https://www.adamson.edu.ph/v1/?page=curriculum&cid=%20%20%20%20%20n&curryear=2022', 1, 'http://localhost/adamson-ccit/public/index.php?page=admission_freshman', '2025-09-18 18:25:26', '2025-09-27 16:52:43'),
(11, 1, 3, 'bs-information-systems', 'BSIS', 'BS Information Systems', '', 'Strategic IT program combining business acumen with technical expertise for effective information systems management.', 'Specialization', 'Business Analytics', 'https://www.adamson.edu.ph/v1/?page=academicsv&col=13&dept=21&link=31', 1, 'https://www.adamson.edu.ph/v1/?page=curriculum&cid=%20%20%20%205J&curryear=2022', 1, 'http://localhost//adamson-ccit/public/index.php?page=admission_freshman', '2025-09-18 18:25:26', '2025-09-27 16:51:58'),
(12, 1, 4, 'bs-computer-science-information-engineering', 'DUAL DEGREE', 'BS Computer Science & Information Engineering', '', 'Accelerated dual degree program allowing students to earn two bachelor\'s degrees in Computer Science plus Business Administration or Engineering.', '', '', 'https://www.adamson.edu.ph/v1/?page=dual-degree-cs-ie-home&col=13', 1, 'https://www.adamson.edu.ph/v1/?page=curriculum&cid=%20%20%20%2076&curryear=2023', 1, 'http://localhost/adamson-ccit/public/index.php?page=admission_freshman', '2025-09-18 18:25:26', '2025-09-27 16:53:24');

-- --------------------------------------------------------

--
-- Table structure for table `programs_undergraduate_settings`
--

CREATE TABLE `programs_undergraduate_settings` (
  `id` int(11) NOT NULL,
  `subhero_image_url` varchar(255) DEFAULT '/adamson-ccit/public/assets/images/programs/undergrad.jpg',
  `subhero_lead` text DEFAULT NULL,
  `programs_grid` mediumtext DEFAULT NULL,
  `cta_title` varchar(255) DEFAULT NULL,
  `cta_description` text DEFAULT NULL,
  `cta_action_url` varchar(255) DEFAULT NULL,
  `cta_action_label` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `card1_active` tinyint(1) NOT NULL DEFAULT 1,
  `card1_position` smallint(6) NOT NULL DEFAULT 1,
  `card1_slug` varchar(64) DEFAULT 'bscs',
  `card1_badge` varchar(64) DEFAULT 'BSCS',
  `card1_title` varchar(255) DEFAULT 'B.S. in Computer Science',
  `card1_title_muted` varchar(255) DEFAULT NULL,
  `card1_summary` text DEFAULT NULL,
  `card1_pillbox_title` varchar(255) DEFAULT NULL,
  `card1_pills` text DEFAULT NULL,
  `card1_learn_more_url` varchar(255) DEFAULT NULL,
  `card1_learn_more_external` tinyint(1) NOT NULL DEFAULT 1,
  `card1_curriculum_url` varchar(255) DEFAULT NULL,
  `card1_curriculum_external` tinyint(1) NOT NULL DEFAULT 1,
  `card1_apply_url` varchar(255) DEFAULT '/adamson-ccit/public/index.php?page=admission_freshman',
  `card2_active` tinyint(1) NOT NULL DEFAULT 1,
  `card2_position` smallint(6) NOT NULL DEFAULT 2,
  `card2_slug` varchar(64) DEFAULT 'dual-degree',
  `card2_badge` varchar(64) DEFAULT 'Dual Degree',
  `card2_title` varchar(255) DEFAULT 'B.S. in Computer Science and Information Engineering',
  `card2_title_muted` varchar(255) DEFAULT '(Dual)',
  `card2_summary` text DEFAULT NULL,
  `card2_pillbox_title` varchar(255) DEFAULT NULL,
  `card2_pills` text DEFAULT NULL,
  `card2_learn_more_url` varchar(255) DEFAULT NULL,
  `card2_learn_more_external` tinyint(1) NOT NULL DEFAULT 1,
  `card2_curriculum_url` varchar(255) DEFAULT NULL,
  `card2_curriculum_external` tinyint(1) NOT NULL DEFAULT 1,
  `card2_apply_url` varchar(255) DEFAULT '/adamson-ccit/public/index.php?page=admission_freshman',
  `card3_active` tinyint(1) NOT NULL DEFAULT 1,
  `card3_position` smallint(6) NOT NULL DEFAULT 3,
  `card3_slug` varchar(64) DEFAULT 'bsis',
  `card3_badge` varchar(64) DEFAULT 'BSIS',
  `card3_title` varchar(255) DEFAULT 'B.S. in Information Systems',
  `card3_title_muted` varchar(255) DEFAULT NULL,
  `card3_summary` text DEFAULT NULL,
  `card3_pillbox_title` varchar(255) DEFAULT NULL,
  `card3_pills` text DEFAULT NULL,
  `card3_learn_more_url` varchar(255) DEFAULT NULL,
  `card3_learn_more_external` tinyint(1) NOT NULL DEFAULT 1,
  `card3_curriculum_url` varchar(255) DEFAULT NULL,
  `card3_curriculum_external` tinyint(1) NOT NULL DEFAULT 1,
  `card3_apply_url` varchar(255) DEFAULT '/adamson-ccit/public/index.php?page=admission_freshman',
  `card4_active` tinyint(1) NOT NULL DEFAULT 1,
  `card4_position` smallint(6) NOT NULL DEFAULT 4,
  `card4_slug` varchar(64) DEFAULT 'bsit',
  `card4_badge` varchar(64) DEFAULT 'BSIT',
  `card4_title` varchar(255) DEFAULT 'B.S. in Information Technology',
  `card4_title_muted` varchar(255) DEFAULT NULL,
  `card4_summary` text DEFAULT NULL,
  `card4_pillbox_title` varchar(255) DEFAULT NULL,
  `card4_pills` text DEFAULT NULL,
  `card4_learn_more_url` varchar(255) DEFAULT NULL,
  `card4_learn_more_external` tinyint(1) NOT NULL DEFAULT 1,
  `card4_curriculum_url` varchar(255) DEFAULT NULL,
  `card4_curriculum_external` tinyint(1) NOT NULL DEFAULT 1,
  `card4_apply_url` varchar(255) DEFAULT '/adamson-ccit/public/index.php?page=admission_freshman',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs_undergraduate_settings`
--

INSERT INTO `programs_undergraduate_settings` (`id`, `subhero_image_url`, `subhero_lead`, `programs_grid`, `cta_title`, `cta_description`, `cta_action_url`, `cta_action_label`, `updated_at`, `card1_active`, `card1_position`, `card1_slug`, `card1_badge`, `card1_title`, `card1_title_muted`, `card1_summary`, `card1_pillbox_title`, `card1_pills`, `card1_learn_more_url`, `card1_learn_more_external`, `card1_curriculum_url`, `card1_curriculum_external`, `card1_apply_url`, `card2_active`, `card2_position`, `card2_slug`, `card2_badge`, `card2_title`, `card2_title_muted`, `card2_summary`, `card2_pillbox_title`, `card2_pills`, `card2_learn_more_url`, `card2_learn_more_external`, `card2_curriculum_url`, `card2_curriculum_external`, `card2_apply_url`, `card3_active`, `card3_position`, `card3_slug`, `card3_badge`, `card3_title`, `card3_title_muted`, `card3_summary`, `card3_pillbox_title`, `card3_pills`, `card3_learn_more_url`, `card3_learn_more_external`, `card3_curriculum_url`, `card3_curriculum_external`, `card3_apply_url`, `card4_active`, `card4_position`, `card4_slug`, `card4_badge`, `card4_title`, `card4_title_muted`, `card4_summary`, `card4_pillbox_title`, `card4_pills`, `card4_learn_more_url`, `card4_learn_more_external`, `card4_curriculum_url`, `card4_curriculum_external`, `card4_apply_url`, `created_at`) VALUES
(1, '/adamson-ccit/public/assets/images/hero-campus.jpg', 'Solid foundations, hands-on practice, and focused CCIT pathways.', NULL, 'Not sure which program fits you?', 'Use the Career Pathway Generator to discover your best match.', '?page=career_pathway_generator', 'Launch the Tool', '2025-11-02 20:29:19', 1, 1, 'bscs', 'BSCS', 'B.S. in Computer Science', NULL, 'Computing theory and algorithmic design for robust software and research-driven solutions.', 'CCIT Specializations', 'Data Science\nWeb Science\nComputer Vision', 'https://www.adamson.edu.ph/v1/?page=academicsv&col=13&dept=21', 1, 'https://www.adamson.edu.ph/v1/?page=curriculum', 1, '/adamson-ccit/public/index.php?page=admission_freshman', 1, 2, 'dual-degree', 'Dual Degree', 'B.S. in Computer Science and Information Engineering', '(Dual)', 'Two credentials via Adamson University and Minghsin University of Science and Technology (Taiwan).', 'Highlights', 'Algorithms & Software Systems\nInformation Engineering\nCross-cultural Experience', 'https://www.adamson.edu.ph/v1/?page=dual-degree-cs-ie-home', 1, 'https://www.adamson.edu.ph/v1/?page=curriculum&cid=%20%20%20%2076&curryear=2023', 1, '/adamson-ccit/public/index.php?page=admission_freshman', 1, 3, 'bsis', 'BSIS', 'B.S. in Information Systems', NULL, 'Design and deployment of information systems that streamline processes and decisions.', 'CCIT Specialization', 'Business Analytics', 'https://www.adamson.edu.ph/v1/?page=academicsv&col=13&dept=21', 1, 'https://www.adamson.edu.ph/v1/?page=curriculum', 1, '/adamson-ccit/public/index.php?page=admission_freshman', 1, 4, 'bsit', 'BSIT', 'B.S. in Information Technology', NULL, 'Applications and IT infrastructure—development, operations, and administration.', 'CCIT Tracks', 'Consumer & Enterprise App Dev\nGame Development\nNetwork Infra & Data Security', 'https://www.adamson.edu.ph/v1/?page=academicsv&col=13&dept=21', 1, 'https://www.adamson.edu.ph/v1/?page=curriculum', 1, '/adamson-ccit/public/index.php?page=admission_freshman', '2025-11-02 20:28:56'),
(2, '/adamson-ccit/public/assets/images/programs/undergrad.jpg', 'Solid foundations, hands-on practice, and focused CCIT pathways.', NULL, 'Not sure which program fits you?', 'Use the Career Pathway Generator to discover your best match.', '?page=career_pathway_generator', 'Launch the Tool', '2025-11-02 20:21:16', 1, 1, 'bscs', 'BSCS', 'B.S. in Computer Science', NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, '/adamson-ccit/public/index.php?page=admission_freshman', 1, 2, 'dual-degree', 'Dual Degree', 'B.S. in Computer Science and Information Engineering', '(Dual)', NULL, NULL, NULL, NULL, 1, NULL, 1, '/adamson-ccit/public/index.php?page=admission_freshman', 1, 3, 'bsis', 'BSIS', 'B.S. in Information Systems', NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, '/adamson-ccit/public/index.php?page=admission_freshman', 1, 4, 'bsit', 'BSIT', 'B.S. in Information Technology', NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, '/adamson-ccit/public/index.php?page=admission_freshman', '2025-11-02 20:28:56'),
(3, '/adamson-ccit/public/assets/images/programs/undergrad.jpg', 'Solid foundations, hands-on practice, and focused CCIT pathways.', NULL, 'Not sure which program fits you?', 'Use the Career Pathway Generator to discover your best match.', '?page=career_pathway_generator', 'Launch the Tool', '2025-11-02 20:21:23', 1, 1, 'bscs', 'BSCS', 'B.S. in Computer Science', NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, '/adamson-ccit/public/index.php?page=admission_freshman', 1, 2, 'dual-degree', 'Dual Degree', 'B.S. in Computer Science and Information Engineering', '(Dual)', NULL, NULL, NULL, NULL, 1, NULL, 1, '/adamson-ccit/public/index.php?page=admission_freshman', 1, 3, 'bsis', 'BSIS', 'B.S. in Information Systems', NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, '/adamson-ccit/public/index.php?page=admission_freshman', 1, 4, 'bsit', 'BSIT', 'B.S. in Information Technology', NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, '/adamson-ccit/public/index.php?page=admission_freshman', '2025-11-02 20:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `program_cards`
--

CREATE TABLE `program_cards` (
  `id` int(11) NOT NULL,
  `level` enum('undergraduate','graduate') NOT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `position` int(11) DEFAULT 999,
  `slug` varchar(80) DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `title_muted` varchar(200) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `pillbox_title` varchar(120) DEFAULT NULL,
  `pills` text DEFAULT NULL,
  `learn_more_url` varchar(255) DEFAULT NULL,
  `learn_more_external` tinyint(1) NOT NULL DEFAULT 1,
  `curriculum_url` varchar(255) DEFAULT NULL,
  `curriculum_external` tinyint(1) NOT NULL DEFAULT 1,
  `apply_url` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_cards`
--

INSERT INTO `program_cards` (`id`, `level`, `status`, `position`, `slug`, `badge`, `title`, `title_muted`, `summary`, `pillbox_title`, `pills`, `learn_more_url`, `learn_more_external`, `curriculum_url`, `curriculum_external`, `apply_url`, `image_url`, `created_at`, `updated_at`) VALUES
(1, 'undergraduate', 'published', 1, 'mit', 'MITS', 'Bachelor of Science in Information Technology', '(BSIT)', 'A comprehensive undergraduate program that provides students with fundamental knowledge and practical skills in information technology, preparing them for diverse career opportunities in the tech industry.', 'Program Tracks', 'Web Development\nMobile App Development\nNetwork Administration\nDatabase Management\nCybersecurity Fundamentals', '', 1, 'https://www.adamson.edu.ph/v1/?page=pos-course&course=j', 1, '/adamson-ccit/public/index.php?page=contact', '', '2025-09-08 05:52:41', '2025-09-17 20:29:36'),
(2, 'graduate', 'published', 1, 'mit-graduate', 'MIT', 'Master in Information Technology', '(MIT)', 'The Master in Information Technology (MIT) at Adamson University College of Computer and Information Technology is designed to equip students with advanced knowledge and skills in information technology, preparing them for leadership roles in the digital age.', 'Program Emphases', 'Advanced Computing Practice\r\nIT Leadership & Governance\r\nSoftware Engineering\r\nData Analytics\r\nCybersecurity', 'https://www.adamson.edu.ph/v1/?page=pos-course&course=mit', 1, '', 0, '/adamson-ccit/public/index.php?page=contact', '', '2025-09-17 20:15:57', '2025-09-17 20:44:39');

-- --------------------------------------------------------

--
-- Table structure for table `program_group_content_blocks`
--

CREATE TABLE `program_group_content_blocks` (
  `id` int(10) UNSIGNED NOT NULL,
  `program_slug` varchar(64) NOT NULL,
  `slot` enum('programs_grid') NOT NULL DEFAULT 'programs_grid',
  `content_html` mediumtext DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_group_content_blocks`
--

INSERT INTO `program_group_content_blocks` (`id`, `program_slug`, `slot`, `content_html`, `sort_order`, `updated_at`) VALUES
(1, 'undergraduate', 'programs_grid', '<div class=\"prog__grid\">\n\n<!-- A — BS Computer Science -->\n<article class=\"prog__card\" id=\"bscs\">\n  <header class=\"prog__head\">\n    <span class=\"badge\" aria-hidden=\"true\">BSCS</span>\n    <h3 class=\"prog__title\">B.S. in Computer Science</h3>\n  </header>\n  <p class=\"prog__summary\">Computing theory and algorithmic design for robust software and research-driven solutions.</p>\n  <div class=\"pillbox\">\n    <h4 class=\"pillbox__title\">CCIT Specializations</h4>\n    <ul class=\"pills\" role=\"list\">\n      <li>Data Science</li>\n      <li>Web Science</li>\n      <li>Computer Vision</li>\n    </ul>\n  </div>\n  <div class=\"prog__footer\">\n    <a class=\"btn btn--outline-blue ext\" href=\"https://www.adamson.edu.ph/v1/?page=academicsv&col=13&dept=21\" target=\"_blank\" rel=\"noopener\">Learn More</a>\n    <nav class=\"mini-links\" aria-label=\"Computer Science quick links\">\n      <a class=\"ext\" href=\"https://www.adamson.edu.ph/v1/?page=curriculum\" target=\"_blank\" rel=\"noopener\">Curriculum</a>\n      <a href=\"/adamson-ccit/public/index.php?page=admission_freshman\">Apply</a>\n    </nav>\n  </div>\n</article>\n\n<!-- B — Dual Degree: CS + Information Engineering -->\n<article class=\"prog__card\" id=\"dual-degree\">\n  <header class=\"prog__head\">\n    <span class=\"badge\" aria-hidden=\"true\">Dual Degree</span>\n    <h3 class=\"prog__title\">B.S. in Computer Science and Information Engineering <span class=\"prog__muted\">(Dual)</span></h3>\n  </header>\n  <p class=\"prog__summary\">Two credentials via Adamson University and Minghsin University of Science and Technology (Taiwan).</p>\n  <div class=\"pillbox\">\n    <h4 class=\"pillbox__title\">Highlights</h4>\n    <ul class=\"pills\" role=\"list\">\n      <li>Algorithms &amp; Software Systems</li>\n      <li>Information Engineering</li>\n      <li>Cross-cultural Experience</li>\n    </ul>\n  </div>\n  <div class=\"prog__footer\">\n    <a class=\"btn btn--outline-blue ext\" href=\"https://www.adamson.edu.ph/v1/?page=dual-degree-cs-ie-home\" target=\"_blank\" rel=\"noopener\">Learn More</a>\n    <nav class=\"mini-links\" aria-label=\"Dual Degree quick links\">\n      <a class=\"ext\" href=\"https://www.adamson.edu.ph/v1/?page=curriculum&cid=%20%20%20%2076&curryear=2023\" target=\"_blank\" rel=\"noopener\">Curriculum</a>\n      <a href=\"/adamson-ccit/public/index.php?page=admission_freshman\">Apply</a>\n    </nav>\n  </div>\n</article>\n\n<!-- C — BS Information Systems -->\n<article class=\"prog__card\" id=\"bsis\">\n  <header class=\"prog__head\">\n    <span class=\"badge\" aria-hidden=\"true\">BSIS</span>\n    <h3 class=\"prog__title\">B.S. in Information Systems</h3>\n  </header>\n  <p class=\"prog__summary\">Design and deployment of information systems that streamline processes and decisions.</p>\n  <div class=\"pillbox\">\n    <h4 class=\"pillbox__title\">CCIT Specialization</h4>\n    <ul class=\"pills\" role=\"list\">\n      <li>Business Analytics</li>\n    </ul>\n  </div>\n  <div class=\"prog__footer\">\n    <a class=\"btn btn--outline-blue ext\" href=\"https://www.adamson.edu.ph/v1/?page=academicsv&col=13&dept=21\" target=\"_blank\" rel=\"noopener\">Learn More</a>\n    <nav class=\"mini-links\" aria-label=\"Information Systems quick links\">\n      <a class=\"ext\" href=\"https://www.adamson.edu.ph/v1/?page=curriculum\" target=\"_blank\" rel=\"noopener\">Curriculum</a>\n      <a href=\"/adamson-ccit/public/index.php?page=admission_freshman\">Apply</a>\n    </nav>\n  </div>\n</article>\n\n<!-- D — BS Information Technology -->\n<article class=\"prog__card\" id=\"bsit\">\n  <header class=\"prog__head\">\n    <span class=\"badge\" aria-hidden=\"true\">BSIT</span>\n    <h3 class=\"prog__title\">B.S. in Information Technology</h3>\n  </header>\n  <p class=\"prog__summary\">Applications and IT infrastructure—development, operations, and administration.</p>\n  <div class=\"pillbox\">\n    <h4 class=\"pillbox__title\">CCIT Tracks</h4>\n    <ul class=\"pills\" role=\"list\">\n      <li>Consumer &amp; Enterprise App Dev</li>\n      <li>Game Development</li>\n      <li>Network Infra &amp; Data Security</li>\n    </ul>\n  </div>\n  <div class=\"prog__footer\">\n    <a class=\"btn btn--outline-blue ext\" href=\"https://www.adamson.edu.ph/v1/?page=academicsv&col=13&dept=21\" target=\"_blank\" rel=\"noopener\">Learn More</a>\n    <nav class=\"mini-links\" aria-label=\"Information Technology quick links\">\n      <a class=\"ext\" href=\"https://www.adamson.edu.ph/v1/?page=curriculum\" target=\"_blank\" rel=\"noopener\">Curriculum</a>\n      <a href=\"/adamson-ccit/public/index.php?page=admission_freshman\">Apply</a>\n    </nav>\n  </div>\n</article>\n\n</div>', 0, '2025-09-07 19:16:07');

-- --------------------------------------------------------

--
-- Table structure for table `program_group_settings`
--

CREATE TABLE `program_group_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `program_slug` varchar(64) NOT NULL,
  `subhero_image_url` varchar(255) DEFAULT NULL,
  `subhero_lead` text DEFAULT NULL,
  `cta_title` varchar(255) DEFAULT NULL,
  `cta_description` text DEFAULT NULL,
  `cta_action_url` varchar(255) DEFAULT NULL,
  `cta_action_label` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_group_settings`
--

INSERT INTO `program_group_settings` (`id`, `program_slug`, `subhero_image_url`, `subhero_lead`, `cta_title`, `cta_description`, `cta_action_url`, `cta_action_label`, `updated_at`) VALUES
(1, 'undergraduate', '/adamson-ccit/public/assets/images/programs/undergrad.jpg', 'Solid foundations, hands-on practice, and focused CCIT pathways.', 'Not sure which program fits you?', 'Use the Career Pathway Generator to discover your best match.', '/adamson-ccit/public/index.php?page=career_pathway_generator', 'Launch the Tool', '2025-09-01 20:03:38');

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `selector` char(24) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `user_agent_hash` char(64) NOT NULL,
  `ip` varbinary(16) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`id`, `user_id`, `selector`, `token_hash`, `user_agent_hash`, `ip`, `expires_at`, `created_at`) VALUES
(1, 1, 'X9cfMzQrhA-r6sD02F2xndOQ', '737bc3eb5a46e167668bc0df3b426412137a131a4c8f2d213686dd8272d2e7e3', '8d3a4310782ed11654708481a971289b2b41320c78fe2ca10c0be5d9c3af9b5b', 0x00000000000000000000000000000001, '2025-11-17 04:34:57', '2025-10-18 12:34:50');

-- --------------------------------------------------------

--
-- Table structure for table `research`
--

CREATE TABLE `research` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `abstract` text DEFAULT NULL,
  `description` text NOT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT 0,
  `status` varchar(50) DEFAULT 'draft',
  `requires_dean_approval` tinyint(1) DEFAULT 1,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `secretary_appointments`
--

CREATE TABLE `secretary_appointments` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `appointee_name` varchar(255) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `secretary_logs`
--

CREATE TABLE `secretary_logs` (
  `id` int(11) NOT NULL,
  `secretary_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(50) DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `username`, `password`, `role`) VALUES
(1, 'student', 'student123', 'student');

-- --------------------------------------------------------

--
-- Table structure for table `student_certification`
--

CREATE TABLE `student_certification` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `badge_url` varchar(255) DEFAULT NULL,
  `issuer` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_certification`
--

INSERT INTO `student_certification` (`id`, `name`, `badge_url`, `issuer`, `description`, `updated_at`) VALUES
(1, 'IT Specialist – Networking', '/adamson-ccit/public/assets/images/certs/its-networking.png', 'Certiport', 'Foundational networking knowledge and skills: TCP/IP, networking services, topologies, and troubleshooting for wired and wireless environments.', '2025-09-01 20:35:36'),
(2, 'IT Specialist – Network Security', '/adamson-ccit/public/assets/images/certs/its-network-security.png', 'Certiport', 'Core security principles; OS, network, and device security; secure computing practices.', '2025-09-01 20:35:36'),
(3, 'IT Specialist – Cybersecurity', '/adamson-ccit/public/assets/images/certs/its-cybersecurity.png', 'Certiport', 'Baseline cybersecurity skills including threats, vulnerabilities, controls, and basic incident response.', '2025-09-01 20:35:36'),
(4, 'IT Specialist – Databases', '/adamson-ccit/public/assets/images/certs/its-databases.png', 'Certiport', 'Designing and querying relational databases (e.g., MySQL, Microsoft SQL Server, Oracle).', '2025-09-01 20:35:36');

-- --------------------------------------------------------

--
-- Table structure for table `student_certifications`
--

CREATE TABLE `student_certifications` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `issuer` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `badge_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_certifications_page_settings`
--

CREATE TABLE `student_certifications_page_settings` (
  `id` int(11) NOT NULL,
  `subhero_image_url` varchar(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  `subhero_lead` text DEFAULT NULL,
  `cstat_note` text DEFAULT NULL,
  `certs_note` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `hero_image_url` varchar(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  `hero_lead` text DEFAULT NULL,
  `exam_note` text DEFAULT NULL,
  `announcement` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_certifications_page_settings`
--

INSERT INTO `student_certifications_page_settings` (`id`, `subhero_image_url`, `subhero_lead`, `cstat_note`, `certs_note`, `updated_at`, `hero_image_url`, `hero_lead`, `exam_note`, `announcement`, `created_at`) VALUES
(1, '/adamson-ccit/public/assets/images/hero-campus.jpg', 'Industry badges aligned with CCIT courses and labs.', 'If an exam isn’t listed here, its passing rate will be posted when available.', 'Certification windows & registration are announced by the department through official CCIT channels and your instructors. Posts include dates, fees (if any), seat counts, and step-by-step registration.', '2025-09-01 20:35:36', '/adamson-ccit/public/assets/images/hero-campus.jpg', NULL, NULL, NULL, '2025-11-02 21:10:06');

-- --------------------------------------------------------

--
-- Table structure for table `student_certification_stat`
--

CREATE TABLE `student_certification_stat` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `rate` varchar(20) NOT NULL,
  `tag` varchar(50) DEFAULT NULL,
  `meta` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_certification_stat`
--

INSERT INTO `student_certification_stat` (`id`, `title`, `rate`, `tag`, `meta`, `updated_at`) VALUES
(1, 'IT Specialist — Cybersecurity', '100%', 'Passing rate', 'BS Computer Science', '2025-09-01 20:35:36'),
(2, 'IT Specialist — Network Security', '99.53%', 'Passing rate', 'BS Information Technology & BS Information Systems', '2025-09-01 20:35:36'),
(3, 'IT Specialist — Networking', '99.02%', 'Passing rate', 'BS Computer Science', '2025-09-01 20:35:36'),
(4, 'IT Specialist — Databases', '96.11%', 'Passing rate', 'BS Information Technology & BS Information Systems', '2025-09-01 20:35:36'),
(5, 'IT Specialist — Databases', '95.62%', 'Passing rate', 'BS Computer Science & BSCS–BSIE (Dual)', '2025-09-01 20:35:36');

-- --------------------------------------------------------

--
-- Table structure for table `student_licenses`
--

CREATE TABLE `student_licenses` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `issue_month` tinyint(3) UNSIGNED DEFAULT NULL,
  `issue_year` smallint(5) UNSIGNED DEFAULT NULL,
  `expires` tinyint(1) NOT NULL DEFAULT 0,
  `expire_month` tinyint(3) UNSIGNED DEFAULT NULL,
  `expire_year` smallint(5) UNSIGNED DEFAULT NULL,
  `credential_id` varchar(191) DEFAULT NULL,
  `credential_url` varchar(512) DEFAULT NULL,
  `visibility` enum('public','private') NOT NULL DEFAULT 'public',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_licenses`
--

INSERT INTO `student_licenses` (`id`, `user_id`, `company_id`, `name`, `issue_month`, `issue_year`, `expires`, `expire_month`, `expire_year`, `credential_id`, `credential_url`, `visibility`, `created_at`, `updated_at`) VALUES
(9, 1, 47, 'Database', 4, 2025, 0, NULL, NULL, '1010101', 'https://www.credly.com/earner/earned/badge/11840748-aadb-4dc6-bb52-0f77e838c064', 'public', '2025-09-13 00:16:00', '2025-09-29 10:29:59'),
(10, 1, 12, 'Network Security', 8, 2025, 0, NULL, NULL, NULL, NULL, 'public', '2025-09-29 08:55:35', '2025-09-29 08:57:01'),
(13, 18, 15, 'Network Security', 12, 2025, 0, NULL, NULL, '123213', NULL, 'public', '2025-11-05 08:28:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_organization`
--

CREATE TABLE `student_organization` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('Academic','Co-Academic') NOT NULL,
  `logo_url` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `audience` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `x_url` varchar(255) DEFAULT NULL,
  `learn_more_url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_organization`
--

INSERT INTO `student_organization` (`id`, `name`, `type`, `logo_url`, `summary`, `audience`, `facebook_url`, `instagram_url`, `x_url`, `learn_more_url`, `updated_at`) VALUES
(1, 'AdU IT&IS Society', 'Academic', '/adamson-ccit/public/assets/images/orgs/aduitis.jpg', 'The Adamson University Information Technology & Information Systems Society is a recognized academic, non-profit organization embodied by the BSIT & BSIS students of Adamson University.', 'BSIT, BSIS', 'https://www.facebook.com/AdU.IT.and.IS.Society/', 'https://www.instagram.com/officialaduitissociety/', 'https://x.com/aduitissociety', 'https://www.adamson.edu.ph/v1/?page=organization&org=22', '2025-09-01 20:20:51'),
(2, 'ACOMSS', 'Academic', 'http://localhost//adamson-ccit/public/assets/images/orgs/acomss.png', 'The Adamson Computer Science Society is a recognized academic student organization composed of Computer Science students of Adamson University.', 'BSCS', 'https://www.facebook.com/ACOMSSofficial/', 'https://www.instagram.com/acomss_official/', '', 'https://www.adamson.edu.ph/v1/?page=organization&org=5', '2025-09-17 20:56:33'),
(3, 'AdU GAME', 'Co-Academic', '/adamson-ccit/public/assets/images/orgs/adugame.jpg', 'The Adamson University Guild of Animation Makers and Esports is a co-academic organization that empowers computer animation enthusiasts and esports players in AdU.', 'Animation & Esports', 'https://www.facebook.com/AdUGAMEOfficial/', NULL, NULL, 'https://www.adamson.edu.ph/v1/?page=organization&org=83', '2025-09-01 20:20:51');

-- --------------------------------------------------------

--
-- Table structure for table `student_organizations_page_settings`
--

CREATE TABLE `student_organizations_page_settings` (
  `id` int(11) NOT NULL,
  `subhero_image_url` varchar(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  `subhero_lead` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_organizations_page_settings`
--

INSERT INTO `student_organizations_page_settings` (`id`, `subhero_image_url`, `subhero_lead`, `updated_at`) VALUES
(1, '/adamson-ccit/public/assets/images/hero-campus.jpg', 'Official academic and co-academic organizations for CCIT students.', '2025-09-01 20:20:51');

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `program` varchar(100) DEFAULT NULL,
  `year_level` enum('1st Year','2nd Year','3rd Year','4th Year','Graduate') DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `interests` text DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `github_url` varchar(255) DEFAULT NULL,
  `portfolio_url` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','graduated','dropped') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`id`, `user_id`, `student_id`, `first_name`, `last_name`, `middle_name`, `email`, `phone`, `address`, `birth_date`, `program`, `year_level`, `section`, `gpa`, `bio`, `skills`, `interests`, `linkedin_url`, `github_url`, `portfolio_url`, `profile_image`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '202213648', 'John', 'Doe', NULL, 'john.doe@student.adamson.edu.ph', NULL, NULL, NULL, 'BS Computer Science', '4th Year', NULL, NULL, 'Passionate BS Information Technology student at Adamson University, focused on building technical expertise and professional certifications in the technology field.', 'Programming, Web Development, Database Management, Problem Solving', NULL, '', '', '', NULL, 'active', '2025-09-28 13:34:55', '2025-09-29 10:31:15');

-- --------------------------------------------------------

--
-- Table structure for table `student_research`
--

CREATE TABLE `student_research` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `year` int(4) DEFAULT NULL,
  `authors` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `doi` varchar(100) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `conference` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'published',
  `view_url` text DEFAULT NULL,
  `doi_url` text DEFAULT NULL,
  `program` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_research`
--

INSERT INTO `student_research` (`id`, `user_id`, `title`, `year`, `authors`, `updated_at`, `created_at`, `doi`, `publisher`, `conference`, `status`, `view_url`, `doi_url`, `program`) VALUES
(4, NULL, 'AdUTrack: A Mobile Application Tracker for Facilities Maintenance and Waste Detection Using Machine Learning at Adamson University', 2025, 'Aliah M. Pascua; Cathryn Gail F. Baltazar; Miguel B. Olivares; Jaymark P. Salvador; Leonard L. Alejandro', '2025-11-05 11:44:19', '2025-11-05 10:31:29', '10.1109/ICBIR65229.2025.11163172', 'IEEE', 'Bangkok, Thailand', 'published', 'https://ieeexplore.ieee.org/document/11082862', 'https://ieeexplore.ieee.org/abstract/document/11163172', 'BSIT');

-- --------------------------------------------------------

--
-- Table structure for table `student_research_page_settings`
--

CREATE TABLE `student_research_page_settings` (
  `id` int(11) NOT NULL,
  `subhero_image_url` varchar(255) DEFAULT '/adamson-ccit/public/assets/images/hero-research.jpg',
  `subhero_lead` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_research_page_settings`
--

INSERT INTO `student_research_page_settings` (`id`, `subhero_image_url`, `subhero_lead`, `updated_at`) VALUES
(1, '/adamson-ccit/public/assets/images/hero-research.jpg', 'Publications and conference papers by our students and faculty mentors.', '2025-09-01 20:32:04');

-- --------------------------------------------------------

--
-- Table structure for table `student_scholarship`
--

CREATE TABLE `student_scholarship` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` enum('Freshmen','University','External') NOT NULL,
  `summary` text DEFAULT NULL,
  `conditions` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `examples` text DEFAULT NULL,
  `learn_more_url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_scholarship`
--

INSERT INTO `student_scholarship` (`id`, `name`, `type`, `summary`, `conditions`, `requirements`, `examples`, `learn_more_url`, `updated_at`) VALUES
(1, 'Scholarships for Freshmen Students', 'Freshmen', 'Rank&nbsp;1: <strong>100% tuition</strong> (1st &amp; 2nd sem). Rank&nbsp;2: <strong>50% tuition</strong> (1st &amp; 2nd sem). Maintain no grade below 2.5 and pass NSTP in 1st sem.', '<ul class=\"bullets\"><li>Graduate of a government-recognized school.</li><li>School with <strong>≥100 graduates</strong> (else Registrar evaluation; good for one sem only).</li><li><strong>Certificate of Honor</strong> with dry seal &amp; total number of graduates.</li></ul>', NULL, NULL, 'https://www.adamson.edu.ph/v1/?page=freshmen-scholarship', '2025-09-01 20:29:25'),
(2, 'Academic Scholarship Program (ASP)', 'University', 'Tuition coverage for <strong>regular load only</strong>. Highly selective; outstanding GWA and clean academic record required.', '<ul class=\"bullets\"><li>Failing (5.0) or Dropped (130)</li><li>Not Attending (120) or No Grade OBE (140)</li><li>Special Consideration (150) or Unofficial Withdrawal (0.0)</li></ul>', NULL, NULL, 'https://www.adamson.edu.ph/v1/?page=academic-scholarship-program', '2025-09-01 20:29:25'),
(3, 'Corporate / Foundation / Individual Sponsorships', 'External', 'Full (100%) or partial (50%) support in tuition/misc. Maintain sponsor-required GWA; <strong>no dropped/failed/incomplete</strong>. Join at least <strong>2 OSAS/college activities</strong> per semester.', '<ul class=\"bullets\"><li>Letter of intent to the VP for Student Affairs.</li><li>Required course; good moral character.</li><li>Form 138 (GWA ≥88%) or sem GWA ≤<strong>1.75</strong>, no drops/fails/incompletes.</li></ul>', NULL, '<ul class=\"bullets\"><li>CHED UniFAST, GBF, Megaworld, Petron Foundation</li><li>San Miguel Foundation, LCCK, Rotary Club of Manila Bay</li><li>NROTC tuition discounts (25–100% by rank)</li></ul>', 'https://www.adamson.edu.ph/v1/?page=corporate-foundation-individual-sponsorships', '2025-09-01 20:29:25');

-- --------------------------------------------------------

--
-- Table structure for table `student_scholarships_page_settings`
--

CREATE TABLE `student_scholarships_page_settings` (
  `id` int(11) NOT NULL,
  `subhero_image_url` varchar(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  `subhero_lead` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `hero_image_url` varchar(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_scholarships_page_settings`
--

INSERT INTO `student_scholarships_page_settings` (`id`, `subhero_image_url`, `subhero_lead`, `note`, `updated_at`, `hero_image_url`, `created_at`) VALUES
(1, '/adamson-ccit/public/assets/images/hero-campus.jpg', 'Financial aid options for incoming and current Adamson students.', 'For new calls, slots, and deadlines, follow OSAS: <a class=\"ext\" href=\"https://www.facebook.com/AdamsonU.osas/\" target=\"_blank\" rel=\"noopener\">Facebook</a>.', '2025-09-01 20:29:25', '/adamson-ccit/public/assets/images/hero-campus.jpg', '2025-11-02 20:49:30');

-- --------------------------------------------------------

--
-- Table structure for table `student_testimonial`
--

CREATE TABLE `student_testimonial` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `program` varchar(20) NOT NULL,
  `grad_year` varchar(10) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `quote` text DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `avatar_initials` varchar(4) DEFAULT NULL,
  `is_alumni` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_testimonial`
--

INSERT INTO `student_testimonial` (`id`, `name`, `program`, `grad_year`, `role`, `quote`, `avatar_url`, `avatar_initials`, `is_alumni`, `created_at`, `updated_at`) VALUES
(1, 'Jane Dela Cruz', 'bsit', '2025', 'Software Engineering Intern — FinTechPH', 'Hands-on labs prepared me well. I shipped features in my second week and passed IT Specialist — Networking on my first try.', NULL, 'JD', 0, '2025-09-01 20:38:26', '2025-09-01 20:38:26'),
(2, 'Mark Santos', 'bscs', '2024', 'Junior Developer — DevWorks', 'Our capstone and algorithms track gave me the confidence to tackle production code. The culture pushed me to keep learning.', '', 'MS', 1, '2025-09-01 20:38:26', '2025-09-17 21:26:00'),
(3, 'Bea Lim', 'bsis', '2025', 'Business Analyst Intern — RetailHub', 'The analytics focus and casework translated directly to my internship. I also cleared the IT Specialist — Databases exam.', NULL, 'BL', 0, '2025-09-01 20:38:26', '2025-09-01 20:38:26'),
(4, 'Ramon Alvarez', 'grad', '2024', 'IT Manager — HealthTech', 'The graduate coursework sharpened my leadership and security foundations. It’s been a big step for my team and career.', NULL, 'RA', 1, '2025-09-01 20:38:26', '2025-09-01 20:38:26');

-- --------------------------------------------------------

--
-- Table structure for table `student_testimonials`
--

CREATE TABLE `student_testimonials` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `program` varchar(50) NOT NULL,
  `grad_year` varchar(10) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `quote` text NOT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `avatar_initials` varchar(4) DEFAULT NULL,
  `is_alumni` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_testimonials_page_settings`
--

CREATE TABLE `student_testimonials_page_settings` (
  `id` int(11) NOT NULL,
  `subhero_image_url` varchar(255) DEFAULT '/adamson-ccit/public/assets/images/hero-campus.jpg',
  `subhero_lead` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_testimonials_page_settings`
--

INSERT INTO `student_testimonials_page_settings` (`id`, `subhero_image_url`, `subhero_lead`, `updated_at`, `created_at`) VALUES
(1, '/adamson-ccit/public/assets/images/hero-campus.jpg', 'Stories from CCIT students and alumni—internships, certifications, and early careers.', '2025-09-01 20:38:26', '2025-11-02 21:16:05');

-- --------------------------------------------------------

--
-- Table structure for table `ug_cards`
--

CREATE TABLE `ug_cards` (
  `id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `position` int(11) NOT NULL DEFAULT 100,
  `slug` varchar(80) DEFAULT NULL,
  `badge` varchar(60) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `title_muted` varchar(120) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `pillbox_title` varchar(120) DEFAULT NULL,
  `pills` text DEFAULT NULL,
  `learn_more_url` varchar(512) DEFAULT NULL,
  `learn_more_external` tinyint(1) NOT NULL DEFAULT 1,
  `curriculum_url` varchar(512) DEFAULT NULL,
  `curriculum_external` tinyint(1) NOT NULL DEFAULT 1,
  `apply_url` varchar(512) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ug_cards`
--

INSERT INTO `ug_cards` (`id`, `is_active`, `position`, `slug`, `badge`, `title`, `title_muted`, `summary`, `pillbox_title`, `pills`, `learn_more_url`, `learn_more_external`, `curriculum_url`, `curriculum_external`, `apply_url`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'bscs', 'BSCS', 'B.S. in Computer Science', NULL, 'Strong foundations in algorithms, software engineering, and intelligent systems.', 'Tracks', 'Data Science\nWeb Science\nComputer Vision', '/adamson-ccit/public/index.php?page=programs_undergraduate#bscs', 0, 'https://www.adamson.edu.ph/', 1, '/adamson-ccit/public/index.php?page=admission_freshman', '2025-09-12 06:04:49', '2025-09-17 18:48:29'),
(2, 1, 2, 'bsis', 'BSIS', 'B.S. in Information Systems', NULL, 'Bridges business processes with technology—analytics, reporting, and systems design.', 'Tracks', 'Business Analytics', '/adamson-ccit/public/index.php?page=programs_undergraduate#bsis', 0, 'https://www.adamson.edu.ph/', 1, '/adamson-ccit/public/index.php?page=admission_freshman', '2025-09-12 06:04:49', '2025-09-17 18:33:59'),
(3, 1, 3, 'bsit', 'BSIT', 'B.S. in Information Technology', NULL, 'Practical foundations in software development, systems administration, and network security.', 'Tracks', 'Consumer Enterprise & Enterprise Application Development\nGame Development\nNetwork Infrastructure & Network Security', '/adamson-ccit/public/index.php?page=programs_undergraduate#bsit', 0, 'https://www.adamson.edu.ph/', 1, '/adamson-ccit/public/index.php?page=admission_freshman', '2025-09-12 06:04:49', '2025-09-17 18:46:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `student_number` varchar(50) DEFAULT NULL,
  `program` varchar(100) DEFAULT NULL,
  `year_level` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password` varchar(255) NOT NULL,
  `role` enum('admin','dean','chairperson','faculty','student') NOT NULL DEFAULT 'student',
  `department_id` int(11) DEFAULT 1,
  `department` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `student_number`, `program`, `year_level`, `status`, `profile_image`, `created_at`, `updated_at`, `password`, `role`, `department_id`, `department`) VALUES
(1, '202213648', 'Marianne', 'Datinguinoo', 'marianne.datinguinoo@adamson.edu.ph', '202213648', 'BS Information Technology', '4th Year', 'active', NULL, '2025-09-29 09:02:56', '2025-10-18 10:27:27', '$2y$10$nnHTMYmcI8AvPbgmbm78bumFDH5VO2PfqrjhqPsfUuer5CdU6CcGu', 'student', 1, NULL),
(2, 'dean', 'Leonard', 'Alejandro', 'leonard.alejandro@adamson.edu.ph', NULL, NULL, NULL, 'active', NULL, '2025-09-29 09:02:56', '2025-10-01 07:29:42', '$2y$10$zYLpGDF4D3KB72TDvb1HSuJXvBdYqapJuS8ENApZNOLbXgEV3SQj2', 'dean', 1, NULL),
(3, 'faculty', 'Felnita', 'Tan', 'felnita.tan@adamson.edu.ph', NULL, NULL, '', 'active', NULL, '2025-09-29 09:02:56', '2025-11-04 15:41:36', '$2y$10$GXHR5xb6sO//YsGd4XFtGOhJ4TvTf5BCijAOu4O1QH3Hb.uhNSfna', 'faculty', 1, 'IT&IS'),
(4, 'admin', 'Admin', 'User', 'admin@adamson.edu.ph', NULL, NULL, NULL, 'active', NULL, '2025-09-29 09:02:56', '2025-09-29 09:02:56', '$2y$10$CBtAyr2d9I8XB4bha9NPGewtp0o/6Ge8xQ6NoNlCHLFkqyohHeyPC', 'admin', 1, NULL),
(18, '202213748', 'Nikkola Divine', 'Damaso', 'nikkola.divine.damaso@adamson.edu.ph', '202214648', 'BS Information Technology', '4th Year', 'active', NULL, '2025-10-18 09:43:35', '2025-10-18 09:46:20', '$2y$10$aYxFcEE.yOk9epxoTGQ4JuyLMG0LXll.BJIOmpifMWPUn7MHHVUZe', 'student', NULL, NULL),
(20, '202212289', 'Justine', 'Reyes', 'justine.reyes@adamson.edu.ph', '202212289', 'BS Information Technology', '4th Year', 'active', NULL, '2025-10-18 15:08:48', '2025-10-18 15:08:48', '$2y$10$3qotU/6oWHBe7l3JQtd41u644tCIdwIAhYSzJIFaoj71DD4KOA7R6', 'student', NULL, NULL),
(21, '202215407', 'Nicholas Andre', 'Fuensalida', 'nicholas.andre.fuensalida@adamson.edu.ph', '202215407', 'BS Information Technology', '4th Year', 'active', NULL, '2025-10-18 15:09:54', '2025-10-18 15:09:54', '$2y$10$XLPOmGHRAiAiq3qpuVSh2./1xYQFiGzqvGnZc0PI81IijnQZ5yf.K', 'student', NULL, NULL),
(24, '200813648', 'Archie', 'Santiago', 'archie.santiago@adamson.edu.ph', NULL, NULL, '', 'active', NULL, '2025-11-04 15:40:46', '2025-11-04 16:11:32', '$2y$10$pgUIosyJGEMn1XFqkbb0qeCVty3eY5Iy.YJk8q1fJbq5rbg.OeKo2', 'chairperson', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_remember_tokens`
--

CREATE TABLE `user_remember_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `selector` char(18) NOT NULL,
  `validator_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `ua` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_remember_tokens`
--

INSERT INTO `user_remember_tokens` (`id`, `user_id`, `selector`, `validator_hash`, `expires_at`, `ip`, `ua`, `created_at`) VALUES
(4, 1, 'adec033ed7a38b5b9b', 'a5a91fca83ad4eb9c8401586abbe7bb50876ef5550bf15353e91ed070ab2f523', '2025-11-17 00:53:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-17 22:53:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_history`
--
ALTER TABLE `about_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `about_vision_mission`
--
ALTER TABLE `about_vision_mission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `admission_freshman_settings`
--
ALTER TABLE `admission_freshman_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admission_graduate_settings`
--
ALTER TABLE `admission_graduate_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admission_transferee_settings`
--
ALTER TABLE `admission_transferee_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_announcements_status` (`status`),
  ADD KEY `idx_announcements_category` (`category`),
  ADD KEY `idx_announcements_date` (`date`);

--
-- Indexes for table `approval_workflow`
--
ALTER TABLE `approval_workflow`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_submission_step` (`submission_id`,`step_number`),
  ADD KEY `idx_submission_id` (`submission_id`),
  ADD KEY `idx_reviewer_role` (`reviewer_role`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Indexes for table `career_pathway_logs`
--
ALTER TABLE `career_pathway_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `certification`
--
ALTER TABLE `certification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `certifications`
--
ALTER TABLE `certifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chairperson_awards`
--
ALTER TABLE `chairperson_awards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `chairperson_certifications`
--
ALTER TABLE `chairperson_certifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `chairperson_education`
--
ALTER TABLE `chairperson_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `chairperson_experience`
--
ALTER TABLE `chairperson_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `chairperson_performance`
--
ALTER TABLE `chairperson_performance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `chairperson_personal`
--
ALTER TABLE `chairperson_personal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `user_id_2` (`user_id`);

--
-- Indexes for table `chairperson_profile`
--
ALTER TABLE `chairperson_profile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `user_id_2` (`user_id`);

--
-- Indexes for table `chairperson_research`
--
ALTER TABLE `chairperson_research`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `chairperson_trainings`
--
ALTER TABLE `chairperson_trainings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `deans_corner`
--
ALTER TABLE `deans_corner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dean_awards`
--
ALTER TABLE `dean_awards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dean_certifications`
--
ALTER TABLE `dean_certifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dean_education`
--
ALTER TABLE `dean_education`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dean_experience`
--
ALTER TABLE `dean_experience`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dean_logs`
--
ALTER TABLE `dean_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_table_name` (`table_name`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `dean_manage_announcements`
--
ALTER TABLE `dean_manage_announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dean_manage_events`
--
ALTER TABLE `dean_manage_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dean_manage_news`
--
ALTER TABLE `dean_manage_news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dean_performance`
--
ALTER TABLE `dean_performance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dean_personal`
--
ALTER TABLE `dean_personal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dean_profile`
--
ALTER TABLE `dean_profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dean_research`
--
ALTER TABLE `dean_research`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dean_trainings`
--
ALTER TABLE `dean_trainings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events_page_settings`
--
ALTER TABLE `events_page_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_activity_log`
--
ALTER TABLE `faculty_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_faculty_id` (`faculty_id`),
  ADD KEY `idx_activity_type` (`activity_type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `faculty_awards`
--
ALTER TABLE `faculty_awards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_certifications`
--
ALTER TABLE `faculty_certifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_certifications_page_settings`
--
ALTER TABLE `faculty_certifications_page_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_certification_award`
--
ALTER TABLE `faculty_certification_award`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `certification_id` (`certification_id`);

--
-- Indexes for table `faculty_education`
--
ALTER TABLE `faculty_education`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_experience`
--
ALTER TABLE `faculty_experience`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_news`
--
ALTER TABLE `faculty_news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_faculty_id` (`faculty_id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_publish_date` (`publish_date`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Indexes for table `faculty_notifications`
--
ALTER TABLE `faculty_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_faculty_id` (`faculty_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `faculty_performance`
--
ALTER TABLE `faculty_performance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_personal`
--
ALTER TABLE `faculty_personal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_portfolio`
--
ALTER TABLE `faculty_portfolio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_profile`
--
ALTER TABLE `faculty_profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_profiles`
--
ALTER TABLE `faculty_profiles`
  ADD PRIMARY KEY (`faculty_id`);

--
-- Indexes for table `faculty_profile_page_settings`
--
ALTER TABLE `faculty_profile_page_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_research`
--
ALTER TABLE `faculty_research`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status_year` (`year`);

--
-- Indexes for table `faculty_research_page_settings`
--
ALTER TABLE `faculty_research_page_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_submissions`
--
ALTER TABLE `faculty_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_faculty_id` (`faculty_id`),
  ADD KEY `idx_submission_type` (`submission_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_submitted_at` (`submitted_at`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `idx_related_item_id` (`related_item_id`);

--
-- Indexes for table `faculty_trainings`
--
ALTER TABLE `faculty_trainings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `footer_links`
--
ALTER TABLE `footer_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `footer_settings`
--
ALTER TABLE `footer_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `footer_socials`
--
ALTER TABLE `footer_socials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `header_menu`
--
ALTER TABLE `header_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `header_menu_items`
--
ALTER TABLE `header_menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `header_settings`
--
ALTER TABLE `header_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `header_utility_links`
--
ALTER TABLE `header_utility_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homepage_quick_actions`
--
ALTER TABLE `homepage_quick_actions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homepage_settings`
--
ALTER TABLE `homepage_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news_page_settings`
--
ALTER TABLE `news_page_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_programs_slug` (`slug`);

--
-- Indexes for table `programs_graduate`
--
ALTER TABLE `programs_graduate`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `programs_graduate_settings`
--
ALTER TABLE `programs_graduate_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs_undergraduate`
--
ALTER TABLE `programs_undergraduate`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ug_cards_slug` (`slug`),
  ADD KEY `idx_active_pos` (`is_active`,`position`),
  ADD KEY `idx_pos` (`position`);

--
-- Indexes for table `programs_undergraduate_settings`
--
ALTER TABLE `programs_undergraduate_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program_cards`
--
ALTER TABLE `program_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_level_slug` (`level`,`slug`),
  ADD KEY `ix_level_status` (`level`,`status`),
  ADD KEY `ix_level_position` (`level`,`position`);

--
-- Indexes for table `program_group_content_blocks`
--
ALTER TABLE `program_group_content_blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pgcb_program_slug` (`program_slug`);

--
-- Indexes for table `program_group_settings`
--
ALTER TABLE `program_group_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pgs_program_slug` (`program_slug`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `selector` (`selector`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `research`
--
ALTER TABLE `research`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `secretary_appointments`
--
ALTER TABLE `secretary_appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `secretary_logs`
--
ALTER TABLE `secretary_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `secretary_id` (`secretary_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `student_certification`
--
ALTER TABLE `student_certification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_certifications`
--
ALTER TABLE `student_certifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_certifications_page_settings`
--
ALTER TABLE `student_certifications_page_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_certification_stat`
--
ALTER TABLE `student_certification_stat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_licenses`
--
ALTER TABLE `student_licenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_company` (`company_id`);

--
-- Indexes for table `student_organization`
--
ALTER TABLE `student_organization`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_organizations_page_settings`
--
ALTER TABLE `student_organizations_page_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user` (`user_id`),
  ADD UNIQUE KEY `ux_student_profiles_user` (`user_id`);

--
-- Indexes for table `student_research`
--
ALTER TABLE `student_research`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_research_year` (`year`),
  ADD KEY `idx_student_research_status` (`status`),
  ADD KEY `idx_student_research_prog` (`program`);

--
-- Indexes for table `student_research_page_settings`
--
ALTER TABLE `student_research_page_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_scholarship`
--
ALTER TABLE `student_scholarship`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_scholarships_page_settings`
--
ALTER TABLE `student_scholarships_page_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_testimonial`
--
ALTER TABLE `student_testimonial`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_testimonials`
--
ALTER TABLE `student_testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_testimonials_page_settings`
--
ALTER TABLE `student_testimonials_page_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ug_cards`
--
ALTER TABLE `ug_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ug_cards_slug` (`slug`),
  ADD KEY `idx_active_pos` (`is_active`,`position`),
  ADD KEY `idx_pos` (`position`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_remember_tokens`
--
ALTER TABLE `user_remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `selector` (`selector`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `expires_at` (`expires_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_history`
--
ALTER TABLE `about_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `about_vision_mission`
--
ALTER TABLE `about_vision_mission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admission_freshman_settings`
--
ALTER TABLE `admission_freshman_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admission_graduate_settings`
--
ALTER TABLE `admission_graduate_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admission_transferee_settings`
--
ALTER TABLE `admission_transferee_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `approval_workflow`
--
ALTER TABLE `approval_workflow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `career_pathway_logs`
--
ALTER TABLE `career_pathway_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certification`
--
ALTER TABLE `certification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `certifications`
--
ALTER TABLE `certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `chairperson_awards`
--
ALTER TABLE `chairperson_awards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chairperson_certifications`
--
ALTER TABLE `chairperson_certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chairperson_education`
--
ALTER TABLE `chairperson_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chairperson_experience`
--
ALTER TABLE `chairperson_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chairperson_performance`
--
ALTER TABLE `chairperson_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chairperson_personal`
--
ALTER TABLE `chairperson_personal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chairperson_profile`
--
ALTER TABLE `chairperson_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chairperson_research`
--
ALTER TABLE `chairperson_research`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chairperson_trainings`
--
ALTER TABLE `chairperson_trainings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `deans_corner`
--
ALTER TABLE `deans_corner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dean_awards`
--
ALTER TABLE `dean_awards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dean_certifications`
--
ALTER TABLE `dean_certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `dean_education`
--
ALTER TABLE `dean_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dean_experience`
--
ALTER TABLE `dean_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dean_logs`
--
ALTER TABLE `dean_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `dean_manage_announcements`
--
ALTER TABLE `dean_manage_announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dean_manage_events`
--
ALTER TABLE `dean_manage_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dean_manage_news`
--
ALTER TABLE `dean_manage_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dean_performance`
--
ALTER TABLE `dean_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dean_personal`
--
ALTER TABLE `dean_personal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `dean_profile`
--
ALTER TABLE `dean_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `dean_research`
--
ALTER TABLE `dean_research`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dean_trainings`
--
ALTER TABLE `dean_trainings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `events_page_settings`
--
ALTER TABLE `events_page_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `faculty_activity_log`
--
ALTER TABLE `faculty_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `faculty_awards`
--
ALTER TABLE `faculty_awards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculty_certifications`
--
ALTER TABLE `faculty_certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculty_certifications_page_settings`
--
ALTER TABLE `faculty_certifications_page_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faculty_certification_award`
--
ALTER TABLE `faculty_certification_award`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `faculty_education`
--
ALTER TABLE `faculty_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculty_experience`
--
ALTER TABLE `faculty_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculty_news`
--
ALTER TABLE `faculty_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faculty_notifications`
--
ALTER TABLE `faculty_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculty_performance`
--
ALTER TABLE `faculty_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculty_personal`
--
ALTER TABLE `faculty_personal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculty_portfolio`
--
ALTER TABLE `faculty_portfolio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `faculty_profile`
--
ALTER TABLE `faculty_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `faculty_profile_page_settings`
--
ALTER TABLE `faculty_profile_page_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faculty_research`
--
ALTER TABLE `faculty_research`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `faculty_research_page_settings`
--
ALTER TABLE `faculty_research_page_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faculty_submissions`
--
ALTER TABLE `faculty_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=355;

--
-- AUTO_INCREMENT for table `faculty_trainings`
--
ALTER TABLE `faculty_trainings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `footer_links`
--
ALTER TABLE `footer_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `footer_settings`
--
ALTER TABLE `footer_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `footer_socials`
--
ALTER TABLE `footer_socials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `header_menu`
--
ALTER TABLE `header_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `header_menu_items`
--
ALTER TABLE `header_menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `header_settings`
--
ALTER TABLE `header_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `header_utility_links`
--
ALTER TABLE `header_utility_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `homepage_quick_actions`
--
ALTER TABLE `homepage_quick_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=262;

--
-- AUTO_INCREMENT for table `news_page_settings`
--
ALTER TABLE `news_page_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `programs_graduate`
--
ALTER TABLE `programs_graduate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `programs_graduate_settings`
--
ALTER TABLE `programs_graduate_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `programs_undergraduate`
--
ALTER TABLE `programs_undergraduate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `programs_undergraduate_settings`
--
ALTER TABLE `programs_undergraduate_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `program_cards`
--
ALTER TABLE `program_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `program_group_content_blocks`
--
ALTER TABLE `program_group_content_blocks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `program_group_settings`
--
ALTER TABLE `program_group_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `research`
--
ALTER TABLE `research`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `secretary_appointments`
--
ALTER TABLE `secretary_appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `secretary_logs`
--
ALTER TABLE `secretary_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_certification`
--
ALTER TABLE `student_certification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `student_certifications`
--
ALTER TABLE `student_certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_certifications_page_settings`
--
ALTER TABLE `student_certifications_page_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_certification_stat`
--
ALTER TABLE `student_certification_stat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `student_licenses`
--
ALTER TABLE `student_licenses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `student_organization`
--
ALTER TABLE `student_organization`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_organizations_page_settings`
--
ALTER TABLE `student_organizations_page_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_research`
--
ALTER TABLE `student_research`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student_research_page_settings`
--
ALTER TABLE `student_research_page_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_scholarship`
--
ALTER TABLE `student_scholarship`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_scholarships_page_settings`
--
ALTER TABLE `student_scholarships_page_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_testimonial`
--
ALTER TABLE `student_testimonial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student_testimonials`
--
ALTER TABLE `student_testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_testimonials_page_settings`
--
ALTER TABLE `student_testimonials_page_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ug_cards`
--
ALTER TABLE `ug_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_remember_tokens`
--
ALTER TABLE `user_remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `approval_workflow`
--
ALTER TABLE `approval_workflow`
  ADD CONSTRAINT `approval_workflow_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `faculty_submissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approval_workflow_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `career_pathway_logs`
--
ALTER TABLE `career_pathway_logs`
  ADD CONSTRAINT `career_pathway_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dean_logs`
--
ALTER TABLE `dean_logs`
  ADD CONSTRAINT `dean_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `faculty_activity_log`
--
ALTER TABLE `faculty_activity_log`
  ADD CONSTRAINT `faculty_activity_log_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty_profile` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faculty_certification_award`
--
ALTER TABLE `faculty_certification_award`
  ADD CONSTRAINT `faculty_certification_award_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `faculty_certification_award_ibfk_2` FOREIGN KEY (`certification_id`) REFERENCES `certification` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faculty_news`
--
ALTER TABLE `faculty_news`
  ADD CONSTRAINT `faculty_news_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `faculty_submissions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `faculty_news_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `faculty_profile` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faculty_notifications`
--
ALTER TABLE `faculty_notifications`
  ADD CONSTRAINT `faculty_notifications_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty_profile` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faculty_submissions`
--
ALTER TABLE `faculty_submissions`
  ADD CONSTRAINT `faculty_submissions_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `header_menu_items`
--
ALTER TABLE `header_menu_items`
  ADD CONSTRAINT `header_menu_items_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `header_menu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `program_group_content_blocks`
--
ALTER TABLE `program_group_content_blocks`
  ADD CONSTRAINT `fk_pgcb_programs_slug` FOREIGN KEY (`program_slug`) REFERENCES `programs` (`slug`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `program_group_settings`
--
ALTER TABLE `program_group_settings`
  ADD CONSTRAINT `fk_pgs_programs_slug` FOREIGN KEY (`program_slug`) REFERENCES `programs` (`slug`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `fk_remember_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `secretary_logs`
--
ALTER TABLE `secretary_logs`
  ADD CONSTRAINT `secretary_logs_ibfk_1` FOREIGN KEY (`secretary_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `fk_sp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
