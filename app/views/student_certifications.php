
<?php
require_once __DIR__ . '/../models/StudentCertificationsPageSettings.php';
require_once __DIR__ . '/../models/StudentCertification.php';
if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}
$settings = StudentCertificationsPageSettings::getSettings();
$certs = StudentCertification::getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Certifications | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
  
  <style>
    /* Consistent container padding */
    .content > .container { 
      padding: 16px 20px clamp(24px,5vw,48px); 
    }
    
    /* Professional grid layout */
    .prog__grid {
      display: grid;
      gap: 20px;
      grid-template-columns: repeat(3,1fr);
      padding: 20px 0 clamp(32px,6vw,56px);
    }
    
    @media (max-width:960px) {
      .prog__grid {
        grid-template-columns: 1fr 1fr;
        gap: 18px;
      }
    }
    
    @media (max-width:580px) {
      .prog__grid {
        grid-template-columns: 1fr;
        gap: 16px;
      }
    }
    
    /* Enhanced card styling */
    .prog__card {
      border: 1px solid var(--edgec);
      border-radius: 16px;
      background: #fff;
      overflow: hidden;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .prog__card:hover {
      box-shadow: 0 12px 32px rgba(0,0,0,0.12);
      transform: translateY(-4px);
      border-color: #d1d5db;
    }
    
    /* Card header */
    .prog__head {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 24px 24px 16px;
      border-bottom: 1px solid #f3f4f6;
    }
    
    .prog__title {
      margin: 0;
      font-size: 18px;
      font-weight: 900;
      color: #0b234c;
      line-height: 1.3;
    }
    
    /* Certification type badges */
    .cert__type {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 12px;
      border-radius: 20px;
      font: 700 11px/1 "Inter",system-ui;
      letter-spacing: .05em;
      text-transform: uppercase;
      border: 1px solid var(--edgec);
      background: #f8fafc;
      color: #0b234c;
      white-space: nowrap;
    }
    
    .cert__type--it {
      background: #dcfce7;
      border-color: #bbf7d0;
      color: #15803d;
    }
    
    .cert__type--web {
      background: #fef3c7;
      border-color: #fcd34d;
      color: #92400e;
    }
    
    /* Card content */
    .prog__summary {
      padding: 0 24px 16px;
      margin: 0;
      color: #374151;
      line-height: 1.6;
    }
    
    /* Card footer */
    .prog__footer {
      padding: 16px 24px 24px;
      border-top: 1px solid #f3f4f6;
      background: #fafbfc;
    }
    
    .prog__meta {
      margin: 0 0 12px;
      color: #6b7280;
      font-size: 13px;
      font-weight: 500;
      padding: 0 24px;
    }
  </style>
</head>
<body>

<main>

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="<?= e($settings['subhero_image_url'] ?? '/adamson-ccit/public/assets/images/hero-campus.jpg') ?>" alt="Adamson University campus">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Students</p>
      <h1 class="subhero__title"><?= e($settings['hero_title'] ?? 'Certifications') ?></h1>
      <p class="subhero__lead"><?= e($settings['hero_lead'] ?? 'Industry badges aligned with CCIT courses and labs.') ?></p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV ============ -->
  <nav class="subnav" aria-label="Student sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li>
          <a href="/adamson-ccit/public/index.php?page=student_organizations">Organizations</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=student_scholarships">Scholarships</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=student_research">Research</a>
        </li>
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=student_certifications" aria-current="page">Certifications</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=student_testimonials">Testimonials</a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- ============ CERTIFICATIONS GRID ============ -->
  <section class="content" aria-labelledby="certifications-heading">
    <div class="container">
      <h2 id="certifications-heading" class="sr-only">Available Certifications</h2>
      <?php if (!empty($certs)): ?>
      <div class="prog__grid">
        <?php foreach ($certs as $cert): ?>
        <article class="prog__card">
          <header class="prog__head">
            <h3 class="prog__title"><?= e($cert['name']) ?></h3>
            <span class="cert__type cert__type--<?= strtolower($cert['category'] ?? 'it') ?>"><?= e($cert['category'] ?? 'IT') ?></span>
          </header>
          <p class="prog__summary"><?= e($cert['description']) ?></p>
          <footer class="prog__footer">
            <p class="prog__meta"><strong>Issuer:</strong> <?= e($cert['issuer'] ?? 'Pearson') ?></p>
            <?php if (!empty($cert['learn_more_url'])): ?>
              <a href="<?= e($cert['learn_more_url']) ?>" class="btn btn--outline-blue ext" target="_blank" rel="noopener">Learn More</a>
            <?php endif; ?>
          </footer>
        </article>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="prog__grid">
        <article class="prog__card">
          <header class="prog__head">
            <h3 class="prog__title">IT Specialist - Python</h3>
            <span class="cert__type cert__type--it">IT</span>
          </header>
          <p class="prog__summary">Demonstrates competency in Python programming fundamentals and application development.</p>
          <footer class="prog__footer">
            <p class="prog__meta"><strong>Issuer:</strong> Pearson</p>
          </footer>
        </article>
        <article class="prog__card">
          <header class="prog__head">
            <h3 class="prog__title">IT Specialist - JavaScript</h3>
            <span class="cert__type cert__type--web">Web</span>
          </header>
          <p class="prog__summary">Validates skills in JavaScript programming and modern web development practices.</p>
          <footer class="prog__footer">
            <p class="prog__meta"><strong>Issuer:</strong> Pearson</p>
          </footer>
        </article>
        <article class="prog__card">
          <header class="prog__head">
            <h3 class="prog__title">IT Specialist - HTML & CSS</h3>
            <span class="cert__type cert__type--web">Web</span>
          </header>
          <p class="prog__summary">Verifies proficiency in web markup and styling technologies for modern applications.</p>
          <footer class="prog__footer">
            <p class="prog__meta"><strong>Issuer:</strong> Pearson</p>
          </footer>
        </article>
      </div>
      <?php endif; ?>
      <?php if (!empty($settings['note'])): ?>
      <p class="cert__note"><?= e($settings['note']) ?></p>
      <?php endif; ?>
    </div>
  </section>

</main>

</body>
</html>
