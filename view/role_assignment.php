<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermanagementmodel.php';

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

// Get initial users list
$users = getAllUsers($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .back-btn {
            padding: 8px 15px;
            background: #666;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
        }

        .card-header {
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-row {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
        }

        .btn-primary {
            background: #007bff;
        }

        .btn-danger {
            background: #dc3545;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background: #f8f9fa;
        }

        .search-box {
            margin-bottom: 20px;
        }

        .search-box input {
            width: 300px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .error {
            color: #dc3545;
            margin-top: 5px;
            font-size: 14px;
        }

        .success {
            color: #28a745;
            margin-top: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>User Management</h2>
            <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
        </div>

        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search users..." onkeyup="searchUsers()">
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Add New User</h4>
            </div>
            <form id="addUserForm" onsubmit="return handleAddUser(event)">
                <div class="form-row">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="first_name" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="last_name" placeholder="Last Name" required>
                    </div>
                    <div class="form-group">
                        <select name="role" required>
                            <option value="">Select Role</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>User List</h4>
            </div>
            <div id="userTable">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <select onchange="updateRole(<?php echo $user['user_id']; ?>, this.value)">
                                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </td>
                            <td>
                                <button onclick="deleteUser(<?php echo $user['user_id']; ?>)" class="btn btn-danger">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="..\assets\js\role_assign.js"></script>
</body>
</html> 