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
      <a href="?page=admin_events_page" class="nav__link is-active">Events Page</a>
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
      <span class="admin-topbar__title">Edit Events Page</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user" aria-label="Signed in user">
        <span class="admin-topbar__avatar">A</span>
        <span class="admin-topbar__name">Admin</span>
      </div>
    </header>
    <section class="admin-dashboard__content container">
      <section class="admin-dashboard__block admin-dashboard__block--overview">
        <h1>Events Page Settings</h1>
        <?php if (!empty($_GET['success'])): ?>
          <div class="alert alert-success">Events page updated successfully!</div>
        <?php endif; ?>
        <form method="post" action="/adamson-ccit/public/index.php?page=admin_events_page_save">
          <fieldset>
            <legend>Subhero</legend>
            <label>Lead: <input type="text" name="subhero_lead" value="<?= htmlspecialchars($settings['subhero_lead'] ?? '') ?>" /></label>
          </fieldset>
          <fieldset>
            <legend>Intro/Overview</legend>
            <label>Title: <input type="text" name="intro_title" value="<?= htmlspecialchars($settings['intro_title'] ?? '') ?>" /></label>
            <label>Lead: <textarea name="intro_lead"><?= htmlspecialchars($settings['intro_lead'] ?? '') ?></textarea></label>
          </fieldset>
          <fieldset>
            <legend>CTA</legend>
            <label>CTA Title: <input type="text" name="cta_title" value="<?= htmlspecialchars($settings['cta_title'] ?? '') ?>" /></label>
            <label>CTA Body: <textarea name="cta_body"><?= htmlspecialchars($settings['cta_body'] ?? '') ?></textarea></label>
            <label>CTA Button Label: <input type="text" name="cta_btn_label" value="<?= htmlspecialchars($settings['cta_btn_label'] ?? '') ?>" /></label>
            <label>CTA Button URL: <input type="text" name="cta_btn_url" value="<?= htmlspecialchars($settings['cta_btn_url'] ?? '') ?>" /></label>
          </fieldset>
          <button type="submit">Save Events Page</button>
        </form>
      </section>
    </section>
  </main>
</div>
