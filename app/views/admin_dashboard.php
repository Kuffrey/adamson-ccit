<?php
declare(strict_types=1);

/** Auth/user + safe defaults */
$user = class_exists('Auth') ? (Auth::user() ?? []) : [];
$username = $user['username'] ?? 'Admin';
$initial  = strtoupper(substr($username, 0, 1));

$recentNews         = $recentNews         ?? [];
$recentPrograms     = $recentPrograms     ?? [];
$pendingSubmissions = $pendingSubmissions ?? [];

function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

/** Count last 30 days */
$days30 = strtotime('-30 days');
$recentNews30 = 0;
foreach ($recentNews as $n) {
  $d = $n['date'] ?? $n['updated_at'] ?? $n['created_at'] ?? null;
  if ($d && strtotime((string)$d) >= $days30) $recentNews30++;
}

/** Active link helper */
$page = $_GET['page'] ?? '';
$active = function(string $prefix) use ($page){ return str_starts_with($page, $prefix) ? ' is-active' : ''; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex, nofollow" />
  <title>Admin Dashboard | AdU-CCIT</title>

  <!-- Base site styles -->
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
  <!-- Admin styles (final) -->
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css" />
</head>
<body>

<main class="admin-dashboard admin--with-sidebar">
  <!-- ===================== SIDEBAR (fixed) ===================== -->
  <aside class="admin-sb" aria-label="Admin navigation">
    <div class="admin-sb__brand">
      <span class="admin-sb__logo">CCIT</span>
      <strong class="admin-sb__title">Admin</strong>
    </div>

    <nav class="admin-sb__nav">
      <a href="?page=admin_dashboard" class="nav__link<?= $active('admin_dashboard') ?>">
        <span class="nav__ico" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm10 8h8V3h-8v18zM3 21h8v-6H3v6z" fill="currentColor"/></svg>
        </span>
        <span class="nav__label">Dashboard</span>
      </a>

      <a href="?page=admin_manage_news" class="nav__link<?= $active('admin_manage_news') ?>">
        <span class="nav__ico" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M4 5h16v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5zm3 4h10M7 13h10M7 17h6" fill="none" stroke="currentColor" stroke-width="2"/></svg>
        </span>
        <span class="nav__label">News</span>
      </a>

      <a href="?page=admin_manage_events" class="nav__link<?= $active('admin_manage_events') ?>">
        <span class="nav__ico" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M3 8h18M7 4v4m10-4v4M5 12h4m6 0h4M5 16h6" fill="none" stroke="currentColor" stroke-width="2"/></svg>
        </span>
        <span class="nav__label">Events</span>
      </a>

      <a href="?page=admin_manage_announcements" class="nav__link<?= $active('admin_manage_announcements') ?>">
        <span class="nav__ico" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M3 11h6l7-5v12l-7-5H3v-2z" fill="none" stroke="currentColor" stroke-width="2"/></svg>
        </span>
        <span class="nav__label">Announcements</span>
      </a>

      <a href="?page=admin_manage_programs" class="nav__link<?= $active('admin_manage_programs') ?>">
        <span class="nav__ico" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z" fill="currentColor"/></svg>
        </span>
        <span class="nav__label">Programs</span>
      </a>

      <a href="?page=admin_manage_faculty" class="nav__link<?= $active('admin_manage_faculty') ?>">
        <span class="nav__ico" aria-hidden="true">
          <svg viewBox="0 0 24 24"><circle cx="9" cy="7" r="4" fill="none" stroke="currentColor" stroke-width="2"/><path d="M3 21v-2a4 4 0 0 1 4-4h4m6-4v8m-4-4h8" fill="none" stroke="currentColor" stroke-width="2"/></svg>
        </span>
        <span class="nav__label">Faculty</span>
      </a>
    </nav>

    <div class="admin-sb__spacer"></div>

    <div class="admin-sb__user">
      <div class="userline">
        <span class="avatar"><?= esc($initial) ?></span>
        <div class="uinfo">
          <strong class="uname"><?= esc($username) ?></strong>
          <small class="urole">Administrator</small>
        </div>
      </div>
      <a class="btn btn--muted btn--sm" href="?page=logout">Logout</a>
    </div>
  </aside>

  <!-- ===================== MAIN ===================== -->
  <section class="admin-main">
    <!-- Topbar (same height & divider as sidebar brand) -->
    <div class="admin-topbar">
      <strong>Admin Dashboard</strong>
      <div class="admin-topbar__spacer"></div>
      <div class="atb-user" aria-label="Signed in user">
        <span class="atb-avatar"><?= esc($initial) ?></span>
        <span class="atb-name"><?= esc($username) ?></span>
      </div>
    </div>

    <div class="admin-dashboard__content container">
      <!-- KPI cards -->
      <div class="admin-dashboard__cards">
        <div class="admin-dashboard__card">
          <h3>News (last 30d)</h3>
          <div class="count"><?= (int)$recentNews30 ?></div>
          <a class="link" href="?page=admin_manage_news">Manage News →</a>
        </div>
        <div class="admin-dashboard__card">
          <h3>Programs updated</h3>
          <div class="count"><?= (int)count($recentPrograms) ?></div>
          <a class="link" href="?page=admin_manage_programs">Manage Programs →</a>
        </div>
        <div class="admin-dashboard__card">
          <h3>Pending approvals</h3>
          <div class="count"><?= (int)count($pendingSubmissions) ?></div>
          <a class="link" href="?page=admin_dashboard#pending">Review pending →</a>
        </div>
      </div>

      <!-- Quick actions -->
      <div class="admin-dashboard__block">
        <h2>Quick Actions</h2>
        <div class="quick-actions">
          <a class="btn btn--muted" href="?page=admin_manage_news&action=new">＋ Create News</a>
          <a class="btn btn--muted" href="?page=admin_manage_events&action=new">＋ Create Event</a>
          <a class="btn btn--muted" href="?page=admin_manage_announcements&action=new">＋ Create Announcement</a>
          <a class="btn btn--muted" href="?page=admin_manage_programs&action=new">＋ Add Program</a>
          <a class="btn btn--muted" href="?page=admin_manage_faculty&action=new">＋ Add Faculty</a>
        </div>
      </div>

      <!-- Recent news -->
      <div class="admin-dashboard__block">
        <h2>Recent News</h2>
        <?php if (!$recentNews): ?>
          <div class="empty">No news yet. Create your first article from “Quick Actions”.</div>
        <?php else: ?>
          <div class="table-scroll">
            <table class="admin-dashboard__table">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Status</th>
                  <th>Updated</th>
                  <th class="t-right">Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($recentNews as $n): ?>
                <?php
                  $id     = (string)($n['id'] ?? '');
                  $status = strtolower((string)($n['status'] ?? 'draft'));
                  $dtRaw  = $n['date'] ?? $n['updated_at'] ?? $n['created_at'] ?? '';
                  $dtDisp = $dtRaw ? date('M j, Y', strtotime((string)$dtRaw)) : '';
                ?>
                <tr>
                  <td class="truncate" title="<?= esc($n['title'] ?? 'Untitled') ?>"><?= esc($n['title'] ?? 'Untitled') ?></td>
                  <td><span class="badge badge--<?= esc($status) ?>"><?= esc(ucfirst($status)) ?></span></td>
                  <td><time datetime="<?= esc((string)$dtRaw) ?>"><?= esc($dtDisp) ?></time></td>
                  <td class="t-right admin-dashboard__actions">
                    <a class="btn btn--muted btn--sm" href="?page=admin_manage_news&action=edit&id=<?= esc(urlencode($id)) ?>">Edit</a>
                    <a class="btn btn--muted btn--sm" href="?page=news&view=<?= esc(urlencode($id)) ?>" target="_blank" rel="noopener">Preview</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

      <!-- Pending submissions -->
      <div id="pending" class="admin-dashboard__block">
        <h2>Pending Submissions (from faculty)</h2>
        <?php if (!$pendingSubmissions): ?>
          <div class="empty">Nothing pending right now.</div>
        <?php else: ?>
          <div class="table-scroll">
            <table class="admin-dashboard__table">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>Title</th>
                  <th>Submitted by</th>
                  <th>Submitted</th>
                  <th class="t-right">Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($pendingSubmissions as $s): ?>
                <?php
                  $sid   = (string)($s['id'] ?? '');
                  $stype = $s['type'] ?? 'item';
                  $sdt   = $s['created_at'] ?? '';
                  $sdtD  = $sdt ? date('M j, Y', strtotime((string)$sdt)) : '';
                ?>
                <tr>
                  <td><span class="badge"><?= esc(ucfirst((string)$stype)) ?></span></td>
                  <td class="truncate" title="<?= esc($s['title'] ?? 'Untitled') ?>"><?= esc($s['title'] ?? 'Untitled') ?></td>
                  <td><?= esc($s['submitted_by'] ?? '-') ?></td>
                  <td><time datetime="<?= esc((string)$sdt) ?>"><?= esc($sdtD) ?></time></td>
                  <td class="t-right admin-dashboard__actions">
                    <a class="btn btn--muted btn--sm" href="?page=admin_manage_news&action=review&id=<?= esc(urlencode($sid)) ?>">Review</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

      <!-- Recently updated programs -->
      <div class="admin-dashboard__block">
        <h2>Recently Updated Programs</h2>
        <?php if (!$recentPrograms): ?>
          <div class="empty">No recent updates.</div>
        <?php else: ?>
          <ul class="grid-tiles" role="list">
            <?php foreach ($recentPrograms as $p): ?>
              <?php
                $pid   = (string)($p['id'] ?? '');
                $pname = $p['name'] ?? $p['title'] ?? 'Program';
                $plevel= $p['level'] ?? '';
                $pdt   = $p['updated_at'] ?? $p['date'] ?? $p['created_at'] ?? '';
                $pdtD  = $pdt ? date('M j, Y', strtotime((string)$pdt)) : '';
              ?>
              <li class="tile">
                <div class="tile__body">
                  <strong class="tile__title"><?= esc($pname) ?></strong>
                  <span class="muted"><?= esc($plevel) ?></span>
                  <?php if ($pdtD): ?><span class="muted">Updated <?= esc($pdtD) ?></span><?php endif; ?>
                </div>
                <div class="tile__actions">
                  <a class="btn btn--muted btn--sm" href="?page=admin_manage_programs&action=edit&id=<?= esc(urlencode($pid)) ?>">Edit</a>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <footer class="admin-dashboard__footer">
        <small>CCIT CMS • You’re signed in as <strong><?= esc($username) ?></strong></small>
      </footer>
    </div>
  </section>
</main>

</body>
</html>
