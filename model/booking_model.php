<?php
require_once 'db.php';
require_once 'usermodel.php';

function deleteBooking($conn, $booking_id) {
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
        return ['success' => true, 'message' => 'Booking deleted successfully'];
        
    } catch (Exception $e) {
        // If there's an error, rollback the transaction
        mysqli_rollback($conn);
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getCarForBooking($conn, $carId) {
    $query = "SELECT * FROM cars WHERE car_id = ? AND status = 'available'";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($conn));
        return ['success' => false, 'message' => 'Database error occurred'];
    }
    
    mysqli_stmt_bind_param($stmt, "i", $carId);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to execute query'];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $car = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$car) {
        return ['success' => false, 'message' => 'Selected car is no longer available'];
    }
    
    return ['success' => true, 'data' => $car];
}

function calculateBookingCost($startDate, $endDate, $dailyRate) {
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $days = $start->diff($end)->days + 1;
    $subtotal = $days * $dailyRate;
    
    return [
        'days' => $days,
        'subtotal' => $subtotal
    ];
}

function processBooking($conn, $userId, $bookingDetails, $paymentMethod, $promoCode = null) {
    error_log("Processing booking for User ID: $userId");
    
    // Calculate final amount with promo code if applicable
    $totalAmount = $bookingDetails['subtotal'];
    if ($promoCode === 'WELCOME10') {
        $totalAmount = $bookingDetails['subtotal'] * 0.9; // 10% discount
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
            mysqli_stmt_bind_param($stmt, "iissdss", $userId, $bookingDetails['car_id'], $bookingDetails['start_date'], 
                                  $bookingDetails['end_date'], $totalAmount, $paymentMethod, $promoCode);
        } else {
            $query = "INSERT INTO bookings (user_id, car_id, start_date, end_date, total_amount, status, payment_method, promo_code, booking_date) 
                      VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, NOW())";
            $stmt = mysqli_prepare($conn, $query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "iissdss", $userId, $bookingDetails['car_id'], $bookingDetails['start_date'], 
                                  $bookingDetails['end_date'], $totalAmount, $paymentMethod, $promoCode);
        }
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
        }
        
        $bookingId = mysqli_insert_id($conn);
        error_log("Booking created successfully - Booking ID: $bookingId");
        mysqli_stmt_close($stmt);
        
        // Update car status
        $updateQuery = "UPDATE cars SET status = 'booked' WHERE car_id = ? AND status = 'available'";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        if (!$updateStmt) {
            throw new Exception("Prepare update failed: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($updateStmt, "i", $bookingDetails['car_id']);
        if (!mysqli_stmt_execute($updateStmt)) {
            throw new Exception("Execute update failed: " . mysqli_stmt_error($updateStmt));
        }
        mysqli_stmt_close($updateStmt);
        
        error_log("Car status updated successfully");
        
        // Create notification
        if ($bookingId) {
            $user = getUserById($conn, $userId);
            $car = getCarById($conn, $bookingDetails['car_id']);
            
            $message = sprintf(
                "New booking from %s %s for %s from %s to %s",
                $user['first_name'],
                $user['last_name'],
                $car['model'],
                date('Y-m-d', strtotime($bookingDetails['start_date'])),
                date('Y-m-d', strtotime($bookingDetails['end_date']))
            );
            
            $notification_sql = "INSERT INTO notifications (message) VALUES (?)";
            $notification_stmt = mysqli_prepare($conn, $notification_sql);
            mysqli_stmt_bind_param($notification_stmt, "s", $message);
            mysqli_stmt_execute($notification_stmt);
            mysqli_stmt_close($notification_stmt);
        }
        
        // Commit the transaction
        mysqli_commit($conn);
        return ['success' => true, 'booking_id' => $bookingId];
        
    } catch (Exception $e) {
        // Rollback the transaction on error
        mysqli_rollback($conn);
        error_log("Booking error: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getValidBookingStatuses() {
    return ['pending', 'confirmed', 'cancelled', 'completed'];
}

function validateBookingStatus($status) {
    return in_array($status, getValidBookingStatuses());
}

function updateBookingStatus($conn, $booking_id, $new_status) {
    // Validate status
    if (!validateBookingStatus($new_status)) {
        return ['success' => false, 'message' => 'Invalid status'];
    }
    
    // Update the booking status
    $sql = "UPDATE bookings SET status = ? WHERE booking_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error'];
    }
    
    mysqli_stmt_bind_param($stmt, "si", $new_status, $booking_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_error($conn);
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Error updating status: ' . $error];
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => true, 'message' => 'Status updated successfully'];
}

function getActiveBookings($conn, $user_id) {
    $sql = "SELECT b.*, c.brand, c.model 
            FROM bookings b 
            JOIN cars c ON b.car_id = c.car_id 
            WHERE b.user_id = ? AND b.status = 'confirmed'";
            
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error', 'data' => []];
    }
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to fetch bookings', 'data' => []];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    
    return ['success' => true, 'data' => $bookings];
}

function verifyBookingOwnership($conn, $booking_id, $user_id) {
    $sql = "SELECT * FROM bookings WHERE booking_id = ? AND user_id = ? AND status = 'confirmed'";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error'];
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to verify booking'];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $verified = mysqli_num_rows($result) > 0;
    mysqli_stmt_close($stmt);
    
    return ['success' => $verified, 'message' => $verified ? 'Booking verified' : 'Invalid booking or unauthorized action'];
}

function processCarReturn($conn, $booking_id) {
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
        mysqli_stmt_close($stmt);
        
        if (!$booking) {
            throw new Exception("Booking not found");
        }
        
        // Update car status to available
        $update_car_query = "UPDATE cars SET status = 'available' WHERE car_id = ?";
        $stmt = mysqli_prepare($conn, $update_car_query);
        mysqli_stmt_bind_param($stmt, "i", $booking['car_id']);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error updating car status");
        }
        mysqli_stmt_close($stmt);
        
        // Delete the booking
        $delete_sql = "DELETE FROM bookings WHERE booking_id = ?";
        $stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($stmt, "i", $booking_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error deleting booking");
        }
        mysqli_stmt_close($stmt);
        
        // Commit transaction
        mysqli_commit($conn);
        return ['success' => true, 'message' => 'Car has been successfully returned!'];
        
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getAllBookingsWithDetails($conn) {
    $query = "SELECT b.*, 
              u.first_name, u.last_name, u.email,
              c.brand, c.model, c.image_url
              FROM bookings b
              JOIN users u ON b.user_id = u.user_id
              JOIN cars c ON b.car_id = c.car_id
              ORDER BY b.booking_date DESC";
              
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return ['success' => false, 'message' => 'Failed to fetch bookings', 'data' => []];
    }
    
    $bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return ['success' => true, 'data' => $bookings];
}

function hasPaymentStatusColumn($conn) {
    $check_column = "SHOW COLUMNS FROM bookings LIKE 'payment_status'";
    $column_result = mysqli_query($conn, $check_column);
    return mysqli_num_rows($column_result) > 0;
}

function searchBookings($conn, $search_term) {
    $search_term = '%' . mysqli_real_escape_string($conn, $search_term) . '%';
    
    $query = "SELECT b.*, 
              u.first_name, u.last_name, u.email,
              c.brand, c.model, c.image_url
              FROM bookings b
              JOIN users u ON b.user_id = u.user_id
              JOIN cars c ON b.car_id = c.car_id
              WHERE b.booking_id LIKE ? 
              OR CONCAT(u.first_name, ' ', u.last_name) LIKE ?
              OR CONCAT(c.brand, ' ', c.model) LIKE ?
              ORDER BY b.booking_date DESC";
              
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $search_term, $search_term, $search_term);
    
    if (!mysqli_stmt_execute($stmt)) {
        return ['success' => false, 'message' => 'Failed to search bookings', 'data' => []];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    
    return ['success' => true, 'data' => $bookings];
}

function getAvailableCarById($conn, $car_id) {
    $query = "SELECT * FROM cars WHERE car_id = ? AND status = 'available'";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Failed to prepare statement'];
    }
    
    mysqli_stmt_bind_param($stmt, "i", $car_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to execute query'];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $car = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$car) {
        return ['success' => false, 'message' => 'Selected car is no longer available'];
    }
    
    return ['success' => true, 'data' => $car];
}
?> 