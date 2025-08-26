<?php /* faculty_certifications.php — Faculty Certifications (draft, no data yet) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Faculty Certifications | AdU-CCIT</title>

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
      <h1 class="subhero__title">Faculty Certifications</h1>
      <p class="subhero__lead">Professional badges, licenses, and industry certifications held by CCIT faculty.</p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (Faculty) ============ -->
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

  <!-- ============ FILTER BAR (reuses .rbar styles) ============ -->
  <section class="rbar section-sep" aria-labelledby="fc-head">
    <div class="container rbar__inner">
      <h2 id="fc-head" class="sr-only">Filter faculty certifications</h2>

      <div class="rbar__left">
        <div class="tpills" role="tablist" aria-label="Filter by department">
          <button class="pill is-active" data-dept="all" role="tab" aria-selected="true">All</button>
          <button class="pill" data-dept="itis" role="tab">IT&amp;IS</button>
          <button class="pill" data-dept="cs" role="tab">CS</button>
        </div>

        <label class="ryear">
          <span class="sr-only">Filter by issuer</span>
          <select id="cIssuer" aria-label="Filter by issuer">
            <option value="all">All Issuers</option>
            <option value="aws">AWS</option>
            <option value="cisco">Cisco</option>
            <option value="microsoft">Microsoft</option>
            <option value="comptia">CompTIA</option>
            <option value="google">Google</option>
            <option value="oracle">Oracle</option>
            <option value="redhat">Red Hat</option>
            <option value="ec-council">EC-Council</option>
            <option value="itil">ITIL</option>
            <option value="pmi">PMI</option>
            <option value="scrum">Scrum</option>
            <option value="adobe">Adobe</option>
            <option value="other">Other</option>
          </select>
        </label>

        <label class="ryear">
          <span class="sr-only">Filter by year</span>
          <select id="cYear" aria-label="Filter by year">
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

      <form class="rsearch" role="search" aria-label="Search certifications">
        <input id="cQuery" type="search" placeholder="Search certification, faculty, issuer…" aria-label="Search certifications"/>
        <button class="btn btn--solid" type="submit">Search</button>
      </form>
    </div>
  </section>

  <!-- ============ LIST ============ -->
  <section class="content rlist" aria-labelledby="fc-list">
    <div class="container">
      <div id="cCount" class="rcount">Showing 0 certifications</div>

      <!-- Grid: reuse .cards layout + .r card styling -->
      <div id="cGrid" class="cards">

        <!--
        SAMPLE CARD (copy when you have data; then remove this comment)
        <article class="r" data-dept="itis" data-issuer="aws" data-year="2024">
          <a class="r__media" href="#" aria-hidden="true">
            <img src="/adamson-ccit/public/assets/images/placeholder-16x9.jpg" alt="">
            <span class="chip chip--green">Active</span>
          </a>
          <div class="r__body">
            <h3 class="r__title"><a href="#">AWS Certified Solutions Architect – Associate</a></h3>
            <p class="r__meta">
              <strong>Dr. Jane Q. Faculty</strong> • Issuer: AWS • Earned: 2024 • Expires: 2027
            </p>
            <div class="r__actions">
              <a class="btn btn--outline-blue" href="#" target="_blank" rel="noopener">Verify</a>
              <a class="btn btn--solid" href="#">View Details</a>
            </div>
          </div>
        </article>
        -->

      </div>

      <!-- Pager (static for now) -->
      <nav class="pager" aria-label="Certifications pagination">
        <button class="pg" disabled>« Prev</button>
        <span class="pg__status">Page 1 of 1</span>
        <button class="pg" disabled>Next »</button>
      </nav>

      <div id="cEmpty" class="nempty">No faculty certifications are posted yet. This directory will be updated once submissions are compiled.</div>
    </div>
  </section>

</main>

<!-- ============ FILTER JS (client-side) ============ -->
<script>
(function(){
  const pills   = Array.from(document.querySelectorAll('.tpills .pill'));
  const issuer  = document.getElementById('cIssuer');
  const yearSel = document.getElementById('cYear');
  const qInput  = document.getElementById('cQuery');
  const form    = document.querySelector('.rsearch');

  const grid  = document.getElementById('cGrid');
  const cards = Array.from(grid.querySelectorAll('.r'));
  const count = document.getElementById('cCount');
  const empty = document.getElementById('cEmpty');

  let activeDept = 'all';

  function apply(){
    const q = (qInput.value || '').trim().toLowerCase();
    const iss = issuer.value;
    const y = yearSel.value;

    let visible = 0;

    cards.forEach(card => {
      const dept = (card.getAttribute('data-dept') || '').toLowerCase();
      const ci   = (card.getAttribute('data-issuer') || '').toLowerCase();
      const cy   = (card.getAttribute('data-year') || '').toLowerCase();
      const text = card.innerText.toLowerCase();

      let ok = true;
      if (activeDept !== 'all' && dept !== activeDept) ok = false;
      if (ok && iss !== 'all' && ci !== iss) ok = false;
      if (ok && y !== 'all' && cy !== y) ok = false;
      if (ok && q && !text.includes(q)) ok = false;

      card.style.display = ok ? '' : 'none';
      if (ok) visible++;
    });

    const deptLabel = (activeDept === 'all') ? 'All departments' : activeDept.toUpperCase();
    const issuerLabel = (iss === 'all') ? 'all issuers' : issuer.options[issuer.selectedIndex].text;
    const yearLabel = (y === 'all') ? 'all years' : y;

    count.textContent = `Showing ${visible} certification${visible!==1?'s':''} • ${deptLabel} • ${issuerLabel} • ${yearLabel}`;
    empty.hidden = visible !== 0;
  }

  pills.forEach(p => p.addEventListener('click', () => {
    pills.forEach(x => x.classList.remove('is-active'));
    p.classList.add('is-active');
    activeDept = p.dataset.dept;
    apply();
  }));

  issuer.addEventListener('change', apply);
  yearSel.addEventListener('change', apply);
  form.addEventListener('submit', e => { e.preventDefault(); apply(); });

  // Initial render (with zero cards, this shows the empty state)
  apply();
})();
</script>

</body>
</html>
