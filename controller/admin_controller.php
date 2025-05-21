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

    // Get unread notifications count
    $notifications_result = getUnreadNotificationsCount($conn, $_SESSION['user_id']);
    $unread_count = $notifications_result['success'] ? $notifications_result['data'] : 0;

    // Store data in session for the view
    $_SESSION['admin'] = $admin_result['data'];
    $_SESSION['dashboard_stats'] = $stats_result['data'];
    $_SESSION['unread_notifications'] = $unread_count;

    return [
        'admin' => $admin_result['data'],
        'stats' => $stats_result['data'],
        'unread_notifications' => $unread_count
    ];
}

function handleAjaxRequests($conn) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
        if (isset($_GET['action']) && $_GET['action'] === 'get_notifications') {
            $last_id = $_GET['last_id'] ?? 0;
            $notifications = getNotifications($conn, $_SESSION['user_id'], $last_id);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'notifications' => $notifications['data'],
                'unread_count' => $_SESSION['unread_notifications']
            ]);
            exit();
        }
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