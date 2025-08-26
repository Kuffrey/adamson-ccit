<?php /* faculty_research.php — Faculty Research (draft, no data yet) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Faculty Research | AdU-CCIT</title>

  <!-- Global site CSS -->
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
</head>
<body>

<main class="page-faculty">

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">People</p>
      <h1 class="subhero__title">Faculty Research</h1>
      <p class="subhero__lead">Peer-reviewed publications, presentations, and other scholarly work by CCIT faculty.</p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (Faculty) ============ -->
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

  <!-- ============ FILTER BAR (reuses .rbar styles) ============ -->
  <section class="rbar section-sep" aria-labelledby="fres-head">
    <div class="container rbar__inner">
      <h2 id="fres-head" class="sr-only">Filter research</h2>

      <div class="rbar__left">
        <div class="tpills" role="tablist" aria-label="Filter by department">
          <button class="pill is-active" data-dept="all" role="tab" aria-selected="true">All</button>
          <button class="pill" data-dept="itis" role="tab">IT&amp;IS</button>
          <button class="pill" data-dept="cs" role="tab">CS</button>
        </div>

        <!-- reuse .ryear select styling for both controls -->
        <label class="ryear">
          <span class="sr-only">Filter by type</span>
          <select id="fType" aria-label="Filter by type">
            <option value="all">All Types</option>
            <option value="journal">Journal Article</option>
            <option value="conference">Conference Paper</option>
            <option value="chapter">Book Chapter</option>
            <option value="patent">Patent</option>
            <option value="other">Other</option>
          </select>
        </label>

        <label class="ryear">
          <span class="sr-only">Filter by year</span>
          <select id="fYear" aria-label="Filter by year">
            <option value="all">All Years</option>
            <?php
              $current = (int)date('Y');
              for ($y = $current; $y >= $current - 10; $y--) {
                echo '<option value="'.htmlspecialchars($y).'">'.htmlspecialchars($y).'</option>';
              }
            ?>
          </select>
        </label>
      </div>

      <form class="rsearch" role="search" aria-label="Search research">
        <input id="fQuery" type="search" placeholder="Search title, author, venue…" aria-label="Search research"/>
        <button class="btn btn--solid" type="submit">Search</button>
      </form>
    </div>
  </section>

  <!-- ============ LIST ============ -->
  <section class="content rlist" aria-labelledby="dir-head">
    <div class="container">
      <div id="rCount" class="rcount">Showing 0 publications</div>

      <!-- Grid (reuses .cards layout and .r card styles) -->
      <div id="rGrid" class="cards">

        <!--
        Sample card structure (keep for reference; remove once real data comes in)

        <article class="r" data-dept="cs" data-type="journal" data-year="2024">
          <a class="r__media" href="#" aria-hidden="true">
            <img src="/adamson-ccit/public/assets/images/placeholder-16x9.jpg" alt="">
            <span class="chip chip--blue">Journal</span>
          </a>
          <div class="r__body">
            <h3 class="r__title"><a href="#">Title of Publication Goes Here</a></h3>
            <p class="r__meta">Doe, J.; Smith, A. • <strong>Proceedings of XYZ</strong> • 2024</p>
            <div class="r__actions">
              <a class="btn btn--outline-blue" href="#">View</a>
              <a class="btn btn--solid" href="#">PDF</a>
            </div>
          </div>
        </article>
        -->

      </div>

      <!-- Pager (static/dormant for now) -->
      <nav class="pager" aria-label="Research pagination">
        <button class="pg" disabled>« Prev</button>
        <span class="pg__status">Page 1 of 1</span>
        <button class="pg" disabled>Next »</button>
      </nav>

      <div id="rEmpty" class="nempty">No faculty research entries are available yet. This directory will be posted once submissions are compiled.</div>
    </div>
  </section>

</main>

<!-- ============ FILTER JS (client-side; works even with no items) ============ -->
<script>
(function(){
  const pills = Array.from(document.querySelectorAll('.tpills .pill'));
  const typeSel = document.getElementById('fType');
  const yearSel = document.getElementById('fYear');
  const qInput  = document.getElementById('fQuery');
  const form    = document.querySelector('.rsearch');

  const grid  = document.getElementById('rGrid');
  const cards = Array.from(grid.querySelectorAll('.r'));
  const count = document.getElementById('rCount');
  const empty = document.getElementById('rEmpty');

  let activeDept = 'all';

  function apply(){
    const q = (qInput.value || '').trim().toLowerCase();
    const t = typeSel.value;
    const y = yearSel.value;

    let visible = 0;

    cards.forEach(card => {
      const dept = (card.getAttribute('data-dept') || '').toLowerCase();
      const type = (card.getAttribute('data-type') || '').toLowerCase();
      const year = (card.getAttribute('data-year') || '').toLowerCase();
      const text = card.innerText.toLowerCase();

      let ok = true;
      if (activeDept !== 'all' && dept !== activeDept) ok = false;
      if (ok && t !== 'all' && type !== t) ok = false;
      if (ok && y !== 'all' && year !== y) ok = false;
      if (ok && q && !text.includes(q)) ok = false;

      card.style.display = ok ? '' : 'none';
      if (ok) visible++;
    });

    const deptLabel = (activeDept === 'all') ? 'All departments' : activeDept.toUpperCase();
    const typeMap = { all:'all types', journal:'Journal Articles', conference:'Conference Papers', chapter:'Book Chapters', patent:'Patents', other:'Other' };
    const typeLabel = typeMap[t] || 'all types';
    const yearLabel = (y === 'all') ? 'all years' : y;

    count.textContent = `Showing ${visible} publication${visible!==1?'s':''} • ${deptLabel} • ${typeLabel} • ${yearLabel}`;
    empty.hidden = visible !== 0;
  }

  pills.forEach(p => p.addEventListener('click', () => {
    pills.forEach(x => x.classList.remove('is-active'));
    p.classList.add('is-active');
    activeDept = p.dataset.dept;
    apply();
  }));

  typeSel.addEventListener('change', apply);
  yearSel.addEventListener('change', apply);
  form.addEventListener('submit', e => { e.preventDefault(); apply(); });

  // initial render (with zero cards, this will show the empty state)
  apply();
})();
</script>

</body>
</html>
