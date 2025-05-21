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
    // Check if file was uploaded
    if (!isset($_FILES['license'])) {
        $_SESSION['license_error'] = "No file was uploaded.";
        header("Location: ../view/customer_profile.php");
        exit();
    }

    // Process license upload using model function
    $result = uploadLicense($conn, $_SESSION['user_id'], $_FILES['license']);
    
    if ($result['success']) {
        $_SESSION['license_success'] = $result['message'];
    } else {
        $_SESSION['license_error'] = $result['message'];
    }

    header("Location: ../view/customer_profile.php");
    exit();
} else {
    // If not POST request, redirect to profile page
    header("Location: ../view/customer_profile.php");
    exit();
} 