
<?php
require_once __DIR__ . '/../models/AdmissionTransfereeSettings.php';
if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}
$settings = AdmissionTransfereeSettings::getSettings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Transferee Admission | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
</head>
<body>

<main>

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior" />
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Admissions</p>
      <h1 class="subhero__title">Transferee</h1>
      <p class="subhero__lead"><?= e($settings['subhero_lead'] ?? '') ?></p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (Admissions) ============ -->
  <nav class="subnav" aria-label="Admissions sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li><a href="/adamson-ccit/public/index.php?page=admission_freshman">Freshman</a></li>
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=admission_transferee" aria-current="page">Transferee</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=admission_graduate_school">Graduate School &amp; Juris Doctor</a></li>
      </ul>
    </div>
  </nav>

  <!-- ============ CONTENT ============ -->
  <section class="content">
    <div class="container content__grid">
      <article class="content__main">
        <header class="stack">
          <?= $settings['how_to_apply'] ?? '' ?>
          <div class="btn-row">
            <a class="btn btn--solid-blue" href="https://www.adamson.edu.ph/cfe" target="_blank" rel="noopener">Apply at Adamson.edu.ph</a>
            <a class="btn btn--outline-blue" href="https://learn.adamson.edu.ph" target="_blank" rel="noopener">Log in to eLearning</a>
          </div>
        </header>

        <section class="card">
          <h3>Requirements for Application (Evaluation &amp; Interview)</h3>
          <?= $settings['requirements'] ?? '' ?>
        </section>

        <section class="card">
          <h3>Enrollment Procedure</h3>
          <?= $settings['enrollment_procedure'] ?? '' ?>
          <?= $settings['enrollment_note'] ?? '' ?>
        </section>
      </article>

      <!-- Sidebar -->
      <aside class="content__aside">
        <div class="fact">
          <?= $settings['sidebar_office'] ?? '' ?>
        </div>
        <div class="fact">
          <h3>Quick Links</h3>
          <?= $settings['sidebar_links'] ?? '' ?>
        </div>
        <figure class="content__photo">
          <img src="<?= e($settings['sidebar_image_url'] ?? '/adamson-ccit/public/assets/images/admissions/transferee.jpg') ?>" alt="Transferee">
          <figcaption><?= e($settings['sidebar_image_caption'] ?? 'Welcome, future Falcons—transfer your journey to CCIT.') ?></figcaption>
        </figure>
      </aside>
    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2><?= e($settings['cta_title'] ?? 'Ready to transfer to CCIT?') ?></h2>
        <p><?= e($settings['cta_description'] ?? 'Apply online and we’ll guide you through evaluation, interview, and enlistment.') ?></p>
      </div>
      <a class="btn btn--solid-blue" href="<?= e($settings['cta_action_url'] ?? 'https://www.adamson.edu.ph/cfe') ?>" target="_blank" rel="noopener"><?= e($settings['cta_action_label'] ?? 'Apply Now') ?></a>
    </div>
  </section>

</main>

</body>
</html>
