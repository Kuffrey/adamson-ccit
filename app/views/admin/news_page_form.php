<?php
// app/views/admin/news_page_form.php
/** @var array $settings */
$msg = $msg ?? null;
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit News Page Settings | Admin | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css" />
</head>
<body>
<main class="admin-main">
  <section class="admin-section">
    <h1>Edit News Page Settings</h1>
    <?php if ($msg): ?><div class="admin-msg"><?= e($msg) ?></div><?php endif; ?>
    <form method="post" class="admin-form" autocomplete="off">
      <label>Subhero Lead
        <input type="text" name="subhero_lead" value="<?= e($settings['subhero_lead'] ?? '') ?>" maxlength="255" required />
      </label>
      <label>Announcement (optional)
        <textarea name="announcement" rows="3"><?= e($settings['announcement'] ?? '') ?></textarea>
      </label>
      <button class="btn btn--solid" type="submit">Save</button>
      <a class="btn btn--outline" href="/adamson-ccit/public/index.php?page=admin_dashboard">Cancel</a>
    </form>
  </section>
</main>
</body>
</html>
