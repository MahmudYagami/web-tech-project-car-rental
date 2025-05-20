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
    // Get form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $_SESSION['profile_error'] = "First name, last name, and email are required fields.";
        header("Location: ../view/edit_profile.php");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['profile_error'] = "Invalid email format.";
        header("Location: ../view/edit_profile.php");
        exit();
    }

    // Check if email is already taken by another user
    if ($email !== $_SESSION['email']) {
        $existing_user = getUserByEmail($conn, $email);
        if ($existing_user && $existing_user['id'] !== $_SESSION['user_id']) {
            $_SESSION['profile_error'] = "Email is already taken by another user.";
            header("Location: ../view/edit_profile.php");
            exit();
        }
    }

    // Update user data
    $update_sql = "UPDATE users SET 
                   first_name = ?, 
                   last_name = ?, 
                   email = ?, 
                   mobile = ?, 
                   address = ? 
                   WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $first_name, $last_name, $email, $mobile, $address, $_SESSION['user_id']);
    
    if (mysqli_stmt_execute($stmt)) {
        // Update session email if it was changed
        if ($email !== $_SESSION['email']) {
            $_SESSION['email'] = $email;
        }
        
        $_SESSION['profile_success'] = "Profile updated successfully.";
    } else {
        $_SESSION['profile_error'] = "Error updating profile. Please try again.";
    }

    mysqli_stmt_close($stmt);
    header("Location: ../view/customer_profile.php");
    exit();
} else {
    // If not POST request, redirect to profile page
    header("Location: ../view/customer_profile.php");
    exit();
} 