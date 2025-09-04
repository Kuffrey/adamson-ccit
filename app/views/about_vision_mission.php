
<?php
require_once __DIR__ . '/../models/AboutVisionMission.php';
$about = (new AboutVisionMission())->get();
?>

<main>

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">About CCIT</p>
  <h1 class="subhero__title">Vision &amp; Mission</h1>
  <p class="subhero__lead"><?= htmlspecialchars($about['main_intro'] ?? 'Our purpose, our promise, and the departmental directions that guide CCIT.') ?></p>
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
      </ul>
    </div>
  </nav>

  <!-- ============ CCIT MISSION & VISION (Top) ============ -->
  <section class="mv">
    <div class="container">
      <header class="sec__head">
        <h2>College of Computing &amp; Information Technology (CCIT)</h2>
        <p class="sec__kicker">College-wide mission and vision</p>
      </header>

      <div class="mv__grid">
        <article class="card">
            <h3 class="card__title">Vision</h3>
            <p><?= nl2br(htmlspecialchars($about['main_vision'] ?? '')) ?></p>
        </article>

        <article class="card">
            <h3 class="card__title">Mission</h3>
            <p><?= nl2br(htmlspecialchars($about['main_mission'] ?? '')) ?></p>
        </article>
      </div>
    </div>
  </section>

  <!-- ============ DEPARTMENTS OVERVIEW ============ -->
  <section class="depts">
    <div class="container">
      <header class="sec__head">
        <h2>Departments</h2>
        <p class="sec__kicker">CCIT houses two departments that advance our mission.</p>
      </header>

      <div class="depts__grid">
        <!-- IT & IS Department -->
        <article class="dept card" id="it-is">
          <header class="dept__head">
            <h3 class="dept__title"><?= htmlspecialchars($about['dept1_title'] ?? 'Information Technology & Information Systems') ?></h3>
            <span class="dept__tag" aria-hidden="true">IT &amp; IS</span>
          </header>

          <div class="dept__body">
            <h4>Vision</h4>
            <p><?= nl2br(htmlspecialchars($about['dept1_vision'] ?? '')) ?></p>

            <h4>Mission</h4>
            <p><?= nl2br(htmlspecialchars($about['dept1_mission'] ?? '')) ?></p>

            <h4>Objectives</h4>
            <p><?= nl2br(htmlspecialchars($about['dept1_objectives'] ?? '')) ?></p>
          </div>

          <footer class="dept__foot">
            <a class="btn btn--outline-blue" href="/adamson-ccit/public/index.php?page=programs_undergraduate">See Programs</a>
          </footer>
        </article>

        <!-- Computer Science Department -->
        <article class="dept card" id="compsci">
          <header class="dept__head">
            <h3 class="dept__title"><?= htmlspecialchars($about['dept2_title'] ?? 'Computer Science') ?></h3>
            <span class="dept__tag" aria-hidden="true">CS</span>
          </header>

          <div class="dept__body">
            <h4>Vision</h4>
            <p><?= nl2br(htmlspecialchars($about['dept2_vision'] ?? '')) ?></p>

            <h4>Mission</h4>
            <p><?= nl2br(htmlspecialchars($about['dept2_mission'] ?? '')) ?></p>

            <h4>Objectives</h4>
            <p><?= nl2br(htmlspecialchars($about['dept2_objectives'] ?? '')) ?></p>
          </div>

          <footer class="dept__foot">
            <a class="btn btn--outline-blue" href="/adamson-ccit/public/index.php?page=programs_undergraduate">Explore BSCS</a>
          </footer>
        </article>
      </div>
    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2>Discover Where CCIT Can Take You</h2>
        <p>Browse our programs and see how our mission becomes your pathway.</p>
      </div>
      <a class="btn btn--solid" href="/adamson-ccit/public/index.php?page=programs_undergraduate">View Programs</a>
    </div>
  </section>

</main>
