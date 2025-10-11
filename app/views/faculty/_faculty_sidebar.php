<?php
if (!function_exists('esc')) {
  function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? 'Faculty';
$roleName = ucfirst($user['role'] ?? 'Faculty');

try {
  $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE username = ? LIMIT 1");
  $stmt->execute([$username]);
  $faculty   = $stmt->fetch(PDO::FETCH_ASSOC);
  $firstName = $faculty['first_name'] ?? "Faculty";
  $lastName  = $faculty['last_name']  ?? "";
  $fullName  = $faculty ? "{$firstName} {$lastName}" : $username;
} catch (PDOException $e) {
  $firstName = "Faculty"; $lastName = ""; $fullName = $username;
}

$pg = $_GET['page'] ?? '';
$newsOpen = in_array($pg, [
  'faculty_manage_news','faculty_manage_research','faculty_manage_certifications'
], true);

/* Palette / tokens */
$bg        = '#2c3e50';
$bg2       = '#34495e';
$muted     = '#95a5a6';
$text      = '#bdc3c7';
$textHi    = '#ecf0f1';
$textDark  = '#e6eef5';
$accent    = '#008040';
$accentHov = '#006d37';
$accent2   = '#00713D';
$danger    = '#e74c3c';
$dangerHov = '#c0392b';
$radius    = '10px';
$linkPad   = '0.7rem 1rem';

/* Helpers */
$linkBase = "display:flex;align-items:center;gap:.65rem;padding:$linkPad;color:$text;text-decoration:none;border:none;background:transparent;width:100%;font-size:.92rem;font-weight:600;line-height:1;border-radius:$radius;transition:all .18s ease;outline:none;box-shadow:none;";
$linkHoverJS   = "this.dataset.bg=this.style.backgroundColor;this.dataset.c=this.style.color;this.style.backgroundColor='$bg2';this.style.color='$textHi';this.style.transform='translateY(-1px)';";
$linkUnhoverJS = "this.style.backgroundColor=this.dataset.bg||'transparent';this.style.color=this.dataset.c||'$text';this.style.transform='';";
$subLinkBase = "display:flex;align-items:center;gap:.55rem;padding:.55rem 1rem .55rem 2.4rem;color:$muted;text-decoration:none;font-size:.86rem;font-weight:500;border-radius:$radius;transition:all .18s ease;outline:none;box-shadow:none;";
$subHoverJS   = "this.dataset.bg=this.style.backgroundColor;this.dataset.c=this.style.color;this.style.backgroundColor='$bg';this.style.color='$textDark';";
$subUnhoverJS = "this.style.backgroundColor=this.dataset.bg||'transparent';this.style.color=this.dataset.c||'$muted';";
$focusOn  = "this.style.boxShadow='0 0 0 3px rgba(0,128,64,.25)';";
$focusOff = "this.style.boxShadow='none';";
?>

<!-- SCRIM (mobile overlay) -->
<div class="sb-scrim" data-sb-close
     style="position:fixed;inset:0;background:rgba(0,0,0,.38);backdrop-filter:saturate(120%) blur(1px);display:none;z-index:900;"></div>

<aside class="admin-sb sb--split" aria-label="Faculty navigation"
  style="position:sticky;top:0;align-self:start;height:100vh;width:264px;min-width:264px;background:<?= $bg ?>;border-right:1px solid <?= $bg2 ?>;display:flex;flex-direction:column;z-index:1000;">

  <!-- Brand -->
  <div class="admin-sb__brand"
       style="background:<?= $bg ?>;color:white;padding:1rem 1.1rem;text-align:center;border-bottom:1px solid <?= $bg2 ?>;">
    <a href="?page=faculty_dashboard" aria-label="CCIT Faculty Portal Home" style="display:inline-block;">
      <img src="/adamson-ccit/public/assets/images/adu-ccit-logo.png"
           alt="AdU · CCIT" width="240" height="40" loading="eager"
           style="max-width:100%;height:auto;vertical-align:middle;filter:drop-shadow(0 0 0 rgba(0,0,0,0));">
    </a>
  </div>

  <!-- Nav -->
  <nav role="navigation" style="display:flex;flex-direction:column;gap:.35rem;padding:.6rem 0;">
    <!-- Section label -->
    <div style="color:<?= $muted ?>;font-size:.78rem;font-weight:700;padding:.45rem 1.1rem .25rem;letter-spacing:.06em;text-transform:uppercase;">
      General
    </div>

    <!-- Dashboard -->
    <a href="?page=faculty_dashboard"
       <?= $pg === 'faculty_dashboard' ? 'aria-current="page"' : '' ?>
       onmouseenter="<?= $linkHoverJS ?>" onmouseleave="<?= $linkUnhoverJS ?>"
       onfocus="<?= $focusOn ?>" onblur="<?= $focusOff ?>"
       style="<?= $linkBase ?><?= $pg==='faculty_dashboard' ? "background:$accent;color:white;box-shadow:inset 0 0 0 1px rgba(255,255,255,.08);" : '' ?>">
      <i class="fas fa-tachometer-alt" aria-hidden="true"
         style="width:18px;min-width:18px;text-align:center;font-size:.95rem;opacity:.95;"></i>
      <span>Dashboard</span>
      <span style="margin-left:auto;"></span>
    </a>

    <!-- Section label -->
    <div style="color:<?= $muted ?>;font-size:.78rem;font-weight:700;padding:.6rem 1.1rem .25rem;letter-spacing:.06em;text-transform:uppercase;">
      Submissions
    </div>

    <!-- Submissions dropdown -->
    <div class="nav__dropdown<?= $newsOpen ? ' open' : '' ?>">
      <button type="button"
        aria-expanded="<?= $newsOpen ? 'true' : 'false' ?>"
        aria-controls="submenu-content"
        onmouseenter="<?= $linkHoverJS ?>" onmouseleave="<?= $linkUnhoverJS ?>"
        onfocus="<?= $focusOn ?>" onblur="<?= $focusOff ?>"
        style="<?= $linkBase ?>justify-content:space-between;cursor:pointer;">
        <span style="display:flex;align-items:center;gap:.65rem;">
          <i class="fas fa-upload" aria-hidden="true"
             style="width:18px;min-width:18px;text-align:center;font-size:.95rem;opacity:.95;"></i>
          <span>Submissions</span>
        </span>
        <span class="caret"
              style="font-size:.8rem;opacity:.75;transform:rotate(<?= $newsOpen ? '180deg' : '0deg' ?>);transition:transform .18s ease;">▾</span>
      </button>

      <!-- Submenu -->
      <div id="submenu-content" role="group" aria-label="Submissions submenu"
           style="background:<?= $bg2 ?>;border-top:1px solid <?= $bg ?>;border-bottom:1px solid <?= $bg ?>;<?= $newsOpen ? '' : 'display:none;' ?>">
        <a href="?page=faculty_manage_news"
           <?= $pg === 'faculty_manage_news' ? 'aria-current="page"' : '' ?>
           onmouseenter="<?= $subHoverJS ?>" onmouseleave="<?= $subUnhoverJS ?>"
           onfocus="<?= $focusOn ?>" onblur="<?= $focusOff ?>"
           style="<?= $subLinkBase ?><?= $pg==='faculty_manage_news' ? "background:$accent2;color:white;font-weight:600;box-shadow:inset 0 0 0 1px rgba(255,255,255,.08);" : '' ?>">
          <i class="fas fa-newspaper" aria-hidden="true"
             style="width:16px;min-width:16px;text-align:center;font-size:.9rem;opacity:.92;"></i>
          <span>News</span>
        </a>

        <a href="?page=faculty_manage_research"
           <?= $pg === 'faculty_manage_research' ? 'aria-current="page"' : '' ?>
           onmouseenter="<?= $subHoverJS ?>" onmouseleave="<?= $subUnhoverJS ?>"
           onfocus="<?= $focusOn ?>" onblur="<?= $focusOff ?>"
           style="<?= $subLinkBase ?><?= $pg==='faculty_manage_research' ? "background:$accent2;color:white;font-weight:600;box-shadow:inset 0 0 0 1px rgba(255,255,255,.08);" : '' ?>">
          <i class="fas fa-microscope" aria-hidden="true"
             style="width:16px;min-width:16px;text-align:center;font-size:.9rem;opacity:.92;"></i>
          <span>Research</span>
        </a>

        <a href="?page=faculty_manage_certifications"
           <?= $pg === 'faculty_manage_certifications' ? 'aria-current="page"' : '' ?>
           onmouseenter="<?= $subHoverJS ?>" onmouseleave="<?= $subUnhoverJS ?>"
           onfocus="<?= $focusOn ?>" onblur="<?= $focusOff ?>"
           style="<?= $subLinkBase ?><?= $pg==='faculty_manage_certifications' ? "background:$accent2;color:white;font-weight:600;box-shadow:inset 0 0 0 1px rgba(255,255,255,.08);" : '' ?>">
          <i class="fas fa-certificate" aria-hidden="true"
             style="width:16px;min-width:16px;text-align:center;font-size:.9rem;opacity:.92;"></i>
          <span>Certifications</span>
        </a>
      </div>
    </div>

    <!-- Section label -->
    <div style="color:<?= $muted ?>;font-size:.78rem;font-weight:700;padding:.6rem 1.1rem .25rem;letter-spacing:.06em;text-transform:uppercase;">
      More
    </div>

    <!-- Portfolio -->
    <a href="?page=faculty_portfolio"
       <?= $pg === 'faculty_portfolio' ? 'aria-current="page"' : '' ?>
       onmouseenter="<?= $linkHoverJS ?>" onmouseleave="<?= $linkUnhoverJS ?>"
       onfocus="<?= $focusOn ?>" onblur="<?= $focusOff ?>"
       style="<?= $linkBase ?><?= $pg==='faculty_portfolio' ? "background:$accent;color:white;box-shadow:inset 0 0 0 1px rgba(255,255,255,.08);" : '' ?>">
      <i class="fas fa-user" aria-hidden="true"
         style="width:18px;min-width:18px;text-align:center;font-size:.95rem;opacity:.95;"></i>
      <span>Portfolio</span>
      <span style="margin-left:auto;"></span>
    </a>
  </nav>

  <div style="flex:1 1 auto;"></div>

  <!-- User block -->
  <div style="padding:1rem 1.1rem;border-top:1px solid <?= $bg2 ?>;background:<?= $bg ?>;">
    <div style="display:flex;align-items:center;margin-bottom:.8rem;">
      <span style="width:38px;height:38px;border-radius:50%;background:<?= $accent ?>;color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.92rem;margin-right:.75rem;">
        <?= esc(strtoupper(($firstName[0] ?? 'F') . ($lastName[0] ?? ''))) ?>
      </span>
      <div style="display:flex;flex-direction:column;">
        <span style="font-weight:600;font-size:.94rem;color:<?= $textHi ?>;"><?= esc($fullName) ?></span>
        <span style="font-size:.82rem;color:<?= $muted ?>;"><?= esc($roleName) ?></span>
      </div>
    </div>
    <a href="?page=logout"
       onmouseenter="this.style.backgroundColor='<?= $dangerHov ?>'"
       onmouseleave="this.style.backgroundColor='<?= $danger ?>'"
       style="display:block;width:100%;padding:.65rem;background:<?= $danger ?>;color:white;text-decoration:none;text-align:center;border-radius:8px;font-size:.86rem;font-weight:700;letter-spacing:.01em;">
      Logout
    </a>
  </div>
</aside>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // Dropdown toggles
  document.querySelectorAll(".nav__dropdown > button").forEach(btn => {
    btn.addEventListener("click", () => {
      const dd = btn.closest(".nav__dropdown");
      const open = dd.classList.toggle("open");
      btn.setAttribute("aria-expanded", open ? "true" : "false");
      const caret = btn.querySelector(".caret");
      if (caret) caret.style.transform = open ? "rotate(180deg)" : "rotate(0deg)";
      const panel = dd.querySelector("#submenu-content");
      if (panel) panel.style.display = open ? "" : "none";
    });
  });

  // Ensure current sublink opens dropdown on load
  document.querySelectorAll(".nav__dropdown").forEach(dd => {
    if (dd.querySelector("[aria-current='page']")) {
      dd.classList.add("open");
      const t = dd.querySelector("button");
      if (t) { t.setAttribute("aria-expanded","true"); const c = t.querySelector(".caret"); if (c) c.style.transform = "rotate(180deg)"; }
      const p = dd.querySelector("#submenu-content"); if (p) p.style.display = "";
    }
  });

  // Scrim closer (if you toggle sidebar on mobile elsewhere)
  document.querySelectorAll("[data-sb-close]").forEach(el => {
    const close = (e)=>{ e.preventDefault(); e.stopPropagation(); document.documentElement.classList.remove("sb-open"); document.body.style.overflow=''; const s=document.querySelector(".sb-scrim"); if (s) s.style.display='none'; };
    el.addEventListener("click", close);
    el.addEventListener("touchstart", close, {passive:false});
  });
});
</script>
