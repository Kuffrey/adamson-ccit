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

$username = Auth::user()['username'] ?? 'Admin';
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'create') {
            // Create new user
            $userData = [
                'username' => trim($_POST['username'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'role' => $_POST['role'] ?? '',
                'first_name' => trim($_POST['first_name'] ?? '') ?: null,
                'last_name' => trim($_POST['last_name'] ?? '') ?: null,
                'email' => trim($_POST['email'] ?? '') ?: null,
                'student_number' => trim($_POST['student_number'] ?? '') ?: null,
                'program' => trim($_POST['program'] ?? '') ?: null,
                'year_level' => $_POST['year_level'] ?? null,
                'department_id' => $_POST['department_id'] ? intval($_POST['department_id']) : null,
                'status' => $_POST['status'] ?? 'active'
            ];

            if (empty($userData['username']) || empty($userData['password']) || empty($userData['role'])) {
                throw new Exception('Username, password, and role are required.');
            }

            if (User::create($userData)) {
                $success = 'User created successfully!';
            } else {
                throw new Exception('Failed to create user.');
            }
            
        } elseif ($action === 'update') {
            // Update existing user
            $userId = intval($_POST['user_id'] ?? 0);
            if (!$userId) {
                throw new Exception('Invalid user ID.');
            }

            $userData = [
                'username' => trim($_POST['username'] ?? ''),
                'role' => $_POST['role'] ?? '',
                'first_name' => trim($_POST['first_name'] ?? '') ?: null,
                'last_name' => trim($_POST['last_name'] ?? '') ?: null,
                'email' => trim($_POST['email'] ?? '') ?: null,
                'student_number' => trim($_POST['student_number'] ?? '') ?: null,
                'program' => trim($_POST['program'] ?? '') ?: null,
                'year_level' => $_POST['year_level'] ?? null,
                'department_id' => $_POST['department_id'] ? intval($_POST['department_id']) : null,
                'status' => $_POST['status'] ?? 'active'
            ];

            // Only update password if provided
            if (!empty($_POST['password'])) {
                $userData['password'] = $_POST['password'];
            }

            if (empty($userData['username']) || empty($userData['role'])) {
                throw new Exception('Username and role are required.');
            }

            if (User::update($userId, $userData)) {
                $success = 'User updated successfully!';
            } else {
                throw new Exception('Failed to update user.');
            }
            
        } elseif ($action === 'delete') {
            // Delete user
            $userId = intval($_POST['user_id'] ?? 0);
            if (!$userId) {
                throw new Exception('Invalid user ID.');
            }

            if ($userId === $_SESSION['user_id']) {
                throw new Exception('You cannot delete your own account.');
            }

            if (User::delete($userId)) {
                $success = 'User deleted successfully!';
            } else {
                throw new Exception('Failed to delete user.');
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

    // Redirect to prevent form resubmission
    header('Location: ?page=admin_manage_users' . ($error ? '&error=' . urlencode($error) : '') . ($success ? '&success=' . urlencode($success) : ''));
    exit;
}

// Get messages from URL parameters
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

// Get all users
try {
    $users = User::getAll();
} catch (Exception $e) {
    $error = 'Failed to load users: ' . $e->getMessage();
    $users = [];
}

if (!function_exists('esc')) {
    function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
?>

<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
/* User Management Theme Enhancements */
.card {
  border-radius: 16px;
  box-shadow: 0 2px 8px rgba(0,128,201,0.07);
  border: 1px solid #e3e6f0;
}
.card-header {
  background: #f5f7fb;
  border-bottom: 1px solid #e3e6f0;
  border-radius: 16px 16px 0 0;
}
.card-title {
  font-weight: 700;
  color: #0b234c;
  font-size: 1.15rem;
}
.btn-primary, .btn-success {
  background: linear-gradient(90deg, #0080c9 0%, #3b4cca 100%);
  border: none;
  font-weight: 600;
  border-radius: 8px;
}
.btn-primary:hover, .btn-success:hover {
  background: linear-gradient(90deg, #3b4cca 0%, #0080c9 100%);
}
.btn-warning {
  background: #fbbf24;
  color: #0b234c;
  border: none;
  font-weight: 600;
  border-radius: 8px;
}
.btn-warning:hover {
  background: #f59e42;
  color: #fff;
}
.btn-danger {
  background: linear-gradient(90deg, #e74c3c 0%, #c0392b 100%);
  color: #fff;
  border: none;
  font-weight: 600;
  border-radius: 8px;
}
.btn-danger:hover {
  background: linear-gradient(90deg, #c0392b 0%, #e74c3c 100%);
}
.table {
  border-radius: 12px;
  overflow: hidden;
  background: #fff;
}
.table thead.table-dark {
  background: #0b234c;
  color: #fff;
}
.table-striped > tbody > tr:nth-of-type(odd) {
  background: #f5f7fb;
}
.badge {
  font-weight: 700;
  font-size: 0.95rem;
  border-radius: 999px;
  padding: 6px 14px;
}
.badge.bg-danger { background: #e74c3c; }
.badge.bg-success { background: #10b981; }
.badge.bg-warning { background: #fbbf24; color: #0b234c; }
.badge.bg-primary { background: #0080c9; }
.badge.bg-secondary { background: #64748b; }
.form-label { font-weight: 600; color: #0b234c; }
.alert { border-radius: 10px; font-weight: 600; }
.modal-content {
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0,128,201,0.12);
  border: 1px solid #e3e6f0;
}
.modal-header {
  background: #f5f7fb;
  border-bottom: 1px solid #e3e6f0;
  border-radius: 16px 16px 0 0;
}
.modal-title { color: #0b234c; font-weight: 700; }
.modal-footer { background: #f5f7fb; border-top: 1px solid #e3e6f0; border-radius: 0 0 16px 16px; }
@media (max-width: 900px) {
  .card { padding: 0.5rem; }
  .modal-content { padding: 0.5rem; }
}
</style>
<div class="admin-cms-layout">
    <?php include __DIR__ . '/_admin_sidebar.php'; ?>

    <main class="admin-main">
        <header class="admin-topbar">
            <span class="admin-topbar__title"><i class="fas fa-users-cog"></i> User Management</span>
            <div class="admin-topbar__spacer"></div>
            <div class="admin-topbar__user">
                <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
                <span class="admin-topbar__name"><?= esc($username) ?></span>
            </div>
        </header>

        <section class="admin-cms-section">
            <h1 class="admin-cms-section__title">User Management</h1>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= esc($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= esc($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Add New User Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-user-plus"></i> Add New User</h5>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse"
                                data-bs-target="#addUserCollapse" aria-expanded="false" aria-controls="addUserCollapse">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div class="collapse" id="addUserCollapse">
                    <div class="card-body">
                        <form id="userForm" method="POST" autocomplete="off">
                            <input type="hidden" name="action" id="action" value="create">
                            <input type="hidden" name="user_id" id="user_id" value="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username *</label>
                                    <input type="text" class="form-control" id="modal_username" name="username" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password *</label>
                                    <input type="password" class="form-control" id="modal_password" name="password" required>
                                    <small class="form-text text-muted" id="passwordHelp">Required for new users</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="modal_first_name" name="first_name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="modal_last_name" name="last_name">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="modal_email" name="email">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Role *</label>
                                    <select class="form-control" id="modal_role" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="admin">Administrator</option>
                                        <option value="dean">Dean</option>
                                        <option value="faculty">Faculty</option>
                                        <option value="student">Student</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Student Number</label>
                                    <input type="text" class="form-control" id="modal_student_number" name="student_number">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Program</label>
                                    <input type="text" class="form-control" id="modal_program" name="program"
                                           placeholder="e.g., BS Information Technology">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Year Level</label>
                                    <select class="form-control" id="modal_year_level" name="year_level">
                                        <option value="">Select Year Level</option>
                                        <option value="1st Year">1st Year</option>
                                        <option value="2nd Year">2nd Year</option>
                                        <option value="3rd Year">3rd Year</option>
                                        <option value="4th Year">4th Year</option>
                                        <option value="5th Year">5th Year</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Department ID</label>
                                    <input type="number" class="form-control" id="modal_department_id" name="department_id"
                                           placeholder="1" min="1">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" id="modal_status" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="suspended">Suspended</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" id="modalSubmitBtn">
                                <i class="fas fa-save"></i> Create User
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Existing Users Card -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-users"></i> Existing Users (<?= count($users) ?>)</h5>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse"
                                data-bs-target="#usersListCollapse" aria-expanded="true" aria-controls="usersListCollapse">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div class="collapse show" id="usersListCollapse">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Role</th>
                                        <th>Program</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($users)): ?>
                                        <tr>
                                            <td colspan="6">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle"></i> No users found.
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($user['username']) ?></strong>
                                                    <?php if (!empty($user['email'])): ?>
                                                        <br><small class="text-muted"><?= esc($user['email']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                                                    echo $fullName ? '<strong>' . esc($fullName) . '</strong>' : '<span class="text-muted">Not provided</span>';
                                                    ?>
                                                    <?php if (!empty($user['student_number'])): ?>
                                                        <br><small class="text-muted"><?= esc($user['student_number']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $role = $user['role'];
                                                    $roleClass = $role === 'admin' ? 'danger' : ($role === 'dean' ? 'success' : ($role === 'faculty' ? 'primary' : 'warning'));
                                                    ?>
                                                    <span class="badge bg-<?= $roleClass ?>">
                                                        <?= ucfirst($role) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($user['program'])): ?>
                                                        <strong><?= esc($user['program']) ?></strong>
                                                        <?php if (!empty($user['year_level'])): ?>
                                                            <br><small class="text-muted"><?= esc($user['year_level']) ?></small>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not applicable</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status = $user['status'] ?? 'active';
                                                    $statusClass = $status === 'active' ? 'success' : ($status === 'inactive' ? 'warning' : 'danger');
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass ?>">
                                                        <?= ucfirst($status) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-warning btn-action"
                                                            title="Edit User"
                                                            onclick="openEditUserModal(<?= htmlspecialchars(json_encode($user)) ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger btn-action" title="Delete User">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-action disabled" title="Cannot delete your own account">
                                                            <i class="fas fa-user"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">
                    <i class="fas fa-edit"></i> Edit User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userFormModal" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" id="modal_action" value="update">
                    <input type="hidden" name="user_id" id="modal_user_id" value="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username *</label>
                            <input type="text" class="form-control" id="modal_edit_username" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="modal_edit_password" name="password">
                            <small class="form-text text-muted" id="modal_passwordHelp">Leave blank to keep current password</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" id="modal_edit_first_name" name="first_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="modal_edit_last_name" name="last_name">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="modal_edit_email" name="email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role *</label>
                            <select class="form-control" id="modal_edit_role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="admin">Administrator</option>
                                <option value="dean">Dean</option>
                                <option value="faculty">Faculty</option>
                                <option value="student">Student</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Student Number</label>
                            <input type="text" class="form-control" id="modal_edit_student_number" name="student_number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Program</label>
                            <input type="text" class="form-control" id="modal_edit_program" name="program"
                                   placeholder="e.g., BS Information Technology">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Year Level</label>
                            <select class="form-control" id="modal_edit_year_level" name="year_level">
                                <option value="">Select Year Level</option>
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                                <option value="5th Year">5th Year</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department ID</label>
                            <input type="number" class="form-control" id="modal_edit_department_id" name="department_id"
                                   placeholder="1" min="1">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-control" id="modal_edit_status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openEditUserModal(user) {
    // Populate modal fields
    document.getElementById('modal_action').value = 'update';
    document.getElementById('modal_user_id').value = user.id;
    document.getElementById('modal_edit_username').value = user.username || '';
    document.getElementById('modal_edit_first_name').value = user.first_name || '';
    document.getElementById('modal_edit_last_name').value = user.last_name || '';
    document.getElementById('modal_edit_email').value = user.email || '';
    document.getElementById('modal_edit_role').value = user.role || '';
    document.getElementById('modal_edit_student_number').value = user.student_number || '';
    document.getElementById('modal_edit_program').value = user.program || '';
    document.getElementById('modal_edit_year_level').value = user.year_level || '';
    document.getElementById('modal_edit_department_id').value = user.department_id || '';
    document.getElementById('modal_edit_status').value = user.status || 'active';
    document.getElementById('modal_edit_password').value = '';
    document.getElementById('modal_edit_password').required = false;
    document.getElementById('modal_passwordHelp').textContent = 'Leave blank to keep current password';

    // Show modal
    var editModal = new bootstrap.Modal(document.getElementById('userModal'));
    editModal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.parentNode) {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert && alert.parentNode) {
                        alert.remove();
                    }
                }, 300);
            }
        }, 5000);
    });
});
</script>