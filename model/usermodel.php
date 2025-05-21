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

function getAllCars($conn) {
    $query = "SELECT * FROM cars WHERE status = 'available' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    $cars = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $cars[] = $row;
    }
    
    return $cars;
}

function getUsername($conn) {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $query = "SELECT first_name, last_name FROM users WHERE id = '$userId'";
        $result = mysqli_query($conn, $query);
        if ($user = mysqli_fetch_assoc($result)) {
            return $user['first_name'] . ' ' . $user['last_name'];
        }
    }
    return 'Guest';
}

function checkLogin($conn, $email, $password) {
    // Get user from database
    $sql = "SELECT user_id, email, password, first_name, last_name, role FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error'];
    }
    
    mysqli_stmt_bind_param($stmt, "s", $email);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Database error'];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    
    if ($user = mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($stmt);
        
        // Verify password
        if ($user['password'] === $password) {
            return [
                'success' => true,
                'data' => [
                    'user_id' => $user['user_id'],
                    'email' => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'role' => $user['role']
                ]
            ];
        }
    }
    
    return ['success' => false, 'message' => 'Invalid email or password'];
}

function saveRememberToken($conn, $user_id, $token) {
    $sql = "INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Failed to prepare token statement'];
    }
    
    mysqli_stmt_bind_param($stmt, "is", $user_id, $token);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to save remember token'];
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => true];
}

function validatePasswordRequirements($password) {
    return strlen($password) >= 5 && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[a-z]/', $password) && 
           preg_match('/[0-9]/', $password);
}

function verifyCurrentPassword($conn, $email, $current_password) {
    $query = "SELECT password FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error'];
    }
    
    mysqli_stmt_bind_param($stmt, "s", $email);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to verify current password'];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$row) {
        return ['success' => false, 'message' => 'Email not found'];
    }
    
    // Compare passwords directly since they're stored as plain text
    if ($current_password !== $row['password']) {
        return ['success' => false, 'message' => 'Current password is incorrect'];
    }
    
    return ['success' => true];
}

function updateUserPassword($conn, $email, $new_password) {
    $update_query = "UPDATE users SET password = ? WHERE email = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error'];
    }
    
    mysqli_stmt_bind_param($stmt, "ss", $new_password, $email);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to update password'];
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => true, 'message' => 'Password updated successfully'];
}

function getUserPreferences($conn, $user_id) {
    $sql = "SELECT * FROM user_preferences WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error'];
    }
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to fetch preferences'];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $preferences = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return ['success' => true, 'data' => $preferences];
}

function updateUserPreferences($conn, $user_id, $preferences) {
    // Validate preferences data
    $required_fields = ['seat_position', 'mirror_position', 'preferred_car_type'];
    foreach ($required_fields as $field) {
        if (!isset($preferences[$field]) || trim($preferences[$field]) === '') {
            return ['success' => false, 'message' => "Missing required field: $field"];
        }
        // Sanitize the input
        $preferences[$field] = trim($preferences[$field]);
    }

    // Check if preferences exist for this user
    $check_sql = "SELECT id FROM user_preferences WHERE user_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    
    if (!$check_stmt) {
        return ['success' => false, 'message' => 'Database error'];
    }
    
    mysqli_stmt_bind_param($check_stmt, "i", $user_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    mysqli_stmt_close($check_stmt);
    
    if (mysqli_num_rows($result) > 0) {
        // Update existing preferences
        $update_sql = "UPDATE user_preferences SET 
                      seat_position = ?, 
                      mirror_position = ?, 
                      preferred_car_type = ? 
                      WHERE user_id = ?";
        
        $stmt = mysqli_prepare($conn, $update_sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Failed to prepare update statement'];
        }
        
        mysqli_stmt_bind_param($stmt, "sssi", 
            $preferences['seat_position'], 
            $preferences['mirror_position'], 
            $preferences['preferred_car_type'], 
            $user_id
        );
    } else {
        // Insert new preferences
        $insert_sql = "INSERT INTO user_preferences 
                      (user_id, seat_position, mirror_position, preferred_car_type) 
                      VALUES (?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $insert_sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Failed to prepare insert statement'];
        }
        
        mysqli_stmt_bind_param($stmt, "isss", 
            $user_id, 
            $preferences['seat_position'], 
            $preferences['mirror_position'], 
            $preferences['preferred_car_type']
        );
    }
    
    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_error($conn);
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to save preferences: ' . $error];
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => true, 'message' => 'Car preferences updated successfully'];
}

function validateProfileData($profile_data) {
    $errors = [];
    
    // Validate required fields
    if (empty($profile_data['first_name']) || empty($profile_data['last_name']) || empty($profile_data['email'])) {
        $errors[] = "First name, last name, and email are required fields.";
    }
    
    // Validate email format
    if (!filter_var($profile_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    return empty($errors) ? ['success' => true] : ['success' => false, 'message' => implode(' ', $errors)];
}

function checkEmailAvailability($conn, $email, $current_user_id) {
    // Get user by email
    $user = getUserByEmail($conn, $email);
    
    // If no user found with this email or the user found is the current user
    if (!$user || $user['user_id'] == $current_user_id) {
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Email is already taken by another user.'];
}

function updateUserProfile($conn, $user_id, $profile_data) {
    // Sanitize input data
    $profile_data = array_map('trim', $profile_data);
    
    // Validate profile data
    $validation = validateProfileData($profile_data);
    if (!$validation['success']) {
        return $validation;
    }
    
    // Check email availability if email is being changed
    $current_user = getUserById($conn, $user_id);
    if ($current_user && $profile_data['email'] !== $current_user['email']) {
        $email_check = checkEmailAvailability($conn, $profile_data['email'], $user_id);
        if (!$email_check['success']) {
            return $email_check;
        }
    }
    
    // Update user data
    $update_sql = "UPDATE users SET 
                   first_name = ?, 
                   last_name = ?, 
                   email = ?, 
                   mobile = ?, 
                   address = ? 
                   WHERE user_id = ?";
    
    $stmt = mysqli_prepare($conn, $update_sql);
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error while preparing statement'];
    }
    
    mysqli_stmt_bind_param($stmt, "sssssi", 
        $profile_data['first_name'],
        $profile_data['last_name'],
        $profile_data['email'],
        $profile_data['mobile'],
        $profile_data['address'],
        $user_id
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_error($conn);
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Error updating profile: ' . $error];
    }
    
    mysqli_stmt_close($stmt);
    return [
        'success' => true, 
        'message' => 'Profile updated successfully',
        'email_changed' => $current_user && $profile_data['email'] !== $current_user['email'],
        'new_email' => $profile_data['email']
    ];
}

function validateLicenseFile($file) {
    $errors = [];
    
    // Check if file was uploaded without errors
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Error uploading file. Please try again.";
        return ['success' => false, 'message' => implode(' ', $errors)];
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($file['type'], $allowed_types)) {
        $errors[] = "Invalid file type. Please upload a JPG or PNG image.";
    }
    
    // Validate file size (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $max_size) {
        $errors[] = "File is too large. Maximum size is 5MB.";
    }
    
    return empty($errors) ? ['success' => true] : ['success' => false, 'message' => implode(' ', $errors)];
}

function handleLicenseUpload($file, $user_id) {
    // Create upload directory if it doesn't exist
    $upload_dir = '../uploads/licenses/';
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            return ['success' => false, 'message' => 'Failed to create upload directory'];
        }
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = 'license_' . $user_id . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    // Move uploaded file to destination
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['success' => false, 'message' => 'Error saving file. Please try again.'];
    }
    
    return [
        'success' => true,
        'file_path' => $upload_path,
        'relative_path' => 'uploads/licenses/' . $new_filename
    ];
}

function updateUserLicense($conn, $user_id, $license_path) {
    $update_sql = "UPDATE users SET license_image = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Error preparing database statement'];
    }
    
    mysqli_stmt_bind_param($stmt, "si", $license_path, $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_error($conn);
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Error updating database: ' . $error];
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => true, 'message' => "Driver's license uploaded successfully"];
}

function uploadLicense($conn, $user_id, $file) {
    // Validate the file
    $validation = validateLicenseFile($file);
    if (!$validation['success']) {
        return $validation;
    }
    
    // Handle file upload
    $upload_result = handleLicenseUpload($file, $user_id);
    if (!$upload_result['success']) {
        return $upload_result;
    }
    
    // Update user record with license path
    $update_result = updateUserLicense($conn, $user_id, $upload_result['relative_path']);
    
    // If database update fails, delete the uploaded file
    if (!$update_result['success']) {
        unlink($upload_result['file_path']);
    }
    
    return $update_result;
}

function getUserPreferencesById($conn, $user_id) {
    $sql = "SELECT * FROM user_preferences WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $preferences = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return $preferences;
}

function getUserBookingHistory($conn, $user_id) {
    $sql = "SELECT b.*, c.model, c.brand 
            FROM bookings b 
            JOIN cars c ON b.car_id = c.car_id 
            WHERE b.user_id = ? 
            ORDER BY b.booking_date DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $bookings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $bookings[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $bookings;
}
?> 