<?php /* student_organizations.php — Student Organizations (uses global styles) */ ?>
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

    /* Type badges */
    .page-orgs .org__type{
      display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;
      font:800 11px/1 "Inter",system-ui;letter-spacing:.05em;text-transform:uppercase;
      border:1px solid var(--edgec);background:#f8fafc;color:#0b234c
    }
    .page-orgs .org__type--academic{background:#e8f5ee;border-color:#d6e9df}
    .page-orgs .org__type--co{background:#eef2ff;border-color:#d8dcef}

    /* Header row */
    .page-orgs .prog__head{display:flex;flex-wrap:wrap;align-items:center;gap:8px 10px}

    /* Logo */
    .page-orgs .org__logo{
      border:1px solid var(--edgec);border-radius:12px;background:#fff;height:100px;
      display:grid;place-items:center;padding:12px
    }
    .page-orgs .org__logo img{max-width:100%;max-height:80px;object-fit:contain;display:block}

    /* Audience */
    .page-orgs .org__meta{margin:0;color:#6b7280;font-size:13px}

    /* External hint */
    .page-orgs .ext::after{content:"↗";font-weight:900;margin-left:.35em;opacity:.7}
  </style>
</head>
<body>

<main class="page-programs page-orgs">

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University student community">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Student Life</p>
      <h1 class="subhero__title">Recognized Student Organizations</h1>
      <p class="subhero__lead">Official academic and co-academic organizations for CCIT students.</p>
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

        <!-- IT&IS (Academic) -->
        <article class="prog__card">
          <header class="prog__head">
            <h3 class="prog__title">AdU IT&IS Society</h3>
            <span class="org__type org__type--academic">Academic</span>
          </header>

          <div class="org__logo" aria-hidden="true">
            <img src="/adamson-ccit/public/assets/images/orgs/aduitis.jpg" alt="AdU IT&IS Society logo">
          </div>

          <p class="prog__summary">
            The Adamson University Information Technology &amp; Information Systems Society is a recognized academic, non-profit organization embodied by the BSIT &amp; BSIS students of Adamson University.
          </p>

          <div class="pillbox" aria-label="Follow AdU IT&IS Society">
            <h4 class="pillbox__title">Connect</h4>
            <ul class="pills" role="list">
              <li><a class="ext" href="https://www.facebook.com/AdU.IT.and.IS.Society/" target="_blank" rel="noopener">Facebook</a></li>
              <li><a class="ext" href="https://www.instagram.com/officialaduitissociety/" target="_blank" rel="noopener">Instagram</a></li>
              <li><a class="ext" href="https://x.com/aduitissociety" target="_blank" rel="noopener">X</a></li>
            </ul>
          </div>

          <p class="org__meta">Audience: BSIT, BSIS</p>

          <div class="prog__footer">
            <a class="btn btn--outline-blue ext" href="https://www.adamson.edu.ph/v1/?page=organization&org=22" target="_blank" rel="noopener">Learn More</a>
          </div>
        </article>

        <!-- ACOMSS (Academic) -->
        <article class="prog__card">
          <header class="prog__head">
            <h3 class="prog__title">ACOMSS</h3>
            <span class="org__type org__type--academic">Academic</span>
          </header>

          <div class="org__logo" aria-hidden="true">
            <img src="/adamson-ccit/public/assets/images/orgs/acomss.png" alt="ACOMSS logo">
          </div>

          <p class="prog__summary">
            The Adamson Computer Science Society is a recognized academic student organization composed of Computer Science students of Adamson University.
          </p>

          <div class="pillbox" aria-label="Follow ACOMSS">
            <h4 class="pillbox__title">Connect</h4>
            <ul class="pills" role="list">
              <li><a class="ext" href="https://www.facebook.com/ACOMSSofficial/" target="_blank" rel="noopener">Facebook</a></li>
              <li><a class="ext" href="https://www.instagram.com/acomss_official/" target="_blank" rel="noopener">Instagram</a></li>
            </ul>
          </div>

          <p class="org__meta">Audience: BSCS</p>

          <div class="prog__footer">
            <a class="btn btn--outline-blue ext" href="https://www.adamson.edu.ph/v1/?page=organization&org=5" target="_blank" rel="noopener">Learn More</a>
          </div>
        </article>

        <!-- AdU GAME (Co-Academic) -->
        <article class="prog__card">
          <header class="prog__head">
            <h3 class="prog__title">AdU GAME</h3>
            <span class="org__type org__type--co">Co-Academic</span>
          </header>

          <div class="org__logo" aria-hidden="true">
            <img src="/adamson-ccit/public/assets/images/orgs/adugame.jpg" alt="AdU GAME logo">
          </div>

          <p class="prog__summary">
            The Adamson University Guild of Animation Makers and Esports is a co-academic organization that empowers computer animation enthusiasts and esports players in AdU.
          </p>

          <div class="pillbox" aria-label="Follow AdU GAME">
            <h4 class="pillbox__title">Connect</h4>
            <ul class="pills" role="list">
              <li><a class="ext" href="https://www.facebook.com/AdUGAMEOfficial/" target="_blank" rel="noopener">Facebook</a></li>
            </ul>
          </div>

          <p class="org__meta">Audience: Animation &amp; Esports</p>

          <div class="prog__footer">
            <a class="btn btn--outline-blue ext" href="https://www.adamson.edu.ph/v1/?page=organization&org=83" target="_blank" rel="noopener">Learn More</a>
          </div>
        </article>

      </div>
    </div>
  </section>

</main>

</body>
</html>
