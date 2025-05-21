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

// Initialize search parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Search cars based on parameters
$result = searchCars($conn, $search, $filter);

// Store results
if ($result['success']) {
    $cars = $result['data'];
    error_log("Successfully fetched " . count($cars) . " cars");
} else {
    $cars = [];
    error_log("Failed to fetch cars: " . $result['message']);
}

mysqli_close($conn);
?> 