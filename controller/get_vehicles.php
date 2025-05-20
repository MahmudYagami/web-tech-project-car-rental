<?php
session_start();
require_once '../model/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Fetch all available vehicles
$sql = "SELECT * FROM cars WHERE status = 'available'";
$result = $conn->query($sql);

$vehicles = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $vehicles[] = [
            'id' => $row['id'],
            'model' => $row['model'],
            'brand' => $row['brand'],
            'year' => $row['year'],
            'price_per_day' => $row['price_per_day'],
            'status' => $row['status'],
            'image' => $row['image']
        ];
    }
}

// Set header to return JSON
header('Content-Type: application/json');
echo json_encode($vehicles); 