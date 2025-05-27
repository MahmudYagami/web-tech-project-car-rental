<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermanagementmodel.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_role':
                if (isset($_POST['user_id']) && isset($_POST['role'])) {
                    if (updateUserRole($conn, $_POST['user_id'], $_POST['role'])) {
                        $response = ['success' => true, 'message' => 'Role updated successfully'];
                    } else {
                        $response = ['success' => false, 'message' => 'Failed to update role'];
                    }
                }
                break;

            case 'delete_user':
                if (isset($_POST['user_id'])) {
                    if (deleteUser($conn, $_POST['user_id'])) {
                        $response = ['success' => true, 'message' => 'User deleted successfully'];
                    } else {
                        $response = ['success' => false, 'message' => 'Failed to delete user'];
                    }
                }
                break;

            case 'add_user':
                if (isset($_POST['email']) && isset($_POST['password']) && 
                    isset($_POST['first_name']) && isset($_POST['last_name'])) {
                    $role = $_POST['role'] ?? 'user';
                    if (createUser($conn, $_POST['email'], $_POST['password'], 
                                 $_POST['first_name'], $_POST['last_name'], $role)) {
                        $response = ['success' => true, 'message' => 'User created successfully'];
                    } else {
                        $response = ['success' => false, 'message' => 'Failed to create user'];
                    }
                }
                break;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle search request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $users = searchUsers($conn, $_GET['search']);
    header('Content-Type: application/json');
    echo json_encode(['users' => $users]);
    exit();
} 