<?php
if (!function_exists('esc')) {
  function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

// Get current user info for personalization
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? 'Faculty';
$roleName = ucfirst($user['role'] ?? 'Faculty');

// Get faculty's information from database
try {
    $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $faculty = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($faculty) {
        $firstName = $faculty['first_name'];
        $lastName = $faculty['last_name'];
        $fullName = $firstName . ' ' . $lastName;
    } else {
        $firstName = "Faculty";
        $lastName = "";
        $fullName = $username;
    }
} catch (PDOException $e) {
    $firstName = "Faculty";
    $lastName = "";
    $fullName = $username;
}

$pg = $_GET['page'] ?? '';

$submissionsOpen = in_array($pg, [
  'faculty_manage_research',
  'faculty_manage_certifications',
  'faculty_manage_news',
  'faculty_manage_events',
  'faculty_manage_announcements'
], true) ? ' open' : '';
?>

<aside class="admin-sb" aria-label="Faculty navigation">
  <div class="admin-sb__brand">
    <span class="admin-sb__logo">CCIT Faculty Portal</span>
  </div>

  <nav class="admin-sb__nav admin-sb__nav--main">
    <a href="?page=faculty_dashboard"
       class="nav__link<?= $pg === 'faculty_dashboard' ? ' is-active' : '' ?>"
       <?= $pg === 'faculty_dashboard' ? 'aria-current="page"' : '' ?>>
       <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>

   <!-- Submissions -->
   <div class="nav__dropdown<?= $submissionsOpen ?>">
    <button class="nav__link nav__toggle" type="button"
          aria-expanded="<?= $submissionsOpen ? 'true' : 'false' ?>" aria-controls="submenu-submissions">
      <i class="fas fa-upload"></i> Submissions <span class="caret">▾</span>
    </button>
    <div id="submenu-submissions" class="nav__submenu">
      <a href="?page=faculty_manage_research"
        class="nav__sublink<?= $pg === 'faculty_manage_research' ? ' is-active' : '' ?>">
        <i class="fas fa-microscope"></i> Submit Research
      </a>
      <a href="?page=faculty_manage_certifications"
        class="nav__sublink<?= $pg === 'faculty_manage_certifications' ? ' is-active' : '' ?>">
        <i class="fas fa-certificate"></i> Submit Certifications
      </a>
      <a href="?page=faculty_manage_news"
        class="nav__sublink<?= $pg === 'faculty_manage_news' ? ' is-active' : '' ?>">
        <i class="fas fa-newspaper"></i> Submit News
      </a>
      <a href="?page=faculty_manage_events"
        class="nav__sublink<?= $pg === 'faculty_manage_events' ? ' is-active' : '' ?>">
        <i class="fas fa-calendar"></i> Submit Events
      </a>
      <a href="?page=faculty_manage_announcements"
        class="nav__sublink<?= $pg === 'faculty_manage_announcements' ? ' is-active' : '' ?>">
        <i class="fas fa-bullhorn"></i> Submit Announcements
      </a>
    </div>
   </div>

    <a href="?page=faculty_portfolio"
      class="nav__link<?= $pg === 'faculty_portfolio' ? ' is-active' : '' ?>"
      <?= $pg === 'faculty_portfolio' ? 'aria-current="page"' : '' ?>>
      <i class="fas fa-user"></i> Portfolio
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
    background: #2c3e50;
    border-right: 1px solid #34495e;
  }
  
  .admin-sb__brand {
    background: #2c3e50;
    color: white;
    padding: 1rem 1.25rem;
    text-align: center;
    border-bottom: 1px solid #34495e;
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
    color: #bdc3c7;
    text-decoration: none;
    border: none;
    background: none;
    width: 100%;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s ease;
  }
  
  .nav__link:hover, .nav__toggle:hover {
    background: #34495e;
    color: #ecf0f1;
  }
  
  .nav__link.is-active {
    background: #008040;
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
    background: #34495e;
    border-top: 1px solid #2c3e50;
    border-bottom: 1px solid #2c3e50;
  }
  
  .nav__sublink {
    display: flex;
    align-items: center;
    padding: 0.6rem 1.25rem 0.6rem 2.5rem;
    color: #95a5a6;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 400;
    transition: all 0.2s ease;
  }
  
  .nav__sublink:hover {
    background: #2c3e50;
    color: #bdc3c7;
  }
  
  .nav__sublink.is-active {
    background: #00713D;
    color: white;
    font-weight: 500;
  }
  
  .admin-sb__user {
    padding: 1rem 1.25rem;
    border-top: 1px solid #34495e;
    background: #2c3e50;
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
    background: #008040;
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
    color: #ecf0f1;
    display: block;
  }
  
  .urole {
    font-size: 0.8rem;
    color: #95a5a6;
    display: block;
  }
  
  .btn--logout {
    display: block;
    width: 100%;
    padding: 0.6rem;
    background: #e74c3c;
    color: white;
    text-decoration: none;
    text-align: center;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: background 0.2s ease;
  }
  
  .btn--logout:hover {
    background: #c0392b;
    color: white;
  }
  </style>
</aside>
