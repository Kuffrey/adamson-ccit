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
      <a href="?page=admin_about_history" class="nav__link is-active">About: History</a>
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
      <span class="admin-topbar__title">Edit About: History</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user" aria-label="Signed in user">
        <span class="admin-topbar__avatar">A</span>
        <span class="admin-topbar__name">Admin</span>
      </div>
    </header>
    <section class="admin-dashboard__content container">
      <section class="admin-dashboard__block admin-dashboard__block--overview">
        <h1>About: History</h1>
        <?php if (!empty($_GET['success'])): ?>
          <div class="alert alert-success">About History updated successfully!</div>
        <?php endif; ?>
        <form method="post" action="/adamson-ccit/public/index.php?page=admin_about_history_save">
          <fieldset>
            <legend>Subhero</legend>
            <label>Lead: <input type="text" name="subhero_lead" value="<?= htmlspecialchars($about['subhero_lead'] ?? '') ?>" /></label>
          </fieldset>
          <fieldset>
            <legend>Intro/Overview</legend>
            <label>Title: <input type="text" name="intro_title" value="<?= htmlspecialchars($about['intro_title'] ?? '') ?>" /></label>
            <label>Lead: <textarea name="intro_lead"><?= htmlspecialchars($about['intro_lead'] ?? '') ?></textarea></label>
          </fieldset>
          <fieldset>
            <legend>At a Glance</legend>
            <label>Fact Title: <input type="text" name="fact_title" value="<?= htmlspecialchars($about['fact_title'] ?? '') ?>" /></label>
            <label>Fact 1: <input type="text" name="fact_1" value="<?= htmlspecialchars($about['fact_1'] ?? '') ?>" /></label>
            <label>Fact 2: <input type="text" name="fact_2" value="<?= htmlspecialchars($about['fact_2'] ?? '') ?>" /></label>
            <label>Fact 3: <input type="text" name="fact_3" value="<?= htmlspecialchars($about['fact_3'] ?? '') ?>" /></label>
          </fieldset>
          <fieldset>
            <legend>Origins</legend>
            <label>Title: <input type="text" name="origins_title" value="<?= htmlspecialchars($about['origins_title'] ?? '') ?>" /></label>
            <label>Body: <textarea name="origins_body"><?= htmlspecialchars($about['origins_body'] ?? '') ?></textarea></label>
          </fieldset>
          <fieldset>
            <legend>Milestones (JSON, advanced)</legend>
            <label>Milestones: <textarea name="milestones"><?= htmlspecialchars(json_encode($about['milestones'] ?? [], JSON_PRETTY_PRINT)) ?></textarea></label>
          </fieldset>
          <fieldset>
            <legend>Leadership</legend>
            <label>Leaders Title: <input type="text" name="leaders_title" value="<?= htmlspecialchars($about['leaders_title'] ?? '') ?>" /></label>
            <label>Leaders List (HTML): <textarea name="leaders_list"><?= htmlspecialchars($about['leaders_list'] ?? '') ?></textarea></label>
            <label>Academic Leads Title: <input type="text" name="academic_leads_title" value="<?= htmlspecialchars($about['academic_leads_title'] ?? '') ?>" /></label>
            <label>Academic Leads List (HTML): <textarea name="academic_leads_list"><?= htmlspecialchars($about['academic_leads_list'] ?? '') ?></textarea></label>
          </fieldset>
          <fieldset>
            <legend>Identity & Values</legend>
            <label>Identity Title: <input type="text" name="identity_title" value="<?= htmlspecialchars($about['identity_title'] ?? '') ?>" /></label>
            <label>Identity Items (HTML): <textarea name="identity_items"><?= htmlspecialchars($about['identity_items'] ?? '') ?></textarea></label>
          </fieldset>
          <fieldset>
            <legend>CTA</legend>
            <label>CTA Title: <input type="text" name="cta_title" value="<?= htmlspecialchars($about['cta_title'] ?? '') ?>" /></label>
            <label>CTA Body: <textarea name="cta_body"><?= htmlspecialchars($about['cta_body'] ?? '') ?></textarea></label>
            <label>CTA Button Label: <input type="text" name="cta_btn_label" value="<?= htmlspecialchars($about['cta_btn_label'] ?? '') ?>" /></label>
            <label>CTA Button URL: <input type="text" name="cta_btn_url" value="<?= htmlspecialchars($about['cta_btn_url'] ?? '') ?>" /></label>
          </fieldset>
          <button type="submit">Save About History</button>
        </form>
      </section>
    </section>
  </main>
</div>
