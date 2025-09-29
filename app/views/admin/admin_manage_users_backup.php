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
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $student_number = trim($_POST['student_number'] ?? '');
            $program = trim($_POST['program'] ?? '');
            $year_level = trim($_POST['year_level'] ?? '');
            $status = $_POST['status'] ?? 'active';
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
                'first_name' => $first_name ?: null,
                'last_name' => $last_name ?: null,
                'email' => $email ?: null,
                'student_number' => $student_number ?: null,
                'program' => $program ?: null,
                'year_level' => $year_level ?: null,
                'status' => $status,
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
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $student_number = trim($_POST['student_number'] ?? '');
            $program = trim($_POST['program'] ?? '');
            $year_level = trim($_POST['year_level'] ?? '');
            $status = $_POST['status'] ?? 'active';
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
                'first_name' => $first_name ?: null,
                'last_name' => $last_name ?: null,
                'email' => $email ?: null,
                'student_number' => $student_number ?: null,
                'program' => $program ?: null,
                'year_level' => $year_level ?: null,
                'status' => $status,
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-cms-section {
            padding: 1.5rem;
        }
        
        .user-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 1px solid #e3e6f0;
        }
        
        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .table-header h3 {
            margin: 0;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }
        
        .table-header h3 i {
            margin-right: 0.5rem;
        }
        
        .user-count {
            background: rgba(255,255,255,0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        .form-control {
            border-radius: 6px;
            border: 1px solid #ddd;
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.1rem rgba(102, 126, 234, 0.15);
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            font-size: 0.85rem;
            margin-bottom: 0.4rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .table {
            margin: 0;
            font-size: 0.85rem;
        }
        
        .table th {
            background: #f8f9fa;
            border: none;
            color: #2c3e50;
            font-weight: 600;
            padding: 0.75rem 0.5rem;
            font-size: 0.8rem;
        }
        
        .table td {
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
            border-color: #e3e6f0;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .badge-role {
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
            border-radius: 15px;
            font-weight: 500;
        }
        
        .badge-admin { 
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }
        .badge-dean { 
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            color: white;
        }
        .badge-faculty { 
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        .badge-student { 
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
        }
        
        .status-active {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
        }
        
        .status-inactive {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }
        
        .status-suspended {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }
        
        .alert {
            margin-bottom: 1rem;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #51cf66, #40c057);
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #7f8c8d;
        }
        
        .empty-state i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #bdc3c7;
        }
        
        .empty-state h5 {
            margin-bottom: 0.5rem;
        }
        
        .btn-group .btn {
            margin: 0;
        }
        
        .mb-3 {
            margin-bottom: 0.75rem !important;
        }
        
        /* Modal Styles */
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            border: none;
        }
        
        .modal-title {
            font-weight: 600;
        }
        
        .btn-close {
            background: none;
            border: none;
            color: white;
            opacity: 0.8;
        }
        
        .btn-close:hover {
            opacity: 1;
        }
        
        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-section-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        @media (max-width: 991px) {
            .col-lg-4, .col-lg-8 {
                margin-bottom: 1rem;
            }
        }
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
        
        <!-- Add User Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openAddUserModal()">
                    <i class="fas fa-plus"></i> Add User
                </button>
            </div>
        </div>
        
        <!-- Users Table - Full Width -->
        <div class="col-12">
                <div class="user-table">
                    <div class="table-header">
                        <h3>
                            <i class="fas fa-users"></i>
                            All Users
                        </h3>
                        <span class="user-count">
                            <?= count($users) ?> users
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Program</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($user['username']) ?></strong>
                                            <?php if (!empty($user['email'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                                            echo $fullName ? '<strong>' . htmlspecialchars($fullName) . '</strong>' : '<span class="text-muted">-</span>';
                                            ?>
                                            <?php if (!empty($user['student_number'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($user['student_number']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $user['role'] ?> badge-role">
                                                <?= ucfirst($user['role']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($user['program'])): ?>
                                                <strong><?= htmlspecialchars($user['program']) ?></strong>
                                                <?php if (!empty($user['year_level'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($user['year_level']) ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $status = $user['status'] ?? 'active';
                                            $statusClass = 'status-' . $status;
                                            ?>
                                            <span class="badge <?= $statusClass ?> badge-role">
                                                <?= ucfirst($status) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                        title="Edit" onclick="openEditUserModal(<?= htmlspecialchars(json_encode($user)) ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                                    <form method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Delete <?= htmlspecialchars($user['username']) ?>?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <?php if (empty($users)): ?>
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <h5>No users found</h5>
                                <p>Add your first user using the form.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- User Modal -->
        <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">
                            <i class="fas fa-plus"></i> Add User
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="userForm" method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="action" id="action" value="create">
                            <input type="hidden" name="user_id" id="user_id" value="">
                            
                            <!-- Basic Information -->
                            <div class="form-section">
                                <h6 class="form-section-title">
                                    <i class="fas fa-user"></i> Basic Information
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_username" class="form-label">Username *</label>
                                            <input type="text" class="form-control" id="modal_username" name="username" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_password" class="form-label">Password *</label>
                                            <input type="password" class="form-control" id="modal_password" name="password">
                                            <small class="form-text text-muted" id="passwordHelp">Required for new users</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_first_name" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="modal_first_name" name="first_name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_last_name" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="modal_last_name" name="last_name">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="modal_email" name="email">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_role" class="form-label">Role *</label>
                                            <select class="form-control" id="modal_role" name="role" required>
                                                <option value="">Select Role</option>
                                                <option value="admin">Admin</option>
                                                <option value="dean">Dean</option>
                                                <option value="faculty">Faculty</option>
                                                <option value="student">Student</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Student Information -->
                            <div class="form-section">
                                <h6 class="form-section-title">
                                    <i class="fas fa-graduation-cap"></i> Student Information
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_student_number" class="form-label">Student Number</label>
                                            <input type="text" class="form-control" id="modal_student_number" name="student_number">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_program" class="form-label">Program</label>
                                            <input type="text" class="form-control" id="modal_program" name="program" 
                                                   placeholder="BS Information Technology">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_year_level" class="form-label">Year Level</label>
                                            <select class="form-control" id="modal_year_level" name="year_level">
                                                <option value="">Select Year Level</option>
                                                <option value="1st Year">1st Year</option>
                                                <option value="2nd Year">2nd Year</option>
                                                <option value="3rd Year">3rd Year</option>
                                                <option value="4th Year">4th Year</option>
                                                <option value="5th Year">5th Year</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_department_id" class="form-label">Department ID</label>
                                            <input type="number" class="form-control" id="modal_department_id" name="department_id" 
                                                   placeholder="1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Account Status -->
                            <div class="form-section">
                                <h6 class="form-section-title">
                                    <i class="fas fa-toggle-on"></i> Account Status
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_status" class="form-label">Status</label>
                                            <select class="form-control" id="modal_status" name="status">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="suspended">Suspended</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" id="modalSubmitBtn">
                                <i class="fas fa-save"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <script>
        function openAddUserModal() {
            // Reset form
            document.getElementById('userForm').reset();
            document.getElementById('action').value = 'create';
            document.getElementById('user_id').value = '';
            document.getElementById('modal_password').required = true;
            
            // Update modal title and button
            document.getElementById('userModalLabel').innerHTML = '<i class="fas fa-plus"></i> Add User';
            document.getElementById('modalSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Create User';
            document.getElementById('passwordHelp').textContent = 'Required for new users';
        }
        
        function openEditUserModal(user) {
            // Populate form with user data
            document.getElementById('action').value = 'update';
            document.getElementById('user_id').value = user.id;
            document.getElementById('modal_username').value = user.username || '';
            document.getElementById('modal_first_name').value = user.first_name || '';
            document.getElementById('modal_last_name').value = user.last_name || '';
            document.getElementById('modal_email').value = user.email || '';
            document.getElementById('modal_role').value = user.role || '';
            document.getElementById('modal_student_number').value = user.student_number || '';
            document.getElementById('modal_program').value = user.program || '';
            document.getElementById('modal_year_level').value = user.year_level || '';
            document.getElementById('modal_department_id').value = user.department_id || '';
            document.getElementById('modal_status').value = user.status || 'active';
            
            // Password not required for editing
            document.getElementById('modal_password').required = false;
            document.getElementById('modal_password').value = '';
            
            // Update modal title and button
            document.getElementById('userModalLabel').innerHTML = '<i class="fas fa-edit"></i> Edit User';
            document.getElementById('modalSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Update User';
            document.getElementById('passwordHelp').textContent = 'Leave blank to keep current password';
            
            // Show modal
            new bootstrap.Modal(document.getElementById('userModal')).show();
        }
        </script>
                
            </section>
        </main>
    </div>
    
    <script src="/adamson-ccit/public/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>