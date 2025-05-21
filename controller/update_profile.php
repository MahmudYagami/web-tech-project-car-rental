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
    // Prepare profile data
    $profile_data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'mobile' => $_POST['mobile'] ?? '',
        'address' => $_POST['address'] ?? ''
    ];

    // Update profile using model function
    $result = updateUserProfile($conn, $_SESSION['user_id'], $profile_data);
    
    if ($result['success']) {
        // Update session email if it was changed
        if ($result['email_changed']) {
            $_SESSION['email'] = $result['new_email'];
        }
        $_SESSION['profile_success'] = $result['message'];
    } else {
        $_SESSION['profile_error'] = $result['message'];
    }

    header("Location: ../view/customer_profile.php");
    exit();
} else {
    // If not POST request, redirect to profile page
    header("Location: ../view/customer_profile.php");
    exit();
} 