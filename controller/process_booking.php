<?php
session_start();
require_once '../model/db.php';
require_once '../model/booking_model.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// Handle initial booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['car_id']) && !isset($_POST['payment_method'])) {
    $carId = $_POST['car_id'];
    $userId = $_SESSION['user_id'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    
    error_log("Initial booking submission - Car ID: $carId, User ID: $userId, Start Date: $startDate, End Date: $endDate");
    
    // Validate dates
    if (empty($startDate) || empty($endDate)) {
        $_SESSION['booking_error'] = "Please select both start and end dates.";
        header("Location: ../view/inventory.php");
        exit();
    }
    
    // Get car details
    $result = getCarForBooking($conn, $carId);
    if (!$result['success']) {
        $_SESSION['booking_error'] = $result['message'];
        header("Location: ../view/inventory.php");
        exit();
    }
    
    $car = $result['data'];
    
    // Calculate booking cost
    $costDetails = calculateBookingCost($startDate, $endDate, $car['daily_rate']);
    
    // Store booking details in session for checkout
    $_SESSION['booking_details'] = [
        'car_id' => $carId,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'days' => $costDetails['days'],
        'subtotal' => $costDetails['subtotal']
    ];
    $_SESSION['car_details'] = $car;
    
    // Redirect to checkout page
    header("Location: ../view/checkout.php");
    exit();
}

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {
    error_log("Processing checkout submission");
    
    // Verify booking details exist in session
    if (!isset($_SESSION['booking_details']) || !isset($_SESSION['car_details'])) {
        error_log("Missing booking details in session");
        $_SESSION['booking_error'] = "Invalid booking session. Please try booking again.";
        header("Location: ../view/inventory.php");
        exit();
    }
    
    $booking = $_SESSION['booking_details'];
    $userId = $_SESSION['user_id'];
    $paymentMethod = $_POST['payment_method'];
    $promoCode = $_POST['promo_code'] ?? null;
    
    error_log("Checkout details - User ID: $userId, Car ID: {$booking['car_id']}, Payment Method: $paymentMethod");
    
    // Process the booking
    $result = processBooking($conn, $userId, $booking, $paymentMethod, $promoCode);
    
    if ($result['success']) {
        // Clear booking session data
        unset($_SESSION['booking_details']);
        unset($_SESSION['car_details']);
        
        $_SESSION['booking_success'] = "Your booking has been submitted successfully!";
        header("Location: ../view/user_dashboard.php");
    } else {
        $_SESSION['booking_error'] = "An error occurred while processing your booking: " . $result['message'];
        header("Location: ../view/checkout.php");
    }
    
    mysqli_close($conn);
    exit();
}

// If we get here, redirect to inventory
header("Location: ../view/inventory.php");
exit();
?> 