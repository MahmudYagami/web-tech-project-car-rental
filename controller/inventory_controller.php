<?php
session_start();
require_once '../model/db.php';

function getAllCars($conn) {
    // First, let's check if the cars table exists and has data
    $check_table = "SHOW TABLES LIKE 'cars'";
    $table_result = mysqli_query($conn, $check_table);
    if (mysqli_num_rows($table_result) == 0) {
        error_log("Cars table does not exist");
        return false;
    }

    // Count total cars
    $count_sql = "SELECT COUNT(*) as total FROM cars";
    $count_result = mysqli_query($conn, $count_sql);
    if ($count_result) {
        $total = mysqli_fetch_assoc($count_result)['total'];
        error_log("Total cars in database: " . $total);
    }

    // Get all cars without status filter first
    $sql = "SELECT * FROM cars ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        error_log("Database query error: " . mysqli_error($conn));
        return false;
    }

    $cars = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cars[] = $row;
    }
    
    error_log("Number of cars fetched: " . count($cars));
    
    // Log each car's status
    foreach ($cars as $car) {
        error_log("Car ID: " . $car['car_id'] . ", Status: " . $car['status']);
    }
    
    return $cars;
}

// Get user name if logged in
$userName = '';
if (isset($_SESSION['user_id'])) {
    $sql = "SELECT first_name, last_name FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $userName = $row['first_name'] . ' ' . $row['last_name'];
    }
    mysqli_stmt_close($stmt);
}

// Initialize search parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Base query
$query = "SELECT * FROM cars WHERE status = 'available'";

// Add search conditions if search term is provided
if (!empty($search)) {
    switch ($filter) {
        case 'brand':
            $query .= " AND brand LIKE ?";
            break;
        case 'model':
            $query .= " AND model LIKE ?";
            break;
        case 'transmission':
            $query .= " AND transmission LIKE ?";
            break;
        case 'fuel_type':
            $query .= " AND fuel_type LIKE ?";
            break;
        default:
            $query .= " AND (brand LIKE ? OR model LIKE ? OR transmission LIKE ? OR fuel_type LIKE ?)";
            break;
    }
}

// Prepare and execute the query
$stmt = mysqli_prepare($conn, $query);

if (!empty($search)) {
    $searchTerm = "%$search%";
    switch ($filter) {
        case 'brand':
        case 'model':
        case 'transmission':
        case 'fuel_type':
            mysqli_stmt_bind_param($stmt, "s", $searchTerm);
            break;
        default:
            mysqli_stmt_bind_param($stmt, "ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
            break;
    }
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch all cars
$cars = [];
while ($row = mysqli_fetch_assoc($result)) {
    $cars[] = $row;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

// Debug information
if ($cars === false) {
    error_log("Failed to fetch cars from database");
} else {
    error_log("Successfully fetched " . count($cars) . " cars");
}
?> 