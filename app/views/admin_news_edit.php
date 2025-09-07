<?php
// admin_news_edit.php — News CMS CRUD page
require_once __DIR__ . '/../models/News.php';
function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$newsList = class_exists('News') ? News::latest(20) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage News | AdU-CCIT Admin</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css" />
</head>
<body>
<div class="admin-cms-layout">
  <aside class="admin-sb" aria-label="Admin navigation">
    <div class="admin-sb__brand">
      <span class="admin-sb__logo">CCIT</span>
      <span class="admin-sb__title">CMS Admin</span>
    </div>
    <nav class="admin-sb__nav">
      <a href="?page=admin_dashboard" class="nav__link">Dashboard</a>
  <a href="?page=admin_manage_homepage" class="nav__link">Homepage</a>
      <a href="?page=admin_news_edit" class="nav__link is-active">News</a>
      <a href="?page=admin_events_edit" class="nav__link">Events</a>
      <a href="?page=admin_announcements_edit" class="nav__link">Announcements</a>
      <a href="?page=admin_faculty_profile_edit" class="nav__link">Faculty Profile</a>
      <a href="?page=admin_faculty_research_edit" class="nav__link">Faculty Research</a>
      <a href="?page=admin_faculty_certifications_edit" class="nav__link">Faculty Certifications</a>
      <a href="?page=admin_student_testimonials_edit" class="nav__link">Student Testimonials</a>
      <a href="?page=admin_student_research_edit" class="nav__link">Student Research</a>
    </nav>
    <div class="admin-sb__spacer"></div>
    <div class="admin-sb__user">
      <div class="userline">
        <span class="avatar">A</span>
        <div class="uinfo">
          <span class="uname">Admin</span>
          <span class="urole">Administrator</span>
        </div>
      </div>
      <a class="btn btn--muted btn--sm" href="?page=logout">Logout</a>
    </div>
  </aside>
  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Manage News</span>
    </header>
    <section class="admin-dashboard__content container">
      <h2>News <a href="?page=admin_news_add" class="btn btn--solid btn--sm" style="float:right;">+ Add News</a></h2>
      <table class="admin-table" style="width:100%;margin-bottom:18px;">
        <thead>
          <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($newsList):
            foreach ($newsList as $news): ?>
              <tr>
                <td><?= esc($news['title'] ?? 'Untitled') ?></td>
                <td><?= esc(ucfirst($news['category'] ?? '')) ?></td>
                <td><?= esc($news['date'] ?? '') ?></td>
                <td><?= !empty($news['published_at']) ? 'Published' : 'Draft' ?></td>
                <td>
                  <a class="btn btn--muted btn--sm" href="?page=admin_news_edit&id=<?= esc($news['id']) ?>">Edit</a>
                  <a class="btn btn--muted btn--sm" href="?page=admin_news_delete&id=<?= esc($news['id']) ?>" onclick="return confirm('Delete this news item?');">Delete</a>
                </td>
              </tr>
            <?php endforeach;
          else: ?>
            <tr><td colspan="5" style="text-align:center;">No news articles found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </main>
</div>
</body>
</html>
