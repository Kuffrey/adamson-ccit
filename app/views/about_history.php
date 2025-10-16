
<?php
require_once __DIR__ . '/../models/AboutHistory.php';
if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}
$about = (new AboutHistory())->get();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>History | AdU-CCIT</title>
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
      <h1 class="subhero__title">History</h1>
      <p class="hero__lead"><?= e($about['subhero_lead'] ?? 'Our journey, our growth, and the milestones that shaped CCIT.') ?></p>
    </div>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV ============ -->
  <nav class="subnav" aria-label="About sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=about_history" aria-current="page">History</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=about_vision_mission">Vision &amp; Mission</a>
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
          <p class="lead">Our journey, our growth, and the milestones that shaped CCIT.</p>
        </header>

        <section class="card history-card">
          <h3>Our Story</h3>
          <p><?= nl2br(e($about['intro_lead'] ?? '')) ?></p>
        </section>

        <section class="card origins-card">
          <h3>Our Origins</h3>
          <p><?= nl2br(e($about['origins_body'] ?? '')) ?></p>
        </section>

        <section class="card timeline-card">
          <h3>Timeline</h3>
          <?php if (!empty($about['milestones'])): ?>
          <div class="timeline-wrapper">
            <?php foreach (($about['milestones'] ?? []) as $milestone): ?>
            <div class="timeline-item">
              <div class="timeline-year">
                <?= isset($milestone['date']) ? date('Y', strtotime($milestone['date'])) : '' ?>
              </div>
              <div class="timeline-content">
                <h4><?= e($milestone['label'] ?? '') ?></h4>
                <p><?= e($milestone['desc'] ?? '') ?></p>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </section>

        <section class="card leadership-card">
          <h3>Leadership</h3>
          <div class="department">
            <h4>College Leadership</h4>
            <?= $about['leaders_list'] ?? '' ?>
          </div>
          <div class="department">
            <h4>Academic Leadership</h4>
            <?= $about['academic_leads_list'] ?? '' ?>
          </div>
        </section>
      </article>

      <aside class="content__aside">
        <div class="fact">
          <h3>Quick Links</h3>
          <ul>
            <li><a href="/adamson-ccit/public/index.php?page=about_vision_mission">Vision &amp; Mission</a></li>
            <li><a href="/adamson-ccit/public/index.php?page=programs_undergraduate">Academic Programs</a></li>
            <li><a href="/adamson-ccit/public/index.php?page=admission_freshman">Admissions</a></li>
          </ul>
        </div>
        <div class="fact">
          <h3>By the Numbers</h3>
          <ul>
            <li><?= e($about['fact_2'] ?? '2 specialized departments') ?></li>
            <li><?= e($about['fact_3'] ?? '3 undergraduate programs') ?></li>
            <li>Expert faculty and staff</li>
          </ul>
        </div>
        <div class="fact">
          <h3><?= e($about['identity_title'] ?? 'Our Values') ?></h3>
          <div style="margin-bottom:8px">
            <?php
            $identity = $about['identity_items'] ?? '';
            $identity = preg_replace('/<\/?ul>/i', '', $identity);
            $identity = preg_replace('/<li>(.*?)<\/li>/i', '<div>$1</div>', $identity);
            echo $identity;
            ?>
          </div>
        </div>
        <figure class="content__photo">
          <img src="<?= e($about['photo_url'] ?? '/adamson-ccit/public/assets/images/hero-campus.jpg') ?>" alt="CCIT Campus">
          <figcaption><?= e($about['photo_caption'] ?? 'CCIT continues to grow and evolve.') ?></figcaption>
        </figure>
      </aside>
    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2>Be Part of Our Story</h2>
        <p>Join CCIT and help write the next chapter of our history.</p>
      </div>
      <a class="btn btn--solid" href="/adamson-ccit/public/index.php?page=programs_undergraduate">View Programs</a>
    </div>
  </section>
</main>