<?php
require_once __DIR__ . '/../models/AboutHistory.php';
if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}
$about = (new AboutHistory())->get();

/* Department History content */
$deptName = '';
$deptBody = '';
if (!empty($about['departments']) && is_array($about['departments'])) {
  $first = $about['departments'][0] ?? null;
  if ($first) {
    $deptName = trim((string)($first['name'] ?? ''));
    $deptBody = trim((string)($first['body'] ?? ''));
  }
}
if ($deptBody === '') {
  $deptName = 'Information Technology & Information Systems';
  $deptBody = "Former Computer Science Chairperson Mr. Rizaldy Rapsing originally proposed the offering of B.S. Information Technology (BSIT), B.S. Information Management (BSIM) and Associate in Computer Technology (ACT) programs. He envisioned that Adamson University can produce graduates ready to function in information technology positions with the competencies, skills, and attitudes necessary for success in the workplace. This vision came into reality when the Commission on Higher Education authorized Adamson University to offer and conduct BSIT, BSIM and ACT programs in summer of 2003. The first batch of BSIT was eight sections, one section for BSIM and another one section for ACT. The department is headed by Prof. Carmela L. Malong-Racelis as the appointed chairperson and the first batch of professors were Prof. Ms. Melany Sindayen and Prof. Marvi Aresta.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>History | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
  <style>
    /* Subtle, clean adjustments for typography harmony */
    .history-card {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: 22px 26px;
      box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .history-card h3 {
      color: #0b234c;
      font-weight: 700;
      margin-bottom: 10px;
      font-size: clamp(20px, 2vw, 24px);
    }

    .history-card p {
      color: #475569;
      line-height: 1.8;
      font-size: 0.97rem;
      text-align: justify;
    }

    /* --- Subtle department history style --- */
    .dept-history-card h3 {
      font-weight: 700;
      color: #0b234c;
      font-size: clamp(19px, 1.9vw, 22px);
      margin-bottom: 8px;
    }

    .dept-history-card h4 {
      color: #1e293b;
      font-weight: 600;
      margin-bottom: 6px;
      font-size: clamp(17px, 1.8vw, 20px);
    }

    .dept-history-card p {
      color: #555;
      line-height: 1.85;
      margin-bottom: 0;
      font-size: 0.95rem;
    }

    /* Remove bullets and left padding globally for department/fact lists */
    .fact ul,
    .department ul {
      list-style: none;
      padding-left: 0;
      margin: 0;
    }

    .fact li,
    .department li {
      list-style-type: none;
      margin-bottom: 6px;
      line-height: 1.6;
      padding-left: 0;
    }

    .fact h3 {
      color: #0b234c;
      font-weight: 700;
      margin-bottom: 8px;
    }
  </style>
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
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=about_history" aria-current="page">History</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=about_vision_mission">Vision &amp; Mission</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=deans_corner">Dean's Corner</a></li>
      </ul>
    </div>
  </nav>

  <!-- ============ CONTENT ============ -->
  <section class="content">
    <div class="container content__grid">
      <article class="content__main">

        <section class="card history-card">
          <h3>About CCIT</h3>
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

        <!-- ============ DEPARTMENT HISTORY (SUBTLE CARD) ============ -->
        <section class="card history-card dept-history-card">
          <h3>Department History</h3>
          <h4><?= e($deptName) ?></h4>
          <p><?= nl2br(e($deptBody)) ?></p>
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
          <h3><?= e($about['fact_title'] ?? 'Quick Links') ?></h3>
          <ul>
            <li><?= e($about['fact_1'] ?? 'Default Fact 1') ?></li>
            <li><?= e($about['fact_2'] ?? 'Default Fact 2') ?></li>
            <li><?= e($about['fact_3'] ?? 'Default Fact 3') ?></li>
          </ul>
        </div>

        <div class="fact">
          <h3><?= e($about['identity_title'] ?? 'Our Values') ?></h3>
          <div style="margin-bottom:8px">
            <?= $about['identity_items'] ?? '<div>Default Value 1</div><div>Default Value 2</div>' ?>
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
</body>
</html>
