<?php /* graduate_studies.php — Graduate Studies (uses global styles) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Graduate Studies | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
</head>
<body>

<main class="page-programs page-graduate">

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <!-- swap this image if you have a dedicated graduate banner -->
      <img src="/adamson-ccit/public/assets/images/programs/graduate.jpg" alt="Graduate studies at CCIT">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Programs</p>
      <h1 class="subhero__title">Graduate Studies</h1>
      <p class="subhero__lead">Advanced training for IT leaders—rigor, ethics, and impact.</p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (CONSISTENT) ============ -->
  <nav class="subnav" aria-label="Programs sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li>
          <a href="/adamson-ccit/public/index.php?page=programs_undergraduate">Undergraduate</a>
        </li>
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=programs_graduate_studies" aria-current="page">Graduate Studies</a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- ============ PROGRAMS GRID ============ -->
  <section class="content" aria-labelledby="grad-heading">
    <div class="container">
      <h2 id="grad-heading" class="sr-only">CCIT Graduate Programs</h2>

      <div class="prog__grid">

        <!-- MIT — Master in Information Technology -->
        <article class="prog__card" id="mit">
          <header class="prog__head">
            <span class="badge" aria-hidden="true">MIT</span>
            <h3 class="prog__title">Master in Information Technology</h3>
          </header>

          <p class="prog__summary">
            The Master in Information Technology (MIT) at Adamson University provides advanced theoretical
            and practical IT training to prepare students for leadership roles. It emphasizes ethical practice
            and social responsibility—developing professionals who drive innovation and support sustainable development.
          </p>

          <div class="pillbox">
            <h4 class="pillbox__title">Program Emphases</h4>
            <ul class="pills" role="list">
              <li>Advanced Computing Practice</li>
              <li>IT Leadership &amp; Governance</li>
              <li>Ethics &amp; Social Responsibility</li>
              <li>Innovation &amp; Sustainable Impact</li>
            </ul>
          </div>

          <div class="prog__footer">
            <!-- “Learn More” not available on AdU site: show disabled CTA with hint -->
            <span class="btn btn--disabled" aria-disabled="true" title="Details coming soon">Learn More</span>

            <nav class="mini-links" aria-label="MIT quick links">
              <a class="ext" href="https://www.adamson.edu.ph/v1/?page=pos-course&course=j" target="_blank" rel="noopener">Curriculum</a>
              <a href="/adamson-ccit/public/index.php?page=contact">Inquire</a>
            </nav>
          </div>
        </article>

      </div>
    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2>Chart your next step.</h2>
        <p>Ask us about MIT schedules, requirements, and scholarships.</p>
      </div>
      <a class="btn btn--solid" href="/adamson-ccit/public/index.php?page=contact">Contact CCIT</a>
    </div>
  </section>

</main>

</body>
</html>
