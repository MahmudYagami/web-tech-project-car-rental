<?php
session_start();
require_once '../model/db.php';
require_once '../model/vehicle_model.php';

// Get user name if logged in
$userName = '';
if (isset($_SESSION['user_id'])) {
    $result = getUserName($conn, $_SESSION['user_id']);
    if ($result['success']) {
        $userName = $result['data'];
    }
}

// Handle AJAX search request
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    
    $result = searchCars($conn, $search_term, $filter);
    
    header('Content-Type: application/json');
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'cars' => $result['data']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $result['message']
        ]);
    }
    exit();
}

// Get all cars for initial page load
$result = getAllCars($conn);
if ($result['success']) {
    $cars = $result['data'];
} else {
    $cars = [];
    error_log("Failed to fetch cars: " . $result['message']);
}

// Include the view
require_once '../view/inventory.php';
?> 