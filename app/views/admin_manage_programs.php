<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ?page=admin_login');
    exit;
}
?>
<main>
    <section class="content">
        <h2>Manage Programs</h2>
        <form method="post" action="?page=admin_manage_programs">
            <input type="text" name="name" placeholder="Program Name" required>
            <textarea name="description" placeholder="Program Description" required></textarea>
            <button type="submit" name="add_program">Add Program</button>
        </form>
        <hr>
        <h3>All Programs</h3>
        <?php
        require_once __DIR__ . '/../models/Program.php';
        if (isset($_POST['add_program'])) {
            Program::create($_POST['name'], $_POST['description']);
            header('Location: ?page=admin_manage_programs');
            exit;
        }
        if (isset($_GET['delete'])) {
            Program::delete($_GET['delete']);
            header('Location: ?page=admin_manage_programs');
            exit;
        }
        $programs = Program::all();
        foreach ($programs as $item) {
            echo '<div style="border:1px solid #ccc; margin:10px 0; padding:10px;">';
            echo '<strong>' . htmlspecialchars($item['name']) . '</strong> <br>';
            echo '<p>' . nl2br(htmlspecialchars($item['description'])) . '</p>';
            echo '<a href="?page=admin_manage_programs&delete=' . $item['id'] . '" onclick="return confirm(\'Delete this program?\')">Delete</a>';
            echo '</div>';
        }
        ?>
    </section>
</main>
