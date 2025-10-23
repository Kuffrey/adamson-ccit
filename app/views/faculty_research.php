<?php
// faculty_research.php — Dynamic Faculty Research (CMS-driven)
require_once __DIR__ . '/../models/FacultyResearchPageSettings.php';
require_once __DIR__ . '/../models/FacultyResearch.php';
$settings = FacultyResearchPageSettings::getSettings();
$research = FacultyResearch::getAll(); // Fetch all published research dynamically

// Filter out research with "Draft" or other non-published statuses
$research = array_filter($research, function ($r) {
    return isset($r['status']) && strtolower($r['status']) === 'published'; // Ensure only 'published' entries are displayed
});

// Build years array from filtered research data
$years = [];
foreach ($research as $r) {
    if (!empty($r['year'])) {
        $years[] = $r['year'];
    }
}
$years = array_unique($years);
sort($years, SORT_DESC);

// Pagination setup
$page = (int)($_GET['page_num'] ?? 1);
$limit = 10; // Publications per page
$offset = ($page - 1) * $limit;
$totalResearch = count($research);
$totalPages = ceil($totalResearch / $limit);

// Apply pagination
$paginatedResearch = array_slice($research, $offset, $limit);
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

    <div class="container hero__inner">
      <div class="hero__copy">
      <span class="hero__eyebrow">People</span>
      <h1 class="subhero__title">Faculty Research</h1>
      <p class="hero__lead"><?= htmlspecialchars($settings['subhero_lead'] ?? 'Peer-reviewed publications, presentations, and other scholarly work by CCIT faculty.') ?></p>
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
        <input id="fQuery" type="search" placeholder="Search…" aria-label="Search research" />
        <button class="btn btn--solid" type="submit"></button>
      </form>
    </div>
  </section>
  <!-- ============ RESEARCH GRID ============ -->
  <section class="rlist">
    <div class="container">
        <div id="rCount" class="rcount">Showing <?= count($paginatedResearch) ?> of <?= $totalResearch ?> publication<?= $totalResearch !== 1 ? 's' : '' ?></div>
        
        <div id="rGrid" class="research-publications">
            <?php foreach ($paginatedResearch as $r): ?>
                <?php
                $title     = isset($r['title'])     ? $r['title']     : '';
                $authors   = isset($r['authors'])   ? $r['authors']   : '';
                $doi       = isset($r['doi'])       ? $r['doi']       : '';
                $publisher = isset($r['publisher']) ? $r['publisher'] : '';
                $conference= isset($r['conference'])? $r['conference']: '';
                $year      = isset($r['year'])      ? $r['year']      : '';
                $view_url  = isset($r['view_url'])  ? $r['view_url']  : '';
                
                // Determine publication venue (conference or publisher)
                $venue = !empty($conference) ? $conference : $publisher;
                $type = !empty($conference) ? 'Conference' : 'Journal';
                ?>
                <article class="research-item">
                    <h3 class="research-title">
                        <?php if (!empty($view_url)): ?>
                            <a href="<?= htmlspecialchars($view_url) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($title) ?></a>
                        <?php else: ?>
                            <?= htmlspecialchars($title) ?>
                        <?php endif; ?>
                    </h3>
                    <div class="research-authors"><strong>Authors:</strong> <?= htmlspecialchars($authors) ?></div>
                    <div class="research-meta">
                        <?php if (!empty($venue)): ?>
                            <span><strong>Venue:</strong> <?= htmlspecialchars($venue) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($year)): ?>
                            <span><strong>Year:</strong> <?= htmlspecialchars($year) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($doi)): ?>
                        <div class="research-doi">
                            <strong>DOI:</strong>
                            <a href="https://doi.org/<?= htmlspecialchars($doi) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($doi) ?></a>
                        </div>
                    <?php endif; ?>
                    <div class="research-actions">
                        <?php if (!empty($view_url)): ?>
                            <a class="btn-link" href="<?= htmlspecialchars($view_url) ?>" target="_blank" rel="noopener">
                                <i class="fas fa-external-link-alt"></i> View Publication
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($doi)): ?>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-wrapper">
                <nav aria-label="Research pagination">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=faculty_research&page_num=<?= $page - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=faculty_research&page_num=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=faculty_research&page_num=<?= $page + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>

        <div id="rEmpty" class="nempty"<?= count($research) ? ' hidden' : '' ?>>No faculty research entries are available yet.</div>
    </div>
  </section>
</main>
<script>
(function(){
  const yearSel = document.getElementById('fYear');
  const qInput  = document.getElementById('fQuery');
  const form    = document.querySelector('.nsearch');
  const grid  = document.getElementById('rGrid');
  const cards = Array.from(grid.querySelectorAll('.research-item'));
  const count = document.getElementById('rCount');
  const empty = document.getElementById('rEmpty');
  
  function apply(){
    const q = (qInput.value || '').trim().toLowerCase();
    const y = yearSel.value;
    let visible = 0;
    cards.forEach(card => {
      const year = (card.getAttribute('data-year') || '').toLowerCase();
      const text = card.innerText.toLowerCase();
      let ok = true;
      if (y !== 'all' && year !== y) ok = false;
      if (ok && q && !text.includes(q)) ok = false;
      card.style.display = ok ? '' : 'none';
      if (ok) visible++;
    });
    const yearLabel = (y === 'all') ? 'all years' : y;
    count.textContent = `Showing ${visible} publication${visible!==1?'s':''} • ${yearLabel}`;
    empty.hidden = visible !== 0;
  }
  
  yearSel.addEventListener('change', apply);
  form.addEventListener('submit', e => { e.preventDefault(); apply(); });
  apply();
})();
</script>
</body>
</html>

