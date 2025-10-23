<?php
// faculty_profile.php — Dynamic Faculty & Staff (CMS-driven)
require_once __DIR__ . '/../models/FacultyProfilePageSettings.php';
require_once __DIR__ . '/../models/FacultyProfile.php';
$settings = FacultyProfilePageSettings::getSettings();
$faculty = FacultyProfile::getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Faculty Profile | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/faculty.css"/>
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
      <h1 class="subhero__title">Faculty Profile</h1>
      <p class="hero__lead"><?= htmlspecialchars($settings['subhero_lead'] ?? 'College of Computing & Information Technology — administration and faculty roster.') ?></p>
    </div>
  </section>
  <!-- ============ LOCAL SUBNAV ============ -->
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
  <section class="nbar">
    <div class="container nbar__inner">
      <div class="nbar__left">
        <div class="ncats" role="tablist" aria-label="Filter by department">
          <button class="pill is-active" data-dept="all" role="tab" aria-selected="true">All</button>
          <button class="pill" data-dept="admin" role="tab">Administration</button>
          <button class="pill" data-dept="itis" role="tab">IT&amp;IS</button>
          <button class="pill" data-dept="cs" role="tab">CS</button>
          <button class="pill" data-dept="mit" role="tab">MIT</button>
        </div>
        
        <form class="nyear" method="get" action="#" onsubmit="return false;">
          <select id="fRole" aria-label="Filter by role">
            <option value="all">All Roles</option>
            <option value="dean">Dean</option>
            <option value="chair">Chairperson</option>
            <option value="full">Full-Time Faculty</option>
            <option value="part">Part-Time Faculty</option>
            <option value="lecturer">Special Lecturer</option>
          </select>
        </form>
      </div>
      
      <form class="nsearch" role="search" aria-label="Search directory">
        <input id="fQuery" type="search" placeholder="Search…" aria-label="Search directory"/>
        <button class="btn btn--solid" type="submit"></button>
      </form>
    </div>
  </section>
  <!-- ============ DIRECTORY ============ -->
  <section class="rlist">
    <div class="container">
      <div id="fCount" class="rcount">Showing all people</div>
      <div id="fGrid" class="cards">
        <?php if (empty($faculty)): ?>
          <!-- Fallback static content -->
          <article class="f" data-dept="admin" data-role="dean">
            <img class="f__avatar" src="/adamson-ccit/public/assets/images/avatar-placeholder.jpg" alt="Portrait of Dr. Sample Dean">
            <div class="f__body">
              <h3 class="f__name">Dr. Maria Santos</h3>
              <p class="f__title">Dean, College of Computing & Information Technology</p>
              <div class="fbadges">
                <span class="fbadge fbadge--dept">Administration</span>
                <span class="fbadge">PhD in Computer Science</span>
              </div>
            </div>
          </article>
        <?php else: ?>
          <?php foreach ($faculty as $f): ?>
          <article class="f" data-dept="<?= htmlspecialchars($f['dept']) ?>" data-role="<?= htmlspecialchars($f['role']) ?>">
            <?php if (!empty($f['avatar_url'])): ?>
              <img class="f__avatar" src="<?= htmlspecialchars($f['avatar_url']) ?>" alt="Portrait of <?= htmlspecialchars($f['name']) ?>" 
                   onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
              <div class="f__avatar f__avatar--ph" style="display:none;" aria-hidden="true">
                <?= htmlspecialchars($f['avatar_initials'] ?? substr($f['name'],0,2)) ?>
              </div>
            <?php else: ?>
              <div class="f__avatar f__avatar--ph" aria-hidden="true">
                <?= htmlspecialchars($f['avatar_initials'] ?? substr($f['name'],0,2)) ?>
              </div>
            <?php endif; ?>
            <div class="f__body">
              <h3 class="f__name"><?= htmlspecialchars($f['name']) ?></h3>
              <div class="fbadges">
                <?php
                  $badges = array_map('trim', explode(',', $f['badges'] ?? ''));
                  foreach ($badges as $b) {
                    if ($b === '') continue;
                    $isDept = in_array($b, ['Administration','IT&IS','CS']);
                    echo '<span class="fbadge'.($isDept?' fbadge--dept':'').'">'.htmlspecialchars($b).'</span>';
                  }
                ?>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      
      <div id="fEmpty" class="nempty" hidden>No people match your filters.</div>
    </div>
  </section>
</main>
<script>
(function(){
  const pills = Array.from(document.querySelectorAll('.ncats .pill'));
  const roleSel = document.getElementById('fRole');
  const qInput = document.getElementById('fQuery');
  const form = document.querySelector('.nsearch');
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
