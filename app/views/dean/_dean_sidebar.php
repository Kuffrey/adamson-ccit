<?php
if (!function_exists('esc')) {
  function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user     = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? 'Dean';
$roleName = ucfirst($user['role'] ?? 'Dean');

$pg = $_GET['page'] ?? '';

$newsOpen  = in_array($pg, ['dean_news_settings', 'dean_manage_news', 'dean_manage_events', 'dean_manage_announcements'], true) ? ' open' : '';
$facultyOpen = in_array($pg, ['dean_manage_faculty_profiles', 'dean_manage_faculty_research', 'dean_manage_faculty_certifications', 'dean_manage_faculty_portfolio'], true) ? ' open' : '';
?>

<aside class="admin-sb" aria-label="Dean navigation">
  <div class="admin-sb__brand">
    <span class="admin-sb__logo">CCIT</span>
    <span class="admin-sb__title">CMS Dean</span>
  </div>

  <nav class="admin-sb__nav admin-sb__nav--main">
    <a href="?page=dean_dashboard"
       class="nav__link<?= $pg === 'dean_dashboard' ? ' is-active' : '' ?>"
       <?= $pg === 'dean_dashboard' ? 'aria-current="page"' : '' ?>>Dashboard</a>

    <!-- News -->
    <div class="nav__dropdown<?= $newsOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $newsOpen ? 'true' : 'false' ?>" aria-controls="submenu-news">
        News & Events <span class="caret" aria-hidden="true">▾</span>
      </button>
      <div id="submenu-news" class="nav__submenu">
        <a href="?page=dean_manage_news"
           class="nav__sublink<?= $pg === 'dean_manage_news' ? ' is-active' : '' ?>"
           <?= $pg === 'dean_manage_news' ? 'aria-current="page"' : '' ?>>Manage News</a>
        <a href="?page=dean_manage_events"
           class="nav__sublink<?= $pg === 'dean_manage_events' ? ' is-active' : '' ?>"
           <?= $pg === 'dean_manage_events' ? 'aria-current="page"' : '' ?>>Manage Events</a>
        <a href="?page=dean_manage_announcements"
           class="nav__sublink<?= $pg === 'dean_manage_announcements' ? ' is-active' : '' ?>"
           <?= $pg === 'dean_manage_announcements' ? 'aria-current="page"' : '' ?>>Manage Announcements</a>
      </div>
    </div>

    <!-- Faculty -->
    <div class="nav__dropdown<?= $facultyOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $facultyOpen ? 'true' : 'false' ?>" aria-controls="submenu-faculty">
        Faculty Management <span class="caret" aria-hidden="true">▾</span>
      </button>
      <div id="submenu-faculty" class="nav__submenu">
        <a href="?page=dean_manage_faculty_profiles"
           class="nav__sublink<?= $pg === 'dean_manage_faculty_profiles' ? ' is-active' : '' ?>"
           <?= $pg === 'dean_manage_faculty_profiles' ? 'aria-current="page"' : '' ?>>Manage Faculty Profiles</a>
        <a href="?page=dean_manage_faculty_research"
           class="nav__sublink<?= $pg === 'dean_manage_faculty_research' ? ' is-active' : '' ?>"
           <?= $pg === 'dean_manage_faculty_research' ? 'aria-current="page"' : '' ?>>Manage Faculty Research</a>
        <a href="?page=dean_manage_faculty_certifications"
           class="nav__sublink<?= $pg === 'dean_manage_faculty_certifications' ? ' is-active' : '' ?>"
           <?= $pg === 'dean_manage_faculty_certifications' ? 'aria-current="page"' : '' ?>>Manage Faculty Certifications</a>
        <a href="?page=dean_manage_faculty_portfolio"
           class="nav__sublink<?= $pg === 'dean_manage_faculty_portfolio' ? ' is-active' : '' ?>"
           <?= $pg === 'dean_manage_faculty_portfolio' ? 'aria-current="page"' : '' ?>>Manage Faculty Portfolio</a>
      </div>
    </div>
  </nav>

  <div class="admin-sb__spacer"></div>

  <div class="admin-sb__user">
    <div class="userline">
      <span class="avatar"><?= esc(strtoupper($username[0] ?? 'D')) ?></span>
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
