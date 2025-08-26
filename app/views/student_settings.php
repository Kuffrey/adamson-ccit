<?php
require_once __DIR__ . '/../../app/lib/Auth.php';
$user = Auth::user();
?>
<main class="page-student">
  <section class="content">
    <div class="container">
      <h2>Profile Settings</h2>
      <p>Update your portfolio information here.</p>
      <form>
        <!-- future: fields for name, bio, links, file uploads -->
        <p><em>Coming soon.</em></p>
      </form>
    </div>
  </section>
</main>
