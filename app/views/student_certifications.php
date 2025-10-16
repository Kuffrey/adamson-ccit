
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
      <span class="hero__eyebrow">Students</span>
      <h1 class="subhero__title"><?= e($settings['hero_title'] ?? 'Certifications') ?></h1>
      <p class="hero__lead"><?= e($settings['hero_lead'] ?? 'Industry badges aligned with CCIT courses and labs.') ?></p>
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
