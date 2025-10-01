<?php
if (!function_exists('esc')) {
  function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

// Get current user info for personalization
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? 'Admin';
$roleName = ucfirst($user['role'] ?? 'Administrator');

// Get admin's information from database
try {
    $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE role = 'admin' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        $firstName = $admin['first_name'];
        $lastName = $admin['last_name'];
        $fullName = $firstName . " " . $lastName;
    } else {
        $firstName = "Admin";
        $lastName = "";
        $fullName = "Administrator";
    }
} catch (PDOException $e) {
    $firstName = "Admin";
    $lastName = "";
    $fullName = "Administrator";
}

$pg = $_GET['page'] ?? '';

$aboutOpen    = in_array($pg, ['admin_manage_about','admin_about_vision_mission'], true) ? ' open' : '';
$homepageOpen = in_array($pg, ['admin_manage_homepage','admin_manage_quick_actions'], true) ? ' open' : '';
$newsOpen     = in_array($pg, ['admin_news_settings','admin_manage_news','admin_manage_events','admin_manage_announcements'], true) ? ' open' : '';
$admitOpen    = in_array($pg, ['admin_admission_freshman','admin_admission_transferee','admin_admission_graduate'], true) ? ' open' : '';
$programsOpen = in_array($pg, ['admin_manage_programs','admin_programs_undergraduate','admin_programs_graduate'], true) ? ' open' : '';
$studentOpen  = in_array($pg, ['admin_student_organizations','admin_student_scholarships','admin_student_research','admin_student_certifications','admin_student_testimonials'], true) ? ' open' : '';
$facultyOpen  = in_array($pg, ['admin_faculty_profile','admin_faculty_research','admin_faculty_certifications'], true) ? ' open' : '';
?>
<aside class="admin-sb" aria-label="Admin navigation">
  <div class="admin-sb__brand">
    <span class="admin-sb__logo">CCIT Admin Portal</span>
  </div>

  <nav class="admin-sb__nav admin-sb__nav--main">
    <a href="?page=admin_dashboard"
       class="nav__link<?= $pg === 'admin_dashboard' ? ' is-active' : '' ?>"
       <?= $pg === 'admin_dashboard' ? 'aria-current="page"' : '' ?>>
       <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>

    <!-- Homepage -->
    <div class="nav__dropdown<?= $homepageOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $homepageOpen ? 'true' : 'false' ?>" aria-controls="submenu-homepage">
        <i class="fas fa-home"></i> Homepage <span class="caret">▾</span>
      </button>
      <div id="submenu-homepage" class="nav__submenu">
        <a href="?page=admin_manage_homepage"
           class="nav__sublink<?= $pg === 'admin_manage_homepage' ? ' is-active' : '' ?>">
           <i class="fas fa-edit"></i> Homepage Content
        </a>
        <a href="?page=admin_manage_quick_actions"
           class="nav__sublink<?= $pg === 'admin_manage_quick_actions' ? ' is-active' : '' ?>">
           <i class="fas fa-bolt"></i> Quick Actions
        </a>
      </div>
    </div>

    <!-- About -->
    <div class="nav__dropdown<?= $aboutOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $aboutOpen ? 'true' : 'false' ?>" aria-controls="submenu-about">
        <i class="fas fa-info-circle"></i> About <span class="caret">▾</span>
      </button>
      <div id="submenu-about" class="nav__submenu">
        <a href="?page=admin_manage_about"
           class="nav__sublink<?= $pg === 'admin_manage_about' ? ' is-active' : '' ?>">
           <i class="fas fa-file-alt"></i> About Page
        </a>
        <a href="?page=admin_about_vision_mission"
           class="nav__sublink<?= $pg === 'admin_about_vision_mission' ? ' is-active' : '' ?>">
           <i class="fas fa-eye"></i> Vision & Mission
        </a>
      </div>
    </div>

    <!-- News -->
    <div class="nav__dropdown<?= $newsOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $newsOpen ? 'true' : 'false' ?>" aria-controls="submenu-news">
        <i class="fas fa-newspaper"></i> News <span class="caret">▾</span>
      </button>
      <div id="submenu-news" class="nav__submenu">
        <a href="?page=admin_manage_news"
           class="nav__sublink<?= $pg === 'admin_manage_news' ? ' is-active' : '' ?>">
           <i class="fas fa-newspaper"></i> Articles
        </a>
        <a href="?page=admin_manage_events"
           class="nav__sublink<?= $pg === 'admin_manage_events' ? ' is-active' : '' ?>">
           <i class="fas fa-calendar"></i> Events
        </a>
        <a href="?page=admin_manage_announcements"
           class="nav__sublink<?= $pg === 'admin_manage_announcements' ? ' is-active' : '' ?>">
           <i class="fas fa-bullhorn"></i> Announcements
        </a>
      </div>
    </div>

    <!-- Admission -->
    <div class="nav__dropdown<?= $admitOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $admitOpen ? 'true' : 'false' ?>" aria-controls="submenu-admission">
        <i class="fas fa-graduation-cap"></i> Admission <span class="caret">▾</span>
      </button>
      <div id="submenu-admission" class="nav__submenu">
        <a href="?page=admin_admission_freshman"
           class="nav__sublink<?= $pg==='admin_admission_freshman' ? ' is-active' : '' ?>">
           <i class="fas fa-user-graduate"></i> Freshman
        </a>
        <a href="?page=admin_admission_transferee"
           class="nav__sublink<?= $pg==='admin_admission_transferee' ? ' is-active' : '' ?>">
           <i class="fas fa-exchange-alt"></i> Transferee
        </a>
        <a href="?page=admin_admission_graduate"
           class="nav__sublink<?= $pg==='admin_admission_graduate' ? ' is-active' : '' ?>">
           <i class="fas fa-user-tie"></i> Graduate
        </a>
      </div>
    </div>

    <!-- Programs -->
    <div class="nav__dropdown<?= $programsOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $programsOpen ? 'true' : 'false' ?>" aria-controls="submenu-programs">
        <i class="fas fa-book"></i> Programs <span class="caret">▾</span>
      </button>
      <div id="submenu-programs" class="nav__submenu">
        <a href="?page=admin_manage_programs"
           class="nav__sublink<?= $pg==='admin_manage_programs' ? ' is-active' : '' ?>">
           <i class="fas fa-list"></i> All Programs
        </a>
        <a href="?page=admin_programs_undergraduate"
           class="nav__sublink<?= $pg==='admin_programs_undergraduate' ? ' is-active' : '' ?>">
           <i class="fas fa-user-graduate"></i> Undergraduate
        </a>
        <a href="?page=admin_programs_graduate"
           class="nav__sublink<?= $pg==='admin_programs_graduate' ? ' is-active' : '' ?>">
           <i class="fas fa-user-tie"></i> Graduate
        </a>
      </div>
    </div>

    <!-- Students -->
    <div class="nav__dropdown<?= $studentOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $studentOpen ? 'true' : 'false' ?>" aria-controls="submenu-students">
        <i class="fas fa-users"></i> Students <span class="caret">▾</span>
      </button>
      <div id="submenu-students" class="nav__submenu">
        <a href="?page=admin_student_organizations"
           class="nav__sublink<?= $pg==='admin_student_organizations' ? ' is-active' : '' ?>">
           <i class="fas fa-sitemap"></i> Organizations
        </a>
        <a href="?page=admin_student_scholarships"
           class="nav__sublink<?= $pg==='admin_student_scholarships' ? ' is-active' : '' ?>">
           <i class="fas fa-money-bill"></i> Scholarships
        </a>
        <a href="?page=admin_student_research"
           class="nav__sublink<?= $pg==='admin_student_research' ? ' is-active' : '' ?>">
           <i class="fas fa-microscope"></i> Research
        </a>
        <a href="?page=admin_student_certifications"
           class="nav__sublink<?= $pg==='admin_student_certifications' ? ' is-active' : '' ?>">
           <i class="fas fa-certificate"></i> Certifications
        </a>
        <a href="?page=admin_student_testimonials"
           class="nav__sublink<?= $pg==='admin_student_testimonials' ? ' is-active' : '' ?>">
           <i class="fas fa-quote-left"></i> Testimonials
        </a>
      </div>
    </div>

    <!-- Faculty -->
    <div class="nav__dropdown<?= $facultyOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $facultyOpen ? 'true' : 'false' ?>" aria-controls="submenu-faculty">
        <i class="fas fa-chalkboard-teacher"></i> Faculty <span class="caret">▾</span>
      </button>
      <div id="submenu-faculty" class="nav__submenu">
        <a href="?page=admin_faculty_profile"
           class="nav__sublink<?= $pg==='admin_faculty_profile' ? ' is-active' : '' ?>">
           <i class="fas fa-id-card"></i> Profiles
        </a>
        <a href="?page=admin_faculty_research"
           class="nav__sublink<?= $pg==='admin_faculty_research' ? ' is-active' : '' ?>">
           <i class="fas fa-microscope"></i> Research
        </a>
        <a href="?page=admin_faculty_certifications"
           class="nav__sublink<?= $pg==='admin_faculty_certifications' ? ' is-active' : '' ?>">
           <i class="fas fa-certificate"></i> Certifications
        </a>
      </div>
    </div>

    <!-- User Management -->
    <a href="?page=admin_manage_users"
       class="nav__link<?= $pg === 'admin_manage_users' ? ' is-active' : '' ?>"
       <?= $pg === 'admin_manage_users' ? 'aria-current="page"' : '' ?>>
       <i class="fas fa-users-cog"></i> User Management
    </a>

  </nav>

  <div class="admin-sb__spacer"></div>

  <div class="admin-sb__user">
    <div class="userline">
      <span class="avatar"><?= esc(strtoupper($firstName[0] . $lastName[0])) ?></span>
      <div class="uinfo">
        <span class="uname"><?= esc($fullName) ?></span>
        <span class="urole"><?= esc($roleName) ?></span>
      </div>
    </div>
    <a class="btn btn--logout" href="?page=logout">Logout</a>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".nav__toggle").forEach(btn => {
      btn.addEventListener("click", () => {
        const dropdown = btn.parentElement;
        const isOpen = dropdown.classList.toggle("open");
        btn.setAttribute("aria-expanded", isOpen ? "true" : "false");
      });
    });
    
    // Auto-open dropdown if contains active page
    document.querySelectorAll(".nav__dropdown").forEach(dropdown => {
      if (dropdown.querySelector(".nav__sublink.is-active")) {
        dropdown.classList.add("open");
        dropdown.querySelector(".nav__toggle")?.setAttribute("aria-expanded", "true");
      }
    });
  });
  </script>
  
  <style>
  /* Minimalist, clean and professional sidebar */
  .admin-sb {
    background: #1a1a1a;
    border-right: 1px solid #333;
  }
  
  .admin-sb__brand {
    background: #1a1a1a;
    color: white;
    padding: 1rem 1.25rem;
    text-align: center;
    border-bottom: 1px solid #333;
  }
  
  .admin-sb__logo {
    font-size: 0.95rem;
    font-weight: 600;
    display: inline-block;
    letter-spacing: 0.5px;
    line-height: 1;
    white-space: nowrap;
  }
  
  .nav__link, .nav__toggle {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.25rem;
    color: #b0b0b0;
    text-decoration: none;
    border: none;
    background: none;
    width: 100%;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s ease;
  }
  
  .nav__link:hover, .nav__toggle:hover {
    background: #333;
    color: #fff;
  }
  
  .nav__link.is-active {
    background: #007bff;
    color: white;
  }
  
  .nav__link i, .nav__sublink i {
    width: 16px;
    text-align: center;
    margin-right: 0.75rem;
    font-size: 0.9rem;
  }
  
  .nav__toggle {
    justify-content: space-between;
    cursor: pointer;
  }
  
  .caret {
    font-size: 0.8rem;
    transition: transform 0.2s ease;
    opacity: 0.7;
  }
  
  .nav__dropdown.open .caret {
    transform: rotate(180deg);
  }
  
  .nav__submenu {
    background: #333;
    border-top: 1px solid #1a1a1a;
    border-bottom: 1px solid #1a1a1a;
  }
  
  .nav__sublink {
    display: flex;
    align-items: center;
    padding: 0.6rem 1.25rem 0.6rem 2.5rem;
    color: #888;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 400;
    transition: all 0.2s ease;
  }
  
  .nav__sublink:hover {
    background: #1a1a1a;
    color: #b0b0b0;
  }
  
  .nav__sublink.is-active {
    background: #005cbf;
    color: white;
    font-weight: 500;
  }
  
  .admin-sb__user {
    padding: 1rem 1.25rem;
    border-top: 1px solid #333;
    background: #1a1a1a;
  }
  
  .userline {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
  }
  
  .avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
    margin-right: 0.75rem;
  }
  
  .uname {
    font-weight: 500;
    font-size: 0.9rem;
    color: #fff;
    display: block;
  }
  
  .urole {
    font-size: 0.8rem;
    color: #888;
    display: block;
  }
  
  .btn--logout {
    display: block;
    width: 100%;
    padding: 0.6rem;
    background: #dc3545;
    color: white;
    text-decoration: none;
    text-align: center;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: background 0.2s ease;
  }
  
  .btn--logout:hover {
    background: #c82333;
    color: white;
  }
  </style>
</aside>