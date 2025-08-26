<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'faculty') {
    header('Location: ?page=login_faculty');
    exit;
}
require_once __DIR__ . '/../models/Research.php';
$faculty_id = $_SESSION['user']['id'];
$dept_id = $_SESSION['user']['department_id'] ?? 0;
$researchModel = new Research();

if (isset($_POST['add_research'])) {
    $researchModel->create([
        'title' => $_POST['title'],
        'abstract' => $_POST['description'],
        'owner_user_id' => $faculty_id,
        'department_id' => $dept_id,
        'status' => 'draft',
        'requires_dean_approval' => 1, // or 0 if not needed
    ]);
    header('Location: ?page=faculty_manage_research');
    exit;
}
if (isset($_GET['delete'])) {
    // Only allow delete if owned by this faculty and status is draft or review
    $myResearch = $researchModel->listByOwner($faculty_id);
    $target = null;
    foreach ($myResearch as $r) {
        if ($r['id'] == (int)$_GET['delete']) $target = $r;
    }
    if ($target && in_array($target['status'], ['draft','review'])) {
        // You need to implement a delete method in Research model if not present
        if (method_exists($researchModel, 'delete')) {
            $researchModel->delete((int)$_GET['delete']);
        }
    }
    header('Location: ?page=faculty_manage_research');
    exit;
}
$research = $researchModel->listByOwner($faculty_id);
?>
<main>
    <section class="content">
        <h2>Manage Research</h2>
        <form method="post" action="?page=faculty_manage_research">
            <input type="text" name="title" placeholder="Research Title" required>
            <textarea name="description" placeholder="Research Description" required></textarea>
            <button type="submit" name="add_research">Add Research</button>
        </form>
        <hr>
        <h3>Your Research</h3>
        <?php
        foreach ($research as $item) {
            echo '<div style="border:1px solid #ccc; margin:10px 0; padding:10px;">';
            echo '<strong>' . htmlspecialchars($item['title']) . '</strong> <br>';
            echo '<span>Status: <b>' . htmlspecialchars(ucfirst($item['status'])) . '</b></span><br>';
            echo '<p>' . nl2br(htmlspecialchars($item['abstract'])) . '</p>';
            if (in_array($item['status'], ['draft','review'])) {
                echo '<a href="?page=faculty_manage_research&delete=' . $item['id'] . '" onclick="return confirm(\'Delete this research?\')">Delete</a>';
            }
            echo '</div>';
        }
        ?>
    </section>
</main>
