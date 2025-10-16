<?php
require_once __DIR__ . '/../models/StudentScholarshipsPageSettings.php';
require_once __DIR__ . '/../models/StudentScholarship.php';

if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}

/** Normalize any type string (e.g. "Merit-Based Scholarship") → "merit-based" */
function sch_slug(?string $type): string {
  if (!$type) return 'general';
  $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $type));
  // a few friendly aliases to keep colors consistent with orgs
  $map = [
    'merit' => 'merit', 'merit-based' => 'merit',
    'need' => 'need', 'need-based' => 'need',
    'athletic' => 'athletic',
    'government' => 'government', 'govt' => 'government', 'university' => 'university', 'internal' => 'university',
    'private' => 'private', 'external' => 'private', 'industry' => 'private',
    'grant' => 'grant', 'loan' => 'loan',
    'freshmen' => 'fresh', 'freshman' => 'fresh'
  ];
  return $map[$slug] ?? $slug;
}

$settings = StudentScholarshipsPageSettings::getSettings();
$scholarships = StudentScholarship::getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Scholarships | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
</head>
<body class="page-sch">

<main>

  <!-- ============ SUB-HERO (hero-like structure) ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="<?= e($settings['subhero_image_url'] ?? '/adamson-ccit/public/assets/images/hero-campus.jpg') ?>" alt="Adamson University campus">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>

    <div class="container hero__inner">
      <div class="hero__copy">
        <span class="hero__eyebrow">Student Support</span>
        <h1 class="subhero__title">Scholarships</h1>
        <p class="hero__lead"><?= e($settings['subhero_lead'] ?? 'Financial aid options for incoming and current Adamson students.') ?></p>
      </div>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (same as Organizations) ============ -->
  <nav class="subnav" aria-label="Student sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li><a href="/adamson-ccit/public/index.php?page=student_organizations">Organizations</a></li>
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=student_scholarships" aria-current="page">Scholarships</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_research">Research</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_certifications">Certifications</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_testimonials">Testimonials</a></li>
      </ul>
    </div>
  </nav>

  <!-- ============ SCHOLARSHIPS GRID (same card system) ============ -->
  <section class="content" aria-labelledby="scholarships-heading">
    <div class="container">
      <h2 id="scholarships-heading" class="sr-only">Available Scholarships</h2>

      <div class="prog__grid">
        <?php foreach ($scholarships as $sch): ?>
          <?php
            $type = $sch['type'] ?? 'Scholarship';
            $typeKey = sch_slug($type);
          ?>
          <article class="prog__card">
            <header class="prog__head">
              <h3 class="prog__title"><?= e($sch['name']) ?></h3>
              <span class="sch__type sch__type--<?= e($typeKey) ?>"><?= e($type) ?></span>
            </header>

            <?php if (!empty($sch['logo_url'])): ?>
              <div class="sch__logo" aria-hidden="true">
                <img src="<?= e($sch['logo_url']) ?>" alt="<?= e($sch['name']) ?> logo">
              </div>
            <?php endif; ?>

            <?php if (!empty($sch['summary'])): ?>
              <p class="prog__summary"><?= $sch['summary'] ?></p>
            <?php endif; ?>

            <?php if (!empty($sch['conditions'])): ?>
              <div class="pillbox">
                <h4 class="pillbox__title">Key Conditions</h4>
                <?= $sch['conditions'] ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($sch['requirements'])): ?>
              <div class="pillbox">
                <h4 class="pillbox__title">Initial Requirements</h4>
                <?= $sch['requirements'] ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($sch['examples'])): ?>
              <div class="pillbox">
                <h4 class="pillbox__title">Examples</h4>
                <?= $sch['examples'] ?>
              </div>
            <?php endif; ?>

            <div class="prog__footer">
              <div class="sch__meta">
                <?php if (!empty($sch['deadline'])): ?>
                  <span class="sch__badge">Deadline: <?= e($sch['deadline']) ?></span>
                <?php endif; ?>
                <?php if (!empty($sch['amount'])): ?>
                  <span class="sch__badge">Amount: <?= e($sch['amount']) ?></span>
                <?php endif; ?>
              </div>

              <?php if (!empty($sch['learn_more_url'])): ?>
                <a class="btn btn--outline-blue ext" href="<?= e($sch['learn_more_url']) ?>" target="_blank" rel="noopener">Learn More</a>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <?php if (!empty($settings['note'])): ?>
        <p class="sch__note"><?= $settings['note'] ?></p>
      <?php endif; ?>
    </div>
  </section>

</main>

</body>
</html>
