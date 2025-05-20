<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermodel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']) ? true : false;

    // Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Please fill in all fields";
        header("Location: ../view/login.php");
        exit();
    }

    // Get user from database
    $sql = "SELECT user_id, email, password, first_name, last_name, role FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // Verify password
        if ($user['password'] === $password) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['role'] = $user['role'];

            // If remember me is checked, set cookies
            if ($remember_me) {
                // Generate a simple token using user ID and timestamp
                $token = $user['user_id'] . '_' . time();
                
                // Store token in database
                $token_sql = "INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))";
                $token_stmt = mysqli_prepare($conn, $token_sql);
                mysqli_stmt_bind_param($token_stmt, "is", $user['user_id'], $token);
                mysqli_stmt_execute($token_stmt);
                mysqli_stmt_close($token_stmt);

                // Set cookies
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true); // 30 days
                setcookie('user_email', $email, time() + (30 * 24 * 60 * 60), '/', '', true, true);
            }

            // Redirect based on role
            if ($user['role'] === 'admin') {
                mysqli_close($conn);
                header("Location: ../view/admin_dashboard.php");
            } else {
                mysqli_close($conn);
                header("Location: ../view/user_dashboard.php");
            }
            exit();
        }
    }

    // If we get here, login failed
    $_SESSION['login_error'] = "Invalid email or password";
    mysqli_close($conn);
    header("Location: ../view/login.php");
    exit();
} else {
    $_SESSION['login_error'] = "Invalid request method.";
    mysqli_close($conn);
    header("Location: ../view/login.php");
    exit();
}
?>
