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
$studentOpen  = in_array($pg, ['admin_student_organizations','admin_student_scholarships','admin_student_research','admin_student_certifications','admin_student_testimonials'], true) ? ' open' : '';
$facultyOpen  = in_array($pg, ['admin_faculty_profile','admin_faculty_research','admin_faculty_certifications'], true) ? ' open' : '';
?>
<aside class="admin-sb" aria-label="Admin navigation">
  <div class="admin-sb__brand">
    <span class="admin-sb__logo">CCIT</span>
    <span class="admin-sb__title">Admin</span>
  </div>

  <nav class="admin-sb__nav admin-sb__nav--main">
    <a href="?page=admin_dashboard"
       class="nav__link<?= $pg === 'admin_dashboard' ? ' is-active' : '' ?>"
       <?= $pg === 'admin_dashboard' ? 'aria-current="page"' : '' ?>>
       Dashboard</a>

    <a href="?page=admin_manage_homepage"
       class="nav__link<?= $pg === 'admin_manage_homepage' ? ' is-active' : '' ?>"
       <?= $pg === 'admin_manage_homepage' ? 'aria-current="page"' : '' ?>>
       Homepage</a>

    <!-- About -->
    <div class="nav__dropdown<?= $aboutOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $aboutOpen ? 'true' : 'false' ?>" aria-controls="submenu-about">
        About <span class="caret">▾</span>
      </button>
      <div id="submenu-about" class="nav__submenu">
        <a href="?page=admin_manage_about"
           class="nav__sublink<?= $pg === 'admin_manage_about' ? ' is-active' : '' ?>"
           <?= $pg === 'admin_manage_about' ? 'aria-current="page"' : '' ?>>
           About Page</a>
        <a href="?page=admin_about_vision_mission"
           class="nav__sublink<?= $pg === 'admin_about_vision_mission' ? ' is-active' : '' ?>"
           <?= $pg === 'admin_about_vision_mission' ? 'aria-current="page"' : '' ?>>
           Vision & Mission</a>
      </div>
    </div>

    <!-- News -->
    <div class="nav__dropdown<?= $newsOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $newsOpen ? 'true' : 'false' ?>" aria-controls="submenu-news">
        News <span class="caret">▾</span>
      </button>
      <div id="submenu-news" class="nav__submenu">
        <a href="?page=admin_manage_news"
           class="nav__sublink<?= $pg === 'admin_manage_news' ? ' is-active' : '' ?>"
           <?= $pg === 'admin_manage_news' ? 'aria-current="page"' : '' ?>>
           Articles</a>
        <a href="?page=admin_manage_events"
           class="nav__sublink<?= $pg === 'admin_manage_events' ? ' is-active' : '' ?>"
           <?= $pg === 'admin_manage_events' ? 'aria-current="page"' : '' ?>>
           Events</a>
        <a href="?page=admin_manage_announcements"
           class="nav__sublink<?= $pg === 'admin_manage_announcements' ? ' is-active' : '' ?>"
           <?= $pg === 'admin_manage_announcements' ? 'aria-current="page"' : '' ?>>
           Announcements</a>
      </div>
    </div>

    <!-- Admission -->
    <div class="nav__dropdown<?= $admitOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $admitOpen ? 'true' : 'false' ?>" aria-controls="submenu-admission">
        Admission <span class="caret">▾</span>
      </button>
      <div id="submenu-admission" class="nav__submenu">
        <a href="?page=admin_admission_freshman"
           class="nav__sublink<?= $pg==='admin_admission_freshman' ? ' is-active' : '' ?>"
           <?= $pg==='admin_admission_freshman' ? 'aria-current="page"' : '' ?>>
           Freshman</a>
        <a href="?page=admin_admission_transferee"
           class="nav__sublink<?= $pg==='admin_admission_transferee' ? ' is-active' : '' ?>"
           <?= $pg==='admin_admission_transferee' ? 'aria-current="page"' : '' ?>>
           Transferee</a>
        <a href="?page=admin_admission_graduate"
           class="nav__sublink<?= $pg==='admin_admission_graduate' ? ' is-active' : '' ?>"
           <?= $pg==='admin_admission_graduate' ? 'aria-current="page"' : '' ?>>
           Graduate School &amp; JD</a>
      </div>
    </div>

    <!-- Programs -->
    <div class="nav__dropdown<?= $programsOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $programsOpen ? 'true' : 'false' ?>" aria-controls="submenu-programs">
        <i class="fas fa-book nav__icon"></i>
        Programs <span class="caret" aria-hidden="true">▾</span>
      </button>
      <div id="submenu-programs" class="nav__submenu">
        <a href="?page=admin_manage_programs"
           class="nav__sublink<?= $pg==='admin_manage_programs' ? ' is-active' : '' ?>"
           <?= $pg==='admin_manage_programs' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-database nav__icon"></i>All Programs</a>
        <a href="?page=admin_programs_undergraduate"
           class="nav__sublink<?= $pg==='admin_programs_undergraduate' ? ' is-active' : '' ?>"
           <?= $pg==='admin_programs_undergraduate' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-book-open nav__icon"></i>Undergraduate</a>
        <a href="?page=admin_programs_graduate"
           class="nav__sublink<?= $pg==='admin_programs_graduate' ? ' is-active' : '' ?>"
           <?= $pg==='admin_programs_graduate' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-university nav__icon"></i>Graduate Studies</a>
      </div>
    </div>
           class="nav__sublink<?= $pg==='admin_programs_undergraduate' ? ' is-active' : '' ?>"
           <?= $pg==='admin_programs_undergraduate' ? 'aria-current="page"' : '' ?>>Undergraduate</a>
        <a href="?page=admin_programs_graduate"
           class="nav__sublink<?= $pg==='admin_programs_graduate' ? ' is-active' : '' ?>"
           <?= $pg==='admin_programs_graduate' ? 'aria-current="page"' : '' ?>>Graduate Studies</a>
      </div>
    </div>

    <!-- Student -->
    <div class="nav__dropdown<?= $studentOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $studentOpen ? 'true' : 'false' ?>" aria-controls="submenu-student">
        <i class="fas fa-users nav__icon"></i>
        Student <span class="caret" aria-hidden="true">▾</span>
      </button>
      <div id="submenu-student" class="nav__submenu">
        <a href="?page=admin_student_organizations"
           class="nav__sublink<?= $pg==='admin_student_organizations' ? ' is-active' : '' ?>"
           <?= $pg==='admin_student_organizations' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-sitemap nav__icon"></i>Organizations</a>
        <a href="?page=admin_student_scholarships"
           class="nav__sublink<?= $pg==='admin_student_scholarships' ? ' is-active' : '' ?>"
           <?= $pg==='admin_student_scholarships' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-award nav__icon"></i>Scholarships</a>
        <a href="?page=admin_student_research"
           class="nav__sublink<?= $pg==='admin_student_research' ? ' is-active' : '' ?>"
           <?= $pg==='admin_student_research' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-flask nav__icon"></i>Research</a>
        <a href="?page=admin_student_certifications"
           class="nav__sublink<?= $pg==='admin_student_certifications' ? ' is-active' : '' ?>"
           <?= $pg==='admin_student_certifications' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-certificate nav__icon"></i>Certifications</a>
        <a href="?page=admin_student_testimonials"
           class="nav__sublink<?= $pg==='admin_student_testimonials' ? ' is-active' : '' ?>"
           <?= $pg==='admin_student_testimonials' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-quote-left nav__icon"></i>Testimonials</a>
      </div>
    </div>

    <!-- Faculty -->
    <div class="nav__dropdown<?= $facultyOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $facultyOpen ? 'true' : 'false' ?>" aria-controls="submenu-faculty">
        <i class="fas fa-chalkboard-teacher nav__icon"></i>
        Faculty <span class="caret" aria-hidden="true">▾</span>
      </button>
      <div id="submenu-faculty" class="nav__submenu">
        <a href="?page=admin_faculty_profile"
           class="nav__sublink<?= $pg==='admin_faculty_profile' ? ' is-active' : '' ?>"
           <?= $pg==='admin_faculty_profile' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-user-tie nav__icon"></i>Profile</a>
        <a href="?page=admin_faculty_research"
           class="nav__sublink<?= $pg==='admin_faculty_research' ? ' is-active' : '' ?>"
           <?= $pg==='admin_faculty_research' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-microscope nav__icon"></i>Research</a>
        <a href="?page=admin_faculty_certifications"
           class="nav__sublink<?= $pg==='admin_faculty_certifications' ? ' is-active' : '' ?>"
           <?= $pg==='admin_faculty_certifications' ? 'aria-current="page"' : '' ?>>
           <i class="fas fa-medal nav__icon"></i>Certifications</a>
      </div>
    </div>
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
