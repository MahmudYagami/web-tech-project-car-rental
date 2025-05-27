<?php
require_once '../model/db.php';
require_once '../model/dashboard_model.php';
require_once '../model/usermodel.php';

function checkAdminAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in and is admin
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header("Location: ../view/login.php");
        exit();
    }
    
    if ($_SESSION['role'] !== 'admin') {
        header("Location: ../view/user_dashboard.php");
        exit();
    }
    
    return true;
}

function getAdminDashboardData($conn) {
    // Get admin details
    $admin_result = getUserByEmail($conn, $_SESSION['email']);
    if (!$admin_result['success']) {
        $_SESSION['error'] = 'Failed to fetch admin details';
        return false;
    }

    // Get dashboard statistics
    $stats_result = getDashboardStats($conn);
    if (!$stats_result['success']) {
        $_SESSION['error'] = 'Failed to fetch dashboard statistics';
        return false;
    }

    // Store data in session for the view
    $_SESSION['admin'] = $admin_result['data'];
    $_SESSION['dashboard_stats'] = $stats_result['data'];

    return [
        'admin' => $admin_result['data'],
        'stats' => $stats_result['data']
    ];
}

function handleAjaxRequests($conn) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // Handle any future AJAX requests here
    }
}

// Handle direct access to this file
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    if (!checkAdminAuth()) {
        exit();
    }
    
    $dashboardData = getAdminDashboardData($conn);
    if (!$dashboardData) {
        header("Location: ../view/login.php");
        exit();
    }
    
    handleAjaxRequests($conn);
}
?> 