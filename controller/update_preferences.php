<?php
session_start();
require_once '../model/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $seat_position = trim($_POST['seat_position'] ?? '');
    $mirror_position = trim($_POST['mirror_position'] ?? '');
    $preferred_car_type = trim($_POST['preferred_car_type'] ?? '');

    // Check if preferences exist for this user
    $check_sql = "SELECT id FROM user_preferences WHERE user_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($result) > 0) {
        // Update existing preferences
        $update_sql = "UPDATE user_preferences SET 
                      seat_position = ?, 
                      mirror_position = ?, 
                      preferred_car_type = ? 
                      WHERE user_id = ?";
        
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "sssi", $seat_position, $mirror_position, $preferred_car_type, $_SESSION['user_id']);
    } else {
        // Insert new preferences
        $insert_sql = "INSERT INTO user_preferences 
                      (user_id, seat_position, mirror_position, preferred_car_type) 
                      VALUES (?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($stmt, "isss", $_SESSION['user_id'], $seat_position, $mirror_position, $preferred_car_type);
    }

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['preferences_success'] = "Car preferences updated successfully.";
    } else {
        $_SESSION['preferences_error'] = "Error updating preferences. Please try again.";
    }

    mysqli_stmt_close($stmt);
    header("Location: ../view/customer_profile.php");
    exit();
} else {
    // If not POST request, redirect to profile page
    header("Location: ../view/customer_profile.php");
    exit();
} 