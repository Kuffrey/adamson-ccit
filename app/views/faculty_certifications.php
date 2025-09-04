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
    <div class="container subhero__inner">
      <p class="eyebrow">People</p>
      <h1 class="subhero__title">Faculty Certifications</h1>
      <p class="subhero__lead"><?= htmlspecialchars($settings['subhero_lead'] ?? 'Professional badges, licenses, and industry certifications held by CCIT faculty.') ?></p>
    </div>
  </section>
  <!-- ============ LOCAL SUBNAV (Faculty, pills, consistent with news.php) ============ -->
  <nav class="subnav" aria-label="Faculty sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li><a href="/adamson-ccit/public/index.php?page=faculty_profile">Profile</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=faculty_research">Research</a></li>
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=faculty_certifications" aria-current="page">Certifications</a></li>
      </ul>
    </div>
  </nav>
  <!-- ============ FILTER BAR (NBAR STYLE, CONSISTENT WITH NEWS) ============ -->
  <section class="nbar">
    <div class="container nbar__inner">
      <div class="nbar__left">
        <div class="ncats" role="tablist" aria-label="Filter by issuer">
          <?php
            $issuerVal = isset($_GET['issuer']) ? $_GET['issuer'] : 'all';
            $yearVal = isset($_GET['year']) ? $_GET['year'] : 'all';
            $searchVal = isset($_GET['search']) ? $_GET['search'] : '';
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
        <button class="btn btn--solid" type="submit">Search</button>
      </form>
    </div>
  </section>
  <!-- ============ GROUPED LIST + COUNT BAR ============ -->
  <section class="content rlist" aria-labelledby="fc-list">
    <div class="container">
      <?php
        // Filter the grouped data by GET params (server-side, not JS)
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
        $countTxt = $totalCerts === 0 ? 'No certifications found' : "Showing {$totalCerts} result" . ($totalCerts!==1?'s':'');
        $issuerLabel = $issuerFilter === 'all' ? 'all issuers' : $issuerFilter;
        $yearLabel = $yearFilter === 'all' ? 'all years' : $yearFilter;
      ?>
      <div id="fcCount" class="ncount" style="margin-bottom:18px;">
        <?= htmlspecialchars($countTxt) ?> • <?= htmlspecialchars(ucwords($issuerLabel)) ?> • <?= htmlspecialchars($yearLabel) ?>
      </div>
      <?php if (empty($filtered)): ?>
        <div class="nempty" style="text-align:center;padding:40px 0;">
          <img src="/adamson-ccit/public/assets/images/empty-cert.svg" alt="No certifications" style="width:120px;opacity:.7;margin-bottom:18px;max-width:100%;" loading="lazy" />
          <div>No faculty certifications match your filters.</div>
        </div>
      <?php else: ?>
        <div id="fcCertGroups" class="fc-certgroups cards" style="gap:28px;">
        <?php foreach ($filtered as $year => $issuers): ?>
          <section class="fc-group fc-group--year card" data-year="<?= htmlspecialchars($year) ?>" aria-label="Certifications for <?= htmlspecialchars($year) ?>">
            <header style="border-bottom:1px solid #e6e9ef;padding-bottom:10px;margin-bottom:16px;">
              <h2 class="fc-year" style="margin:0;font-size:1.3em;color:#0b234c;font-weight:900;letter-spacing:.01em;">Certifications Earned in <?= htmlspecialchars($year) ?></h2>
            </header>
            <div class="fc-group__issuers" style="display:grid;gap:18px;">
            <?php foreach ($issuers as $issuer => $certs): ?>
              <section class="fc-group fc-group--issuer" data-issuer="<?= htmlspecialchars($issuer) ?>" aria-label="Issuer <?= htmlspecialchars($issuer) ?>" style="background:#f8fafc;border-radius:12px;padding:14px 16px;box-shadow:0 1px 4px 0 rgba(0,0,0,.03);">
                <header style="margin-bottom:10px;display:flex;align-items:center;gap:8px;">
                  <span class="fc-issuer" style="font-size:1.08em;font-weight:800;color:var(--blue2);letter-spacing:.01em;">Issuer: <?= htmlspecialchars($issuer) ?></span>
                  <span style="background:#e8f5ee;color:#0b234c;font-size:12px;font-weight:700;padding:2px 10px;border-radius:999px;">x<?= count($certs) ?></span>
                </header>
                <ul class="fc-certs" style="list-style:none;margin:0;padding:0;display:grid;gap:10px;">
                  <?php foreach ($certs as $cert_title => $cert): ?>
                    <li class="fc-cert" style="background:#fff;border:1px solid #e6e9ef;border-radius:10px;padding:10px 14px;">
                      <div class="fc-cert__body" style="display:flex;flex-direction:column;gap:4px;">
                        <span class="fc-cert__title" style="font-weight:700;color:#0b234c;font-size:1em;">&bull; <?= htmlspecialchars($cert_title) ?></span>
                        <ul class="fc-cert__faculty" style="margin:0 0 0 18px;padding:0;list-style:disc;color:#475569;font-size:.98em;">
                          <?php foreach ($cert['faculty'] as $faculty_name): ?>
                            <li><?= htmlspecialchars($faculty_name) ?></li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </section>
            <?php endforeach; ?>
            </div>
          </section>
        <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>
</body>
</html>
