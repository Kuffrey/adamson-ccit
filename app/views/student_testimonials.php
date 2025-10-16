<?php
// student_testimonials.php — Dynamic Student Testimonials (CMS-driven)
require_once __DIR__ . '/../models/StudentTestimonialsPageSettings.php';
require_once __DIR__ . '/../models/StudentTestimonial.php';
$settings = StudentTestimonialsPageSettings::getSettings();
$testimonials = StudentTestimonial::getAll();
$years = StudentTestimonial::getYears();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Testimonials | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
</head>
<body>

<main>
</head>
<body>

<main>

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="<?= htmlspecialchars($settings['subhero_image_url'] ?? '/adamson-ccit/public/assets/images/hero-campus.jpg') ?>" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>

    <div class="container hero__inner">
      <div class="hero__copy">
      <span class="hero__eyebrow">Students</span>
      <h1 class="subhero__title">Testimonials</h1>
      <p class="hero__lead"><?= htmlspecialchars($settings['subhero_lead'] ?? 'Stories from CCIT students and alumni—internships, certifications, and early careers.') ?></p>
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
        <li>
          <a href="/adamson-ccit/public/index.php?page=student_certifications">Certifications</a>
        </li>
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=student_testimonials" aria-current="page">Testimonials</a>
        </li>
      </ul>
    </div>
  </nav>

<!-- ============ TESTIMONIALS GRID ============ -->
<section class="content" aria-labelledby="testimonials-heading">
  <div class="container">
    <h2 id="testimonials-heading" class="sr-only">Student Testimonials</h2>

    <div id="tGrid" class="prog__grid">
      <?php foreach ($testimonials as $t): ?>
<article class="prog__card tcard" data-prog="<?= htmlspecialchars($t['program']) ?>" data-year="<?= htmlspecialchars($t['grad_year']) ?>">
  <div class="tcard__head">
    <?php if (!empty($t['avatar_url'])): ?>
      <img class="tcard__avatar" src="<?= htmlspecialchars($t['avatar_url']) ?>" alt="Portrait of <?= htmlspecialchars($t['name']) ?>">
    <?php else: ?>
      <div class="tcard__avatar tcard__avatar--ph" aria-hidden="true">
        <?= htmlspecialchars($t['avatar_initials'] ?? strtoupper(substr($t['name'],0,2))) ?>
      </div>
    <?php endif; ?>

    <h3 class="tcard__name"><?= htmlspecialchars($t['name']) ?></h3>

    <div class="tcard__meta">
      <span class="tcard__tag">
        <?php
          $prog = strtoupper($t['program']);
          echo ($prog === 'GRAD') ? 'MIT (Graduate)' : $prog . ' ’' . htmlspecialchars($t['grad_year']);
        ?>
      </span>
      <span class="tcard__role"><?= htmlspecialchars($t['role']) ?></span>
    </div>
  </div>

  <div class="tcard__body">
    <blockquote class="tcard__quote">
      <?= htmlspecialchars($t['quote']) ?>
    </blockquote>
  </div>
</article>
      <?php endforeach; ?>
    </div>

    <!-- Pagination (kept simple) -->
    <nav class="pager" aria-label="Testimonials pagination">
      <button class="pg" disabled>« Prev</button>
      <span class="pg__status">Page 1 of 1</span>
      <button class="pg" disabled>Next »</button>
    </nav>
  </div>
</section>

</main>

</body>
</html>
