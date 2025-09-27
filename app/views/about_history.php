<?php
require_once __DIR__ . '/../models/AboutHistory.php';
$about = (new AboutHistory())->get();
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
      <h1 class="subhero__title">History</h1>
      <p class="subhero__lead"><?= htmlspecialchars($about['subhero_lead'] ?? 'Our journey, our growth, and the milestones that shaped CCIT.') ?></p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (About section tabs) ============ -->
  <nav class="subnav" aria-label="About sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=about_history" aria-current="page">History</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=about_vision_mission">Vision &amp; Mission</a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- ============ CONTENT ============ -->
  <section class="content">
    <div class="container content__grid">

      <article class="content__main">
        <header class="stack">
          <h2 class="h2"><?= htmlspecialchars($about['intro_title'] ?? 'Our Story') ?></h2>
          <p class="lead"><?= nl2br(htmlspecialchars($about['intro_lead'] ?? '')) ?></p>
        </header>

        <section class="card">
          <h3><?= htmlspecialchars($about['origins_title'] ?? 'Our Origins') ?></h3>
          <p><?= nl2br(htmlspecialchars($about['origins_body'] ?? '')) ?></p>
        </section>

        <section class="card">
          <h3>Timeline</h3>
          <div class="timeline-wrapper">
            <?php foreach (($about['milestones'] ?? []) as $milestone): ?>
            <div class="timeline-item">
              <div class="timeline-year"><?= isset($milestone['date']) ? date('Y', strtotime($milestone['date'])) : '' ?></div>
              <div class="timeline-content">
                <h4><?= htmlspecialchars($milestone['label'] ?? '') ?></h4>
                <p><?= htmlspecialchars($milestone['desc'] ?? '') ?></p>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </section>

        <section class="card">
          <h3><?= htmlspecialchars($about['leaders_title'] ?? 'Leadership') ?></h3>
          <div class="leadership-grid">
            <div class="leadership-group">
              <h4>College Leadership</h4>
              <?= $about['leaders_list'] ?? '' ?>
            </div>
            <div class="leadership-group">
              <h4><?= htmlspecialchars($about['academic_leads_title'] ?? 'Academic Leadership') ?></h4>
              <?= $about['academic_leads_list'] ?? '' ?>
            </div>
          </div>
        </section>
      </article>

      <aside class="content__aside">
        <div class="fact">
          <h3>Quick Facts</h3>
          <ul>
            <li><a href="/adamson-ccit/public/index.php?page=about_vision_mission">Vision &amp; Mission</a></li>
            <li><?= htmlspecialchars($about['fact_2'] ?? '') ?></li>
            <li><?= htmlspecialchars($about['fact_3'] ?? '') ?></li>
          </ul>
        </div>
        
        <div class="fact">
          <h3><?= htmlspecialchars($about['identity_title'] ?? 'Our Values') ?></h3>
          <?= $about['identity_items'] ?? '' ?>
        </div>

        <figure class="content__photo">
          <img src="<?= htmlspecialchars($about['photo_url'] ?? '/adamson-ccit/public/assets/images/hero-campus.jpg') ?>" alt="CCIT Campus">
          <figcaption><?= htmlspecialchars($about['photo_caption'] ?? 'CCIT continues to grow and evolve.') ?></figcaption>
        </figure>
      </aside>

    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2><?= htmlspecialchars($about['cta_title'] ?? 'Be Part of Our Story') ?></h2>
        <p><?= htmlspecialchars($about['cta_body'] ?? 'Join CCIT and help write the next chapter of our history.') ?></p>
      </div>
      <a class="btn btn--solid" href="<?= htmlspecialchars($about['cta_btn_url'] ?? '/adamson-ccit/public/index.php?page=programs_undergraduate') ?>"><?= htmlspecialchars($about['cta_btn_label'] ?? 'View Programs') ?></a>
    </div>
  </section>

</main>