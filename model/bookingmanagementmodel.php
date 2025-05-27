<?php
require_once 'db.php';

function hasPaymentStatusColumn($conn) {
    $check_column = "SHOW COLUMNS FROM bookings LIKE 'payment_status'";
    $column_result = mysqli_query($conn, $check_column);
    return mysqli_num_rows($column_result) > 0;
}

function searchBookings($conn, $searchTerm) {
    $searchTerm = "%" . mysqli_real_escape_string($conn, $searchTerm) . "%";
    
    // Check if payment_status column exists
    $has_payment_status = hasPaymentStatusColumn($conn);
    
    // Build the query based on whether payment_status exists
    $query = "SELECT b.*, u.first_name, u.last_name, u.email, 
                     c.brand, c.model, c.image_url";
    
    if ($has_payment_status) {
        $query .= ", b.payment_status";
    } else {
        $query .= ", 'N/A' as payment_status";
    }
    
    $query .= " FROM bookings b
                JOIN users u ON b.user_id = u.user_id
                JOIN cars c ON b.car_id = c.car_id
                WHERE b.booking_id LIKE ? 
                OR u.first_name LIKE ? 
                OR u.last_name LIKE ? 
                OR u.email LIKE ?
                OR c.brand LIKE ?
                OR c.model LIKE ?
                ORDER BY b.booking_id DESC";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssss", 
        $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getAllBookings($conn) {
    // Check if payment_status column exists
    $has_payment_status = hasPaymentStatusColumn($conn);
    
    // Build the query based on whether payment_status exists
    $query = "SELECT b.*, u.first_name, u.last_name, u.email, 
                     c.brand, c.model, c.image_url";
    
    if ($has_payment_status) {
        $query .= ", b.payment_status";
    } else {
        $query .= ", 'N/A' as payment_status";
    }
    
    $query .= " FROM bookings b
                JOIN users u ON b.user_id = u.user_id
                JOIN cars c ON b.car_id = c.car_id
                ORDER BY b.booking_id DESC";
    
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function updateBookingStatus($conn, $bookingId, $status) {
    $query = "UPDATE bookings SET status = ? WHERE booking_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $bookingId);
    return mysqli_stmt_execute($stmt);
}

function deleteBooking($conn, $bookingId) {
    $query = "DELETE FROM bookings WHERE booking_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $bookingId);
    return mysqli_stmt_execute($stmt);
} 