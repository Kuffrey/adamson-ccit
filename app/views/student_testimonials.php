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

  <style>
    /* Consistent container padding */
    .content > .container { 
      padding: 16px 20px clamp(24px,5vw,48px); 
    }
    
    /* Consistent grid layout */
    .tgrid {
      padding: 16px 0 clamp(32px,6vw,56px);
    }
  </style>
</head>
<body>

<main>

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="<?= htmlspecialchars($settings['subhero_image_url'] ?? '/adamson-ccit/public/assets/images/hero-campus.jpg') ?>" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Students</p>
      <h1 class="subhero__title">Testimonials</h1>
      <p class="subhero__lead"><?= htmlspecialchars($settings['subhero_lead'] ?? 'Stories from CCIT students and alumni—internships, certifications, and early careers.') ?></p>
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
      <div id="tGrid" class="tgrid">
        <?php foreach ($testimonials as $t): ?>
        <article class="t" data-prog="<?= htmlspecialchars($t['program']) ?>" data-year="<?= htmlspecialchars($t['grad_year']) ?>">
          <header class="t__head">
            <?php if (!empty($t['avatar_url'])): ?>
              <img class="t__avatar" src="<?= htmlspecialchars($t['avatar_url']) ?>" alt="Portrait of <?= htmlspecialchars($t['name']) ?>">
            <?php else: ?>
              <div class="t__avatar t__avatar--ph" aria-hidden="true"><?= htmlspecialchars($t['avatar_initials'] ?? substr($t['name'],0,2)) ?></div>
            <?php endif; ?>
            <div class="t__meta">
              <h3 class="t__name"><?= htmlspecialchars($t['name']) ?></h3>
              <div class="t__row">
                <span class="ttag">
                  <?php
                  $prog = strtoupper($t['program']);
                  if ($prog === 'GRAD') {
                    echo 'MIT (Graduate)';
                  } else {
                    echo $prog . ' ’' . htmlspecialchars($t['grad_year']);
                  }
                  ?>
                </span>
                <span class="t__sep">•</span>
                <span class="t__role"><?= htmlspecialchars($t['role']) ?></span>
              </div>
            </div>
          </header>
          <blockquote class="t__quote">
            <?= htmlspecialchars($t['quote']) ?>
          </blockquote>
        </article>
        <?php endforeach; ?>
      </div>
      <!-- Pagination placeholder -->
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
