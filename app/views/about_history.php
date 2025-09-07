
<?php
require_once __DIR__ . '/../models/AboutHistory.php';
$about = (new AboutHistory())->get();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CCIT History | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
</head>
<body>

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
  <p class="subhero__lead"><?= htmlspecialchars($about['subhero_lead'] ?? '') ?></p>
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

  <!-- ============ INTRO / OVERVIEW ============ -->
  <section class="content">
    <div class="container content__grid">
      <article class="content__main">
  <h2 class="h2"><?= htmlspecialchars($about['intro_title'] ?? '') ?></h2>
  <p class="lead"><?= nl2br(htmlspecialchars($about['intro_lead'] ?? '')) ?></p>
      </article>

      <aside class="content__aside">
        <div class="fact">
          <h3><?= htmlspecialchars($about['fact_title'] ?? 'At a Glance') ?></h3>
          <ul>
              <a href="/adamson-ccit/public/index.php?page=about_vision_mission">Vision &amp; Mission</a>
            <li><?= htmlspecialchars($about['fact_2'] ?? '') ?></li>
            <li><?= htmlspecialchars($about['fact_3'] ?? '') ?></li>
          </ul>
        </div>
      </aside>
    </div>
  </section>

  <!-- ============ ORIGINS (NARRATIVE) ============ -->
  <section class="origin">
    <div class="container">
      <div class="origin__box">
  <h2 class="h2"><?= htmlspecialchars($about['origins_title'] ?? '') ?></h2>
  <p><?= nl2br(htmlspecialchars($about['origins_body'] ?? '')) ?></p>
      </div>
    </div>
  </section>

  <!-- ============ MILESTONES (PROFESSIONAL TIMELINE) ============ -->
  <section class="milestones">
    <div class="container">
      <header class="sec__head">
        <h2>Key Milestones</h2>
        <p class="sec__kicker">Defining moments in CCIT’s early history.</p>
      </header>

      <ol class="timeline" role="list">
        <?php foreach (($about['milestones'] ?? []) as $m): ?>
        <li class="tl__item">
          <div class="tl__dot" aria-hidden="true"></div>
          <div class="tl__card">
            <time datetime="<?= htmlspecialchars($m['date'] ?? '') ?>" class="tl__date"><?= isset($m['date']) ? date('F j, Y', strtotime($m['date'])) : '' ?><?= !empty($m['meta']) ? ' · ' . htmlspecialchars($m['meta']) : '' ?></time>
            <h3 class="tl__title"><?= htmlspecialchars($m['label'] ?? '') ?></h3>
            <?php if (!empty($m['meta'])): ?><p class="tl__meta"><?= htmlspecialchars($m['meta']) ?></p><?php endif; ?>
            <p class="tl__text"><?= htmlspecialchars($m['desc'] ?? '') ?></p>
          </div>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <!-- ============ LEADERSHIP (NARRATIVE LIST) ============ -->
  <section class="leaders">
    <div class="container">
      <div class="leaders__grid">
        <div class="leaders__col">
          <h2 class="h2"><?= htmlspecialchars($about['leaders_title'] ?? '') ?></h2>
          <ul class="leaders__list">
            <?= $about['leaders_list'] ?? '' ?>
          </ul>
        </div>
        <div class="leaders__col">
          <h3 class="h3"><?= htmlspecialchars($about['academic_leads_title'] ?? '') ?></h3>
          <ul class="leaders__list">
            <?= $about['academic_leads_list'] ?? '' ?>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ IDENTITY & VALUES ============ -->
  <section class="identity">
    <div class="container">
      <div class="id__wrap">
        <h2 class="h2"><?= htmlspecialchars($about['identity_title'] ?? '') ?></h2>
        <ul class="id__grid" role="list">
          <?= $about['identity_items'] ?? '' ?>
        </ul>
      </div>
    </div>
  </section>

  <!-- ============ CLOSING CTA ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
  <h2><?= htmlspecialchars($about['cta_title'] ?? '') ?></h2>
  <p><?= nl2br(htmlspecialchars($about['cta_body'] ?? '')) ?></p>
      </div>
  <a class="btn btn--solid" href="<?= htmlspecialchars($about['cta_btn_url'] ?? '#') ?>"><?= htmlspecialchars($about['cta_btn_label'] ?? 'See our Programs') ?></a>
    </div>
  </section>

</main>

</body>
</html>


