<?php
require_once 'db.php';

function getDashboardStats($conn) {
    $stats = [
        'total_users' => 0,
        'total_bookings' => 0,
        'total_cars' => 0,
        'monthly_revenue' => 0,
        'total_damage_reports' => 0,
        'has_payment_status' => false
    ];
    
    // Get total users
    $users_query = "SELECT COUNT(*) as total FROM users";
    $users_result = mysqli_query($conn, $users_query);
    if ($users_result) {
        $stats['total_users'] = mysqli_fetch_assoc($users_result)['total'];
    }
    
    // Get total bookings
    $bookings_query = "SELECT COUNT(*) as total FROM bookings";
    $bookings_result = mysqli_query($conn, $bookings_query);
    if ($bookings_result) {
        $stats['total_bookings'] = mysqli_fetch_assoc($bookings_result)['total'];
    }
    
    // Get total cars
    $cars_query = "SELECT COUNT(*) as total FROM cars";
    $cars_result = mysqli_query($conn, $cars_query);
    if ($cars_result) {
        $stats['total_cars'] = mysqli_fetch_assoc($cars_result)['total'];
    }
    
    // Check if payment_status column exists
    $check_column = "SHOW COLUMNS FROM bookings LIKE 'payment_status'";
    $column_result = mysqli_query($conn, $check_column);
    $stats['has_payment_status'] = mysqli_num_rows($column_result) > 0;
    
    // Get revenue for current month
    if ($stats['has_payment_status']) {
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
    if ($revenue_result) {
        $stats['monthly_revenue'] = mysqli_fetch_assoc($revenue_result)['total'] ?? 0;
    }
    
    // Get total damage reports
    $damage_query = "SELECT COUNT(*) as total FROM reports";
    $damage_result = mysqli_query($conn, $damage_query);
    if ($damage_result) {
        $stats['total_damage_reports'] = mysqli_fetch_assoc($damage_result)['total'];
    }
    
    return ['success' => true, 'data' => $stats];
}

function getUnreadNotificationsCount($conn, $user_id) {
    $query = "SELECT COUNT(*) as count FROM notifications 
              WHERE user_id = ? AND is_read = 0";
              
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return ['success' => false, 'message' => 'Failed to prepare statement'];
    }
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to execute query'];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $count = mysqli_fetch_assoc($result)['count'];
    mysqli_stmt_close($stmt);
    
    return ['success' => true, 'data' => $count];
}

function getNotifications($conn, $user_id, $last_id = 0) {
    $query = "SELECT * FROM notifications 
              WHERE user_id = ? AND id > ? 
              ORDER BY created_at DESC 
              LIMIT 10";
              
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return ['success' => false, 'message' => 'Failed to prepare statement'];
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $last_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to execute query'];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $notifications = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => true, 'data' => $notifications];
}
?> 