
<?php
require_once __DIR__ . '/../models/StudentScholarshipsPageSettings.php';
require_once __DIR__ . '/../models/StudentScholarship.php';
if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}
$settings = StudentScholarshipsPageSettings::getSettings();
$scholarships = StudentScholarship::getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Scholarships | AdU-CCIT</title>
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
      display: flex;
      flex-direction: column;
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
      flex: 1;
    }
    
    /* Scholarship type badges */
    .sch__type {
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
    
    .sch__type--need {
      background: #fef3c7;
      border-color: #fcd34d;
      color: #92400e;
    }
    
    .sch__type--merit {
      background: #dbeafe;
      border-color: #bfdbfe;
      color: #1d4ed8;
    }
    
    /* Card content area */
    .prog__summary {
      padding: 0 24px 20px;
      margin: 0;
      color: #374151;
      line-height: 1.6;
      font-size: 15px;
    }
    
    /* Enhanced pillbox styling for scholarship details */
    .pillbox {
      margin: 0 24px 16px;
      padding: 16px;
      background: #f8fafc;
      border-radius: 12px;
      border-left: 4px solid #e5e7eb;
    }
    
    .pillbox:nth-of-type(1) { border-left-color: #10b981; } /* Conditions - green */
    .pillbox:nth-of-type(2) { border-left-color: #3b82f6; } /* Requirements - blue */
    .pillbox:nth-of-type(3) { border-left-color: #8b5cf6; } /* Examples - purple */
    
    .pillbox__title {
      margin: 0 0 8px;
      font-size: 14px;
      font-weight: 700;
      color: #374151;
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }
    
    .pillbox ul {
      margin: 0;
      padding-left: 16px;
      color: #6b7280;
      line-height: 1.5;
    }
    
    .pillbox li {
      margin: 4px 0;
      font-size: 14px;
    }
    
    /* Card footer */
    .prog__footer { 
      padding: 16px 24px 24px;
      margin-top: auto;
      border-top: 1px solid #f3f4f6;
      background: #fafbfc;
    }
    
    /* Scholarship note styling */
    .sch__note {
      margin: 24px 0 0;
      padding: 16px 20px;
      background: #f0f9ff;
      border: 1px solid #bae6fd;
      border-radius: 12px;
      color: #0c4a6e;
      font-size: 14px;
      line-height: 1.5;
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

    <div class="container hero__inner">
      <div class="hero__copy">
      <span class="hero__eyebrow">Student Support</span>
      <h1 class="subhero__title">Scholarships</h1>
      <p class="hero__lead"><?= e($settings['subhero_lead'] ?? 'Financial aid options for incoming and current Adamson students.') ?></p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV ============ -->
  <nav class="subnav" aria-label="Student sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li>
          <a href="/adamson-ccit/public/index.php?page=student_organizations">Organizations</a>
        </li>
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=student_scholarships" aria-current="page">Scholarships</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=student_research">Research</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=student_certifications">Certifications</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=student_testimonials">Testimonials</a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- ============ SCHOLARSHIPS GRID ============ -->
  <section class="content" aria-labelledby="scholarships-heading">
    <div class="container">
      <h2 id="scholarships-heading" class="sr-only">Available Scholarships</h2>
      <div class="prog__grid">
        <?php foreach ($scholarships as $sch): ?>
        <article class="prog__card">
          <header class="prog__head">
            <h3 class="prog__title"><?= e($sch['name']) ?></h3>
            <span class="sch__type sch__type--<?= strtolower($sch['type']) ?>"><?= e($sch['type']) ?></span>
          </header>
          <p class="prog__summary"><?= $sch['summary'] ?></p>
          <?php if (!empty($sch['conditions'])): ?>
          <div class="pillbox">
            <h4 class="pillbox__title">Key Conditions</h4>
            <?= $sch['conditions'] ?>
          </div>
          <?php endif; ?>
          <?php if (!empty($sch['requirements'])): ?>
          <div class="pillbox">
            <h4 class="pillbox__title">Initial Requirements</h4>
            <?= $sch['requirements'] ?>
          </div>
          <?php endif; ?>
          <?php if (!empty($sch['examples'])): ?>
          <div class="pillbox">
            <h4 class="pillbox__title">Examples</h4>
            <?= $sch['examples'] ?>
          </div>
          <?php endif; ?>
          <div class="prog__footer">
            <?php if (!empty($sch['learn_more_url'])): ?>
            <a class="btn btn--outline-blue ext" href="<?= e($sch['learn_more_url']) ?>" target="_blank" rel="noopener">Learn More</a>
            <?php endif; ?>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <?php if (!empty($settings['note'])): ?>
      <p class="sch__note"><?= $settings['note'] ?></p>
      <?php endif; ?>
    </div>
  </section>

</main>

</body>
</html>
