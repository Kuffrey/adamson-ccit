<?php
require_once __DIR__ . '/../models/Announcement.php';
$id = (int)($_GET['id'] ?? 0);
$annModel = new Announcement();
$announcement = $id ? $annModel->find($id) : null;
?>
<main class="container">
  <?php if (!$announcement): ?>
    <p class="muted">Announcement not found.</p>
  <?php else: ?>
    <h1><?= htmlspecialchars($announcement['title']) ?></h1>
    <p class="muted">Published: <?= date('M j, Y', strtotime($announcement['published_at'])) ?></p>
    <article>
      <?= nl2br(htmlspecialchars($announcement['body'])) ?>
    </article>
  <?php endif; ?>
</main>
