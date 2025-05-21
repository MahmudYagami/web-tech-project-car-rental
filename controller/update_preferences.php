<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermodel.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare preferences data
    $preferences = [
        'seat_position' => $_POST['seat_position'] ?? '',
        'mirror_position' => $_POST['mirror_position'] ?? '',
        'preferred_car_type' => $_POST['preferred_car_type'] ?? ''
    ];

    // Update preferences using model function
    $result = updateUserPreferences($conn, $_SESSION['user_id'], $preferences);
    
    if ($result['success']) {
        $_SESSION['preferences_success'] = $result['message'];
    } else {
        $_SESSION['preferences_error'] = $result['message'];
    }

    header("Location: ../view/customer_profile.php");
    exit();
} else {
    // If not POST request, redirect to profile page
    header("Location: ../view/customer_profile.php");
    exit();
} 