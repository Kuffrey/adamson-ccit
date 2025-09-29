<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','dean'])) {
    header('Location: ?page=login');
    exit;
}
?>
<main>
    <section class="content">
        <h2>Manage Faculty</h2>
        <form method="post" action="?page=admin_manage_faculty">
            <input type="text" name="name" placeholder="Faculty Name" required>
            <textarea name="profile" placeholder="Faculty Profile" required></textarea>
            <button type="submit" name="add_faculty">Add Faculty</button>
        </form>
        <hr>
        <h3>All Faculty</h3>
        <?php
        require_once __DIR__ . '/../models/Faculty.php';
        if (isset($_POST['add_faculty'])) {
            Faculty::create($_POST['name'], $_POST['profile']);
            header('Location: ?page=admin_manage_faculty');
            exit;
        }
        if (isset($_GET['delete'])) {
            Faculty::delete($_GET['delete']);
            header('Location: ?page=admin_manage_faculty');
            exit;
        }
        $faculty = Faculty::all();
        foreach ($faculty as $item) {
            echo '<div style="border:1px solid #ccc; margin:10px 0; padding:10px;">';
            echo '<strong>' . htmlspecialchars($item['name']) . '</strong> <br>';
            echo '<p>' . nl2br(htmlspecialchars($item['profile'])) . '</p>';
            echo '<a href="?page=admin_manage_faculty&delete=' . $item['id'] . '" onclick="return confirm(\'Delete this faculty?\')">Delete</a>';
            echo '</div>';
        }
        ?>
    </section>
</main>
