
<?php
require_once __DIR__ . '/../models/AdmissionGraduateSettings.php';
if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}
$settings = AdmissionGraduateSettings::getSettings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Graduate School & JD Admission | AdU-CCIT</title>
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

    <div class="container hero__inner">
      <div class="hero__copy">
      <span class="hero__eyebrow">Admissions</span>
      <h1 class="subhero__title">Graduate School &amp; Juris Doctor</h1>
      <p class="hero__lead"><?= e($settings['subhero_lead'] ?? '') ?></p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV  ============ -->
  <nav class="subnav" aria-label="Admissions sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li><a href="/adamson-ccit/public/index.php?page=admission_freshman">Freshman</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=admission_transferee">Transferee</a></li>
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=admission_graduate_school" aria-current="page">Graduate School &amp; Juris Doctor</a></li>
      </ul>
    </div>
  </nav>

  <!-- ============ CONTENT (Transferee-style layout) ============ -->
  <section class="content">
    <div class="container content__grid">

      <article class="content__main">
        <!-- How to Apply -->
        <header class="stack">
          <?= $settings['how_to_apply'] ?? '' ?>
          <div class="btn-row">
            <a class="btn btn--solid-blue" href="https://www.adamson.edu.ph/cfe" target="_blank" rel="noopener">Apply at Adamson.edu.ph</a>
          </div>
        </header>

        <section class="card">
          <h3>Initial Uploads (Online Evaluation)</h3>
          <?= $settings['initial_uploads'] ?? '' ?>
        </section>

        <!-- Qualifications -->
        <section class="card">
          <h3>Qualifications</h3>

          <h4 class="h4">Master’s Degree</h4>
          <?= $settings['qualifications_masters'] ?? '' ?>

          <h4 class="h4" style="margin-top:10px">Doctoral Program</h4>
          <?= $settings['qualifications_doctoral'] ?? '' ?>

          <h4 class="h4" style="margin-top:10px">Juris Doctor</h4>
          <?= $settings['qualifications_jd'] ?? '' ?>
        </section>

        <!-- Requirements for Enrollment -->
        <section class="card">
          <h3>Requirements for Enrollment</h3>
          <?= $settings['requirements_enrollment'] ?? '' ?>
        </section>

        <!-- Enrollment Procedure -->
        <section class="card">
          <h3>Enrollment Procedure</h3>
          <?= $settings['enrollment_procedure'] ?? '' ?>
        </section>
      </article>

      <!-- ============ SIDEBAR (same visual rhythm as Transferee) ============ -->
      <aside class="content__aside">
        <div class="fact">
          <?= $settings['sidebar_office'] ?? '' ?>
        </div>
        <div class="fact">
          <h3>Quick Links</h3>
          <?= $settings['sidebar_links'] ?? '' ?>
        </div>
        <figure class="content__photo">
          <img src="<?= e($settings['sidebar_image_url'] ?? '/adamson-ccit/public/assets/images/programs/grad.jpg') ?>" alt="Graduate &amp; Juris Doctor students at Adamson University" />
          <figcaption><?= e($settings['sidebar_image_caption'] ?? 'Welcome to advanced studies—Graduate School & Juris Doctor at Adamson.') ?></figcaption>
        </figure>
      </aside>

    </div>
  </section>

  <!-- ============ CTA (Adamson blue) ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2><?= e($settings['cta_title'] ?? 'Ready to take the next step?') ?></h2>
        <p><?= e($settings['cta_description'] ?? 'Apply online and begin your graduate or JD journey with Adamson University.') ?></p>
      </div>
      <a class="btn btn--solid-blue" href="<?= e($settings['cta_action_url'] ?? 'https://www.adamson.edu.ph/cfe') ?>" target="_blank" rel="noopener"><?= e($settings['cta_action_label'] ?? 'Apply Now') ?></a>
    </div>
  </section>

</main>

</body>
</html>
