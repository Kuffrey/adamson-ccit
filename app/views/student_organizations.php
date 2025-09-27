
<?php
require_once __DIR__ . '/../models/StudentOrganizationsPageSettings.php';
require_once __DIR__ . '/../models/StudentOrganization.php';
if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}
$settings = StudentOrganizationsPageSettings::getSettings();
$orgs = StudentOrganization::getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Organizations | AdU-CCIT</title>
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
    
    /* Organization type badges */
    .org__type {
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
    
    .org__type--academic {
      background: #dcfce7;
      border-color: #bbf7d0;
      color: #15803d;
    }
    
    .org__type--co {
      background: #dbeafe;
      border-color: #bfdbfe;
      color: #1d4ed8;
    }
    
    /* Organization logo */
    .org__logo {
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      background: #fff;
      height: 100px;
      display: grid;
      place-items: center;
      padding: 16px;
      margin: 16px 24px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .org__logo img {
      max-width: 100%;
      max-height: 80px;
      object-fit: contain;
      display: block;
    }
    
    /* Card content */
    .prog__summary { 
      padding: 0 24px 16px; 
      margin: 0;
      color: #374151;
      line-height: 1.6;
    }
    
    .pillbox { 
      margin: 0 24px 16px;
      padding: 16px;
      background: #f9fafb;
      border-radius: 12px;
      border: 1px solid #e5e7eb;
    }
    
    .pillbox__title {
      margin: 0 0 8px;
      font-size: 14px;
      font-weight: 700;
      color: #374151;
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }
    
    /* Card footer */
    .prog__footer { 
      padding: 16px 24px 24px;
      border-top: 1px solid #f3f4f6;
      background: #fafbfc;
    }
    
    .org__meta {
      margin: 0 0 16px;
      color: #6b7280;
      font-size: 13px;
      padding: 0 24px;
      font-weight: 500;
    }
    
    /* External link styling */
    .ext::after {
      content: "↗";
      font-weight: 900;
      margin-left: .5em;
      opacity: .7;
      transition: opacity 0.2s ease;
    }
    
    .ext:hover::after {
      opacity: 1;
    }
  </style>
</head>
<body>

<main>  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
  <img src="<?= e($settings['subhero_image_url'] ?? '/adamson-ccit/public/assets/images/hero-campus.jpg') ?>" alt="Adamson University student community">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Student Life</p>
      <h1 class="subhero__title">Recognized Student Organizations</h1>
  <p class="subhero__lead"><?= e($settings['subhero_lead'] ?? 'Official academic and co-academic organizations for CCIT students.') ?></p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV ============ -->
  <nav class="subnav" aria-label="Student sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=student_organizations" aria-current="page">Organizations</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=student_scholarships">Scholarships</a>
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

  <!-- ============ ORGS GRID ============ -->
  <section class="content" aria-labelledby="orgs-heading">
    <div class="container">
      <h2 id="orgs-heading" class="sr-only">Recognized CCIT Student Organizations</h2>
      <div class="prog__grid">
        <?php foreach ($orgs as $org): ?>
        <article class="prog__card">
          <header class="prog__head">
            <h3 class="prog__title"><?= e($org['name']) ?></h3>
            <span class="org__type org__type--<?= strtolower($org['type']) ?>"><?= e($org['type']) ?></span>
          </header>
          <div class="org__logo" aria-hidden="true">
            <img src="<?= e($org['logo_url']) ?>" alt="<?= e($org['name']) ?> logo">
          </div>
          <p class="prog__summary"><?= e($org['summary']) ?></p>
          <div class="pillbox" aria-label="Follow <?= e($org['name']) ?>">
            <h4 class="pillbox__title">Connect</h4>
            <ul class="pills" role="list">
              <?php if (!empty($org['facebook_url'])): ?><li><a class="ext" href="<?= e($org['facebook_url']) ?>" target="_blank" rel="noopener">Facebook</a></li><?php endif; ?>
              <?php if (!empty($org['instagram_url'])): ?><li><a class="ext" href="<?= e($org['instagram_url']) ?>" target="_blank" rel="noopener">Instagram</a></li><?php endif; ?>
              <?php if (!empty($org['x_url'])): ?><li><a class="ext" href="<?= e($org['x_url']) ?>" target="_blank" rel="noopener">X</a></li><?php endif; ?>
            </ul>
          </div>
          <p class="org__meta">Audience: <?= e($org['audience']) ?></p>
          <div class="prog__footer">
            <?php if (!empty($org['learn_more_url'])): ?>
              <a class="btn btn--outline-blue ext" href="<?= e($org['learn_more_url']) ?>" target="_blank" rel="noopener">Learn More</a>
            <?php endif; ?>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

</main>

</body>
</html>
