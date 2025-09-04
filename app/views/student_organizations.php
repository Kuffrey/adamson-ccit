
<?php
require_once __DIR__ . '/../models/StudentOrganizationsPageSettings.php';
require_once __DIR__ . '/../models/StudentOrganization.php';
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
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

  <!-- Page-scoped polish (on-brand with your style.css) -->
  <style>
    .page-orgs .content > .container{padding-block:clamp(24px,5vw,48px)}
    .page-orgs .org__type{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;font:800 11px/1 "Inter",system-ui;letter-spacing:.05em;text-transform:uppercase;border:1px solid var(--edgec);background:#f8fafc;color:#0b234c}
    .page-orgs .org__type--academic{background:#e8f5ee;border-color:#d6e9df}
    .page-orgs .org__type--co{background:#eef2ff;border-color:#d8dcef}
    .page-orgs .prog__head{display:flex;flex-wrap:wrap;align-items:center;gap:8px 10px}
    .page-orgs .org__logo{border:1px solid var(--edgec);border-radius:12px;background:#fff;height:100px;display:grid;place-items:center;padding:12px}
    .page-orgs .org__logo img{max-width:100%;max-height:80px;object-fit:contain;display:block}
    .page-orgs .org__meta{margin:0;color:#6b7280;font-size:13px}
    .page-orgs .ext::after{content:"↗";font-weight:900;margin-left:.35em;opacity:.7}
  </style>
</head>
<body>

<main class="page-programs page-orgs">

  <!-- ============ SUB-HERO ============ -->
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

  <!-- ============ STUDENT SUBNAV ============ -->
  <nav class="subnav" aria-label="Student sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=student_organizations" aria-current="page">Organizations</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_scholarships">Scholarships</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_research">Research</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_certifications">Certifications</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_testimonials">Testimonials</a></li>
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
