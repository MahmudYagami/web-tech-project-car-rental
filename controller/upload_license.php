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
    // Check if file was uploaded without errors
    if (!isset($_FILES['license']) || $_FILES['license']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['license_error'] = "Error uploading file. Please try again.";
        header("Location: ../view/customer_profile.php");
        exit();
    }

    $file = $_FILES['license'];
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['license_error'] = "Invalid file type. Please upload a JPG or PNG image.";
        header("Location: ../view/customer_profile.php");
        exit();
    }

    // Validate file size (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $max_size) {
        $_SESSION['license_error'] = "File is too large. Maximum size is 5MB.";
        header("Location: ../view/customer_profile.php");
        exit();
    }

    // Create upload directory if it doesn't exist
    $upload_dir = '../uploads/licenses/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = 'license_' . $_SESSION['user_id'] . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;

    // Move uploaded file to destination
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // Update user record with license image path
        $relative_path = 'uploads/licenses/' . $new_filename;
        $update_sql = "UPDATE users SET license_image = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $relative_path, $_SESSION['user_id']);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['license_success'] = "Driver's license uploaded successfully.";
            } else {
                $_SESSION['license_error'] = "Error updating database. Please try again.";
                // Delete uploaded file if database update fails
                unlink($upload_path);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['license_error'] = "Error preparing database statement. Please try again.";
            // Delete uploaded file if database preparation fails
            unlink($upload_path);
        }
    } else {
        $_SESSION['license_error'] = "Error saving file. Please try again.";
    }

    header("Location: ../view/customer_profile.php");
    exit();
} else {
    // If not POST request, redirect to profile page
    header("Location: ../view/customer_profile.php");
    exit();
} 