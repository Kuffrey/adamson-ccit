<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/lib/Auth.php';
require_once __DIR__ . '/../app/models/Model.php';

// Require login
$user = Auth::user();
if (!$user) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

// Only POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: /adamson-ccit/public/index.php?page=student_profile');
    exit;
}

// Read form
$currentPassword = trim($_POST['current_password'] ?? '');
$newPassword     = trim($_POST['new_password'] ?? '');
$confirmPassword = trim($_POST['confirm_password'] ?? '');

// Basic validation (same rules as client)
if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
    $err = 'All password fields are required.';
    header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($err));
    exit;
}
if ($newPassword !== $confirmPassword) {
    $err = 'New password and confirm password do not match.';
    header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($err));
    exit;
}
if (strlen($newPassword) < 6) {
    $err = 'New password must be at least 6 characters long.';
    header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($err));
    exit;
}

try {
    // Get DB
    class _DBAccess extends Model { public function getDB(){ return parent::db(); } }
    $db = (new _DBAccess())->getDB();

    // Always trust the session user ID
    $userId = (int)$user['id'];

    // Fetch current hash
    $stmt = $db->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $err = 'User not found.';
        header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($err));
        exit;
    }

    // Verify current password
    if (!password_verify($currentPassword, $row['password'])) {
        $err = 'Current password is incorrect.';
        header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($err));
        exit;
    }

    // Hash & update
    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

    $upd = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
    $ok  = $upd->execute([$newHash, $userId]);

    if ($ok && $upd->rowCount() >= 0) {
        // Success toast on profile page
        header('Location: /adamson-ccit/public/index.php?page=student_profile&password_changed=1');
        exit;
    }

    // Fallback error
    error_log("Password update affected 0 rows for user $userId");
    $err = 'No changes were made.';
    header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($err));
    exit;

} catch (Throwable $e) {
    error_log('Change password error: ' . $e->getMessage());
    $err = 'An error occurred while changing password. Please try again.';
    header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($err));
    exit;
}
