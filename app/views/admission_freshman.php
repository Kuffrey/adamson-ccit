
<?php
require_once __DIR__ . '/../models/AdmissionFreshmanSettings.php';
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
$settings = AdmissionFreshmanSettings::getSettings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Freshman Admission | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
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
      <p class="eyebrow">Admissions</p>
      <h1 class="subhero__title">Freshman Admission</h1>
      <p class="subhero__lead"><?= e($settings['subhero_lead'] ?? '') ?></p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (Admissions) ============ -->
  <nav class="subnav" aria-label="Admissions sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=admission_freshman" aria-current="page">Freshman</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=admission_transferee">Transferee</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=admission_graduate_school">Graduate School &amp; Juris Doctor</a></li>
      </ul>
    </div>
  </nav>

  <!-- ============ CONTENT ============ -->
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

        <section class="card">
          <h3>Requirements for Freshmen Enrollment</h3>

          <h4 class="h4">Senior High School Graduates</h4>
          <?= $settings['requirements_shs'] ?? '' ?>

          <h4 class="h4" style="margin-top:10px">ALS / Non-Formal Education A&amp;E / PEPT Passers</h4>
          <?= $settings['requirements_als'] ?? '' ?>

          <div class="note" style="margin-top:10px">
            <?= $settings['requirements_abroad'] ?? '' ?>
          </div>
        </section>

        <section class="card">
          <h3>Enrollment Procedure</h3>
          <?= $settings['enrollment_procedure'] ?? '' ?>
        </section>
      </article>

      <!-- ============ SIDEBAR ============ -->
      <aside class="content__aside">
        <div class="fact">
          <?= $settings['sidebar_office'] ?? '' ?>
        </div>
        <div class="fact">
          <h3>Quick Links</h3>
          <?= $settings['sidebar_links'] ?? '' ?>
        </div>
        <figure class="content__photo">
          <img src="<?= e($settings['sidebar_image_url'] ?? '/adamson-ccit/public/assets/images/admissions/freshman.jpg') ?>" alt="First-year students at CCIT">
          <figcaption><?= e($settings['sidebar_image_caption'] ?? 'Welcome, future Falcons—start your CCIT journey at Adamson.') ?></figcaption>
        </figure>
      </aside>

    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2><?= e($settings['cta_title'] ?? 'Explore CCIT Programs') ?></h2>
        <p><?= e($settings['cta_description'] ?? 'Match your interests to a pathway in Computing and IT.') ?></p>
      </div>
      <a class="btn btn--solid" href="<?= e($settings['cta_action_url'] ?? '/adamson-ccit/public/index.php?page=programs_undergraduate') ?>"><?= e($settings['cta_action_label'] ?? 'See Programs') ?></a>
    </div>
  </section>

</main>

</body>
</html>
