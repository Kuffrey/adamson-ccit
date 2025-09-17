<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<div class="admin-cms-layout">
  <aside class="admin-sb" aria-label="Admin navigation">
    <div class="admin-sb__brand">
      <span class="admin-sb__logo">CCIT</span>
      <span class="admin-sb__title">CMS Admin</span>
    </div>
    <nav class="admin-sb__nav">
      <a href="?page=admin_dashboard" class="nav__link">Dashboard</a>
      <a href="?page=admin_homepage" class="nav__link is-active">Home</a>
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
      <span class="admin-topbar__title">Edit Homepage</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user" aria-label="Signed in user">
        <span class="admin-topbar__avatar">A</span>
        <span class="admin-topbar__name">Admin</span>
      </div>
    </header>

    <section class="admin-dashboard__content container">
      <section class="admin-dashboard__block admin-dashboard__block--overview">
        <h1>Homepage Settings</h1>

        <?php if (!empty($_GET['success'])): ?>
          <div class="alert alert-success">Homepage updated successfully!</div>
        <?php endif; ?>

        <form method="post" action="/adamson-ccit/public/index.php?page=admin_homepage_save">

          <!-- HERO -->
          <fieldset>
            <legend>Hero Section</legend>
            <label>Eyebrow:
              <input type="text" name="hero_eyebrow" value="<?= htmlspecialchars($settings['hero_eyebrow'] ?? '') ?>">
            </label>
            <label>Title:
              <input type="text" name="hero_title" value="<?= htmlspecialchars($settings['hero_title'] ?? '') ?>">
            </label>
            <label>Subtitle:
              <input type="text" name="hero_subtitle" value="<?= htmlspecialchars($settings['hero_subtitle'] ?? '') ?>">
            </label>
            <label>Background Image:
              <input type="text" name="hero_bg" value="<?= htmlspecialchars($settings['hero_bg'] ?? '') ?>">
            </label>
            <label>Primary Button Text:
              <input type="text" name="btn_primary_text" value="<?= htmlspecialchars($settings['btn_primary_text'] ?? '') ?>">
            </label>
            <label>Primary Button URL:
              <input type="text" name="btn_primary_url" value="<?= htmlspecialchars($settings['btn_primary_url'] ?? '') ?>">
            </label>
            <label>Secondary Button Text:
              <input type="text" name="btn_secondary_text" value="<?= htmlspecialchars($settings['btn_secondary_text'] ?? '') ?>">
            </label>
            <label>Secondary Button URL:
              <input type="text" name="btn_secondary_url" value="<?= htmlspecialchars($settings['btn_secondary_url'] ?? '') ?>">
            </label>
          </fieldset>

          <!-- WHY -->
          <fieldset>
            <legend>Why Section</legend>
            <label>Title:
              <input type="text" name="why_title" value="<?= htmlspecialchars($settings['why_title'] ?? '') ?>">
            </label>
            <label>Subtitle:
              <input type="text" name="why_subtitle" value="<?= htmlspecialchars($settings['why_subtitle'] ?? '') ?>">
            </label>

            <h4>Faculty Card</h4>
            <label>Title:
              <input type="text" name="why_faculty_text" value="<?= htmlspecialchars($settings['why_faculty_text'] ?? '') ?>">
            </label>
            <label>Description:
              <input type="text" name="why_faculty_desc" value="<?= htmlspecialchars($settings['why_faculty_desc'] ?? '') ?>">
            </label>
            <label>Link Label:
              <input type="text" name="why_faculty_link_label" value="<?= htmlspecialchars($settings['why_faculty_link_label'] ?? '') ?>">
            </label>
            <label>Link URL:
              <input type="text" name="why_faculty_link" value="<?= htmlspecialchars($settings['why_faculty_link'] ?? '') ?>">
            </label>

            <h4>Facilities Card</h4>
            <label>Title:
              <input type="text" name="why_facilities_text" value="<?= htmlspecialchars($settings['why_facilities_text'] ?? '') ?>">
            </label>
            <label>Description:
              <input type="text" name="why_facilities_desc" value="<?= htmlspecialchars($settings['why_facilities_desc'] ?? '') ?>">
            </label>
            <label>Link Label:
              <input type="text" name="why_facilities_link_label" value="<?= htmlspecialchars($settings['why_facilities_link_label'] ?? '') ?>">
            </label>
            <label>Link URL:
              <input type="text" name="why_facilities_link" value="<?= htmlspecialchars($settings['why_facilities_link'] ?? '') ?>">
            </label>

            <h4>Career Card</h4>
            <label>Title:
              <input type="text" name="why_career_text" value="<?= htmlspecialchars($settings['why_career_text'] ?? '') ?>">
            </label>
            <label>Description:
              <input type="text" name="why_career_desc" value="<?= htmlspecialchars($settings['why_career_desc'] ?? '') ?>">
            </label>
            <label>Link Label:
              <input type="text" name="why_career_link_label" value="<?= htmlspecialchars($settings['why_career_link_label'] ?? '') ?>">
            </label>
            <label>Link URL:
              <input type="text" name="why_career_link" value="<?= htmlspecialchars($settings['why_career_link'] ?? '') ?>">
            </label>
          </fieldset>

          <!-- SPOTLIGHT -->
          <fieldset>
            <legend>Spotlight Section</legend>
            <label>Eyebrow:
              <input type="text" name="spotlight_eyebrow" value="<?= htmlspecialchars($settings['spotlight_eyebrow'] ?? '') ?>">
            </label>
            <label>Title:
              <input type="text" name="spotlight_title" value="<?= htmlspecialchars($settings['spotlight_title'] ?? '') ?>">
            </label>
            <label>Blurb:
              <input type="text" name="spotlight_blurb" value="<?= htmlspecialchars($settings['spotlight_blurb'] ?? '') ?>">
            </label>
            <label>Image:
              <input type="text" name="spotlight_image" value="<?= htmlspecialchars($settings['spotlight_image'] ?? '') ?>">
            </label>
            <label>Image Alt:
              <input type="text" name="spotlight_image_alt" value="<?= htmlspecialchars($settings['spotlight_image_alt'] ?? '') ?>">
            </label>
            <label>CTA Text:
              <input type="text" name="spotlight_cta_text" value="<?= htmlspecialchars($settings['spotlight_cta_text'] ?? '') ?>">
            </label>
            <label>CTA URL:
              <input type="text" name="spotlight_cta_url" value="<?= htmlspecialchars($settings['spotlight_cta_url'] ?? '') ?>">
            </label>
            <label>CTA2 Text:
              <input type="text" name="spotlight_cta2_text" value="<?= htmlspecialchars($settings['spotlight_cta2_text'] ?? '') ?>">
            </label>
            <label>CTA2 URL:
              <input type="text" name="spotlight_cta2_url" value="<?= htmlspecialchars($settings['spotlight_cta2_url'] ?? '') ?>">
            </label>
            <label>Video URL:
              <input type="text" name="spotlight_video_url" value="<?= htmlspecialchars($settings['spotlight_video_url'] ?? '') ?>">
            </label>
          </fieldset>

          <!-- PROGRAMS BLURBS -->
          <fieldset>
            <legend>Programs Blurbs</legend>
            <label>Undergraduate:
              <textarea name="programs_undergrad_blurb" rows="2"><?= htmlspecialchars($settings['programs_undergrad_blurb'] ?? '') ?></textarea>
            </label>
            <label>Dual Degree:
              <textarea name="programs_dual_blurb" rows="2"><?= htmlspecialchars($settings['programs_dual_blurb'] ?? '') ?></textarea>
            </label>
            <label>Graduate Studies:
              <textarea name="programs_grad_blurb" rows="2"><?= htmlspecialchars($settings['programs_grad_blurb'] ?? '') ?></textarea>
            </label>
          </fieldset>

          <!-- CTA -->
          <fieldset>
            <legend>Final CTA</legend>
            <label>Title:
              <input type="text" name="cta_title" value="<?= htmlspecialchars($settings['cta_title'] ?? '') ?>">
            </label>
            <label>Description:
              <input type="text" name="cta_description" value="<?= htmlspecialchars($settings['cta_description'] ?? '') ?>">
            </label>
            <label>Action Label:
              <input type="text" name="cta_action_label" value="<?= htmlspecialchars($settings['cta_action_label'] ?? '') ?>">
            </label>
            <label>Action URL:
              <input type="text" name="cta_action_url" value="<?= htmlspecialchars($settings['cta_action_url'] ?? '') ?>">
            </label>
            <label>
              <input type="checkbox" name="show_pinned_announcements" value="1" <?= !empty($settings['show_pinned_announcements']) ? 'checked' : '' ?>>
              Show pinned announcements
            </label>
          </fieldset>

          <button type="submit">Save Homepage</button>
        </form>
      </section>
    </section>
  </main>
</div>
