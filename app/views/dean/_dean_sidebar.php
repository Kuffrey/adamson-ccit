<?php
if (!function_exists('esc')) {
  function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

// Get current user info for personalization
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? 'Dean';
$roleName = ucfirst($user['role'] ?? 'Dean');

// Get dean's information from database
try {
    $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE role = 'dean' LIMIT 1");
    $stmt->execute();
    $dean = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($dean) {
        $firstName = $dean['first_name'];
        $lastName = $dean['last_name'];
        $fullName = "Dr. " . $firstName . " " . $lastName;
    } else {
        $firstName = "Dean";
        $lastName = "";
        $fullName = "Dean";
    }
} catch (PDOException $e) {
    $firstName = "Dean";
    $lastName = "";
    $fullName = "Dean";
}

$pg = $_GET['page'] ?? '';

$newsOpen = in_array($pg, ['dean_news_settings', 'dean_manage_news', 'dean_manage_events', 'dean_manage_announcements', 'dean_manage_research', 'dean_manage_certifications'], true) ? ' open' : '';
$approvalsOpen = in_array($pg, ['dean_approvals', 'dean_pending_submissions', 'dean_certifications', 'dean_research_approvals', 'dean_news_approvals'], true) ? ' open' : '';
?>

<aside class="admin-sb" aria-label="Dean navigation">
  <div class="admin-sb__brand">
    <span class="admin-sb__logo">CCIT Dean Portal</span>
  </div>

  <nav class="admin-sb__nav admin-sb__nav--main">
    <a href="?page=dean_dashboard"
       class="nav__link<?= $pg === 'dean_dashboard' ? ' is-active' : '' ?>"
       <?= $pg === 'dean_dashboard' ? 'aria-current="page"' : '' ?>>
       <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>

    <!-- Content Management -->
    <div class="nav__dropdown<?= $newsOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $newsOpen ? 'true' : 'false' ?>" aria-controls="submenu-news">
        <i class="fas fa-edit"></i> Content <span class="caret">▾</span>
      </button>
      <div id="submenu-news" class="nav__submenu">
        <a href="?page=dean_manage_news"
           class="nav__sublink<?= $pg === 'dean_manage_news' ? ' is-active' : '' ?>">
           <i class="fas fa-newspaper"></i> News
        </a>
        <a href="?page=dean_manage_events"
           class="nav__sublink<?= $pg === 'dean_manage_events' ? ' is-active' : '' ?>">
           <i class="fas fa-calendar"></i> Events
        </a>
        <a href="?page=dean_manage_announcements"
           class="nav__sublink<?= $pg === 'dean_manage_announcements' ? ' is-active' : '' ?>">
           <i class="fas fa-bullhorn"></i> Announcements
        </a>
        <a href="?page=dean_manage_research"
           class="nav__sublink<?= $pg === 'dean_manage_research' ? ' is-active' : '' ?>">
           <i class="fas fa-microscope"></i> Research
        </a>
        <a href="?page=dean_manage_certifications"
           class="nav__sublink<?= $pg === 'dean_manage_certifications' ? ' is-active' : '' ?>">
           <i class="fas fa-certificate"></i> Certifications
        </a>
      </div>
    </div>

    <!-- Approvals -->
    <div class="nav__dropdown<?= $approvalsOpen ?>">
      <button class="nav__link nav__toggle" type="button"
              aria-expanded="<?= $approvalsOpen ? 'true' : 'false' ?>" aria-controls="submenu-approvals">
        <i class="fas fa-check-circle"></i> Approvals <span class="caret">▾</span>
      </button>
      <div id="submenu-approvals" class="nav__submenu">
        <a href="?page=dean_approvals"
           class="nav__sublink<?= $pg === 'dean_approvals' ? ' is-active' : '' ?>">
           <i class="fas fa-tasks"></i> All Pending
        </a>
        <a href="?page=dean_certifications"
           class="nav__sublink<?= $pg === 'dean_certifications' ? ' is-active' : '' ?>">
           <i class="fas fa-certificate"></i> Certifications
        </a>
        <a href="?page=dean_research_approvals"
           class="nav__sublink<?= $pg === 'dean_research_approvals' ? ' is-active' : '' ?>">
           <i class="fas fa-microscope"></i> Research
        </a>
        <a href="?page=dean_news_approvals"
           class="nav__sublink<?= $pg === 'dean_news_approvals' ? ' is-active' : '' ?>">
           <i class="fas fa-newspaper"></i> News
        </a>
        <a href="?page=dean_pending_submissions"
           class="nav__sublink<?= $pg === 'dean_pending_submissions' ? ' is-active' : '' ?>">
           <i class="fas fa-inbox"></i> Queue
        </a>
      </div>
    </div>

    <!-- Activity Logs -->
    <a href="?page=dean_logs"
       class="nav__link<?= $pg === 'dean_logs' ? ' is-active' : '' ?>"
       <?= $pg === 'dean_logs' ? 'aria-current="page"' : '' ?>>
       <i class="fas fa-history"></i> Activity Logs
    </a>

    <!-- Portfolio -->
    <a href="?page=dean_portfolio"
       class="nav__link<?= $pg === 'dean_portfolio' ? ' is-active' : '' ?>"
       <?= $pg === 'dean_portfolio' ? 'aria-current="page"' : '' ?>>
       <i class="fas fa-user-tie"></i> Portfolio
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
    background: #0080c9;
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
    background: #00713D;
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
