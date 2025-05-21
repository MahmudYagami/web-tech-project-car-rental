<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermodel.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// Get user details
$user = getUserByEmail($conn, $_SESSION['email']);
if (!$user) {
    $_SESSION['error'] = "Failed to load user details.";
    header("Location: ../view/customer_profile.php");
    exit();
}

// Get user preferences
$preferences = getUserPreferencesById($conn, $_SESSION['user_id']);

// Store data in session for the view
$_SESSION['edit_profile_data'] = [
    'user' => $user,
    'preferences' => $preferences
];

// Redirect to the view
header("Location: ../view/edit_profile.php");
exit();
?> 