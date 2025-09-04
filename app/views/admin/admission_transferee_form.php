<?php
// app/views/admin/admission_transferee_form.php
/** @var array $settings */
$msg = $msg ?? null;
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Transferee Admission Page | Admin | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css" />
</head>
<body>
<main class="admin-main">
  <section class="admin-section">
    <h1>Edit Transferee Admission Page</h1>
    <?php if ($msg): ?><div class="admin-msg"><?= e($msg) ?></div><?php endif; ?>
    <form method="post" class="admin-form" autocomplete="off">
      <label>Subhero Lead
        <input type="text" name="subhero_lead" value="<?= e($settings['subhero_lead'] ?? '') ?>" maxlength="255" required />
      </label>
      <label>How to Apply (HTML allowed)
        <textarea name="how_to_apply" rows="3"><?= e($settings['how_to_apply'] ?? '') ?></textarea>
      </label>
      <label>Requirements (HTML allowed)
        <textarea name="requirements" rows="3"><?= e($settings['requirements'] ?? '') ?></textarea>
      </label>
      <label>Enrollment Procedure (HTML allowed)
        <textarea name="enrollment_procedure" rows="3"><?= e($settings['enrollment_procedure'] ?? '') ?></textarea>
      </label>
      <label>Enrollment Note (HTML allowed)
        <textarea name="enrollment_note" rows="2"><?= e($settings['enrollment_note'] ?? '') ?></textarea>
      </label>
      <label>Sidebar: Office Info (HTML allowed)
        <textarea name="sidebar_office" rows="2"><?= e($settings['sidebar_office'] ?? '') ?></textarea>
      </label>
      <label>Sidebar: Quick Links (HTML allowed)
        <textarea name="sidebar_links" rows="2"><?= e($settings['sidebar_links'] ?? '') ?></textarea>
      </label>
      <label>Sidebar: Image URL
        <input type="text" name="sidebar_image_url" value="<?= e($settings['sidebar_image_url'] ?? '') ?>" />
      </label>
      <label>Sidebar: Image Caption
        <input type="text" name="sidebar_image_caption" value="<?= e($settings['sidebar_image_caption'] ?? '') ?>" />
      </label>
      <label>CTA Title
        <input type="text" name="cta_title" value="<?= e($settings['cta_title'] ?? '') ?>" />
      </label>
      <label>CTA Description
        <input type="text" name="cta_description" value="<?= e($settings['cta_description'] ?? '') ?>" />
      </label>
      <label>CTA Action Label
        <input type="text" name="cta_action_label" value="<?= e($settings['cta_action_label'] ?? '') ?>" />
      </label>
      <label>CTA Action URL
        <input type="text" name="cta_action_url" value="<?= e($settings['cta_action_url'] ?? '') ?>" />
      </label>
      <button class="btn btn--solid" type="submit">Save</button>
      <a class="btn btn--outline" href="/adamson-ccit/public/index.php?page=admin_dashboard">Cancel</a>
    </form>
  </section>
</main>
</body>
</html>
