
<?php
require_once __DIR__ . '/../models/AboutVisionMission.php';
$about = (new AboutVisionMission())->get();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vission & Mission | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
</head>
<body>
<main>

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior">
    </div>
    <div class="hero__scrim" aria-hidden="true"></div>

    <div class="container hero__inner">
      <div class="hero__copy">
      <span class="hero__eyebrow">About CCIT</span>
      <h1 class="subhero__title">Vision &amp; Mission</h1>
      <p class="hero__lead"><?= htmlspecialchars($about['main_intro'] ?? 'Our purpose, our promise, and the departmental directions that guide CCIT.') ?></p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (About section tabs) ============ -->
  <nav class="subnav" aria-label="About sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li>
          <a href="/adamson-ccit/public/index.php?page=about_history">History</a>
        </li>
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=about_vision_mission" aria-current="page">Vision &amp; Mission</a>
        </li>
         <li>
          <a href="/adamson-ccit/public/index.php?page=deans_corner">Dean's Corner</a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- ============ CONTENT ============ -->
  <section class="content">
    <div class="container content__grid">

      <article class="content__main">
        <header class="stack">
          <h2 class="h2">College of Computing &amp; Information Technology</h2>
          <p class="lead">Our institutional commitment to excellence in computing education</p>
        </header>

        <section class="card vision-card">
          <h3>Our Vision</h3>
          <p class="vision-text"><?= nl2br(htmlspecialchars($about['main_vision'] ?? '')) ?></p>
        </section>

        <section class="card mission-card">
          <h3>Our Mission</h3>
          <p class="mission-text"><?= nl2br(htmlspecialchars($about['main_mission'] ?? '')) ?></p>
        </section>

        <section class="card">
          <h3>Departments</h3>
          
          <div class="department">
            <h4><?= htmlspecialchars($about['dept1_title'] ?? 'Information Technology & Information Systems') ?></h4>
            
            <div class="dept-section">
              <h5>Vision</h5>
              <p><?= nl2br(htmlspecialchars($about['dept1_vision'] ?? '')) ?></p>
            </div>
            
            <div class="dept-section">
              <h5>Mission</h5>
              <p><?= nl2br(htmlspecialchars($about['dept1_mission'] ?? '')) ?></p>
            </div>
            
            <div class="dept-section">
              <h5>Objectives</h5>
              <p><?= nl2br(htmlspecialchars($about['dept1_objectives'] ?? '')) ?></p>
            </div>
          </div>
          
          <div class="department">
            <h4><?= htmlspecialchars($about['dept2_title'] ?? 'Computer Science') ?></h4>
            
            <div class="dept-section">
              <h5>Vision</h5>
              <p><?= nl2br(htmlspecialchars($about['dept2_vision'] ?? '')) ?></p>
            </div>
            
            <div class="dept-section">
              <h5>Mission</h5>
              <p><?= nl2br(htmlspecialchars($about['dept2_mission'] ?? '')) ?></p>
            </div>
            
            <div class="dept-section">
              <h5>Objectives</h5>
              <p><?= nl2br(htmlspecialchars($about['dept2_objectives'] ?? '')) ?></p>
            </div>
          </div>
        </section>
      </article>

      <aside class="content__aside">
        <div class="fact">
          <h3>Quick Links</h3>
          <ul>
            <li><a href="/adamson-ccit/public/index.php?page=about_history">CCIT History</a></li>
            <li><a href="/adamson-ccit/public/index.php?page=programs_undergraduate">Academic Programs</a></li>
            <li><a href="/adamson-ccit/public/index.php?page=admission_freshman">Admissions</a></li>
          </ul>
        </div>
        
        <div class="fact">
          <h3>By the Numbers</h3>
          <ul>
            <li>2 specialized departments</li>
            <li>3 undergraduate programs</li>
            <li>Expert faculty and staff</li>
          </ul>
        </div>

        <figure class="content__photo">
          <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="CCIT Students">
          <figcaption>Students collaborating in our modern computing facilities.</figcaption>
        </figure>
      </aside>

    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2>Ready to Join Our Mission?</h2>
        <p>Explore our programs and become part of CCIT's vision for the future of computing education.</p>
      </div>
      <a class="btn btn--solid" href="/adamson-ccit/public/index.php?page=programs_undergraduate">Explore Programs</a>
    </div>
  </section>

</main>
