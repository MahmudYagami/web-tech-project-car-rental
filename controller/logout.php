<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear remember me cookies if they exist
if (isset($_COOKIE['remember_token']) || isset($_COOKIE['user_email'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    setcookie('user_email', '', time() - 3600, '/', '', true, true);
}

// Delete the remember token from database if user was logged in
if (isset($_SESSION['user_id'])) {
    require_once '../model/db.php';
    $sql = "DELETE FROM remember_tokens WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: ../view/login.php");
exit();
?>