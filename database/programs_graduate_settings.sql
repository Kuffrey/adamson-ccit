-- programs_graduate_settings.sql
CREATE TABLE IF NOT EXISTS programs_graduate_settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  subhero_image_url VARCHAR(255) DEFAULT '/adamson-ccit/public/assets/images/programs/graduate.jpg',
  subhero_lead TEXT,
  programs_grid MEDIUMTEXT,
  cta_title VARCHAR(255),
  cta_description TEXT,
  cta_action_url VARCHAR(255),
  cta_action_label VARCHAR(100),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO programs_graduate_settings (
  subhero_image_url, subhero_lead, programs_grid, cta_title, cta_description, cta_action_url, cta_action_label
) VALUES (
  '/adamson-ccit/public/assets/images/programs/graduate.jpg',
  'Advanced training for IT leaders—rigor, ethics, and impact.',
  '<div class="prog__grid">\n\n<!-- MIT — Master in Information Technology -->\n<article class="prog__card" id="mit">\n  <header class="prog__head">\n    <span class="badge" aria-hidden="true">MIT</span>\n    <h3 class="prog__title">Master in Information Technology</h3>\n  </header>\n  <p class="prog__summary">The Master in Information Technology (MIT) at Adamson University provides advanced theoretical and practical IT training to prepare students for leadership roles. It emphasizes ethical practice and social responsibility—developing professionals who drive innovation and support sustainable development.</p>\n  <div class="pillbox">\n    <h4 class="pillbox__title">Program Emphases</h4>\n    <ul class="pills" role="list">\n      <li>Advanced Computing Practice</li>\n      <li>IT Leadership &amp; Governance</li>\n      <li>Ethics &amp; Social Responsibility</li>\n      <li>Innovation &amp; Sustainable Impact</li>\n    </ul>\n  </div>\n  <div class="prog__footer">\n    <span class="btn btn--disabled" aria-disabled="true" title="Details coming soon">Learn More</span>\n    <nav class="mini-links" aria-label="MIT quick links">\n      <a class="ext" href="https://www.adamson.edu.ph/v1/?page=pos-course&course=j" target="_blank" rel="noopener">Curriculum</a>\n      <a href="/adamson-ccit/public/index.php?page=contact">Inquire</a>\n    </nav>\n  </div>\n</article>\n\n</div>',
  'Chart your next step.',
  'Ask us about MIT schedules, requirements, and scholarships.',
  '/adamson-ccit/public/index.php?page=contact',
  'Contact CCIT'
);
