<?php /* faculty_profile.php — Faculty & Staff (with subnav) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Faculty &amp; Staff | AdU-CCIT</title>

  <!-- Global site CSS -->
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>

  <!-- Page-specific CSS -->
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/faculty.css"/>
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
      <h1 class="subhero__title">Faculty &amp; Staff</h1>
      <p class="subhero__lead">College of Computing &amp; Information Technology — administration and faculty roster.</p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (Faculty) ============ -->
  <nav class="subnav" aria-label="Faculty sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=faculty_profile" aria-current="page">Profile</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=faculty_research">Research</a>
        </li>
        <li>
          <a href="/adamson-ccit/public/index.php?page=faculty_certifications">Certifications</a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- ============ FILTER BAR ============ -->
  <section class="fbar section-sep" aria-labelledby="filt-head">
    <div class="container fbar__inner">
      <h2 id="filt-head" class="sr-only">Filter directory</h2>

      <div class="fbar__left">
        <div class="fpills" role="tablist" aria-label="Filter by department">
          <button class="pill is-active" data-dept="all" role="tab" aria-selected="true">All</button>
          <button class="pill" data-dept="admin" role="tab">Administration</button>
          <button class="pill" data-dept="itis" role="tab">IT&amp;IS</button>
          <button class="pill" data-dept="cs" role="tab">CS</button>
        </div>

        <label class="frole">
          <span class="sr-only">Filter by role</span>
          <select id="fRole" aria-label="Filter by role">
            <option value="all">All Roles</option>
            <option value="dean">Dean</option>
            <option value="chair">Chairperson</option>
            <option value="full">Full-Time Faculty</option>
            <option value="part">Part-Time Faculty</option>
            <option value="lecturer">Special Lecturer</option>
          </select>
        </label>
      </div>

      <form class="fsearch" role="search" aria-label="Search directory">
        <input id="fQuery" type="search" placeholder="Search name, degree, title…" aria-label="Search directory"/>
        <button class="btn btn--solid" type="submit">Search</button>
      </form>
    </div>
  </section>

  <!-- ============ DIRECTORY ============ -->
  <section class="content flist" aria-labelledby="dir-head">
    <div class="container">
      <div id="fCount" class="fcount">Showing all people</div>

      <div id="fGrid" class="fgrid">

        <!-- ===== Administration ===== -->
        <article class="f" data-dept="admin" data-role="dean">
          <div class="f__avatar f__avatar--ph" aria-hidden="true">LA</div>
          <div class="f__body">
            <h3 class="f__name">Dr. Leonard L. Alejandro</h3>
            <p class="f__title">College Dean</p>
            <div class="fbadges">
              <span class="fbadge fbadge--dept">Administration</span>
              <span class="fbadge">Dean</span>
            </div>
          </div>
        </article>

        <article class="f" data-dept="admin" data-role="chair">
          <div class="f__avatar f__avatar--ph" aria-hidden="true">AS</div>
          <div class="f__body">
            <h3 class="f__name">Mr. Archie G. Santiago, MSIT</h3>
            <p class="f__title">Chairperson, IT&amp;IS Department</p>
            <div class="fbadges">
              <span class="fbadge fbadge--dept">Administration</span>
              <span class="fbadge">Chairperson</span>
            </div>
          </div>
        </article>

        <article class="f" data-dept="admin" data-role="chair">
          <div class="f__avatar f__avatar--ph" aria-hidden="true">CN</div>
          <div class="f__body">
            <h3 class="f__name">Ms. Ma. Christina R. Navarro</h3>
            <p class="f__title">Chairperson, CS Department</p>
            <div class="fbadges">
              <span class="fbadge fbadge--dept">Administration</span>
              <span class="fbadge">Chairperson</span>
            </div>
          </div>
        </article>

        <!-- ===== IT&IS — Full-Time ===== -->
        <?php
          $itis_full = [
            ["Mrs. Charlene I. Gonzales-Vergara", "Full-Time Faculty, IT&IS Department"],
            ["Dr. Carmelita H. Benito", "Full-Time Faculty, IT&IS Department"],
            ["Maricel G. Barrameda, MSIT", "Full-Time Faculty, IT&IS Department"],
            ["Marvi A. Bayrante, MSIT", "Full-Time Faculty, IT&IS Department"],
            ["Mark Christopher R. Blanco", "Full-Time Faculty, IT&IS Department"],
            ["Jun O. Bumagat", "Full-Time Faculty, IT&IS Department"],
            ["Anette G. Daligcon, MSIT", "Full-Time Faculty, IT&IS Department"],
            ["Gloria R. Dela Cruz, MSIT, LPT", "Full-Time Faculty, IT&IS Department"],
            ["Mark Anthony J. Esmeralda, MBA", "Full-Time Faculty, IT&IS Department"],
            ["Lesliean U. Latosa", "Full-Time Faculty, IT&IS Department"],
            ["Dr. Jesus S. Paguigan", "Full-Time Faculty, IT&IS Department"],
            ["Ma. Carmela M. Racelis, MSIT", "Full-Time Faculty, IT&IS Department"],
            ["Dr. Nina Ana Marie Jocelyn A. Sales", "Full-Time Faculty, IT&IS Department"],
            ["Dr. Felnita V. Tan", "Full-Time Faculty, IT&IS Department"],
            ["Rizalina C. Valenicia, MIT", "Full-Time Faculty, IT&IS Department"],
            ["Quintina R. Verceles, MIT", "Full-Time Faculty, IT&IS Department"],
          ];
          foreach ($itis_full as $p):
            $initials = preg_replace('/[^A-Z]/', '', mb_strtoupper(mb_substr($p[0],0,1).preg_replace('/.*\s([A-Za-z]).*$/','$1',$p[0])));
        ?>
        <article class="f" data-dept="itis" data-role="full">
          <div class="f__avatar f__avatar--ph" aria-hidden="true"><?= htmlspecialchars($initials) ?></div>
          <div class="f__body">
            <h3 class="f__name"><?= htmlspecialchars($p[0]) ?></h3>
            <p class="f__title"><?= htmlspecialchars($p[1]) ?></p>
            <div class="fbadges">
              <span class="fbadge fbadge--dept">IT&amp;IS</span>
              <span class="fbadge">Full-Time</span>
            </div>
          </div>
        </article>
        <?php endforeach; ?>

        <!-- ===== CS — Full-Time ===== -->
        <?php
          $cs_full = [
            ["Jerome Alvez", "Full-Time Faculty, CS Department"],
            ["Jay A. Abaleta", "Full-Time Faculty, CS Department"],
            ["Paul Jacob C. Cruz", "Full-Time Faculty, CS Department"],
          ];
          foreach ($cs_full as $p):
            $initials = preg_replace('/[^A-Z]/', '', mb_strtoupper(mb_substr($p[0],0,1).preg_replace('/.*\s([A-Za-z]).*$/','$1',$p[0])));
        ?>
        <article class="f" data-dept="cs" data-role="full">
          <div class="f__avatar f__avatar--ph" aria-hidden="true"><?= htmlspecialchars($initials) ?></div>
          <div class="f__body">
            <h3 class="f__name"><?= htmlspecialchars($p[0]) ?></h3>
            <p class="f__title"><?= htmlspecialchars($p[1]) ?></p>
            <div class="fbadges">
              <span class="fbadge fbadge--dept">CS</span>
              <span class="fbadge">Full-Time</span>
            </div>
          </div>
        </article>
        <?php endforeach; ?>

        <!-- ===== CS — Part-Time ===== -->
        <?php
          $cs_part = [
            ["Jessie Alamil", "Part-Time Faculty, CS Department"],
            ["Renato Baisa", "Part-Time Faculty, CS Department"],
            ["Paul Jacob C. Cruz", "Part-Time Faculty, CS Department"],
          ];
          foreach ($cs_part as $p):
            $initials = preg_replace('/[^A-Z]/', '', mb_strtoupper(mb_substr($p[0],0,1).preg_replace('/.*\s([A-Za-z]).*$/','$1',$p[0])));
        ?>
        <article class="f" data-dept="cs" data-role="part">
          <div class="f__avatar f__avatar--ph" aria-hidden="true"><?= htmlspecialchars($initials) ?></div>
          <div class="f__body">
            <h3 class="f__name"><?= htmlspecialchars($p[0]) ?></h3>
            <p class="f__title"><?= htmlspecialchars($p[1]) ?></p>
            <div class="fbadges">
              <span class="fbadge fbadge--dept">CS</span>
              <span class="fbadge">Part-Time</span>
            </div>
          </div>
        </article>
        <?php endforeach; ?>

        <!-- ===== CS — Special Lecturers ===== -->
        <?php
          $cs_lect = [
            ["Davood Pour Yousefian Barfeh", "Special Lecturer, CS Department"],
            ["Billy Jay Angeles", "Special Lecturer, CS Department"],
            ["Carlo Felipe C. Poblete", "Special Lecturer, CS Department"],
            ["Jay A. Abaleta", "Special Lecturer, CS Department"],
            ["Joel R. Hernandez", "Special Lecturer, CS Department"],
          ];
          foreach ($cs_lect as $p):
            $initials = preg_replace('/[^A-Z]/', '', mb_strtoupper(mb_substr($p[0],0,1).preg_replace('/.*\s([A-Za-z]).*$/','$1',$p[0])));
        ?>
        <article class="f" data-dept="cs" data-role="lecturer">
          <div class="f__avatar f__avatar--ph" aria-hidden="true"><?= htmlspecialchars($initials) ?></div>
          <div class="f__body">
            <h3 class="f__name"><?= htmlspecialchars($p[0]) ?></h3>
            <p class="f__title"><?= htmlspecialchars($p[1]) ?></p>
            <div class="fbadges">
              <span class="fbadge fbadge--dept">CS</span>
              <span class="fbadge">Special Lecturer</span>
            </div>
          </div>
        </article>
        <?php endforeach; ?>

      </div>

      <!-- Optional pager (static for now) -->
      <nav class="pager" aria-label="Faculty pagination">
        <button class="pg" disabled>« Prev</button>
        <span class="pg__status">Page 1 of 1</span>
        <button class="pg" disabled>Next »</button>
      </nav>

      <div id="fEmpty" class="fempty" hidden>No people match your filters.</div>
    </div>
  </section>

</main>

<!-- ============ FILTER JS (client-side) ============ -->
<script>
(function(){
  const pills = Array.from(document.querySelectorAll('.fpills .pill'));
  const roleSel = document.getElementById('fRole');
  const qInput = document.getElementById('fQuery');
  const form = document.querySelector('.fsearch');

  const grid = document.getElementById('fGrid');
  const cards = Array.from(grid.querySelectorAll('.f'));
  const count = document.getElementById('fCount');
  const empty = document.getElementById('fEmpty');

  let activeDept = 'all';

  function apply(){
    const q = (qInput.value || '').trim().toLowerCase();
    const r = roleSel.value; // 'all' or role key
    let visible = 0;

    cards.forEach(card => {
      const dept = (card.getAttribute('data-dept') || '').toLowerCase();
      const role = (card.getAttribute('data-role') || '').toLowerCase();
      const text = card.innerText.toLowerCase();

      let ok = true;
      if (activeDept !== 'all' && dept !== activeDept) ok = false;
      if (ok && r !== 'all' && role !== r) ok = false;
      if (ok && q && !text.includes(q)) ok = false;

      card.style.display = ok ? '' : 'none';
      if (ok) visible++;
    });

    const deptLabel = (activeDept === 'all') ? 'All departments'
                     : activeDept === 'admin' ? 'Administration'
                     : activeDept.toUpperCase();
    const roleLabel = (r === 'all')
                     ? 'all roles'
                     : (r === 'full' ? 'Full-Time' :
                        r === 'part' ? 'Part-Time' :
                        r === 'lecturer' ? 'Special Lecturer' :
                        r === 'chair' ? 'Chairperson' : 'Dean');

    count.textContent = `Showing ${visible} person${visible!==1?'s':''} • ${deptLabel} • ${roleLabel}`;
    empty.hidden = visible !== 0;
  }

  pills.forEach(p => p.addEventListener('click', () => {
    pills.forEach(x => x.classList.remove('is-active'));
    p.classList.add('is-active');
    activeDept = p.dataset.dept;
    apply();
  }));

  roleSel.addEventListener('change', apply);
  form.addEventListener('submit', e => { e.preventDefault(); apply(); });

  apply();
})();
</script>

</body>
</html>
