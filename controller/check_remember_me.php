<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../model/db.php';

// Only proceed if user is not already logged in
if (!isset($_SESSION['user_id'])) {
    // Check if remember me cookies exist
    if (isset($_COOKIE['remember_token']) && isset($_COOKIE['user_email'])) {
        $token = $_COOKIE['remember_token'];
        $email = $_COOKIE['user_email'];

        // Verify token in database
        $sql = "SELECT u.*, rt.token 
                FROM users u 
                JOIN remember_tokens rt ON u.user_id = rt.user_id 
                WHERE rt.token = ? AND rt.expires_at > NOW()";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            // Verify email matches
            if ($user['email'] === $email) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['role'] = $user['role'];

                // Refresh token
                $new_token = $user['user_id'] . '_' . time();
                $update_sql = "UPDATE remember_tokens SET token = ?, expires_at = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE token = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "ss", $new_token, $token);
                mysqli_stmt_execute($update_stmt);
                mysqli_stmt_close($update_stmt);

                // Update cookies
                setcookie('remember_token', $new_token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
                setcookie('user_email', $email, time() + (30 * 24 * 60 * 60), '/', '', true, true);
            }
        }
        mysqli_stmt_close($stmt);
    }
}
?> 