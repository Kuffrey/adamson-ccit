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
       class="nav__link<?= $pg === 'admin_dashboard' ? ' is-active' : '' ?>">
       Dashboard</a>

    <a href="?page=admin_manage_homepage"
       class="nav__link<?= $pg === 'admin_manage_homepage' ? ' is-active' : '' ?>">
       Homepage</a>

    <!-- About -->
    <div class="nav__dropdown<?= $aboutOpen ?>">
      <button class="nav__link nav__toggle" type="button">
        About <span class="caret">▾</span>
      </button>
      <div class="nav__submenu">
        <a href="?page=admin_manage_about"
           class="nav__sublink<?= $pg === 'admin_manage_about' ? ' is-active' : '' ?>">
           About Page</a>
        <a href="?page=admin_about_vision_mission"
           class="nav__sublink<?= $pg === 'admin_about_vision_mission' ? ' is-active' : '' ?>">
           Vision & Mission</a>
      </div>
    </div>

    <!-- News -->
    <div class="nav__dropdown<?= $newsOpen ?>">
      <button class="nav__link nav__toggle" type="button">
        News <span class="caret">▾</span>
      </button>
      <div class="nav__submenu">
        <a href="?page=admin_manage_news"
           class="nav__sublink<?= $pg === 'admin_manage_news' ? ' is-active' : '' ?>">
           Articles</a>
        <a href="?page=admin_manage_events"
           class="nav__sublink<?= $pg === 'admin_manage_events' ? ' is-active' : '' ?>">
           Events</a>
        <a href="?page=admin_manage_announcements"
           class="nav__sublink<?= $pg === 'admin_manage_announcements' ? ' is-active' : '' ?>">
           Announcements</a>
      </div>
    </div>

    <!-- Admission -->
    <div class="nav__dropdown<?= $admitOpen ?>">
      <button class="nav__link nav__toggle" type="button">
        Admission <span class="caret">▾</span>
      </button>
      <div class="nav__submenu">
        <a href="?page=admin_admission_freshman"
           class="nav__sublink<?= $pg==='admin_admission_freshman' ? ' is-active' : '' ?>">
           Freshman</a>
        <a href="?page=admin_admission_transferee"
           class="nav__sublink<?= $pg==='admin_admission_transferee' ? ' is-active' : '' ?>">
           Transferee</a>
        <a href="?page=admin_admission_graduate"
           class="nav__sublink<?= $pg==='admin_admission_graduate' ? ' is-active' : '' ?>">
           Graduate</a>
      </div>
    </div>

    <!-- Programs -->
    <div class="nav__dropdown<?= $programsOpen ?>">
      <button class="nav__link nav__toggle" type="button">
        Programs <span class="caret">▾</span>
      </button>
      <div class="nav__submenu">
        <a href="?page=admin_manage_programs"
           class="nav__sublink<?= $pg==='admin_manage_programs' ? ' is-active' : '' ?>">
           All Programs</a>
        <a href="?page=admin_programs_undergraduate"
           class="nav__sublink<?= $pg==='admin_programs_undergraduate' ? ' is-active' : '' ?>">
           Undergraduate</a>
        <a href="?page=admin_programs_graduate"
           class="nav__sublink<?= $pg==='admin_programs_graduate' ? ' is-active' : '' ?>">
           Graduate</a>
      </div>
    </div>

    <!-- Students -->
    <div class="nav__dropdown<?= $studentOpen ?>">
      <button class="nav__link nav__toggle" type="button">
        Students <span class="caret">▾</span>
      </button>
      <div class="nav__submenu">
        <a href="?page=admin_student_organizations"
           class="nav__sublink<?= $pg==='admin_student_organizations' ? ' is-active' : '' ?>">
           Organizations</a>
        <a href="?page=admin_student_scholarships"
           class="nav__sublink<?= $pg==='admin_student_scholarships' ? ' is-active' : '' ?>">
           Scholarships</a>
        <a href="?page=admin_student_research"
           class="nav__sublink<?= $pg==='admin_student_research' ? ' is-active' : '' ?>">
           Research</a>
        <a href="?page=admin_student_certifications"
           class="nav__sublink<?= $pg==='admin_student_certifications' ? ' is-active' : '' ?>">
           Certifications</a>
        <a href="?page=admin_student_testimonials"
           class="nav__sublink<?= $pg==='admin_student_testimonials' ? ' is-active' : '' ?>">
           Testimonials</a>
      </div>
    </div>

    <!-- Faculty -->
    <div class="nav__dropdown<?= $facultyOpen ?>">
      <button class="nav__link nav__toggle" type="button">
        Faculty <span class="caret">▾</span>
      </button>
      <div class="nav__submenu">
        <a href="?page=admin_manage_faculty"
           class="nav__sublink<?= $pg==='admin_manage_faculty' ? ' is-active' : '' ?>">
           Faculty List</a>
        <a href="?page=admin_faculty_profile"
           class="nav__sublink<?= $pg==='admin_faculty_profile' ? ' is-active' : '' ?>">
           Profiles</a>
        <a href="?page=admin_faculty_research"
           class="nav__sublink<?= $pg==='admin_faculty_research' ? ' is-active' : '' ?>">
           Research</a>
        <a href="?page=admin_faculty_certifications"
           class="nav__sublink<?= $pg==='admin_faculty_certifications' ? ' is-active' : '' ?>">
           Certifications</a>
      </div>
    </div>

  </nav>

  <!-- User section -->
  <div class="admin-sb__spacer"></div>
  <div class="admin-sb__user">
    <div class="userline">
      <span class="avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
      <div>
        <span class="uname"><?= esc($username) ?></span>
        <span class="urole"><?= esc($roleName) ?></span>
      </div>
    </div>
    <a href="?page=logout_admin" class="nav__link">Logout</a>
  </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggles = document.querySelectorAll('.nav__toggle');
  toggles.forEach(toggle => {
    toggle.addEventListener('click', function() {
      const dropdown = this.closest('.nav__dropdown');
      dropdown.classList.toggle('open');
    });
  });
});
</script>