<?php
// Modular admin sidebar for CMS
if (!function_exists('esc')) {
  function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? 'Admin';

$pg = $_GET['page'] ?? '';

// Helper: mark dropdown open if one of its children is active
$aboutOpen = in_array($pg, ['admin_manage_about','admin_about_vision_mission']) ? ' open' : '';
$newsOpen  = in_array($pg, ['admin_news_settings','admin_manage_news']) ? ' open' : '';
?>
<aside class="admin-sb" aria-label="Admin navigation">
  <div class="admin-sb__brand">
    <span class="admin-sb__logo">CCIT</span>
    <span class="admin-sb__title">CMS Admin</span>
  </div>

  <nav class="admin-sb__nav admin-sb__nav--main">
    <a href="?page=admin_dashboard" class="nav__link<?= $pg === 'admin_dashboard' ? ' is-active' : '' ?>">Dashboard</a>
    <a href="?page=admin_manage_homepage" class="nav__link<?= $pg === 'admin_manage_homepage' ? ' is-active' : '' ?>">Homepage</a>

    <!-- About dropdown -->
    <div class="nav__dropdown<?= $aboutOpen ?>">
      <button class="nav__link nav__toggle" type="button" aria-expanded="<?= $aboutOpen ? 'true' : 'false' ?>" aria-controls="submenu-about">
        About <span class="caret" aria-hidden="true">▾</span>
      </button>
      <div id="submenu-about" class="nav__submenu">
        <a href="?page=admin_manage_about" class="nav__sublink<?= $pg === 'admin_manage_about' ? ' is-active' : '' ?>">About Page (History)</a>
        <a href="?page=admin_about_vision_mission" class="nav__sublink<?= $pg === 'admin_about_vision_mission' ? ' is-active' : '' ?>">Vision &amp; Mission</a>
      </div>
    </div>

    <!-- Dropdown for News -->
    <div class="nav__dropdown<?= in_array(($_GET['page'] ?? ''), [
        'admin_news_settings','admin_manage_news',
        'admin_events_settings','admin_manage_events',
        'admin_announcements_settings','admin_manage_announcements'
    ]) ? ' open' : '' ?>">
      <button class="nav__link nav__toggle">
        News ▾
      </button>
      <div class="nav__submenu">
        <a href="?page=admin_news_settings"
          class="nav__sublink<?= (($_GET['page'] ?? '') === 'admin_news_settings') ? ' is-active' : '' ?>">
          News Page Settings
        </a>
        <a href="?page=admin_manage_news"
          class="nav__sublink<?= (($_GET['page'] ?? '') === 'admin_manage_news') ? ' is-active' : '' ?>">
          Manage News
        </a>

        <a href="?page=admin_manage_events"
          class="nav__sublink<?= (($_GET['page'] ?? '') === 'admin_manage_events') ? ' is-active' : '' ?>">
          Manage Events
        </a>

        <a href="?page=admin_manage_announcements"
          class="nav__sublink<?= (($_GET['page'] ?? '') === 'admin_manage_announcements') ? ' is-active' : '' ?>">
          Manage Announcements
        </a>
      </div>
    </div>

    <a href="?page=admin_manage_admission" class="nav__link<?= $pg === 'admin_manage_admission' ? ' is-active' : '' ?>">Admission</a>
    <a href="?page=admin_manage_programs" class="nav__link<?= $pg === 'admin_manage_programs' ? ' is-active' : '' ?>">Programs</a>
    <a href="?page=admin_manage_student" class="nav__link<?= $pg === 'admin_manage_student' ? ' is-active' : '' ?>">Student</a>
    <a href="?page=admin_manage_faculty" class="nav__link<?= $pg === 'admin_manage_faculty' ? ' is-active' : '' ?>">Faculty</a>
  </nav>

  <div class="admin-sb__spacer"></div>

  <div class="admin-sb__user">
    <div class="userline">
      <span class="avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
      <div class="uinfo">
        <span class="uname"><?= esc($username) ?></span>
        <span class="urole">Administrator</span>
      </div>
    </div>
    <a class="btn btn--muted btn--sm" href="?page=logout">Logout</a>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", () => {
    // Toggle open/close
    document.querySelectorAll(".nav__toggle").forEach(btn => {
      btn.addEventListener("click", () => {
        const dd = btn.parentElement;
        const open = dd.classList.toggle("open");
        btn.setAttribute("aria-expanded", open ? "true" : "false");
      });
    });

    // Auto-open any dropdown that already has an active sublink (server-side also sets this, but this is a safety net)
    document.querySelectorAll(".nav__dropdown").forEach(dd => {
      if (dd.querySelector(".nav__sublink.is-active")) {
        dd.classList.add("open");
        dd.querySelector(".nav__toggle")?.setAttribute("aria-expanded", "true");
      }
    });
  });
  </script>
</aside>
