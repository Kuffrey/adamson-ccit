<?php
require_once __DIR__ . '/../../app/lib/Auth.php';
$user = Auth::user();
?>
<main class="page-student">
  <section class="content">
    <div class="container">
      <h2>Student Profile &amp; Portfolio</h2>
      <p>Welcome, <strong><?= htmlspecialchars($user['username'] ?? 'Student') ?></strong>.</p>

      <div class="card">
        <h3>Credly Badges</h3>
        <p>Connect your Credly account to display verified badges.</p>
        <!-- placeholder / future: embed or link -->
        <a class="btn btn--outline-blue" href="#">Connect Credly</a>
      </div>

      <div class="card">
        <h3>Career Pathway Results</h3>
        <p>Save and view your latest generator results here.</p>
        <a class="btn btn--solid" href="/adamson-ccit/public/index.php?page=career_pathway_generator">Open Generator</a>
      </div>

      <div class="card">
        <h3>Documents</h3>
        <p>Upload certifications or supporting documents.</p>
        <a class="btn btn--outline-blue" href="/adamson-ccit/public/index.php?page=student_settings">Manage Profile</a>
      </div>
    </div>
  </section>
</main>
