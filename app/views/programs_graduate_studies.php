
<?php
require_once __DIR__ . '/../models/ProgramsGraduateSettings.php';
if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}

// Get graduate settings and cards
$settings = ProgramsGraduateSettings::getSettings();
$cards = ProgramsGraduateSettings::getCards($settings, false); // Frontend mode = false for active only
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
      
      <?php if (!empty($cards)): ?>
        <div class="prog__grid">
          <?php foreach ($cards as $card): ?>
            <article class="prog__card" <?= $card['slug'] ? 'id="' . e($card['slug']) . '"' : '' ?>>
              <!-- Header -->
              <div class="prog__head">
                <?php if ($card['badge']): ?>
                  <span class="badge"><?= e($card['badge']) ?></span>
                <?php endif; ?>
                <h3 class="prog__title">
                  <?= e($card['title']) ?>
                  <?php if ($card['muted']): ?>
                    <span class="prog__muted"><?= e($card['muted']) ?></span>
                  <?php endif; ?>
                </h3>
              </div>

              <!-- Summary -->
              <?php if ($card['summary']): ?>
                <p class="prog__summary"><?= e($card['summary']) ?></p>
              <?php endif; ?>

              <!-- Pillbox -->
              <?php if (!empty($card['pill_t']) || !empty($card['pills'])): ?>
                <div class="pillbox">
                  <?php if ($card['pill_t']): ?>
                    <h4 class="pillbox__title"><?= e($card['pill_t']) ?></h4>
                  <?php endif; ?>
                  <?php if (!empty($card['pills'])): ?>
                    <ul class="pills" role="list">
                      <?php 
                      $pills = is_array($card['pills']) ? $card['pills'] : explode("\n", $card['pills']);
                      foreach ($pills as $pill): 
                        $pill = trim($pill);
                        if ($pill):
                      ?>
                        <li><?= e($pill) ?></li>
                      <?php 
                        endif;
                      endforeach; 
                      ?>
                    </ul>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <!-- Footer actions -->
              <div class="prog__footer">
                <div class="mini-links">
                  <?php if ($card['lm_url']): ?>
                    <a class="ext" href="<?= e($card['lm_url']) ?>"<?= $card['lm_ext'] ? ' target="_blank" rel="noopener"' : '' ?>>Learn more</a>
                  <?php endif; ?>
                  <?php if ($card['cur_url']): ?>
                    <a class="ext" href="<?= e($card['cur_url']) ?>"<?= $card['cur_ext'] ? ' target="_blank" rel="noopener"' : '' ?>>Curriculum</a>
                  <?php endif; ?>
                </div>
                <?php if ($card['apply']): ?>
                  <a class="btn btn--solid" href="<?= e($card['apply']) ?>">Apply</a>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <p>No graduate programs are currently available. Please check back later.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2><?= e($settings['cta_title'] ?? 'Chart your next step.') ?></h2>
        <p><?= e($settings['cta_description'] ?? 'Ask us about MIT schedules, requirements, and scholarships.') ?></p>
      </div>
      <?php if (!empty($settings['cta_action_url'])): ?>
        <a class="btn btn--solid" href="<?= e($settings['cta_action_url']) ?>"><?= e($settings['cta_action_label'] ?? 'Contact CCIT') ?></a>
      <?php else: ?>
        <a class="btn btn--solid" href="/adamson-ccit/public/index.php?page=contact">Contact CCIT</a>
      <?php endif; ?>
    </div>
  </section>

</main>

</body>
</html>
