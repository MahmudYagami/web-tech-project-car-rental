<?php
session_start();
require_once '../model/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $current_password = mysqli_real_escape_string($conn, $_POST['recent_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_new_password']);

    // Validate password requirements
    if (strlen($new_password) < 5 || 
        !preg_match('/[A-Z]/', $new_password) || 
        !preg_match('/[a-z]/', $new_password) || 
        !preg_match('/[0-9]/', $new_password)) {
        echo json_encode(['status' => 'error', 'message' => 'Password does not meet requirements']);
        exit();
    }

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'New passwords do not match']);
        exit();
    }

    // Verify current password
    $query = "SELECT password FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Compare passwords directly since they're stored as plain text
        if ($current_password === $row['password']) {
            // Update password (storing as plain text to match the system)
            $update_query = "UPDATE users SET password = ? WHERE email = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "ss", $new_password, $email);
            
            if (mysqli_stmt_execute($update_stmt)) {
                echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Email not found']);
    }

    mysqli_stmt_close($stmt);
    if (isset($update_stmt)) {
        mysqli_stmt_close($update_stmt);
    }
}

closeConnection($conn);
?> 