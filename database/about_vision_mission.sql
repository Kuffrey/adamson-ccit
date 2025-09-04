-- Table for About > Vision & Mission page
CREATE TABLE IF NOT EXISTS about_vision_mission (
  id INT PRIMARY KEY AUTO_INCREMENT,
  main_vision TEXT,
  main_mission TEXT,
  main_intro TEXT,
  dept1_title VARCHAR(255),
  dept1_vision TEXT,
  dept1_mission TEXT,
  dept1_objectives TEXT,
  dept2_title VARCHAR(255),
  dept2_vision TEXT,
  dept2_mission TEXT,
  dept2_objectives TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default row (run only once)
INSERT INTO about_vision_mission (main_vision, main_mission, main_intro, dept1_title, dept1_vision, dept1_mission, dept1_objectives, dept2_title, dept2_vision, dept2_mission, dept2_objectives)
VALUES (
  'We are recognized globally for pioneering education in computing and information technologies, fostering innovation, and producing graduates who are leaders in the IT industry, capable of addressing the challenges and opportunities of a technology-focused society.',
  'As an academic unit, we provide an inclusive and dynamic learning environment that delivers specialized, industry-relevant curriculum led by highly qualified faculty.',
  'Our purpose, our promise, and the departmental directions that guide CCIT.',
  'Information Technology & Information Systems',
  'A College dedicated in developing Christian professionals with solid foundation in the fields of Chemistry, Computer Science, Information Management, Information Technology, Mathematics, Natural Science, Psychology and Physics.',
  'To provide graduates with adequate knowledge and skills in their major field of specialization; To develop quality graduates who will be globally competitive in their chosen field; To prepare graduates for entry to industry, research and entrepreneurship.',
  'To offer courses which will provide solid foundation in the Sciences particularly in Mathematics, Chemistry, Natural Sciences, Psychology, Computer Science, Information Management and Information Technology; To provide adequate coverage of major fields of specialization; To qualify a student for career in his chosen field of specialization as well as entry into advance studies in Sciences.',
  'Computer Science',
  'To be a nationally and regionally recognized center for excellence in Computer Science education and research, developing innovators who create impactful computing solutions for society.',
  'To deliver a rigorous, research-informed curriculum grounded in algorithms, systems, and data, empowering students to design, build, and evaluate trustworthy software and intelligent systems with ethical and social responsibility.',
  'Prepare students to be computer professionals and researchers; Develop proficiency in designing and developing robust computing solutions.'
);
