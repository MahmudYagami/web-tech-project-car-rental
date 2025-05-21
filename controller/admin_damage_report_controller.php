<?php
session_start();
require_once '../model/db.php';
require_once '../model/dmgreport_model.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../view/login.php');
    exit();
}

// Handle search request
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    $result = searchDamageReports($conn, $search_term);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit();
}

// Handle delete request
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!isset($_POST['report_id'])) {
        $response = ['success' => false, 'message' => 'Report ID is required'];
    } else {
        $response = deleteDamageReport($conn, $_POST['report_id']);
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Get initial data for the view
$total_result = getTotalReports($conn);
$reports_result = getAllDamageReports($conn);

// Store data in session
$_SESSION['total_reports'] = $total_result['data'];
$_SESSION['damage_reports'] = $reports_result['data'];

// Redirect to view
header('Location: ../view/admin_damage_report.php');
exit();
?> 