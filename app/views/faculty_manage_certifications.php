<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'faculty') {
    header('Location: ?page=login_faculty');
    exit;
}
require_once __DIR__ . '/../models/Certification.php';
$faculty_id = $_SESSION['user']['id']; // Use real faculty ID from session
$certModel = new Certification();

if (isset($_POST['add_cert'])) {
    $certModel->create([
        'title' => $_POST['name'],
        'issuer' => $_POST['issuer'],
        'owner_user_id' => $faculty_id,
        'department_id' => $_SESSION['user']['department_id'] ?? 0,
        'issued_at' => date('Y-m-d'),
        'expires_at' => null,
        'status' => 'pending',
    ]);
    header('Location: ?page=faculty_manage_certifications');
    exit;
}
if (isset($_GET['delete'])) {
    // Only allow delete if status is pending and owned by this faculty
    $cert = null;
    foreach ($certModel->listByOwner($faculty_id) as $c) {
        if ($c['id'] == (int)$_GET['delete']) $cert = $c;
    }
    if ($cert && $cert['status'] === 'pending') {
        $certModel->delete((int)$_GET['delete']);
    }
    header('Location: ?page=faculty_manage_certifications');
    exit;
}
$certs = $certModel->listByOwner($faculty_id);
?>
<main>
    <section class="content">
        <h2>Manage Certifications</h2>
        <form method="post" action="?page=faculty_manage_certifications">
            <input type="text" name="name" placeholder="Certification Name" required>
            <input type="text" name="issuer" placeholder="Issuer" required>
            <button type="submit" name="add_cert">Add Certification</button>
        </form>
        <hr>
        <h3>Your Certifications</h3>
        <?php
        foreach ($certs as $item) {
            echo '<div style="border:1px solid #ccc; margin:10px 0; padding:10px;">';
            echo '<strong>' . htmlspecialchars($item['title']) . '</strong> <br>';
            echo '<em>Issuer: ' . htmlspecialchars($item['issuer']) . '</em><br>';
            echo '<span>Status: <b>' . htmlspecialchars(ucfirst($item['status'])) . '</b></span><br>';
            if ($item['status'] === 'pending') {
                echo '<a href="?page=faculty_manage_certifications&delete=' . $item['id'] . '" onclick="return confirm(\'Delete this certification?\')">Delete</a>';
                // Optionally, add an Edit link here
            }
            echo '</div>';
        }
        ?>
    </section>
</main>
