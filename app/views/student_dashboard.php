<?php
// Optional-login site: only gate this page, not the whole site.
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'student') {
  header('Location: /adamson-ccit/public/index.php?page=login&next=' . urlencode($_SERVER['REQUEST_URI']));
  exit;
}
$u = $_SESSION['user'];
?>
<main class="page-student">
  <section class="content">
    <div class="container">
      <div class="sec__head">
        <h2>Student Dashboard</h2>
        <p class="sec__kicker">Welcome, <?= htmlspecialchars($u['username']) ?>!</p>
      </div>

      <div class="prog__grid">
        <article class="prog__card">
          <div class="prog__head"><span class="badge">Portfolio</span><h3 class="prog__title">Profile &amp; Documents</h3></div>
          <p class="prog__summary">Upload certifications and documents. Build your portfolio page.</p>
          <div class="prog__footer">
            <a class="btn btn--solid" href="/adamson-ccit/public/index.php?page=student_profile">Open Profile</a>
            <div class="mini-links"><a href="/adamson-ccit/public/index.php?page=student_settings">Settings</a></div>
          </div>
        </article>

        <article class="prog__card">
          <div class="prog__head"><span class="badge">Badges</span><h3 class="prog__title">Credly Integration</h3></div>
          <p class="prog__summary">Connect your Credly account to display verified badges on your profile.</p>
          <div class="prog__footer">
            <a class="btn btn--outline-blue" href="/adamson-ccit/public/index.php?page=student_settings#credly">Connect Credly</a>
            <div class="mini-links"><a class="ext" href="https://www.credly.com/">About Credly</a></div>
          </div>
        </article>

        <article class="prog__card">
          <div class="prog__head"><span class="badge">Planner</span><h3 class="prog__title">Career Pathway</h3></div>
          <p class="prog__summary">Run the generator and save your results for advising.</p>
          <div class="prog__footer">
            <a class="btn btn--solid" href="/adamson-ccit/public/index.php?page=career_pathway_generator">Run Generator</a>
            <div class="mini-links"><a href="/adamson-ccit/public/index.php?page=student_career_results">Saved Results</a></div>
          </div>
        </article>
      </div>
    </div>
  </section>
</main>
