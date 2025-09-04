
<?php
require_once __DIR__ . '/../models/ProgramsUndergraduateSettings.php';
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
$settings = ProgramsUndergraduateSettings::getSettings();
?>
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
      <img src="<?= e($settings['subhero_image_url'] ?? '/adamson-ccit/public/assets/images/programs/undergrad.jpg') ?>" alt="CCIT learning spaces and labs">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Programs</p>
      <h1 class="subhero__title">Undergraduate Programs</h1>
      <p class="subhero__lead"><?= e($settings['subhero_lead'] ?? 'Solid foundations, hands-on practice, and focused CCIT pathways.') ?></p>
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
      <?= $settings['programs_grid'] ?? '' ?>
    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2><?= e($settings['cta_title'] ?? 'Not sure which program fits you?') ?></h2>
        <p><?= e($settings['cta_description'] ?? 'Use the Career Pathway Generator to discover your best match.') ?></p>
      </div>
      <a class="btn btn--solid" href="<?= e($settings['cta_action_url'] ?? '/adamson-ccit/public/index.php?page=career_pathway_generator') ?>"><?= e($settings['cta_action_label'] ?? 'Launch the Tool') ?></a>
    </div>
  </section>

</main>

</body>
</html>
