<?php
function getUserByEmail($conn, $email) {
    $sql = "SELECT user_id, email, password, first_name, last_name, role FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $user;
    }
    
    mysqli_stmt_close($stmt);
    return false;
}

function createUser($conn, $email, $password, $firstName, $lastName, $role = 'user') {
    $sql = "INSERT INTO users (email, password, first_name, last_name, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "sssss", $email, $password, $firstName, $lastName, $role);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $result;
}

function updateUser($conn, $userId, $email, $firstName, $lastName, $role) {
    $sql = "UPDATE users SET email = ?, first_name = ?, last_name = ?, role = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ssssi", $email, $firstName, $lastName, $role, $userId);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $result;
}

function deleteUser($conn, $userId) {
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $result;
}

function getAllUsers($conn) {
    $sql = "SELECT user_id, email, first_name, last_name, role FROM users";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        return false;
    }

    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    
    return $users;
}

function getUserLoyaltyPoints($conn, $userId) {
    $sql = "SELECT points FROM loyalty_program WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return 0;
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($stmt);
        return $row['points'];
    }
    
    mysqli_stmt_close($stmt);
    return 0;
}

function getUserDamageReports($conn, $userId) {
    $sql = "SELECT COUNT(*) as total FROM reports WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return 0;
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($stmt);
        return $row['total'];
    }
    
    mysqli_stmt_close($stmt);
    return 0;
}

function getUserBookingStats($conn, $userId) {
    $stats = [
        'total_bookings' => 0,
        'active_bookings' => 0,
        'total_spent' => 0,
        'pending_returns' => 0
    ];

    // Get total bookings
    $total_sql = "SELECT COUNT(*) as total FROM bookings WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $total_sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $stats['total_bookings'] = $row['total'];
    }
    mysqli_stmt_close($stmt);

    // Get active bookings (status = 'confirmed')
    $active_sql = "SELECT COUNT(*) as active FROM bookings WHERE user_id = ? AND status = 'confirmed'";
    $stmt = mysqli_prepare($conn, $active_sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $stats['active_bookings'] = $row['active'];
    }
    mysqli_stmt_close($stmt);

    // Get total spent
    $check_column = "SHOW COLUMNS FROM bookings LIKE 'payment_status'";
    $column_result = mysqli_query($conn, $check_column);
    $has_payment_status = mysqli_num_rows($column_result) > 0;

    if ($has_payment_status) {
        $spent_sql = "SELECT SUM(total_amount) as total FROM bookings WHERE user_id = ? AND payment_status = 'paid'";
    } else {
        $spent_sql = "SELECT SUM(total_amount) as total FROM bookings WHERE user_id = ?";
    }
    $stmt = mysqli_prepare($conn, $spent_sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $stats['total_spent'] = $row['total'] ?? 0;
    }
    mysqli_stmt_close($stmt);

    // Get pending returns (count bookings with 'pending' status)
    $pending_sql = "SELECT COUNT(*) as pending FROM bookings 
                   WHERE user_id = ? 
                   AND status = 'pending'";
    $stmt = mysqli_prepare($conn, $pending_sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $stats['pending_returns'] = $row['pending'];
    }
    mysqli_stmt_close($stmt);

    return $stats;
}

function getUserBookings($conn, $userId) {
    $sql = "SELECT b.*, c.brand, c.model, c.image_url 
            FROM bookings b 
            JOIN cars c ON b.car_id = c.car_id 
            WHERE b.user_id = ? 
            ORDER BY b.booking_date DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $bookings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $bookings[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $bookings;
}

function getUserById($conn, $userId) {
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function getCarById($conn, $carId) {
    $sql = "SELECT * FROM cars WHERE car_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $carId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}
?> 