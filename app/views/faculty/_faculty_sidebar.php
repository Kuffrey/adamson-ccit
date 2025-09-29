<?php
if (!function_exists('esc')) {
  function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user     = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? 'Faculty';
$roleName = ucfirst($user['role'] ?? 'Faculty');

$pg = $_GET['page'] ?? '';
?>

<aside class="admin-sb" aria-label="Faculty navigation">
  <div class="admin-sb__brand">
    <span class="admin-sb__logo">CCIT</span>
    <span class="admin-sb__title">CMS Faculty</span>
  </div>

  <nav class="admin-sb__nav admin-sb__nav--main">
    <a href="?page=faculty_dashboard"
       class="nav__link<?= $pg === 'faculty_dashboard' ? ' is-active' : '' ?>"
       <?= $pg === 'faculty_dashboard' ? 'aria-current="page"' : '' ?>>Dashboard</a>

    <a href="?page=faculty_manage_research"
       class="nav__link<?= $pg === 'faculty_manage_research' ? ' is-active' : '' ?>"
       <?= $pg === 'faculty_manage_research' ? 'aria-current="page"' : '' ?>>Submit Research</a>

    <a href="?page=faculty_manage_certifications"
       class="nav__link<?= $pg === 'faculty_manage_certifications' ? ' is-active' : '' ?>"
       <?= $pg === 'faculty_manage_certifications' ? 'aria-current="page"' : '' ?>>Submit Certifications</a>

    <a href="?page=faculty_portfolio"
      class="nav__link<?= $pg === 'faculty_portfolio' ? ' is-active' : '' ?>"
      <?= $pg === 'faculty_portfolio' ? 'aria-current="page"' : '' ?>>Portfolio</a>
  </nav>

  <div class="admin-sb__spacer"></div>

  <div class="admin-sb__user">
    <div class="userline">
      <span class="avatar"><?= esc(strtoupper($username[0] ?? 'F')) ?></span>
      <div class="uinfo">
        <span class="uname"><?= esc($username) ?></span>
        <span class="urole"><?= esc($roleName) ?></span>
      </div>
    </div>
    <a class="btn btn--muted btn--sm" href="?page=logout">Logout</a>
  </div>
</aside>
