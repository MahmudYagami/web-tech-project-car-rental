<?php
session_start();
require_once '../model/db.php';
require_once '../model/bookingmanagementmodel.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../view/login.php");
    exit();
}

// Handle search request
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    $bookings = searchBookings($conn, $search_term);
    
    // Store search results in session
    $_SESSION['bookings'] = $bookings;
    $_SESSION['has_payment_status'] = hasPaymentStatusColumn($conn);
    
    // Redirect back to view
    header("Location: ../view/manage_bookings.php");
    exit();
}

// Get all bookings for initial page load
$bookings = getAllBookings($conn);

// Store data in session for the view
$_SESSION['has_payment_status'] = hasPaymentStatusColumn($conn);
$_SESSION['bookings'] = $bookings;

// Redirect to view
header("Location: ../view/manage_bookings.php");
exit(); 