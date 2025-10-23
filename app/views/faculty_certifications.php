<?php
// faculty_certifications.php — Dynamic Faculty Certifications (CMS-driven)
require_once __DIR__ . '/../models/FacultyCertificationsPageSettings.php';
require_once __DIR__ . '/../models/FacultyCertification.php';
$settings = FacultyCertificationsPageSettings::getSettings();
$grouped  = FacultyCertification::getGrouped();

// Ensure the years are sorted in descending order
$years = array_keys($grouped);
rsort($years);

// Build issuers list
$issuers = [];
foreach ($grouped as $yearData) {
  foreach ($yearData as $issuer => $certs) {
    if (!in_array($issuer, $issuers)) $issuers[] = $issuer;
  }
}

// Filters
$issuerVal  = $_GET['issuer'] ?? 'all';
$yearVal    = $_GET['year'] ?? 'all';
$searchVal  = $_GET['search'] ?? '';

$filtered   = [];
$totalCerts = 0;
$issuerFilter = $issuerVal;
$yearFilter   = $yearVal;
$searchFilter = mb_strtolower($searchVal);

// Apply server-side filtering
foreach ($grouped as $year => $issuersData) {
  if ($yearFilter !== 'all' && $year != $yearFilter) continue;
  $filteredIssuers = [];
  foreach ($issuersData as $issuer => $certs) {
    if ($issuerFilter !== 'all' && $issuer != $issuerFilter) continue;
    $filteredCerts = [];
    foreach ($certs as $cert_title => $cert) {
      $certText = mb_strtolower($cert_title . ' ' . implode(' ', $cert['faculty']));
      if ($searchFilter && mb_strpos($certText, $searchFilter) === false) continue;
      $filteredCerts[$cert_title] = $cert;
      $totalCerts++;
    }
    if ($filteredCerts) $filteredIssuers[$issuer] = $filteredCerts;
  }
  if ($filteredIssuers) $filtered[$year] = $filteredIssuers;
}
$filtered = array_reverse($filtered, true); // Ensure filtered array is sorted by year in descending order
uksort($filtered, function($a, $b) {
    return $b <=> $a;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Faculty Certifications | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
</head>
<body>
<main class="page-faculty">

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="<?= htmlspecialchars($settings['subhero_image_url'] ?? '/adamson-ccit/public/assets/images/hero-campus.jpg') ?>" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>

    <div class="container hero__inner">
      <div class="hero__copy">
        <span class="hero__eyebrow">People</span>
        <h1 class="subhero__title">Faculty Certifications</h1>
        <p class="hero__lead"><?= htmlspecialchars($settings['subhero_lead'] ?? 'Professional badges, licenses, and industry certifications held by CCIT faculty.') ?></p>
      </div>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV ============ -->
  <nav class="subnav" aria-label="Faculty sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li><a href="/adamson-ccit/public/index.php?page=faculty_profile">Profile</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=faculty_research">Research</a></li>
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=faculty_certifications" aria-current="page">Certifications</a></li>
      </ul>
    </div>
  </nav>

  <!-- ============ FILTER BAR (same nbar theme) ============ -->
  <section class="nbar">
    <div class="container nbar__inner">
      <div class="nbar__left">
        <div class="ncats" role="tablist" aria-label="Filter by issuer">
          <?php
            $issuersList = $issuers;
            array_unshift($issuersList, 'all');
            foreach ($issuersList as $issuer):
              $active = ($issuerVal === $issuer) ? 'is-active' : '';
              $label  = $issuer === 'all' ? 'All Issuers' : $issuer;
              $href   = '/adamson-ccit/public/index.php?page=faculty_certifications&issuer=' . urlencode($issuer) . '&year=' . urlencode($yearVal) . '&search=' . urlencode($searchVal);
          ?>
            <a class="pill <?= $active ?>" role="tab" aria-selected="<?= $active ? 'true' : 'false' ?>" href="<?= htmlspecialchars($href) ?>">
              <?= htmlspecialchars($label) ?>
            </a>
          <?php endforeach; ?>
        </div>

        <form class="nyear" method="get" action="/adamson-ccit/public/index.php">
          <input type="hidden" name="page" value="faculty_certifications">
          <input type="hidden" name="issuer" value="<?= htmlspecialchars($issuerVal) ?>">
          <input type="hidden" name="search" value="<?= htmlspecialchars($searchVal) ?>">
          <select name="year" onchange="this.form.submit()" aria-label="Filter by year">
            <option value="all" <?= $yearVal==='all'?'selected':''; ?>>All Years</option>
            <?php foreach ($years as $year): ?>
              <option value="<?= htmlspecialchars($year) ?>" <?= $yearVal==$year?'selected':''; ?>><?= htmlspecialchars($year) ?></option>
            <?php endforeach; ?>
          </select>
        </form>
      </div>

      <form class="nsearch" role="search" aria-label="Search certifications or faculty" method="get" action="/adamson-ccit/public/index.php">
        <input type="hidden" name="page" value="faculty_certifications">
        <input type="hidden" name="issuer" value="<?= htmlspecialchars($issuerVal) ?>">
        <input type="hidden" name="year" value="<?= htmlspecialchars($yearVal) ?>">
        <input id="fcSearch" name="search" type="search" value="<?= htmlspecialchars($searchVal) ?>" placeholder="Search…" aria-label="Search certifications or faculty" autocomplete="off" />
        <button class="btn btn--solid" type="submit"></button>
      </form>
    </div>
  </section>

  <!-- ============ CERTIFICATIONS (one-row year cards) ============ -->
  <section class="rlist">
    <div class="container">
      <?php
        $countTxt    = $totalCerts === 0 ? 'No certifications found' : "Showing {$totalCerts} result" . ($totalCerts!==1?'s':'');
        $issuerLabel = $issuerFilter === 'all' ? 'all issuers' : $issuerFilter;
        $yearLabel   = $yearFilter === 'all' ? 'all years' : $yearFilter;
      ?>
      <div id="fcCount" class="rcount rcount--pad">
        <?= htmlspecialchars($countTxt) ?> • <?= htmlspecialchars(ucwords($issuerLabel)) ?> • <?= htmlspecialchars($yearLabel) ?>
      </div>

      <?php if (empty($filtered)): ?>
        <div class="nempty">No faculty certifications match your filters.</div>
      <?php else: ?>
        <div id="fcCertGroups" class="fcards">
          <?php foreach ($filtered as $year => $issuers): ?>
          <article class="fcard" data-year="<?= htmlspecialchars($year) ?>">
            <header class="fcard__head">
              <h2 class="fcard__title">Certifications Earned in <?= htmlspecialchars($year) ?></h2>
            </header>

            <div class="fcard__body">
              <?php foreach ($issuers as $issuer => $certs): ?>
              <section class="issuer">
                <div class="issuer__head">
                  <span class="issuer__name"><?= htmlspecialchars($issuer) ?></span>
                  <span class="issuer__count">×<?= count($certs) ?></span>
                </div>

                <div class="issuer__list">
                  <?php foreach ($certs as $cert_title => $cert): ?>
                  <div class="cert">
                    <div class="cert__title">
                      <?= htmlspecialchars($cert_title) ?>
                      <?php if (!empty($cert['status'])): ?>
                        <?php if ($cert['status'] === 'Expired'): ?>
                          <span class="cert__status cert__status--expired">Expired</span>
                        <?php elseif ($cert['status'] === 'Revoked'): ?>
                          <span class="cert__status cert__status--revoked">Revoked</span>
                        <?php endif; ?>
                      <?php endif; ?>
                    </div>
                    <ul class="cert__faculty">
                      <?php foreach ($cert['faculty'] as $name): ?>
                        <li><?= htmlspecialchars($name) ?></li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                  <?php endforeach; ?>
                </div>
              </section>
              <?php endforeach; ?>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>
</body>
</html>
