<?php
require_once 'db.php';

function getAdminDashboardStats($conn) {
    $stats = array();
    
    // Get total users
    $users_query = "SELECT COUNT(*) as total FROM users";
    $users_result = mysqli_query($conn, $users_query);
    $stats['total_users'] = mysqli_fetch_assoc($users_result)['total'];

    // Get total bookings
    $bookings_query = "SELECT COUNT(*) as total FROM bookings";
    $bookings_result = mysqli_query($conn, $bookings_query);
    $stats['total_bookings'] = mysqli_fetch_assoc($bookings_result)['total'];

    // Get total cars
    $cars_query = "SELECT COUNT(*) as total FROM cars";
    $cars_result = mysqli_query($conn, $cars_query);
    $stats['total_cars'] = mysqli_fetch_assoc($cars_result)['total'];

    // Check if payment_status column exists
    $check_column = "SHOW COLUMNS FROM bookings LIKE 'payment_status'";
    $column_result = mysqli_query($conn, $check_column);
    $has_payment_status = mysqli_num_rows($column_result) > 0;

    // Get revenue for current month
    if ($has_payment_status) {
        $revenue_query = "SELECT SUM(total_amount) as total FROM bookings 
                         WHERE MONTH(booking_date) = MONTH(CURRENT_DATE()) 
                         AND YEAR(booking_date) = YEAR(CURRENT_DATE())
                         AND payment_status = 'paid'";
    } else {
        $revenue_query = "SELECT SUM(total_amount) as total FROM bookings 
                         WHERE MONTH(booking_date) = MONTH(CURRENT_DATE()) 
                         AND YEAR(booking_date) = YEAR(CURRENT_DATE())";
    }
    $revenue_result = mysqli_query($conn, $revenue_query);
    $stats['monthly_revenue'] = mysqli_fetch_assoc($revenue_result)['total'] ?? 0;

    // Get total damage reports
    $damage_query = "SELECT COUNT(*) as total FROM reports";
    $damage_result = mysqli_query($conn, $damage_query);
    $stats['total_damage_reports'] = mysqli_fetch_assoc($damage_result)['total'];

    return $stats;
} 