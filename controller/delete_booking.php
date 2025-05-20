<?php
session_start();
require_once '../model/db.php';

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

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Get car_id from booking
    $get_car_query = "SELECT car_id FROM bookings WHERE booking_id = ?";
    $stmt = mysqli_prepare($conn, $get_car_query);
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($result);
    
    if (!$booking) {
        throw new Exception("Booking not found");
    }
    
    // Delete the booking
    $delete_query = "DELETE FROM bookings WHERE booking_id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error deleting booking");
    }
    
    // Update car status to available
    $update_car_query = "UPDATE cars SET status = 'available' WHERE car_id = ?";
    $stmt = mysqli_prepare($conn, $update_car_query);
    mysqli_stmt_bind_param($stmt, "i", $booking['car_id']);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error updating car status");
    }
    
    // If everything is successful, commit the transaction
    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Booking deleted successfully']);
    
} catch (Exception $e) {
    // If there's an error, rollback the transaction
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?> 