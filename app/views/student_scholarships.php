
<?php
require_once __DIR__ . '/../models/StudentScholarshipsPageSettings.php';
require_once __DIR__ . '/../models/StudentScholarship.php';
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
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
</head>
<body>

<main class="page-sch">

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="<?= e($settings['subhero_image_url'] ?? '/adamson-ccit/public/assets/images/hero-campus.jpg') ?>" alt="Adamson University campus">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Student Support</p>
      <h1 class="subhero__title">Scholarships</h1>
      <p class="subhero__lead"><?= e($settings['subhero_lead'] ?? 'Financial aid options for incoming and current Adamson students.') ?></p>
    </div>
  </section>

  <!-- ============ STUDENT SUBNAV ============ -->
  <nav class="subnav" aria-label="Student sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li><a href="/adamson-ccit/public/index.php?page=student_organizations">Organizations</a></li>
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=student_scholarships" aria-current="page">Scholarships</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_research">Research</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_certifications">Certifications</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_testimonials">Testimonials</a></li>
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
