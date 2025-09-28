<?php
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../lib/Auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication using Auth class
if (!Auth::check() || !Auth::is('admin')) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

// Helper function for escaping
if (!function_exists('esc')) {
    function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$username = Auth::user()['username'] ?? 'Admin';

$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'create') {
            // Create new user
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';
            $department_id = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
            
            // Validation
            if (empty($username) || empty($password) || empty($role)) {
                throw new Exception('Username, password, and role are required.');
            }
            
            if (User::usernameExists($username)) {
                throw new Exception('Username already exists.');
            }
            
            if (User::create([
                'username' => $username,
                'password' => $password,
                'role' => $role,
                'department_id' => $department_id,
                'is_active' => 1
            ])) {
                $success = 'User created successfully.';
            } else {
                throw new Exception('Failed to create user.');
            }
            
        } elseif ($action === 'update') {
            // Update existing user
            $id = (int)$_POST['user_id'];
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';
            $department_id = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
            
            if (empty($username) || empty($role)) {
                throw new Exception('Username and role are required.');
            }
            
            if (User::usernameExists($username, $id)) {
                throw new Exception('Username already exists.');
            }
            
            $updateData = [
                'username' => $username,
                'role' => $role,
                'department_id' => $department_id
            ];
            
            // Only update password if provided
            if (!empty($password)) {
                $updateData['password'] = $password;
            }
            
            if (User::update($id, $updateData)) {
                $success = 'User updated successfully.';
            } else {
                throw new Exception('Failed to update user.');
            }
            
        } elseif ($action === 'delete') {
            // Delete user
            $id = (int)$_POST['user_id'];
            
            // Don't allow deleting yourself
            if ($id === $_SESSION['user_id']) {
                throw new Exception('You cannot delete your own account.');
            }
            
            if (User::delete($id)) {
                $success = 'User deleted successfully.';
            } else {
                throw new Exception('Failed to delete user.');
            }
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all users
$users = User::getAll();

// Get editing user if specified
$editUser = null;
if (isset($_GET['edit'])) {
    $editUser = User::getById((int)$_GET['edit']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | CCIT Admin</title>
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/bootstrap.min.css">
    <style>
        .admin-cms-section {
            padding: 2rem;
        }
        
        .user-form {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .user-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .btn-action {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .alert {
            margin-bottom: 2rem;
        }
        
        .badge-role {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        
        .badge-admin { background-color: #dc3545; }
        .badge-dean { background-color: #6f42c1; }
        .badge-faculty { background-color: #0d6efd; }
        .badge-student { background-color: #198754; }
    </style>
</head>
<body>
    <div class="admin-cms-layout">
        <?php include __DIR__ . '/_admin_sidebar.php'; ?>
        
        <main class="admin-main">
            <header class="admin-topbar">
                <span class="admin-topbar__title">User Management</span>
                <div class="admin-topbar__spacer"></div>
                <div class="admin-topbar__user">
                    <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
                    <span class="admin-topbar__name"><?= esc($username ?? 'Admin') ?></span>
                </div>
            </header>

            <section class="admin-cms-section">
                <h1 class="admin-cms-section__title">User Management</h1>
                <p class="intro">Manage system users and their roles.</p>
                
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <strong>Success:</strong> <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- User Form -->
        <div class="user-form">
            <h3><?= $editUser ? 'Edit User' : 'Add New User' ?></h3>
            <form method="POST">
                <input type="hidden" name="action" value="<?= $editUser ? 'update' : 'create' ?>">
                <?php if ($editUser): ?>
                    <input type="hidden" name="user_id" value="<?= $editUser['id'] ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= $editUser ? htmlspecialchars($editUser['username']) : '' ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <?= $editUser ? '(leave blank to keep current)' : '*' ?></label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   <?= !$editUser ? 'required' : '' ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="role" class="form-label">Role *</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="admin" <?= ($editUser && $editUser['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                                <option value="dean" <?= ($editUser && $editUser['role'] === 'dean') ? 'selected' : '' ?>>Dean</option>
                                <option value="faculty" <?= ($editUser && $editUser['role'] === 'faculty') ? 'selected' : '' ?>>Faculty</option>
                                <option value="student" <?= ($editUser && $editUser['role'] === 'student') ? 'selected' : '' ?>>Student</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="department_id" class="form-label">Department ID</label>
                            <input type="number" class="form-control" id="department_id" name="department_id" 
                                   value="<?= $editUser ? $editUser['department_id'] : '' ?>">
                            <small class="form-text text-muted">Optional - for faculty and students</small>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?= $editUser ? 'Update User' : 'Create User' ?>
                    </button>
                    
                    <?php if ($editUser): ?>
                        <a href="?page=admin_manage_users" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Users Table -->
        <div class="user-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $user['role'] ?> badge-role">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td><?= $user['department_id'] ?? 'N/A' ?></td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td>N/A</td>
                                <td>
                                    <a href="?page=admin_manage_users&edit=<?= $user['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary btn-action">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-action">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="btn btn-sm btn-outline-secondary btn-action disabled">
                                            <i class="fas fa-user"></i> You
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if (empty($users)): ?>
                    <div class="text-center py-5">
                        <p class="text-muted">No users found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
                
            </section>
        </main>
    </div>
    
    <script src="/adamson-ccit/public/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>