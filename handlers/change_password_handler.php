<?php
require_once __DIR__ . '/../app/lib/Auth.php';
require_once __DIR__ . '/../app/models/Model.php';

// Ensure user is authenticated
$user = Auth::user();
if (!$user) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $user['id'];
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'All password fields are required.';
        header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($error));
        exit;
    }
    
    if ($newPassword !== $confirmPassword) {
        $error = 'New password and confirm password do not match.';
        header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($error));
        exit;
    }
    
    if (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters long.';
        header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($error));
        exit;
    }
    
    try {
        // Use a simple database extension class to access the protected db method
        class _DBAccess extends Model { 
            public function getDB() { 
                return parent::db(); 
            } 
        }
        $db = (new _DBAccess())->getDB();
        
        // Verify current password
        $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$userData) {
            $error = 'User not found.';
            header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($error));
            exit;
        }
        
        // Check if current password is correct
        if (!password_verify($currentPassword, $userData['password'])) {
            $error = 'Current password is incorrect.';
            header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($error));
            exit;
        }
        
        // Update password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $updateResult = $updateStmt->execute([$hashedNewPassword, $userId]);
        
        if ($updateResult) {
            $success = 'Password changed successfully!';
            header('Location: /adamson-ccit/public/index.php?page=student_profile&password_changed=1');
            exit;
        } else {
            $error = 'Failed to update password. Please try again.';
            header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($error));
            exit;
        }
        
    } catch (Exception $e) {
        error_log("Change password error: " . $e->getMessage());
        $error = 'An error occurred while changing password. Please try again.';
        header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($error));
        exit;
    }
} else {
    // Not a POST request
    header('Location: /adamson-ccit/public/index.php?page=student_profile');
    exit;
}
?>