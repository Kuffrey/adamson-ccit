<?php
// admin_homepage_edit.php — CMS CRUD for Homepage Content
require_once __DIR__ . '/../models/HomepageSettings.php';
function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Load current settings
$settings = (new HomepageSettings())->get();
$success = false;
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = [
    'hero_title' => trim($_POST['hero_title'] ?? ''),
    'hero_subtitle' => trim($_POST['hero_subtitle'] ?? ''),
    'btn_primary_text' => trim($_POST['btn_primary_text'] ?? ''),
    'btn_primary_url' => trim($_POST['btn_primary_url'] ?? ''),
    'why_title' => trim($_POST['why_title'] ?? ''),
  ];
  if (class_exists('HomepageSettings')) {
    $model = new HomepageSettings();
    if ($model->update($data)) {
      $success = true;
      $settings = $model->get();
    } else {
      $error = 'Failed to update homepage settings.';
    }
  } else {
    $error = 'HomepageSettings model not found.';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Homepage Content | AdU-CCIT Admin</title>
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
      <a href="?page=admin_homepage_edit" class="nav__link is-active">Edit Homepage</a>
      <a href="?page=admin_news_edit" class="nav__link">Edit News</a>
      <a href="?page=admin_events_edit" class="nav__link">Edit Events</a>
      <a href="?page=admin_announcements_edit" class="nav__link">Edit Announcements</a>
      <a href="?page=admin_faculty_profile_edit" class="nav__link">Edit Faculty Profile</a>
      <a href="?page=admin_faculty_research_edit" class="nav__link">Edit Faculty Research</a>
      <a href="?page=admin_faculty_certifications_edit" class="nav__link">Edit Faculty Certifications</a>
      <a href="?page=admin_student_testimonials_edit" class="nav__link">Edit Student Testimonials</a>
      <a href="?page=admin_student_research_edit" class="nav__link">Edit Student Research</a>
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
      <span class="admin-topbar__title">Edit Homepage Content</span>
    </header>
    <section class="admin-dashboard__content container">
      <h2>Edit Homepage Content</h2>
      <?php if ($success): ?>
        <div class="admin-alert admin-alert--success">Homepage content updated successfully.</div>
      <?php elseif ($error): ?>
        <div class="admin-alert admin-alert--error"><?= esc($error) ?></div>
      <?php endif; ?>
      <form method="post" class="admin-form" style="max-width:520px;">
        <label>Hero Title
          <input type="text" name="hero_title" value="<?= esc($settings['hero_title'] ?? '') ?>" required />
        </label>
        <label>Hero Subtitle
          <input type="text" name="hero_subtitle" value="<?= esc($settings['hero_subtitle'] ?? '') ?>" required />
        </label>
        <label>Primary Button Text
          <input type="text" name="btn_primary_text" value="<?= esc($settings['btn_primary_text'] ?? '') ?>" required />
        </label>
        <label>Primary Button URL
          <input type="url" name="btn_primary_url" value="<?= esc($settings['btn_primary_url'] ?? '') ?>" required />
        </label>
        <label>Why CCIT Title
          <input type="text" name="why_title" value="<?= esc($settings['why_title'] ?? '') ?>" required />
        </label>
        <div style="margin-top:18px;display:flex;gap:12px;">
          <button class="btn btn--solid" type="submit">Save Changes</button>
          <a class="btn btn--muted" href="?page=admin_dashboard">Cancel</a>
        </div>
      </form>
    </section>
  </main>
</div>
</body>
</html>
