<?php
if (!function_exists('esc')) {
  function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

/* User context (keep faculty’s own info) */
$user      = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username  = $user['username'] ?? 'Faculty';
$roleName  = ucfirst($user['role'] ?? 'Faculty');

/* Lookup faculty name for avatar initials */
try {
  $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit","root","",
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
  );
  $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE username = ? LIMIT 1");
  $stmt->execute([$username]);
  $faculty   = $stmt->fetch(PDO::FETCH_ASSOC);
  $firstName = $faculty['first_name'] ?? 'Faculty';
  $lastName  = $faculty['last_name']  ?? '';
  $fullName  = $faculty ? "{$firstName} {$lastName}" : $username;
} catch (PDOException $e) {
  $firstName = "Faculty"; $lastName = ""; $fullName = $username;
}

/* Active page + which dropdowns open (match dean structure) */
$pg = $_GET['page'] ?? '';
$subsOpen = in_array($pg, [
  'faculty_manage_news','faculty_manage_research','faculty_manage_certifications'
], true);
?>

<!-- SCRIM (mobile overlay) -->
<div class="sb-scrim" data-sb-close></div>

<aside class="admin-sb sb--split" aria-label="Faculty navigation">
  <div class="admin-sb__brand">
    <a href="?page=faculty_dashboard" class="brand" aria-label="CCIT Faculty Portal Home">
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

    <!-- Dashboard (aligned like dean: trailing empty span) -->
    <a href="?page=faculty_dashboard"
       class="nav__link<?= $pg === 'faculty_dashboard' ? ' is-active' : '' ?>"
       <?= $pg === 'faculty_dashboard' ? 'aria-current="page"' : '' ?>>
      <i class="fas fa-tachometer-alt" aria-hidden="true"></i>
      <span>Dashboard</span>
      <span></span>
    </a>

    <!-- Submissions dropdown (uses dean’s structure/classes) -->
    <div class="sb-section">Submissions</div>
    <div class="nav__dropdown<?= $subsOpen ? ' open' : '' ?>">
      <button
        class="nav__toggle<?= $subsOpen ? ' is-active' : '' ?>"
        type="button"
        aria-expanded="<?= $subsOpen ? 'true' : 'false' ?>"
        aria-controls="submenu-submissions"
      >
        <i class="fas fa-upload" aria-hidden="true"></i>
        <span style="text-align:left;display:block;">Submissions</span>
        <span class="caret">▾</span>
      </button>

      <div id="submenu-submissions" class="nav__submenu" role="group" aria-label="Submissions submenu">
        <a href="?page=faculty_manage_news"
           class="nav__sublink<?= $pg === 'faculty_manage_news' ? ' is-active' : '' ?>">
          <i class="fas fa-newspaper" aria-hidden="true"></i>
          <span>Submit News</span>
        </a>

        <a href="?page=faculty_manage_research"
           class="nav__sublink<?= $pg === 'faculty_manage_research' ? ' is-active' : '' ?>">
          <i class="fas fa-microscope" aria-hidden="true"></i>
          <span>Submit Research</span>
        </a>

        <a href="?page=faculty_manage_certifications"
           class="nav__sublink<?= $pg === 'faculty_manage_certifications' ? ' is-active' : '' ?>">
          <i class="fas fa-certificate" aria-hidden="true"></i>
          <span>Submit Certifications</span>
        </a>
      </div>
    </div>

    <!-- More (match dean layout; keep Faculty Portfolio only) -->
    <div class="sb-section">More</div>
    <a href="?page=faculty_portfolio"
       class="nav__link<?= $pg === 'faculty_portfolio' ? ' is-active' : '' ?>"
       <?= $pg === 'faculty_portfolio' ? 'aria-current="page"' : '' ?>>
      <i class="fas fa-user" aria-hidden="true"></i>
      <span>Portfolio</span>
      <span></span>
    </a>
  </nav>

  <div class="admin-sb__spacer"></div>

  <!-- User block (identical layout to dean; faculty data retained) -->
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
/* Dropdown toggle with ARIA support — mirrored from dean */
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
/* Auto-open the dropdown when a sublink is active */
document.querySelectorAll(".nav__dropdown").forEach(dd=>{
  if (dd.querySelector(".nav__sublink.is-active")) {
    dd.classList.add("open");
    const t = dd.querySelector(".nav__toggle");
    if(t){ t.classList.add("is-active"); t.setAttribute("aria-expanded","true"); }
  }
});

/* Mobile drawer behavior — same as dean */
function openSB(){ document.documentElement.classList.add("sb-open"); document.body.style.overflow='hidden'; }
function closeSB(){ document.documentElement.classList.remove("sb-open"); document.body.style.overflow=''; }
window.openSB = openSB; window.closeSB = closeSB;

document.addEventListener('DOMContentLoaded', function() {
  function bindHamburgerEvents(){
    const btns = document.querySelectorAll('[data-sb-open]');
    btns.forEach(btn=>{
      btn.addEventListener('click', handleHamburger, { passive:false });
      btn.addEventListener('touchstart', handleHamburger, { passive:false });
    });
  }
  function handleHamburger(e){ e.preventDefault(); e.stopPropagation(); openSB(); }
  bindHamburgerEvents();

  /* Scrim close */
  document.querySelectorAll("[data-sb-close]").forEach(el=>{
    const close = (e)=>{ e.preventDefault(); e.stopPropagation(); closeSB(); };
    el.addEventListener("click", close, { passive:false });
    el.addEventListener("touchstart", close, { passive:false });
  });

  /* Click outside to close (mobile) */
  document.addEventListener('click', (e)=>{
    if (window.innerWidth <= 1024 && document.documentElement.classList.contains('sb-open')) {
      const sidebar = document.querySelector('.admin-sb');
      const hamburger = document.querySelector('[data-sb-open]');
      if (sidebar && hamburger && !sidebar.contains(e.target) && !hamburger.contains(e.target)) closeSB();
    }
  });

  /* Resize/ESC handling */
  window.addEventListener('resize', ()=>{ if (window.innerWidth > 1024) closeSB(); });
  document.addEventListener('keydown', (e)=>{ if (e.key === 'Escape' && document.documentElement.classList.contains('sb-open')) closeSB(); });

  /* Optional: topbar scroll state (matches dean pages that have .admin-topbar) */
  const topbar = document.querySelector('.admin-topbar');
  const adminMain = document.querySelector('.admin-main');
  if (topbar && adminMain) {
    const apply = (st)=>{ if (st > 10) topbar.classList.add('scrolled'); else topbar.classList.remove('scrolled'); };
    adminMain.addEventListener('scroll', ()=> apply(adminMain.scrollTop));
    window.addEventListener('scroll', ()=> apply(window.pageYOffset || document.documentElement.scrollTop));
  }
});
</script>
