<main class="container">
  <h1>Homepage — Hero & Sections</h1>
  <?php if (!empty($_GET['saved'])): ?><p class="notice">Saved.</p><?php endif; ?>
  <form method="post" action="?page=admin_manage_homepage">
    <fieldset>
      <legend>Hero</legend>
      <label>Title <input type="text" name="hero_title" value="<?= htmlspecialchars($settings['hero_title'] ?? '') ?>" required></label><br>
      <label>Subtitle <input type="text" name="hero_subtitle" value="<?= htmlspecialchars($settings['hero_subtitle'] ?? '') ?>"></label><br>
      <label>Background Image Path <input type="text" name="hero_bg" value="<?= htmlspecialchars($settings['hero_bg'] ?? '') ?>"></label><br>
      <label>Primary Button Text <input type="text" name="btn_primary_text" value="<?= htmlspecialchars($settings['btn_primary_text'] ?? '') ?>"></label>
      <label>Primary Button URL <input type="text" name="btn_primary_url" value="<?= htmlspecialchars($settings['btn_primary_url'] ?? '') ?>"></label><br>
      <label>Secondary Button Text <input type="text" name="btn_secondary_text" value="<?= htmlspecialchars($settings['btn_secondary_text'] ?? '') ?>"></label>
      <label>Secondary Button URL <input type="text" name="btn_secondary_url" value="<?= htmlspecialchars($settings['btn_secondary_url'] ?? '') ?>"></label>
    </fieldset>

    <fieldset>
      <legend>Why Choose CCIT</legend>
      <label>Experienced Faculty Text <input type="text" name="why_faculty_text" value="<?= htmlspecialchars($settings['why_faculty_text'] ?? '') ?>"></label>
      <label>Link <input type="text" name="why_faculty_link" value="<?= htmlspecialchars($settings['why_faculty_link'] ?? '') ?>"></label><br>
      <label>Facilities Text <input type="text" name="why_facilities_text" value="<?= htmlspecialchars($settings['why_facilities_text'] ?? '') ?>"></label>
      <label>Link <input type="text" name="why_facilities_link" value="<?= htmlspecialchars($settings['why_facilities_link'] ?? '') ?>"></label><br>
      <label>Career Text <input type="text" name="why_career_text" value="<?= htmlspecialchars($settings['why_career_text'] ?? '') ?>"></label>
      <label>Link <input type="text" name="why_career_link" value="<?= htmlspecialchars($settings['why_career_link'] ?? '') ?>"></label>
    </fieldset>

    <fieldset>
      <legend>Spotlight</legend>
      <label>Title <input type="text" name="spotlight_title" value="<?= htmlspecialchars($settings['spotlight_title'] ?? '') ?>"></label><br>
      <label>Blurb <textarea name="spotlight_blurb" rows="4"><?= htmlspecialchars($settings['spotlight_blurb'] ?? '') ?></textarea></label><br>
      <label>Image Path <input type="text" name="spotlight_image" value="<?= htmlspecialchars($settings['spotlight_image'] ?? '') ?>"></label><br>
      <label>CTA Text <input type="text" name="spotlight_cta_text" value="<?= htmlspecialchars($settings['spotlight_cta_text'] ?? '') ?>"></label>
      <label>CTA URL <input type="text" name="spotlight_cta_url" value="<?= htmlspecialchars($settings['spotlight_cta_url'] ?? '') ?>"></label><br>
      <label>Video URL (YouTube) <input type="text" name="spotlight_video_url" value="<?= htmlspecialchars($settings['spotlight_video_url'] ?? '') ?>"></label>
    </fieldset>

    <fieldset>
      <legend>Programs Blurbs</legend>
      <label>Undergraduate <input type="text" name="programs_undergrad_blurb" value="<?= htmlspecialchars($settings['programs_undergrad_blurb'] ?? '') ?>"></label><br>
      <label>Dual Degree <input type="text" name="programs_dual_blurb" value="<?= htmlspecialchars($settings['programs_dual_blurb'] ?? '') ?>"></label><br>
      <label>Graduate Studies <input type="text" name="programs_grad_blurb" value="<?= htmlspecialchars($settings['programs_grad_blurb'] ?? '') ?>"></label>
    </fieldset>

    <label><input type="checkbox" name="show_pinned_announcements" value="1" <?= !empty($settings['show_pinned_announcements'])?'checked':''; ?>> Show pinned announcements strip</label><br><br>

    <button class="btn" type="submit">Save</button>
  </form>
</main>
