<?php
session_start();
require_once '../model/db.php';
require_once '../model/booking_model.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? null;
    $new_status = $_POST['status'] ?? null;
    
    if (!$booking_id || !$new_status) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit();
    }
    
    // Update the booking status using the model function
    $result = updateBookingStatus($conn, $booking_id, $new_status);
    echo json_encode($result);
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

closeConnection($conn);
?> 