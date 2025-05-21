<?php
session_start();
require_once '../model/db.php';
require_once '../model/booking_model.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../view/login.php");
    exit();
}

// Handle search request
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    $result = searchBookings($conn, $search_term);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit();
}

// Get all bookings for initial page load
$has_payment_status = hasPaymentStatusColumn($conn);
$bookings_result = getAllBookingsWithDetails($conn);

// Store data in session for the view
$_SESSION['has_payment_status'] = $has_payment_status;
$_SESSION['bookings'] = $bookings_result['data'];

// Redirect to view
header("Location: ../view/manage_bookings.php");
exit();
?> 