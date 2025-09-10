<?php
if (!function_exists('esc')) {
  function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
$user     = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? 'Admin';
$roleName = ucfirst($user['role'] ?? 'Administrator');

$pg = $_GET['page'] ?? '';

$aboutOpen    = in_array($pg, ['admin_manage_about','admin_about_vision_mission'], true) ? ' open' : '';
$newsOpen     = in_array($pg, ['admin_news_settings','admin_manage_news','admin_manage_events','admin_manage_announcements'], true) ? ' open' : '';
$admitOpen    = in_array($pg, ['admin_admission_freshman','admin_admission_transferee','admin_admission_graduate'], true) ? ' open' : '';
$programsOpen = in_array($pg, ['admin_manage_programs','admin_programs_undergraduate','admin_programs_graduate'], true) ? ' open' : '';
?>
<aside class="admin-sb" aria-label="Admin navigation">
  <div class="admin-sb__brand">
    <span class="admin-sb__logo">CCIT</span>
    <span class="admin-sb__title">CMS Admin</span>
  </div>

  <nav class="admin-sb__nav admin-sb__nav--main">
    <a href="?page=admin_dashboard"
       class="nav__link<?= $pg === 'admin_dashboard' ? ' is-active' : '' ?>"
       <?= $pg === 'admin_dashboard' ? 'aria-current="page"' : '' ?>>Dashboard</a>

    <a href="?page=admin_manage_homepage"
       class="nav__link<?= $pg === 'admin_manage_homepage' ? ' is-active' : '' ?>"
       <?= $pg === 'admin_manage_homepage' ? 'aria-current="page"' : '' ?>>Homepage</a>

    <!-- About -->
    <div class="nav__dropdown<?= $aboutOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $aboutOpen ? 'true' : 'false' ?>" aria-controls="submenu-about">
        About <span class="caret" aria-hidden="true">▾</span>
      </button>
      <div id="submenu-about" class="nav__submenu">
        <a href="?page=admin_manage_about"
           class="nav__sublink<?= $pg === 'admin_manage_about' ? ' is-active' : '' ?>"
           <?= $pg === 'admin_manage_about' ? 'aria-current="page"' : '' ?>>About Page (History)</a>
        <a href="?page=admin_about_vision_mission"
           class="nav__sublink<?= $pg === 'admin_about_vision_mission' ? ' is-active' : '' ?>"
           <?= $pg === 'admin_about_vision_mission' ? 'aria-current="page"' : '' ?>>Vision &amp; Mission</a>
      </div>
    </div>

    <!-- News -->
    <div class="nav__dropdown<?= $newsOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $newsOpen ? 'true' : 'false' ?>" aria-controls="submenu-news">
        News <span class="caret" aria-hidden="true">▾</span>
      </button>
      <div id="submenu-news" class="nav__submenu">
        <a href="?page=admin_news_settings"
           class="nav__sublink<?= $pg === 'admin_news_settings' ? ' is-active' : '' ?>"
           <?= $pg === 'admin_news_settings' ? 'aria-current="page"' : '' ?>>News Page Settings</a>
        <a href="?page=admin_manage_news"
           class="nav__sublink<?= $pg === 'admin_manage_news' ? ' is-active' : '' ?>"
           <?= $pg === 'admin_manage_news' ? 'aria-current="page"' : '' ?>>Manage News</a>
        <a href="?page=admin_manage_events"
           class="nav__sublink<?= $pg === 'admin_manage_events' ? ' is-active' : '' ?>"
           <?= $pg === 'admin_manage_events' ? 'aria-current="page"' : '' ?>>Manage Events</a>
        <a href="?page=admin_manage_announcements"
           class="nav__sublink<?= $pg === 'admin_manage_announcements' ? ' is-active' : '' ?>"
           <?= $pg === 'admin_manage_announcements' ? 'aria-current="page"' : '' ?>>Manage Announcements</a>
      </div>
    </div>

    <!-- Admission -->
    <div class="nav__dropdown<?= $admitOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $admitOpen ? 'true' : 'false' ?>" aria-controls="submenu-admission">
        Admission <span class="caret" aria-hidden="true">▾</span>
      </button>
      <div id="submenu-admission" class="nav__submenu">
        <a href="?page=admin_admission_freshman"
           class="nav__sublink<?= $pg==='admin_admission_freshman' ? ' is-active' : '' ?>"
           <?= $pg==='admin_admission_freshman' ? 'aria-current="page"' : '' ?>>Freshman</a>
        <a href="?page=admin_admission_transferee"
           class="nav__sublink<?= $pg==='admin_admission_transferee' ? ' is-active' : '' ?>"
           <?= $pg==='admin_admission_transferee' ? 'aria-current="page"' : '' ?>>Transferee</a>
        <a href="?page=admin_admission_graduate"
           class="nav__sublink<?= $pg==='admin_admission_graduate' ? ' is-active' : '' ?>"
           <?= $pg==='admin_admission_graduate' ? 'aria-current="page"' : '' ?>>Graduate School &amp; JD</a>
      </div>
    </div>

    <!-- Programs -->
    <div class="nav__dropdown<?= $programsOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $programsOpen ? 'true' : 'false' ?>" aria-controls="submenu-programs">
        Programs <span class="caret" aria-hidden="true">▾</span>
      </button>
      <div id="submenu-programs" class="nav__submenu">
        <a href="?page=admin_manage_programs"
           class="nav__sublink<?= $pg==='admin_manage_programs' ? ' is-active' : '' ?>"
           <?= $pg==='admin_manage_programs' ? 'aria-current="page"' : '' ?>>All Programs</a>
        <a href="?page=admin_programs_undergraduate"
           class="nav__sublink<?= $pg==='admin_programs_undergraduate' ? ' is-active' : '' ?>"
           <?= $pg==='admin_programs_undergraduate' ? 'aria-current="page"' : '' ?>>Undergraduate</a>
        <a href="?page=admin_programs_graduate"
           class="nav__sublink<?= $pg==='admin_programs_graduate' ? ' is-active' : '' ?>"
           <?= $pg==='admin_programs_graduate' ? 'aria-current="page"' : '' ?>>Graduate Studies</a>
      </div>
    </div>

    <a href="?page=admin_manage_student"
       class="nav__link<?= $pg === 'admin_manage_student' ? ' is-active' : '' ?>"
       <?= $pg === 'admin_manage_student' ? 'aria-current="page"' : '' ?>>Student</a>

    <a href="?page=admin_manage_faculty"
       class="nav__link<?= $pg === 'admin_manage_faculty' ? ' is-active' : '' ?>"
       <?= $pg === 'admin_manage_faculty' ? 'aria-current="page"' : '' ?>>Faculty</a>
  </nav>

  <div class="admin-sb__spacer"></div>

  <div class="admin-sb__user">
    <div class="userline">
      <span class="avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
      <div class="uinfo">
        <span class="uname"><?= esc($username) ?></span>
        <span class="urole"><?= esc($roleName) ?></span>
      </div>
    </div>
    <a class="btn btn--muted btn--sm" href="?page=logout">Logout</a>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".nav__toggle").forEach(btn => {
      btn.addEventListener("click", () => {
        const dd = btn.parentElement;
        const open = dd.classList.toggle("open");
        btn.setAttribute("aria-expanded", open ? "true" : "false");
      });
    });
    document.querySelectorAll(".nav__dropdown").forEach(dd => {
      if (dd.querySelector(".nav__sublink.is-active")) {
        dd.classList.add("open");
        dd.querySelector(".nav__toggle")?.setAttribute("aria-expanded", "true");
      }
    });
  });
  </script>
</aside>
