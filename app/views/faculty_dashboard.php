<?php
require_once __DIR__ . '/../../app/lib/Auth.php';
Auth::requireRole(['faculty'], '/adamson-ccit/public/index.php?page=login_faculty');
$user = Auth::user();
?>
<main>
  <section class="content">
    <h2>Faculty Dashboard</h2>
    <p>Welcome, <?= htmlspecialchars($user['username'] ?? 'Faculty') ?>.</p>
    <ul>
      <li><a href="?page=faculty_manage_research">Manage Research</a></li>
      <li><a href="?page=faculty_manage_certifications">Manage Certifications</a></li>
    </ul>
    <a href="?page=logout">Logout</a>
  </section>
</main>
