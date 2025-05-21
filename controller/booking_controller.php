<?php
session_start();
require_once '../model/db.php';
require_once '../model/vehicle_model.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// Check if car_id is provided
if (!isset($_GET['car_id'])) {
    header("Location: ../view/inventory.php");
    exit();
}

$car_id = $_GET['car_id'];

// Get car details
$result = getCarForBooking($conn, $car_id);

if (!$result['success']) {
    $_SESSION['error'] = $result['message'];
    header("Location: ../view/inventory.php");
    exit();
}

// Store car data in session
$_SESSION['booking_car'] = $result['data'];

// Redirect to booking page
header("Location: ../view/booking.php");
exit();
?> 