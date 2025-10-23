
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/DeanCorner.php';
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
$pdo = new PDO($dsn, DB_USER, DB_PASS, [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$dean = DeanCorner::get($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dean's Corner | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
</head>
<body>
<main>
  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="<?= htmlspecialchars($dean['subhero_image']) ?>" alt="Dean's Corner Banner">
    </div>
    <div class="hero__scrim" aria-hidden="true"></div>

    <div class="container hero__inner">
      <div class="hero__copy">
      <span class="hero__eyebrow">About CCIT</span>
      <h1 class="subhero__title">Dean's Corner</h1>
      <p class="hero__lead">Message from the Dean</p>
    </div>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (About section tabs) ============ -->
  <nav class="subnav" aria-label="About sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li>
          <a href="/adamson-ccit/public/index.php?page=about_history">History</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=about_vision_mission">Vision &amp; Mission</a>
        </li>
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=deans_corner" aria-current="page">Dean's Corner</a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- ============ CONTENT ============ -->
  <section class="content">
    <div class="container" style="display:flex;justify-content:center;">
      <article class="content__main">

        <section class="card" style="background:#fff;border:1px solid #e3e8f2;border-radius:18px;padding:36px 28px;box-shadow:0 2px 12px rgba(0,0,0,0.04);display:flex;flex-direction:column;align-items:center;">
          <img src="<?= htmlspecialchars($dean['photo']) ?>" alt="Dean Photo" style="width:200px;height:200px;object-fit:cover;border-radius:18px;border:3px solid #0a204b;box-shadow:0 2px 12px rgba(0,0,0,0.10);margin-bottom:18px;">
          <h3>
            <?= htmlspecialchars($dean['name']) ?>
          </h3>
          <div style="color:#1e3c72;margin-bottom:18px;">
            <?= htmlspecialchars($dean['title']) ?>
          </div>
          <p class="lead" style="font-size: 16px;line-height:1.85;margin:0 0 18px 0;text-align:center;">
            <?= nl2br(htmlspecialchars($dean['message'])) ?>
          </p>
          <span style="font-size:1rem;color:#374151;">Email: <a href="mailto:<?= htmlspecialchars($dean['email']) ?>" style="color:#0a204b;text-decoration:underline;"><?= htmlspecialchars($dean['email']) ?></a></span>
        </section>
      </article>
    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta">
    <div class="container">
    <div class="container cta__inner">
      <div>
        <h2><?= htmlspecialchars($dean['cta_title']) ?></h2>
        <p><?= nl2br(htmlspecialchars($dean['cta_body'])) ?></p>
      </div>
      <a class="btn btn--solid" href="mailto:<?= htmlspecialchars($dean['email']) ?>"><?= htmlspecialchars($dean['cta_btn_label']) ?></a>
    </div>
    </div>
  </section>
</main>
