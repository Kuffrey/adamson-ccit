<?php /* student_testimonials.php — Student Testimonials (uses global styles) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Testimonials | AdU-CCIT</title>

  <!-- Global site CSS -->
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>

  <!-- Page-specific CSS -->
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/student-testimonials.css"/>
</head>
<body>

<main class="page-testi">

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Students</p>
      <h1 class="subhero__title">Testimonials</h1>
      <p class="subhero__lead">Stories from CCIT students and alumni—internships, certifications, and early careers.</p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (Students) ============ -->
  <nav class="subnav" aria-label="Students sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li><a href="/adamson-ccit/public/index.php?page=student_organizations">Organizations</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_scholarships">Scholarships</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_research">Research</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_certifications">Certifications</a></li>
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=student_testimonials" aria-current="page">Testimonials</a></li>
      </ul>
    </div>
  </nav>

  <!-- ============ FILTER BAR ============ -->
  <section class="tbar section-sep" aria-labelledby="filter-head">
    <div class="container tbar__inner">
      <h2 id="filter-head" class="sr-only">Filter testimonials</h2>

      <div class="tbar__left">
        <div class="tpills" role="tablist" aria-label="Filter by program">
          <button class="pill is-active" data-prog="all" role="tab" aria-selected="true">All</button>
          <button class="pill" data-prog="bscs" role="tab">BSCS</button>
          <button class="pill" data-prog="bsit" role="tab">BSIT</button>
          <button class="pill" data-prog="bsis" role="tab">BSIS</button>
          <button class="pill" data-prog="grad" role="tab">Graduate</button>
        </div>

        <label class="tyear">
          <span class="sr-only">Filter by year</span>
          <select id="tYear" aria-label="Filter by year">
            <option value="all">All Years</option>
            <option>2025</option>
            <option>2024</option>
          </select>
        </label>
      </div>

      <form class="tsearch" role="search" aria-label="Search testimonials">
        <input id="tQuery" type="search" placeholder="Search names, roles, keywords…" aria-label="Search testimonials"/>
        <button class="btn btn--solid" type="submit">Search</button>
      </form>
    </div>
  </section>

  <!-- ============ TESTIMONIALS GRID ============ -->
  <section class="content tlist" aria-labelledby="tlist-head">
    <div class="container">
      <div id="tCount" class="tcount">Showing all testimonials</div>

      <div id="tGrid" class="tgrid">
        <!-- SAMPLE ITEMS — replace with CMS/PHP loop later -->

        <article class="t" data-prog="bsit" data-year="2025">
          <header class="t__head">
            <div class="t__avatar t__avatar--ph" aria-hidden="true">JD</div>
            <!-- If you have a photo, replace the div above with:
            <img class="t__avatar" src="/adamson-ccit/public/assets/images/students/jane.jpg" alt="Portrait of Jane Dela Cruz"> -->
            <div class="t__meta">
              <h3 class="t__name">Jane Dela Cruz</h3>
              <div class="t__row">
                <span class="ttag">BSIT ’25</span>
                <span class="t__sep">•</span>
                <span class="t__role">Software Engineering Intern — FinTechPH</span>
              </div>
            </div>
          </header>
          <blockquote class="t__quote">
            Hands-on labs prepared me well. I shipped features in my second week and passed IT Specialist — Networking on my first try.
          </blockquote>
        </article>

        <article class="t" data-prog="bscs" data-year="2024">
          <header class="t__head">
            <div class="t__avatar t__avatar--ph" aria-hidden="true">MS</div>
            <div class="t__meta">
              <h3 class="t__name">Mark Santos</h3>
              <div class="t__row">
                <span class="ttag">BSCS ’24</span>
                <span class="t__sep">•</span>
                <span class="t__role">Junior Developer — DevWorks</span>
              </div>
            </div>
          </header>
          <blockquote class="t__quote">
            Our capstone and algorithms track gave me the confidence to tackle production code. The culture pushed me to keep learning.
          </blockquote>
        </article>

        <article class="t" data-prog="bsis" data-year="2025">
          <header class="t__head">
            <div class="t__avatar t__avatar--ph" aria-hidden="true">BL</div>
            <div class="t__meta">
              <h3 class="t__name">Bea Lim</h3>
              <div class="t__row">
                <span class="ttag">BSIS ’25</span>
                <span class="t__sep">•</span>
                <span class="t__role">Business Analyst Intern — RetailHub</span>
              </div>
            </div>
          </header>
          <blockquote class="t__quote">
            The analytics focus and casework translated directly to my internship. I also cleared the IT Specialist — Databases exam.
          </blockquote>
        </article>

        <article class="t" data-prog="grad" data-year="2024">
          <header class="t__head">
            <div class="t__avatar t__avatar--ph" aria-hidden="true">RA</div>
            <div class="t__meta">
              <h3 class="t__name">Ramon Alvarez</h3>
              <div class="t__row">
                <span class="ttag">MIT (Graduate)</span>
                <span class="t__sep">•</span>
                <span class="t__role">IT Manager — HealthTech</span>
              </div>
            </div>
          </header>
          <blockquote class="t__quote">
            The graduate coursework sharpened my leadership and security foundations. It’s been a big step for my team and career.
          </blockquote>
        </article>

      </div>

      <!-- Pagination placeholder -->
      <nav class="pager" aria-label="Testimonials pagination">
        <button class="pg" disabled>« Prev</button>
        <span class="pg__status">Page 1 of 1</span>
        <button class="pg" disabled>Next »</button>
      </nav>

      <div id="tEmpty" class="tempty" hidden>No testimonials match your filters.</div>
    </div>
  </section>

</main>

<!-- ============ FILTER JS (client-side) ============ -->
<script>
(function(){
  const pills = Array.from(document.querySelectorAll('.tpills .pill'));
  const yearSel = document.getElementById('tYear');
  const qInput = document.getElementById('tQuery');
  const form = document.querySelector('.tsearch');

  const grid = document.getElementById('tGrid');
  const cards = Array.from(grid.querySelectorAll('.t'));
  const count = document.getElementById('tCount');
  const empty = document.getElementById('tEmpty');

  let activeProg = 'all';

  function apply(){
    const q = (qInput.value || '').trim().toLowerCase();
    const y = yearSel.value; // 'all' or year
    let visible = 0;

    cards.forEach(card => {
      const prog = (card.getAttribute('data-prog') || '').toLowerCase();
      const year = (card.getAttribute('data-year') || '');
      const text = card.innerText.toLowerCase();

      let ok = true;
      if (activeProg !== 'all' && prog !== activeProg) ok = false;
      if (ok && y !== 'all' && year !== y) ok = false;
      if (ok && q && !text.includes(q)) ok = false;

      card.style.display = ok ? '' : 'none';
      if (ok) visible++;
    });

    const progLabel = activeProg === 'all'
      ? 'All programs'
      : activeProg.toUpperCase();
    const yearLabel = y === 'all' ? 'all years' : y;

    count.textContent = `Showing ${visible} testimonial${visible!==1?'s':''} • ${progLabel} • ${yearLabel}`;
    empty.hidden = visible !== 0;
  }

  pills.forEach(p => p.addEventListener('click', () => {
    pills.forEach(x => x.classList.remove('is-active'));
    p.classList.add('is-active');
    activeProg = p.dataset.prog;
    apply();
  }));

  yearSel.addEventListener('change', apply);
  form.addEventListener('submit', e => { e.preventDefault(); apply(); });

  apply();
})();
</script>

</body>
</html>
