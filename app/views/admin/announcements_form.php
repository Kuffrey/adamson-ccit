<?php
// app/views/admin/announcements_form.php
/** @var array $settings */
$msg = $msg ?? null;
if (!function_exists('e')) {
    function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Announcements | Admin | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css" />
</head>
<body>
<main class="admin-main">
  <section class="admin-section">
    <h1>Edit Announcements</h1>
    <?php if ($msg): ?><div class="admin-msg"><?= e($msg) ?></div><?php endif; ?>
    <form method="post" class="admin-form" autocomplete="off">
      <!-- Announcement form fields here -->
      <button class="btn btn--solid" type="submit">Save</button>
      <a class="btn btn--outline" href="/adamson-ccit/public/index.php?page=admin_dashboard">Cancel</a>
    </form>
  </section>
</main>
</body>
</html>
