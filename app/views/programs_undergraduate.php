<?php /* programs_undergraduate.php — Undergraduate Programs (uses global styles) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Undergraduate Programs | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
</head>
<body>

<main class="page-programs">

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/programs/undergrad.jpg" alt="CCIT learning spaces and labs">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Programs</p>
      <h1 class="subhero__title">Undergraduate Programs</h1>
      <p class="subhero__lead">Solid foundations, hands-on practice, and focused CCIT pathways.</p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (CONSISTENT) ============ -->
  <nav class="subnav" aria-label="Programs sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=programs_undergraduate" aria-current="page">Undergraduate</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=programs_graduate_studies">Graduate Studies</a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- ============ PROGRAMS GRID ============ -->
  <section class="content" aria-labelledby="programs-heading">
    <div class="container">
      <h2 id="programs-heading" class="sr-only">CCIT Undergraduate Programs</h2>

      <div class="prog__grid">

        <!-- A — BS Computer Science -->
        <article class="prog__card" id="bscs">
          <header class="prog__head">
            <span class="badge" aria-hidden="true">BSCS</span>
            <h3 class="prog__title">B.S. in Computer Science</h3>
          </header>

          <p class="prog__summary">
            Computing theory and algorithmic design for robust software and research-driven solutions.
          </p>

          <div class="pillbox">
            <h4 class="pillbox__title">CCIT Specializations</h4>
            <ul class="pills" role="list">
              <li>Data Science</li>
              <li>Web Science</li>
              <li>Computer Vision</li>
            </ul>
          </div>

          <div class="prog__footer">
            <a class="btn btn--outline-blue ext" href="https://www.adamson.edu.ph/v1/?page=academicsv&col=13&dept=21" target="_blank" rel="noopener">Learn More</a>
            <nav class="mini-links" aria-label="Computer Science quick links">
              <a class="ext" href="https://www.adamson.edu.ph/v1/?page=curriculum" target="_blank" rel="noopener">Curriculum</a>
              <a href="/adamson-ccit/public/index.php?page=admission_freshman">Apply</a>
            </nav>
          </div>
        </article>

        <!-- B — Dual Degree: CS + Information Engineering -->
        <article class="prog__card" id="dual-degree">
          <header class="prog__head">
            <span class="badge" aria-hidden="true">Dual Degree</span>
            <h3 class="prog__title">
              B.S. in Computer Science and Information Engineering <span class="prog__muted">(Dual)</span>
            </h3>
          </header>

          <p class="prog__summary">
            Two credentials via Adamson University and Minghsin University of Science and Technology (Taiwan).
          </p>

          <div class="pillbox">
            <h4 class="pillbox__title">Highlights</h4>
            <ul class="pills" role="list">
              <li>Algorithms &amp; Software Systems</li>
              <li>Information Engineering</li>
              <li>Cross-cultural Experience</li>
            </ul>
          </div>

          <div class="prog__footer">
            <a class="btn btn--outline-blue ext" href="https://www.adamson.edu.ph/v1/?page=dual-degree-cs-ie-home" target="_blank" rel="noopener">Learn More</a>
            <nav class="mini-links" aria-label="Dual Degree quick links">
              <a class="ext" href="https://www.adamson.edu.ph/v1/?page=curriculum&cid=%20%20%20%2076&curryear=2023" target="_blank" rel="noopener">Curriculum</a>
              <a href="/adamson-ccit/public/index.php?page=admission_freshman">Apply</a>
            </nav>
          </div>
        </article>

        <!-- C — BS Information Systems -->
        <article class="prog__card" id="bsis">
          <header class="prog__head">
            <span class="badge" aria-hidden="true">BSIS</span>
            <h3 class="prog__title">B.S. in Information Systems</h3>
          </header>

          <p class="prog__summary">
            Design and deployment of information systems that streamline processes and decisions.
          </p>

          <div class="pillbox">
            <h4 class="pillbox__title">CCIT Specialization</h4>
            <ul class="pills" role="list">
              <li>Business Analytics</li>
            </ul>
          </div>

          <div class="prog__footer">
            <a class="btn btn--outline-blue ext" href="https://www.adamson.edu.ph/v1/?page=academicsv&col=13&dept=21" target="_blank" rel="noopener">Learn More</a>
            <nav class="mini-links" aria-label="Information Systems quick links">
              <a class="ext" href="https://www.adamson.edu.ph/v1/?page=curriculum" target="_blank" rel="noopener">Curriculum</a>
              <a href="/adamson-ccit/public/index.php?page=admission_freshman">Apply</a>
            </nav>
          </div>
        </article>

        <!-- D — BS Information Technology -->
        <article class="prog__card" id="bsit">
          <header class="prog__head">
            <span class="badge" aria-hidden="true">BSIT</span>
            <h3 class="prog__title">B.S. in Information Technology</h3>
          </header>

          <p class="prog__summary">
            Applications and IT infrastructure—development, operations, and administration.
          </p>

          <div class="pillbox">
            <h4 class="pillbox__title">CCIT Tracks</h4>
            <ul class="pills" role="list">
              <li>Consumer &amp; Enterprise App Dev</li>
              <li>Game Development</li>
              <li>Network Infra &amp; Data Security</li>
            </ul>
          </div>

          <div class="prog__footer">
            <a class="btn btn--outline-blue ext" href="https://www.adamson.edu.ph/v1/?page=academicsv&col=13&dept=21" target="_blank" rel="noopener">Learn More</a>
            <nav class="mini-links" aria-label="Information Technology quick links">
              <a class="ext" href="https://www.adamson.edu.ph/v1/?page=curriculum" target="_blank" rel="noopener">Curriculum</a>
              <a href="/adamson-ccit/public/index.php?page=admission_freshman">Apply</a>
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
        <h2>Not sure which program fits you?</h2>
        <p>Use the Career Pathway Generator to discover your best match.</p>
      </div>
      <a class="btn btn--solid" href="/adamson-ccit/public/index.php?page=career_pathway_generator">Launch the Tool</a>
    </div>
  </section>

</main>

</body>
</html>
