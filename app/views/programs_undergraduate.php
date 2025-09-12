<?php
// app/views/programs_undergraduate.php

// Try not to redeclare e() if header already defined it
if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}

// ✅ Correct, robust path to the model (views -> app -> models)
require_once dirname(__DIR__) . '/models/ProgramsUndergraduateSettings.php';

// Settings (subhero, CTA, legacy grid)
$settings = ProgramsUndergraduateSettings::getSettings();

// Active dynamic cards from ug_cards
$cards = [];
try {
  $cards = ProgramsUndergraduateSettings::getCards(); // returns only active cards, ordered
} catch (Throwable $e) {
  error_log('[programs_undergraduate] getCards failed: ' . $e->getMessage());
}

// Helpers
$extAttr = function ($flag) { return !empty($flag) ? ' target="_blank" rel="noopener"' : ''; };

?><!DOCTYPE html>
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

  <!-- ============ PROGRAMS GRID (dynamic first, legacy fallback) ============ -->
  <section class="content" aria-labelledby="programs-heading">
    <div class="container">
      <h2 id="programs-heading" class="sr-only">CCIT Undergraduate Programs</h2>

      <?php if (!empty($cards)): ?>
        <div class="prog__grid">
          <?php foreach ($cards as $c): ?>
            <article class="prog__card" <?= !empty($c['slug']) ? 'id="'.e($c['slug']).'"' : '' ?>>
              <!-- Header -->
              <div class="prog__head">
                <?php if (!empty($c['badge'])): ?>
                  <span class="badge"><?= e($c['badge']) ?></span>
                <?php endif; ?>
                <h3 class="prog__title">
                  <?= e($c['title']) ?>
                  <?php if (!empty($c['muted'])): ?>
                    <span class="prog__muted"><?= e($c['muted']) ?></span>
                  <?php endif; ?>
                </h3>
              </div>

              <!-- Summary -->
              <?php if (!empty($c['summary'])): ?>
                <p class="prog__summary"><?= e($c['summary']) ?></p>
              <?php endif; ?>

              <!-- Pillbox -->
              <?php if (!empty($c['pill_t']) || !empty($c['pills'])): ?>
                <div class="pillbox">
                  <?php if (!empty($c['pill_t'])): ?>
                    <h4 class="pillbox__title"><?= e($c['pill_t']) ?></h4>
                  <?php endif; ?>
                  <?php if (!empty($c['pills'])): ?>
                    <ul class="pills" role="list">
                      <?php foreach ($c['pills'] as $pl): ?>
                        <li><?= e($pl) ?></li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <!-- Footer actions -->
              <div class="prog__footer">
                <div class="mini-links">
                  <?php if (!empty($c['lm_url'])): ?>
                    <a class="ext" href="<?= e($c['lm_url']) ?>"<?= $extAttr($c['lm_ext']) ?>>Learn more</a>
                  <?php endif; ?>
                  <?php if (!empty($c['cur_url'])): ?>
                    <a class="ext" href="<?= e($c['cur_url']) ?>"<?= $extAttr($c['cur_ext']) ?>>Curriculum</a>
                  <?php endif; ?>
                </div>
                <?php if (!empty($c['apply'])): ?>
                  <a class="btn btn--solid" href="<?= e($c['apply']) ?>">Apply</a>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <!-- Fallback to your legacy HTML grid if no dynamic cards yet -->
        <?= $settings['programs_grid'] ?? '' ?>
      <?php endif; ?>
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
