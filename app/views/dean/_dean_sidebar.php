<?php
if (!function_exists('esc')) {
  function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user     = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? 'Dean';
$roleName = ucfirst($user['role'] ?? 'Dean');

$pg = $_GET['page'] ?? '';

$newsOpen = in_array($pg, ['dean_news_settings', 'dean_manage_news', 'dean_manage_events', 'dean_manage_announcements'], true) ? ' open' : '';
$facultyOpen = in_array($pg, ['dean_manage_faculty_profiles', 'dean_manage_faculty_research'], true) ? ' open' : '';
$approvalsOpen = in_array($pg, ['dean_approvals', 'dean_pending_submissions'], true) ? ' open' : '';
?>

<aside class="admin-sb" aria-label="Dean navigation">
  <div class="admin-sb__brand">
    <span class="admin-sb__logo">CCIT</span>
    <span class="admin-sb__title">Dean Portal</span>
  </div>

  <nav class="admin-sb__nav admin-sb__nav--main">
    <a href="?page=dean_dashboard"
       class="nav__link<?= $pg === 'dean_dashboard' ? ' is-active' : '' ?>"
       <?= $pg === 'dean_dashboard' ? 'aria-current="page"' : '' ?>>
       <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>

    <!-- Faculty Management -->
    <div class="nav__dropdown<?= $facultyOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $facultyOpen ? 'true' : 'false' ?>" aria-controls="submenu-faculty">
        <i class="fas fa-users"></i> Faculty Management <span class="caret" aria-hidden="true">▾</span>
      </button>
      <div id="submenu-faculty" class="nav__submenu">
        <a href="?page=dean_manage_faculty_profiles"
           class="nav__sublink<?= $pg === 'dean_manage_faculty_profiles' ? ' is-active' : '' ?>"
           <?= $pg === 'dean_manage_faculty_profiles' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-user-tie"></i> Faculty Hub
        </a>
        <a href="?page=dean_manage_faculty_research"
           class="nav__sublink<?= $pg === 'dean_manage_faculty_research' ? ' is-active' : '' ?>"
           <?= $pg === 'dean_manage_faculty_research' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-microscope"></i> Research Management
        </a>
      </div>
    </div>

    <!-- Content & News -->
    <div class="nav__dropdown<?= $newsOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $newsOpen ? 'true' : 'false' ?>" aria-controls="submenu-news">
        <i class="fas fa-newspaper"></i> Content Management <span class="caret" aria-hidden="true">▾</span>
      </button>
      <div id="submenu-news" class="nav__submenu">
        <a href="?page=dean_manage_news"
           class="nav__sublink<?= $pg === 'dean_manage_news' ? ' is-active' : '' ?>"
           <?= $pg === 'dean_manage_news' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-newspaper"></i> News Articles
        </a>
        <a href="?page=dean_manage_events"
           class="nav__sublink<?= $pg === 'dean_manage_events' ? ' is-active' : '' ?>"
           <?= $pg === 'dean_manage_events' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-calendar-alt"></i> Events
        </a>
        <a href="?page=dean_manage_announcements"
           class="nav__sublink<?= $pg === 'dean_manage_announcements' ? ' is-active' : '' ?>"
           <?= $pg === 'dean_manage_announcements' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-bullhorn"></i> Announcements
        </a>
      </div>
    </div>

    <!-- Approvals & Reviews -->
    <div class="nav__dropdown<?= $approvalsOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $approvalsOpen ? 'true' : 'false' ?>" aria-controls="submenu-approvals">
        <i class="fas fa-check-circle"></i> Approvals <span class="caret" aria-hidden="true">▾</span>
      </button>
      <div id="submenu-approvals" class="nav__submenu">
        <a href="?page=dean_approvals"
           class="nav__sublink<?= $pg === 'dean_approvals' ? ' is-active' : '' ?>"
           <?= $pg === 'dean_approvals' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-tasks"></i> Pending Approvals
        </a>
        <a href="?page=dean_pending_submissions"
           class="nav__sublink<?= $pg === 'dean_pending_submissions' ? ' is-active' : '' ?>"
           <?= $pg === 'dean_pending_submissions' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-inbox"></i> Submission Queue
        </a>
      </div>
    </div>

    <!-- Activity Logs -->
    <a href="?page=dean_logs"
       class="nav__link<?= $pg === 'dean_logs' ? ' is-active' : '' ?>"
       <?= $pg === 'dean_logs' ? 'aria-current="page"' : '' ?>>
       <i class="fas fa-history"></i> Activity Logs
    </a>
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
  
  <style>
  .nav__link i, .nav__sublink i {
    width: 16px;
    text-align: center;
    margin-right: 8px;
    opacity: 0.8;
  }
  
  .nav__link.is-active i, 
  .nav__sublink.is-active i {
    opacity: 1;
  }
  
  .nav__toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
  }
  
  .nav__toggle .caret {
    margin-left: auto;
    transition: transform 0.2s ease;
  }
  
  .nav__dropdown.open .nav__toggle .caret {
    transform: rotate(180deg);
  }
  
  .admin-sb__brand {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    padding: 1rem;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
  }
  
  .admin-sb__logo {
    font-size: 1.5rem;
    font-weight: bold;
    display: block;
  }
  
  .admin-sb__title {
    font-size: 0.9rem;
    opacity: 0.8;
    display: block;
    margin-top: 0.25rem;
  }
  </style>
</aside>
