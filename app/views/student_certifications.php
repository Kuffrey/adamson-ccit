<?php
// student_certifications.php — Dynamic Student Certifications (CMS-driven)
require_once __DIR__ . '/../models/StudentCertificationsPageSettings.php';
require_once __DIR__ . '/../models/StudentCertificationStat.php';
require_once __DIR__ . '/../models/StudentCertification.php';

$settings = StudentCertificationsPageSettings::getSettings();
$stats = StudentCertificationStat::getAll();
$certs = StudentCertification::getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Certifications | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/student-certifications.css"/>
</head>
<body>
<main class="page-certs">
  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="<?= htmlspecialchars($settings['subhero_image_url'] ?? '/adamson-ccit/public/assets/images/hero-campus.jpg') ?>" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Students</p>
      <h1 class="subhero__title">Certifications</h1>
      <p class="subhero__lead"><?= htmlspecialchars($settings['subhero_lead'] ?? 'Industry badges aligned with CCIT courses and labs.') ?></p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (Students) ============ -->
  <nav class="subnav" aria-label="Students sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li><a href="/adamson-ccit/public/index.php?page=student_organizations">Organizations</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_scholarships">Scholarships</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_research">Research</a></li>
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=student_certifications" aria-current="page">Certifications</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_testimonials">Testimonials</a></li>
      </ul>
    </div>
  </nav>

  <!-- ============ LATEST PASSING RATES ============ -->
  <section class="content cstats section-sep" aria-labelledby="latest-rates">
    <div class="container">
      <div class="sec__head">
        <h2 id="latest-rates" class="h2">Recent Passing Rates</h2>
        <p class="sec__kicker">SY 2024–2025 • 2nd Semester</p>
      </div>
      <ul class="cstat__grid" role="list">
        <?php foreach ($stats as $stat): ?>
        <li class="cstat">
          <div class="cstat__rate">
            <span class="num"><?= htmlspecialchars($stat['rate']) ?></span>
            <span class="tag"><?= htmlspecialchars($stat['tag']) ?></span>
          </div>
          <div class="cstat__body">
            <h3 class="cstat__title"><?= htmlspecialchars($stat['title']) ?></h3>
            <p class="cstat__meta"><?= htmlspecialchars($stat['meta']) ?></p>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <p class="cstat__note"><?= htmlspecialchars($settings['cstat_note'] ?? 'If an exam isn’t listed here, its passing rate will be posted when available.') ?></p>
    </div>
  </section>

  <!-- ============ AVAILABLE CERTIFICATIONS ============ -->
  <section class="content certs section-sep" aria-labelledby="available-certs">
    <div class="container">
      <div class="sec__head">
        <h2 id="available-certs" class="h2">Available Industry Certifications</h2>
        <p class="sec__kicker">Pearson IT Specialist series (via Certiport)</p>
      </div>
      <div class="certs__grid">
        <?php foreach ($certs as $cert): ?>
        <article class="cert">
          <figure class="cert__badge">
            <img src="<?= htmlspecialchars($cert['badge_url']) ?>" alt="<?= htmlspecialchars($cert['name']) ?> badge">
          </figure>
          <div class="cert__body">
            <h3 class="cert__title"><?= htmlspecialchars($cert['name']) ?></h3>
            <p class="cert__issuer">Issued by <?= htmlspecialchars($cert['issuer']) ?></p>
            <p class="cert__desc"><?= htmlspecialchars($cert['description']) ?></p>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <div class="cnotice cnotice--inline" role="note">
        <?= nl2br(htmlspecialchars($settings['certs_note'] ?? 'Certification windows & registration are announced by the department through official CCIT channels and your instructors. Posts include dates, fees (if any), seat counts, and step-by-step registration.')) ?>
      </div>
    </div>
  </section>
</main>
</body>
</html>
