<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermodel.php';

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
    $query = "SELECT * FROM cars WHERE car_id = ? AND status = 'available'";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($conn));
        $_SESSION['booking_error'] = "Database error occurred.";
        header("Location: ../view/inventory.php");
        exit();
    }
    
    mysqli_stmt_bind_param($stmt, "i", $carId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $car = mysqli_fetch_assoc($result);
    
    if (!$car) {
        error_log("Car not found or not available - Car ID: $carId");
        $_SESSION['booking_error'] = "Selected car is no longer available.";
        header("Location: ../view/inventory.php");
        exit();
    }
    
    // Calculate days and total amount
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $days = $start->diff($end)->days + 1;
    $subtotal = $days * $car['daily_rate'];
    
    error_log("Calculated booking details - Days: $days, Subtotal: $subtotal");
    
    // Store booking details in session for checkout
    $_SESSION['booking_details'] = [
        'car_id' => $carId,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'days' => $days,
        'subtotal' => $subtotal
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
    $car = $_SESSION['car_details'];
    $userId = $_SESSION['user_id'];
    $paymentMethod = $_POST['payment_method'];
    $promoCode = $_POST['promo_code'] ?? null;
    
    error_log("Checkout details - User ID: $userId, Car ID: {$booking['car_id']}, Payment Method: $paymentMethod");
    
    // Calculate final amount with promo code if applicable
    $totalAmount = $booking['subtotal'];
    if ($promoCode === 'WELCOME10') {
        $totalAmount = $booking['subtotal'] * 0.9; // 10% discount
    }
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Check if bookings table exists
        $check_table = "SHOW TABLES LIKE 'bookings'";
        $table_result = mysqli_query($conn, $check_table);
        if (mysqli_num_rows($table_result) == 0) {
            throw new Exception("Bookings table does not exist");
        }

        // Check if payment_status column exists
        $check_column = "SHOW COLUMNS FROM bookings LIKE 'payment_status'";
        $column_result = mysqli_query($conn, $check_column);
        $has_payment_status = mysqli_num_rows($column_result) > 0;
        
        // Insert booking
        if ($has_payment_status) {
            $query = "INSERT INTO bookings (user_id, car_id, start_date, end_date, total_amount, status, payment_method, payment_status, promo_code, booking_date) 
                      VALUES (?, ?, ?, ?, ?, 'pending', ?, 'paid', ?, NOW())";
            $stmt = mysqli_prepare($conn, $query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "iissdss", $userId, $booking['car_id'], $booking['start_date'], 
                                  $booking['end_date'], $totalAmount, $paymentMethod, $promoCode);
        } else {
            $query = "INSERT INTO bookings (user_id, car_id, start_date, end_date, total_amount, status, payment_method, promo_code, booking_date) 
                      VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, NOW())";
            $stmt = mysqli_prepare($conn, $query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "iissdss", $userId, $booking['car_id'], $booking['start_date'], 
                                  $booking['end_date'], $totalAmount, $paymentMethod, $promoCode);
        }
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
        }
        
        $bookingId = mysqli_insert_id($conn);
        error_log("Booking created successfully - Booking ID: $bookingId");
        
        // Update car status
        $updateQuery = "UPDATE cars SET status = 'booked' WHERE car_id = ? AND status = 'available'";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        if (!$updateStmt) {
            throw new Exception("Prepare update failed: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($updateStmt, "i", $booking['car_id']);
        if (!mysqli_stmt_execute($updateStmt)) {
            throw new Exception("Execute update failed: " . mysqli_stmt_error($updateStmt));
        }
        
        error_log("Car status updated successfully");
        
        // After successful booking insertion, create a notification
        if ($bookingId) {
            $user = getUserById($conn, $userId);
            $car = getCarById($conn, $booking['car_id']);
            
            $message = sprintf(
                "New booking from %s %s for %s from %s to %s",
                $user['first_name'],
                $user['last_name'],
                $car['model'],
                date('Y-m-d', strtotime($booking['start_date'])),
                date('Y-m-d', strtotime($booking['end_date']))
            );
            
            $notification_sql = "INSERT INTO notifications (message) VALUES (?)";
            $notification_stmt = mysqli_prepare($conn, $notification_sql);
            mysqli_stmt_bind_param($notification_stmt, "s", $message);
            mysqli_stmt_execute($notification_stmt);
            mysqli_stmt_close($notification_stmt);
        }
        
        // If we got here, commit the transaction
        mysqli_commit($conn);
        
        // Clear booking session data
        unset($_SESSION['booking_details']);
        unset($_SESSION['car_details']);
        
        $_SESSION['booking_success'] = "Your booking has been submitted successfully!";
        header("Location: ../view/user_dashboard.php");
        
    } catch (Exception $e) {
        // If there was an error, rollback the transaction
        mysqli_rollback($conn);
        error_log("Booking error: " . $e->getMessage());
        $_SESSION['booking_error'] = "An error occurred while processing your booking: " . $e->getMessage();
        header("Location: ../view/checkout.php");
    }
    
    exit();
}

// If we get here, redirect to inventory
header("Location: ../view/inventory.php");
exit();
?> 