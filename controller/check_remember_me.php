<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../model/db.php';
require_once '../model/remember_model.php';

// Only proceed if user is not already logged in
if (!isset($_SESSION['user_id'])) {
    // Check if remember me cookies exist
    if (isset($_COOKIE['remember_token']) && isset($_COOKIE['user_email'])) {
        $token = $_COOKIE['remember_token'];
        $email = $_COOKIE['user_email'];

        // Verify token in database
        $user = verifyRememberToken($conn, $token);

        if ($user && $user['email'] === $email) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['role'] = $user['role'];

            // Refresh token
            $new_token = updateRememberToken($conn, $user['user_id'], $token);
            
            if ($new_token) {
                // Update cookies
                setcookie('remember_token', $new_token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
                setcookie('user_email', $email, time() + (30 * 24 * 60 * 60), '/', '', true, true);
            }
        }
    }
}
?> 