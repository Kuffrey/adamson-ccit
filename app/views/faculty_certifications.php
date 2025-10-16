<?php
// faculty_certifications.php — Dynamic Faculty Certifications (CMS-driven)
require_once __DIR__ . '/../models/FacultyCertificationsPageSettings.php';
require_once __DIR__ . '/../models/FacultyCertification.php';
$settings = FacultyCertificationsPageSettings::getSettings();
$grouped = FacultyCertification::getGrouped();
$years = array_keys($grouped);
$issuers = [];
foreach ($grouped as $yearData) {
  foreach ($yearData as $issuer => $certs) {
    if (!in_array($issuer, $issuers)) $issuers[] = $issuer;
  }
}

// Filter the grouped data by GET params (server-side, not JS)
$issuerVal = isset($_GET['issuer']) ? $_GET['issuer'] : 'all';
$yearVal = isset($_GET['year']) ? $_GET['year'] : 'all';
$searchVal = isset($_GET['search']) ? $_GET['search'] : '';

$filtered = [];
$totalCerts = 0;
$issuerFilter = $issuerVal;
$yearFilter = $yearVal;
$searchFilter = mb_strtolower($searchVal);
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
  </section>
  <!-- ============ LOCAL SUBNAV ============ -->
  <nav class="subnav" aria-label="Faculty sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li>
          <a href="/adamson-ccit/public/index.php?page=faculty_profile">Profile</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=faculty_research">Research</a>
        </li>
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=faculty_certifications" aria-current="page">Certifications</a>
        </li>
      </ul>
    </div>
  </nav>
  <!-- ============ FILTER BAR (NBAR STYLE, CONSISTENT WITH NEWS) ============ -->
  <section class="nbar">
    <div class="container nbar__inner">
      <div class="nbar__left">
        <div class="ncats" role="tablist" aria-label="Filter by issuer">
          <?php
            $issuersList = $issuers;
            array_unshift($issuersList, 'all');
            foreach ($issuersList as $issuer):
              $active = ($issuerVal === $issuer) ? 'is-active' : '';
              $label = $issuer === 'all' ? 'All Issuers' : $issuer;
              $href = '/adamson-ccit/public/index.php?page=faculty_certifications&issuer=' . urlencode($issuer) . '&year=' . urlencode($yearVal) . '&search=' . urlencode($searchVal);
          ?>
            <a class="pill <?= $active ?>" role="tab" aria-selected="<?= $active ? 'true' : 'false' ?>" href="<?= htmlspecialchars($href) ?>"><?= htmlspecialchars($label) ?></a>
          <?php endforeach; ?>
        </div>
        <form class="nyear" method="get" action="/adamson-ccit/public/index.php" style="margin-bottom:0;">
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
      <form class="nsearch" role="search" aria-label="Search certifications or faculty" method="get" action="/adamson-ccit/public/index.php" style="margin-bottom:0;">
        <input type="hidden" name="page" value="faculty_certifications">
        <input type="hidden" name="issuer" value="<?= htmlspecialchars($issuerVal) ?>">
        <input type="hidden" name="year" value="<?= htmlspecialchars($yearVal) ?>">
        <input id="fcSearch" name="search" type="search" value="<?= htmlspecialchars($searchVal) ?>" placeholder="Search certifications or faculty…" aria-label="Search certifications or faculty" autocomplete="off" />
        <button class="btn btn--solid" type="submit"></button>
      </form>
    </div>
  </section>
  <!-- ============ CERTIFICATIONS GRID ============ -->
  <section class="rlist">
    <div class="container">
      <?php
        $countTxt = $totalCerts === 0 ? 'No certifications found' : "Showing {$totalCerts} result" . ($totalCerts!==1?'s':'');
        $issuerLabel = $issuerFilter === 'all' ? 'all issuers' : $issuerFilter;
        $yearLabel = $yearFilter === 'all' ? 'all years' : $yearFilter;
      ?>
      <div id="fcCount" class="rcount" style="margin-bottom:20px;">
        <?= htmlspecialchars($countTxt) ?> • <?= htmlspecialchars(ucwords($issuerLabel)) ?> • <?= htmlspecialchars($yearLabel) ?>
      </div>
      
      <?php if (empty($filtered)): ?>
        <div class="nempty" style="text-align:center;padding:60px 20px;">
          <div>No faculty certifications match your filters.</div>
        </div>
      <?php else: ?>
        <div id="fcCertGroups" class="cards" style="gap:24px;">
        <?php foreach ($filtered as $year => $issuers): ?>
          <article class="card" data-year="<?= htmlspecialchars($year) ?>" style="border: 1px solid var(--edgec); border-radius: 16px; background: #fff; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div class="card__content" style="padding: 24px;">
              <header class="card__header" style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
                <h2 style="margin: 0; font-size: 20px; font-weight: 900; color: #0b234c;">Certifications Earned in <?= htmlspecialchars($year) ?></h2>
              </header>
              
              <div class="card__body" style="display: grid; gap: 16px;">
              <?php foreach ($issuers as $issuer => $certs): ?>
                <div class="issuer-section" style="background: #f8fafc; border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0;">
                  <div class="issuer-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <span style="font-weight: 700; color: #3b82f6; font-size: 16px;">Issuer: <?= htmlspecialchars($issuer) ?></span>
                    <span style="background: #dbeafe; color: #1d4ed8; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px; border: 1px solid #93c5fd;">×<?= count($certs) ?></span>
                  </div>
                  
                  <div class="cert-list" style="display: grid; gap: 8px;">
                    <?php foreach ($certs as $cert_title => $cert): ?>
                      <div class="cert-item" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                        <div style="font-weight: 600; color: #0b234c; margin-bottom: 4px; display: flex; align-items: center; gap: 8px;">
                          • <?= htmlspecialchars($cert_title) ?>
                          <?php 
                          // Add status indicators for expired/revoked certifications
                          if (isset($cert['status'])):
                            if ($cert['status'] === 'Expired'): ?>
                              <span style="background: #fee2e2; color: #dc2626; font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: 700;">EXPIRED</span>
                            <?php elseif ($cert['status'] === 'Revoked'): ?>
                              <span style="background: #fef2f2; color: #dc2626; font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: 700;">REVOKED</span>
                            <?php endif;
                          endif; ?>
                        </div>
                        <ul style="margin: 0 0 0 16px; padding: 0; list-style: disc; color: #64748b; font-size: 14px;">
                          <?php foreach ($cert['faculty'] as $faculty_name): ?>
                            <li style="margin-bottom: 2px;"><?= htmlspecialchars($faculty_name) ?></li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endforeach; ?>
              </div>
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
