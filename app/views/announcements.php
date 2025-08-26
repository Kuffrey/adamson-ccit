<?php
require_once __DIR__ . '/../models/Announcement.php';
$annModel = new Announcement();
$announcements = $annModel->listActive(1, 20); // page 1, 20 per page
?>
<main class="container">
  <h1>Announcements</h1>
  <ul class="ann-list">
    <?php if (!$announcements): ?>
      <li class="muted">No announcements at this time.</li>
    <?php else: ?>
      <?php foreach ($announcements as $ann): ?>
        <li>
          <a href="?page=announcement_view&amp;id=<?= htmlspecialchars($ann['id']) ?>">
            <?= htmlspecialchars($ann['title']) ?>
          </a>
          <span class="muted">&mdash; <?= date('M j, Y', strtotime($ann['published_at'])) ?></span>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>
</main>
