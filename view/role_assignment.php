<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermodel.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_role':
                if (isset($_POST['user_id']) && isset($_POST['role'])) {
                    $userId = $_POST['user_id'];
                    $role = $_POST['role'];
                    $user = getUserByEmail($conn, $_POST['email']);
                    updateUser($conn, $userId, $user['email'], $user['first_name'], $user['last_name'], $role);
                }
                break;
            
            case 'delete_user':
                if (isset($_POST['user_id'])) {
                    deleteUser($conn, $_POST['user_id']);
                }
                break;
            
            case 'add_user':
                if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['first_name']) && isset($_POST['last_name'])) {
                    $email = $_POST['email'];
                    $password = $_POST['password'];
                    $firstName = $_POST['first_name'];
                    $lastName = $_POST['last_name'];
                    $role = $_POST['role'] ?? 'user';
                    createUser($conn, $email, $password, $firstName, $lastName, $role);
                }
                break;
        }
    }
}

// Get all users
$users = getAllUsers($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>User Management</h2>
            <a href="admin_dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <!-- Add New User Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Add New User</h4>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3" id="addUserForm" onsubmit="return validateAddUserForm()">
                    <input type="hidden" name="action" value="add_user">
                    <div class="col-md-3">
                        <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
                        <div class="invalid-feedback">Field is empty</div>
                    </div>
                    <div class="col-md-2">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                        <div class="invalid-feedback">Field is empty</div>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name" required>
                        <div class="invalid-feedback">Field is empty</div>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last Name" required>
                        <div class="invalid-feedback">Field is empty</div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" name="role" id="role" required>
                            <option value="">Select Role</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div class="invalid-feedback">Field is empty</div>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card">
            <div class="card-header">
                <h4>User List</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <form method="POST" class="d-inline" onsubmit="return validateRoleUpdate(this)">
                                        <input type="hidden" name="action" value="update_role">
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        <input type="hidden" name="email" value="<?php echo $user['email']; ?>">
                                        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validateAddUserForm() {
            const form = document.getElementById('addUserForm');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const firstName = document.getElementById('first_name');
            const lastName = document.getElementById('last_name');
            const role = document.getElementById('role');
            
            let isValid = true;
            
            // Reset previous validation states
            [email, password, firstName, lastName, role].forEach(field => {
                field.classList.remove('is-invalid');
            });
            
            // Validate all fields
            [email, password, firstName, lastName, role].forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            });
            
            return isValid;
        }
        
        function validateRoleUpdate(form) {
            const roleSelect = form.querySelector('select[name="role"]');
            if (!roleSelect.value) {
                roleSelect.classList.add('is-invalid');
                return false;
            }
            return true;
        }
        
        // Add input event listeners to remove invalid state when user starts typing
        document.querySelectorAll('.form-control, .form-select').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    </script>
</body>
</html> 