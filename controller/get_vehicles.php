<?php
session_start();
require_once '../model/db.php';
require_once '../model/vehicle_model.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get available vehicles using model function
$result = getAvailableVehicles($conn);

// Set header to return JSON
header('Content-Type: application/json');

if ($result['success']) {
    echo json_encode($result['data']);
} else {
    http_response_code(500);
    echo json_encode(['error' => $result['message']]);
}

mysqli_close($conn);
?> 