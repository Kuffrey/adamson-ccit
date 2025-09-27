<?php
// faculty_research.php — Dynamic Faculty Research (CMS-driven)
require_once __DIR__ . '/../models/FacultyResearchPageSettings.php';
require_once __DIR__ . '/../models/FacultyResearch.php';
$settings = FacultyResearchPageSettings::getSettings();
$research = FacultyResearch::getAll();
$years = FacultyResearch::getYears();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Faculty Research | AdU-CCIT</title>
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
      <h1 class="subhero__title">Faculty Research</h1>
      <p class="subhero__lead"><?= htmlspecialchars($settings['subhero_lead'] ?? 'Peer-reviewed publications, presentations, and other scholarly work by CCIT faculty.') ?></p>
    </div>
  </section>
  <!-- ============ LOCAL SUBNAV ============ -->
  <nav class="subnav" aria-label="Faculty sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li>
          <a href="/adamson-ccit/public/index.php?page=faculty_profile">Profile</a>
        </li>
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=faculty_research" aria-current="page">Research</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=faculty_certifications">Certifications</a>
        </li>
      </ul>
    </div>
  </nav>
  <!-- ============ FILTER BAR ============ -->
  <section class="nbar">
    <div class="container nbar__inner">
      <div class="nbar__left">
        <div class="ncats" role="tablist" aria-label="Filter by department">
          <button class="pill is-active" role="tab" aria-selected="true" data-dept="all">All</button>
          <button class="pill" role="tab" data-dept="itis">IT&amp;IS</button>
        </div>
        <form class="nyear" method="get" action="#" onsubmit="return false;">
          <select id="fYear" name="year">
            <option value="all">All Years</option>
            <?php foreach ($years as $y): ?>
              <option value="<?= htmlspecialchars($y) ?>"><?= htmlspecialchars($y) ?></option>
            <?php endforeach; ?>
          </select>
        </form>
      </div>
      <form class="nsearch" role="search" aria-label="Search research" onsubmit="return false;">
        <input id="fQuery" type="search" placeholder="Search title, author, venue…" aria-label="Search research" />
        <button class="btn btn--solid" type="submit"></button>
      </form>
    </div>
  </section>
  <!-- ============ RESEARCH GRID ============ -->
  <section class="rlist">
    <div class="container">
      <div id="rCount" class="rcount">Showing <?= count($research) ?> publication<?= count($research) !== 1 ? 's' : '' ?></div>
      
      <div id="rGrid" class="cards">
        <?php if (empty($research)): ?>
          <!-- Fallback static content -->
          <article class="r" data-dept="itis" data-type="journal" data-year="2024">
            <a class="r__media" href="#" target="_blank" rel="noopener">
              <img src="/adamson-ccit/public/assets/images/research-placeholder.jpg" alt="Research paper">
              <span class="chip chip--blue">Journal</span>
            </a>
            <div class="r__body">
              <h3 class="r__title">
                <a href="#" target="_blank" rel="noopener">Machine Learning Applications in Computer Science Education</a>
              </h3>
              <p class="r__meta">Dr. Maria Santos • IEEE Transactions on Education • 2024</p>
              <div class="r__actions">
                <a class="btn btn--outline-blue" href="#" target="_blank" rel="noopener">Read Paper</a>
              </div>
            </div>
          </article>
        <?php else: ?>
          <?php foreach ($research as $r): ?>
          <article class="r" data-dept="<?= htmlspecialchars($r['dept']) ?>" data-type="<?= htmlspecialchars($r['type']) ?>" data-year="<?= htmlspecialchars($r['year']) ?>">
            <a class="r__media" href="<?= !empty($r['view_url']) ? htmlspecialchars($r['view_url']) : '#' ?>" target="_blank" rel="noopener">
              <img src="<?= htmlspecialchars($r['image_url'] ?: '/adamson-ccit/public/assets/images/placeholder-16x9.jpg') ?>" alt="Poster: <?= htmlspecialchars($r['title']) ?>">
              <span class="chip chip--blue"><?= ucfirst(htmlspecialchars($r['type'])) ?></span>
            </a>
            <div class="r__body">
              <h3 class="r__title">
                <?php if (!empty($r['view_url'])): ?>
                  <a href="<?= htmlspecialchars($r['view_url']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($r['title']) ?></a>
                <?php else: ?>
                  <?= htmlspecialchars($r['title']) ?>
                <?php endif; ?>
              </h3>
              <p class="r__meta"><?= htmlspecialchars($r['authors']) ?> • <strong><?= htmlspecialchars($r['venue']) ?></strong> • <?= htmlspecialchars($r['year']) ?></p>
              <?php if (!empty($r['view_url'])): ?>
              <div class="r__actions">
                <a class="btn btn--outline-blue" href="<?= htmlspecialchars($r['view_url']) ?>" target="_blank" rel="noopener">Read</a>
              </div>
              <?php endif; ?>
            </div>
          </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div id="rEmpty" class="nempty"<?= count($research) ? ' hidden' : '' ?>>No faculty research entries are available yet.</div>
    </div>
  </section>
</main>
<script>
(function(){
  const pills = Array.from(document.querySelectorAll('.ncats .pill'));
  const yearSel = document.getElementById('fYear');
  const qInput  = document.getElementById('fQuery');
  const form    = document.querySelector('.nsearch');
  const grid  = document.getElementById('rGrid');
  const cards = Array.from(grid.querySelectorAll('.r'));
  const count = document.getElementById('rCount');
  const empty = document.getElementById('rEmpty');
  let activeDept = 'all';
  function apply(){
    const q = (qInput.value || '').trim().toLowerCase();
    const y = yearSel.value;
    let visible = 0;
    cards.forEach(card => {
      const dept = (card.getAttribute('data-dept') || '').toLowerCase();
      const type = (card.getAttribute('data-type') || '').toLowerCase();
      const year = (card.getAttribute('data-year') || '').toLowerCase();
      const text = card.innerText.toLowerCase();
      let ok = true;
      if (activeDept !== 'all' && dept !== activeDept) ok = false;
      if (ok && y !== 'all' && year !== y) ok = false;
      if (ok && q && !text.includes(q)) ok = false;
      card.style.display = ok ? '' : 'none';
      if (ok) visible++;
    });
    const deptLabel = (activeDept === 'all') ? 'All departments' : activeDept.toUpperCase();
    const yearLabel = (y === 'all') ? 'all years' : y;
    count.textContent = `Showing ${visible} publication${visible!==1?'s':''} • ${deptLabel} • ${yearLabel}`;
    empty.hidden = visible !== 0;
  }
  pills.forEach(p => p.addEventListener('click', () => {
    pills.forEach(x => x.classList.remove('is-active'));
    p.classList.add('is-active');
    activeDept = p.dataset.dept;
    apply();
  }));
  yearSel.addEventListener('change', apply);
  form.addEventListener('submit', e => { e.preventDefault(); apply(); });
  apply();
})();
</script>
</body>
</html>
