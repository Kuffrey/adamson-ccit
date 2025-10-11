<?php
if (!function_exists('esc')) {
  function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? 'Dean';
$roleName = ucfirst($user['role'] ?? 'Dean');

/* Lookup dean name for avatar initials */
try{
  $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit","root","",
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
  $stmt=$pdo->prepare("SELECT first_name,last_name FROM users WHERE role='dean' LIMIT 1");
  $stmt->execute();
  $dean=$stmt->fetch(PDO::FETCH_ASSOC);
  $firstName=$dean['first_name'] ?? 'Dean';
  $lastName =$dean['last_name']  ?? '';
  $fullName = $dean ? "Dr. {$firstName} {$lastName}" : "Dean";
}catch(PDOException $e){
  $firstName="Dean"; $lastName=""; $fullName="Dean";
}

/* Active page + which dropdowns open */
$pg = $_GET['page'] ?? '';
$newsOpen = in_array($pg, [
  'dean_news_settings','dean_manage_news','dean_manage_events',
  'dean_manage_announcements','dean_manage_research','dean_manage_certifications'
], true);

$approvalsOpen = in_array($pg, [
  'dean_approvals','dean_pending_submissions','dean_certifications',
  'dean_research_approvals','dean_news_approvals'
], true);
?>

<!-- SCRIM (mobile overlay) -->
<div class="sb-scrim" data-sb-close></div>

<aside class="admin-sb sb--split" aria-label="Dean navigation">
  <div class="admin-sb__brand">
    <a href="?page=dean_dashboard" class="brand" aria-label="CCIT Dean Portal Home">
      <!-- Single combined logo (blue + green in one image) -->
      <img
        src="/adamson-ccit/public/assets/images/adu-ccit-logo.png"
        class="brand__logo--single"
        alt="AdU · CCIT"
        width="240"
        height="40"
        loading="eager"
      />
    </a>
  </div>

  <nav class="admin-sb__nav admin-sb__nav--main" role="navigation">
    <div class="sb-section">General</div>

    <!-- Dashboard -->
    <a href="?page=dean_dashboard"
       class="nav__link<?= $pg === 'dean_dashboard' ? ' is-active' : '' ?>"
       <?= $pg === 'dean_dashboard' ? 'aria-current="page"' : '' ?>>
      <i class="fas fa-tachometer-alt" aria-hidden="true"></i>
      <span>Dashboard</span>
      <!-- empty cell for alignment -->
      <span></span>
    </a>

    <!-- CONTENT DROPDOWN -->
    <div class="sb-section">Content</div>
    <div class="nav__dropdown<?= $newsOpen ? ' open' : '' ?>">
      <button
        class="nav__toggle<?= $newsOpen ? ' is-active' : '' ?>"
        type="button"
        aria-expanded="<?= $newsOpen ? 'true' : 'false' ?>"
        aria-controls="submenu-news"
      >
        <i class="fas fa-edit" aria-hidden="true"></i>
       
          <span style="text-align:left;display:block;">Content</span>
          <span class="caret">▾</span>

      </button>

      <div id="submenu-news" class="nav__submenu" role="group" aria-label="Content submenu">
        <a href="?page=dean_manage_news"
           class="nav__sublink<?= $pg === 'dean_manage_news' ? ' is-active' : '' ?>">
          <i class="fas fa-newspaper" aria-hidden="true"></i>
          <span>News</span>
        </a>

        <a href="?page=dean_manage_events"
           class="nav__sublink<?= $pg === 'dean_manage_events' ? ' is-active' : '' ?>">
          <i class="fas fa-calendar" aria-hidden="true"></i>
          <span>Events</span>
        </a>

        <a href="?page=dean_manage_announcements"
           class="nav__sublink<?= $pg === 'dean_manage_announcements' ? ' is-active' : '' ?>">
          <i class="fas fa-bullhorn" aria-hidden="true"></i>
          <span>Announcements</span>
        </a>

        <a href="?page=dean_manage_research"
           class="nav__sublink<?= $pg === 'dean_manage_research' ? ' is-active' : '' ?>">
          <i class="fas fa-microscope" aria-hidden="true"></i>
          <span>Research</span>
        </a>

        <a href="?page=dean_manage_certifications"
           class="nav__sublink<?= $pg === 'dean_manage_certifications' ? ' is-active' : '' ?>">
          <i class="fas fa-certificate" aria-hidden="true"></i>
          <span>Certifications</span>
        </a>
      </div>
    </div>

    <!-- APPROVALS DROPDOWN -->
    <a href="?page=dean_approvals"
       class="nav__link<?= $pg === 'dean_approvals' ? ' is-active' : '' ?>"
       <?= $pg === 'dean_approvals' ? 'aria-current="page"' : '' ?>>
      <i class="fas fa-check-circle" aria-hidden="true"></i>
      <span>Approvals</span>
      <!-- empty cell for alignment -->
      <span></span>
    </a>

    <!-- Other pages -->
    <div class="sb-section">More</div>

    <a href="?page=dean_logs"
       class="nav__link<?= $pg === 'dean_logs' ? ' is-active' : '' ?>"
       <?= $pg === 'dean_logs' ? 'aria-current="page"' : '' ?>>
      <i class="fas fa-history" aria-hidden="true"></i>
      <span>Activity Logs</span>
      <span></span>
    </a>

    <a href="?page=dean_portfolio"
       class="nav__link<?= $pg === 'dean_portfolio' ? ' is-active' : '' ?>"
       <?= $pg === 'dean_portfolio' ? 'aria-current="page"' : '' ?>>
      <i class="fas fa-user-tie" aria-hidden="true"></i>
      <span>Portfolio</span>
      <span></span>
    </a>
  </nav>

  <div class="admin-sb__spacer"></div>

  <div class="admin-sb__user">
    <div class="userline">
      <span class="avatar"><?= esc(strtoupper($firstName[0] . ($lastName[0] ?? ''))) ?></span>
      <div class="uinfo">
        <span class="uname"><?= esc($fullName) ?></span>
        <span class="urole"><?= esc($roleName) ?></span>
      </div>
    </div>
    <a class="btn--logout" href="?page=logout">Logout</a>
  </div>
</aside>

<script>
/* Toggle dropdowns with perfect ARIA + keyboard support */
document.querySelectorAll(".nav__toggle").forEach(btn=>{
  btn.addEventListener("click", () => toggleDropdown(btn));
  btn.addEventListener("keydown", (e)=>{
    if(e.key === "Enter" || e.key === " "){ e.preventDefault(); toggleDropdown(btn); }
  });
});

function toggleDropdown(btn){
  const dd = btn.closest(".nav__dropdown");
  const open = dd.classList.toggle("open");
  btn.classList.toggle("is-active", open);
  btn.setAttribute("aria-expanded", open ? "true" : "false");
}

/* Auto-open dropdown if it contains the active sublink */
document.querySelectorAll(".nav__dropdown").forEach(dd=>{
  if (dd.querySelector(".nav__sublink.is-active")) {
    dd.classList.add("open");
    const t = dd.querySelector(".nav__toggle");
    if(t){ t.classList.add("is-active"); t.setAttribute("aria-expanded","true"); }
  }
});

/* Mobile drawer functionality - FIXED */
function openSB() { 
  console.log('Opening sidebar');
  document.documentElement.classList.add("sb-open"); 
  document.body.style.overflow = 'hidden';
}

function closeSB() { 
  console.log('Closing sidebar');
  document.documentElement.classList.remove("sb-open"); 
  document.body.style.overflow = '';
}

// Global functions to be called from other pages
window.openSB = openSB;
window.closeSB = closeSB;

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
  // Handle hamburger button clicks
  function bindHamburgerEvents() {
    const hamburgerButtons = document.querySelectorAll('[data-sb-open]');
    console.log('Found hamburger buttons:', hamburgerButtons.length);
    
    hamburgerButtons.forEach(btn => {
      // Remove existing listeners to prevent duplicates
      btn.removeEventListener('click', handleHamburgerClick);
      btn.removeEventListener('touchstart', handleHamburgerTouch);
      
      // Add new listeners
      btn.addEventListener('click', handleHamburgerClick);
      btn.addEventListener('touchstart', handleHamburgerTouch);
      
      console.log('Bound events to hamburger button:', btn);
    });
  }
  
  function handleHamburgerClick(e) {
    console.log('Hamburger clicked');
    e.preventDefault();
    e.stopPropagation();
    openSB();
  }
  
  function handleHamburgerTouch(e) {
    console.log('Hamburger touched');
    e.preventDefault();
    e.stopPropagation();
    openSB();
  }
  
  // Initial bind
  bindHamburgerEvents();
  
  // Re-bind when new elements are added (for dynamic content)
  const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.type === 'childList') {
        bindHamburgerEvents();
      }
    });
  });
  
  observer.observe(document.body, {
    childList: true,
    subtree: true
  });

  // Handle scrim/overlay clicks to close
  document.querySelectorAll("[data-sb-close]").forEach(btn => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      closeSB();
    });
    
    btn.addEventListener("touchstart", (e) => {
      e.preventDefault();
      e.stopPropagation();
      closeSB();
    });
  });

  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', (e) => {
    if (window.innerWidth <= 1024 && document.documentElement.classList.contains('sb-open')) {
      const sidebar = document.querySelector('.admin-sb');
      const hamburger = document.querySelector('[data-sb-open]');
      
      if (sidebar && hamburger && !sidebar.contains(e.target) && !hamburger.contains(e.target)) {
        closeSB();
      }
    }
  });

  // Handle window resize
  window.addEventListener('resize', () => {
    if (window.innerWidth > 1024) {
      closeSB();
    }
  });

  // Keyboard navigation - ESC to close sidebar
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && document.documentElement.classList.contains('sb-open')) {
      closeSB();
    }
  });
  
  // Enhance sticky topbar with scroll detection
  const topbar = document.querySelector('.admin-topbar');
  const adminMain = document.querySelector('.admin-main');
  
  if (topbar && adminMain) {
    let lastScrollTop = 0;
    
    adminMain.addEventListener('scroll', function() {
      const scrollTop = adminMain.scrollTop;
      
      // Add scrolled class when scrolled down
      if (scrollTop > 10) {
        topbar.classList.add('scrolled');
      } else {
        topbar.classList.remove('scrolled');
      }
      
      lastScrollTop = scrollTop;
    });
    
    // Also listen to window scroll as fallback
    window.addEventListener('scroll', function() {
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      
      if (scrollTop > 10) {
        topbar.classList.add('scrolled');
      } else {
        topbar.classList.remove('scrolled');
      }
    });
  }
});
</script>
