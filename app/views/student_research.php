
<?php
require_once __DIR__ . '/../models/StudentResearchPageSettings.php';
require_once __DIR__ . '/../models/StudentResearch.php';
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
$settings = StudentResearchPageSettings::getSettings();
$research = StudentResearch::getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Research | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
</head>
<body>

<main class="page-students page-research">

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="<?= e($settings['subhero_image_url'] ?? '/adamson-ccit/public/assets/images/hero-research.jpg') ?>" alt="Students presenting research posters">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Students</p>
      <h1 class="subhero__title">Research</h1>
      <p class="subhero__lead"><?= e($settings['subhero_lead'] ?? 'Publications and conference papers by our students and faculty mentors.') ?></p>
    </div>
  </section>

  <!-- ============ STUDENT SUBNAV ============ -->
  <nav class="subnav" aria-label="Students sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li><a href="/adamson-ccit/public/index.php?page=student_organizations">Organizations</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_scholarships">Scholarships</a></li>
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=student_research" aria-current="page">Research</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_certifications">Certifications</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_testimonials">Testimonials</a></li>
      </ul>
    </div>
  </nav>

  <!-- ============ FILTER BAR ============ -->
  <section class="rbar">
    <div class="container rbar__inner">
      <div class="rbar__left">
        <div class="rcats" role="tablist" aria-label="Filter research by category">
          <button class="pill is-active" data-filter="all" role="tab" aria-selected="true">All</button>
          <button class="pill" data-filter="publication" role="tab">Publications</button>
          <button class="pill" data-filter="project" role="tab">Projects</button>
          <button class="pill" data-filter="award" role="tab">Awards</button>
        </div>

        <label class="ryear">
          <span class="sr-only">Filter by year</span>
          <select id="rYear">
            <option value="all">All Years</option>
            <option>2025</option>
            <option>2024</option>
          </select>
        </label>
      </div>

      <form class="rsearch" role="search" aria-label="Search research">
        <input id="rQuery" type="search" placeholder="Search titles, authors…" aria-label="Search research" />
        <button class="btn btn--solid" type="submit">Search</button>
      </form>
    </div>
  </section>

  <!-- ============ RESEARCH GRID ============ -->
  <section class="rlist">
    <div class="container">
      <div id="rCount" class="rcount">Showing all research</div>

      <div id="rGrid" class="cards">
        <?php foreach ($research as $r): ?>
        <article class="r" data-cat="<?= e($r['category']) ?>" data-year="<?= e($r['year']) ?>">
          <a class="r__media" href="<?= e($r['link_url']) ?>" target="_blank" rel="noopener">
            <img src="<?= e($r['image_url']) ?>" alt="Poster: <?= e($r['title']) ?>">
            <span class="chip chip--blue"><?= ucfirst(e($r['category'])) ?></span>
          </a>
          <div class="r__body">
            <h3 class="r__title">
              <a href="<?= e($r['link_url']) ?>" target="_blank" rel="noopener">
                <?= e($r['title']) ?>
              </a>
            </h3>
            <?php if (!empty($r['meta'])): ?><p class="r__meta"><?= $r['meta'] ?></p><?php endif; ?>
            <div class="r__actions">
              <?php if (!empty($r['link_url']) && !empty($r['link_label'])): ?>
                <a class="btn btn--outline-blue" href="<?= e($r['link_url']) ?>" target="_blank" rel="noopener"><?= e($r['link_label']) ?></a>
              <?php endif; ?>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <div id="rEmpty" class="nempty" hidden>No research matches your filters.</div>
    </div>
  </section>

</main>

<script>
  // Research filter/search (client-side)
  (function(){
    const pills = Array.from(document.querySelectorAll('.rcats .pill'));
    const yearSel = document.getElementById('rYear');
    const qInput = document.getElementById('rQuery');
    const form = document.querySelector('.rsearch');

    const grid = document.getElementById('rGrid');
    const cards = Array.from(grid.querySelectorAll('.r'));
    const count = document.getElementById('rCount');
    const empty = document.getElementById('rEmpty');

    // Preselect current year if present
    const curYear = String(new Date().getFullYear());
    if ([...yearSel.options].some(o => o.value === curYear || o.textContent === curYear)) {
      yearSel.value = 'all'; // default to all; change to curYear if you prefer auto-select
    }

    let activeCat = 'all';

    function apply(){
      const q = (qInput.value || '').trim().toLowerCase();
      const y = yearSel.value;
      let visible = 0;

      cards.forEach(c => {
        const cat = (c.getAttribute('data-cat') || '').toLowerCase();
        const year = (c.getAttribute('data-year') || '');
        const text = c.innerText.toLowerCase();

        let ok = true;
        if (activeCat !== 'all' && cat !== activeCat) ok = false;
        if (ok && y !== 'all' && year !== y) ok = false;
        if (ok && q && !text.includes(q)) ok = false;

        c.style.display = ok ? '' : 'none';
        if (ok) visible++;
      });

      const catLabel = activeCat === 'all' ? 'All' :
        activeCat.charAt(0).toUpperCase() + activeCat.slice(1) + (activeCat.endsWith('s') ? '' : 's');
      const yearLabel = (y === 'all') ? 'all years' : y;

      count.textContent = `Showing ${visible} item${visible!==1?'s':''} • ${catLabel} • ${yearLabel}`;
      empty.hidden = visible !== 0;
    }

    pills.forEach(p => p.addEventListener('click', () => {
      pills.forEach(x => x.classList.remove('is-active'));
      p.classList.add('is-active');
      activeCat = p.dataset.filter;
      apply();
    }));

    yearSel.addEventListener('change', apply);
    form.addEventListener('submit', (e) => { e.preventDefault(); apply(); });

    apply();
  })();
</script>

</body>
</html>
