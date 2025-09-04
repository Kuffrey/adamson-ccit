
<?php
require_once __DIR__ . '/../models/ProgramsGraduateSettings.php';
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
$settings = ProgramsGraduateSettings::getSettings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Graduate Studies | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
</head>
<body>

<main class="page-programs page-graduate">

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="<?= e($settings['subhero_image_url'] ?? '/adamson-ccit/public/assets/images/programs/graduate.jpg') ?>" alt="Graduate studies at CCIT">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Programs</p>
      <h1 class="subhero__title">Graduate Studies</h1>
      <p class="subhero__lead"><?= e($settings['subhero_lead'] ?? 'Advanced training for IT leaders—rigor, ethics, and impact.') ?></p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (CONSISTENT) ============ -->
  <nav class="subnav" aria-label="Programs sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li>
          <a href="/adamson-ccit/public/index.php?page=programs_undergraduate">Undergraduate</a>
        </li>
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=programs_graduate_studies" aria-current="page">Graduate Studies</a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- ============ PROGRAMS GRID ============ -->
  <section class="content" aria-labelledby="grad-heading">
    <div class="container">
      <h2 id="grad-heading" class="sr-only">CCIT Graduate Programs</h2>
      <?= $settings['programs_grid'] ?? '' ?>
    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2><?= e($settings['cta_title'] ?? 'Chart your next step.') ?></h2>
        <p><?= e($settings['cta_description'] ?? 'Ask us about MIT schedules, requirements, and scholarships.') ?></p>
      </div>
      <a class="btn btn--solid" href="<?= e($settings['cta_action_url'] ?? '/adamson-ccit/public/index.php?page=contact') ?>"><?= e($settings['cta_action_label'] ?? 'Contact CCIT') ?></a>
    </div>
  </section>

</main>

</body>
</html>
