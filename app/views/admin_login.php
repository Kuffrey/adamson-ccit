<?php
session_start();
if (isset($_POST['username'], $_POST['password'])) {
    // For demo, hardcoded admin. Replace with DB check in production.
    if ($_POST['username'] === 'admin' && $_POST['password'] === 'admin123') {
        $_SESSION['user'] = [ 'username' => 'admin', 'role' => 'admin' ];
        header('Location: ?page=admin_dashboard');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<main>
    <section class="content">
        <h2>Admin Login</h2>
        <?php if (!empty($error)) echo '<p style="color:red">' . $error . '</p>'; ?>
        <form method="post" action="?page=admin_login">
            <label>Username: <input type="text" name="username" required></label><br>
            <label>Password: <input type="password" name="password" required></label><br>
            <button type="submit">Login</button>
        </form>
    </section>
</main>
