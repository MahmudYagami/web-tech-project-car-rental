<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermodel.php';
require_once '../model/booking_model.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// Check if payment_status column exists in bookings table
$has_payment_status = hasPaymentStatusColumn($conn);

// Get user's bookings
$bookings = getUserBookings($conn, $_SESSION['user_id']);

// Store data in session for the view
$_SESSION['user_bookings_data'] = [
    'bookings' => $bookings,
    'has_payment_status' => $has_payment_status
];

// Redirect to the view
header("Location: ../view/user_booking.php");
exit();
?> 