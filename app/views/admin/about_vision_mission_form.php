<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<div class="admin-cms-layout">
  <aside class="admin-sb" aria-label="Admin navigation">
    <div class="admin-sb__brand">
      <span class="admin-sb__logo">CCIT</span>
      <span class="admin-sb__title">CMS Admin</span>
    </div>
    <nav class="admin-sb__nav">
      <a href="?page=admin_dashboard" class="nav__link">Dashboard</a>
      <a href="?page=admin_homepage" class="nav__link">Home</a>
      <a href="?page=admin_about_vision_mission" class="nav__link is-active">About: Vision & Mission</a>
    </nav>
    <div class="admin-sb__spacer"></div>
    <div class="admin-sb__user">
      <div class="userline">
        <span class="avatar">A</span>
        <div class="uinfo">
          <span class="uname">Admin</span>
          <span class="urole">Administrator</span>
        </div>
      </div>
      <a class="btn btn--muted btn--sm" href="?page=logout">Logout</a>
    </div>
  </aside>
  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Edit About: Vision & Mission</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user" aria-label="Signed in user">
        <span class="admin-topbar__avatar">A</span>
        <span class="admin-topbar__name">Admin</span>
      </div>
    </header>
    <section class="admin-dashboard__content container">
      <section class="admin-dashboard__block admin-dashboard__block--overview">
        <h1>About: Vision & Mission</h1>
        <?php if (!empty($_GET['success'])): ?>
          <div class="alert alert-success">About page updated successfully!</div>
        <?php endif; ?>
        <form method="post" action="/adamson-ccit/public/index.php?page=admin_about_vision_mission_save">
          <fieldset>
            <legend>Main Section</legend>
            <label>Intro: <input type="text" name="main_intro" value="<?= htmlspecialchars($about['main_intro'] ?? '') ?>" /></label>
            <label>Vision: <textarea name="main_vision"><?= htmlspecialchars($about['main_vision'] ?? '') ?></textarea></label>
            <label>Mission: <textarea name="main_mission"><?= htmlspecialchars($about['main_mission'] ?? '') ?></textarea></label>
          </fieldset>
          <fieldset>
            <legend>Department 1 (IT & IS)</legend>
            <label>Title: <input type="text" name="dept1_title" value="<?= htmlspecialchars($about['dept1_title'] ?? '') ?>" /></label>
            <label>Vision: <textarea name="dept1_vision"><?= htmlspecialchars($about['dept1_vision'] ?? '') ?></textarea></label>
            <label>Mission: <textarea name="dept1_mission"><?= htmlspecialchars($about['dept1_mission'] ?? '') ?></textarea></label>
            <label>Objectives: <textarea name="dept1_objectives"><?= htmlspecialchars($about['dept1_objectives'] ?? '') ?></textarea></label>
          </fieldset>
          <fieldset>
            <legend>Department 2 (Computer Science)</legend>
            <label>Title: <input type="text" name="dept2_title" value="<?= htmlspecialchars($about['dept2_title'] ?? '') ?>" /></label>
            <label>Vision: <textarea name="dept2_vision"><?= htmlspecialchars($about['dept2_vision'] ?? '') ?></textarea></label>
            <label>Mission: <textarea name="dept2_mission"><?= htmlspecialchars($about['dept2_mission'] ?? '') ?></textarea></label>
            <label>Objectives: <textarea name="dept2_objectives"><?= htmlspecialchars($about['dept2_objectives'] ?? '') ?></textarea></label>
          </fieldset>
          <button type="submit">Save About Page</button>
        </form>
      </section>
    </section>
  </main>
</div>
