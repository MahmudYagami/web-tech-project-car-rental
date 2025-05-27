<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermodel.php';
require_once '../model/adminmodel.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get admin details
$admin = getUserByEmail($conn, $_SESSION['email']);

// Get dashboard statistics
$stats = getAdminDashboardStats($conn);

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode([
    'admin' => $admin,
    'stats' => $stats
]); 