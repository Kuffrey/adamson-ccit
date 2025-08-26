<?php /* student_scholarships.php — Scholarships (uses global styles) */ ?>
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
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Student Support</p>
      <h1 class="subhero__title">Scholarships</h1>
      <p class="subhero__lead">Financial aid options for incoming and current Adamson students.</p>
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

        <!-- Freshmen Scholarships -->
        <article class="prog__card">
          <header class="prog__head">
            <h3 class="prog__title">Scholarships for Freshmen Students</h3>
            <span class="sch__type sch__type--fresh">Freshmen</span>
          </header>

          <p class="prog__summary">
            Rank&nbsp;1: <strong>100% tuition</strong> (1st &amp; 2nd sem). Rank&nbsp;2: <strong>50% tuition</strong> (1st &amp; 2nd sem). Maintain no grade below 2.5 and pass NSTP in 1st sem.
          </p>

          <div class="pillbox">
            <h4 class="pillbox__title">Key Conditions</h4>
            <ul class="bullets">
              <li>Graduate of a government-recognized school.</li>
              <li>School with <strong>≥100 graduates</strong> (else Registrar evaluation; good for one sem only).</li>
              <li><strong>Certificate of Honor</strong> with dry seal &amp; total number of graduates.</li>
            </ul>
          </div>

          <div class="prog__footer">
            <a class="btn btn--outline-blue ext" href="https://www.adamson.edu.ph/v1/?page=freshmen-scholarship" target="_blank" rel="noopener">Learn More</a>
          </div>
        </article>

        <!-- Academic Scholarship Program (ASP) -->
        <article class="prog__card">
          <header class="prog__head">
            <h3 class="prog__title">Academic Scholarship Program (ASP)</h3>
            <span class="sch__type sch__type--univ">University</span>
          </header>

          <p class="prog__summary">
            Tuition coverage for <strong>regular load only</strong>. Highly selective; outstanding GWA and clean academic record required.
          </p>

          <div class="pillbox">
            <h4 class="pillbox__title">Not Eligible If Prior Sem Has</h4>
            <ul class="bullets">
              <li>Failing (5.0) or Dropped (130)</li>
              <li>Not Attending (120) or No Grade OBE (140)</li>
              <li>Special Consideration (150) or Unofficial Withdrawal (0.0)</li>
            </ul>
          </div>

          <div class="prog__footer">
            <a class="btn btn--outline-blue ext" href="https://www.adamson.edu.ph/v1/?page=academic-scholarship-program" target="_blank" rel="noopener">Learn More</a>
          </div>
        </article>

        <!-- Corporate / Foundation / Individual Sponsorships -->
        <article class="prog__card">
          <header class="prog__head">
            <h3 class="prog__title">Corporate / Foundation / Individual Sponsorships</h3>
            <span class="sch__type sch__type--ext">External</span>
          </header>

          <p class="prog__summary">
            Full (100%) or partial (50%) support in tuition/misc. Maintain sponsor-required GWA; <strong>no dropped/failed/incomplete</strong>. Join at least <strong>2 OSAS/college activities</strong> per semester.
          </p>

          <div class="pillbox">
            <h4 class="pillbox__title">Initial Requirements</h4>
            <ul class="bullets">
              <li>Letter of intent to the VP for Student Affairs.</li>
              <li>Required course; good moral character.</li>
              <li>Form 138 (GWA ≥88%) or sem GWA ≤<strong>1.75</strong>, no drops/fails/incompletes.</li>
            </ul>
          </div>

          <div class="pillbox">
            <h4 class="pillbox__title">Examples</h4>
            <ul class="bullets">
              <li>CHED UniFAST, GBF, Megaworld, Petron Foundation</li>
              <li>San Miguel Foundation, LCCK, Rotary Club of Manila Bay</li>
              <li>NROTC tuition discounts (25–100% by rank)</li>
            </ul>
          </div>

          <div class="prog__footer">
            <a class="btn btn--outline-blue ext" href="https://www.adamson.edu.ph/v1/?page=corporate-foundation-individual-sponsorships" target="_blank" rel="noopener">Learn More</a>
          </div>
        </article>

      </div>

      <p class="sch__note">
        For new calls, slots, and deadlines, follow OSAS:
        <a class="ext" href="https://www.facebook.com/AdamsonU.osas/" target="_blank" rel="noopener">Facebook</a>.
      </p>
    </div>
  </section>

</main>

</body>
</html>
