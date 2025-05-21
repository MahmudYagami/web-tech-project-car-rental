<?php
session_start();
require_once '../model/db.php';
require_once '../model/booking_model.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if booking_id is provided
if (!isset($_POST['booking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
    exit();
}

$booking_id = $_POST['booking_id'];

// Call the model function to delete the booking
$result = deleteBooking($conn, $booking_id);
echo json_encode($result);

mysqli_close($conn);
?> 