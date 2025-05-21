<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    // Check login credentials
    $result = checkLogin($conn, $email, $password);

    if ($result['success']) {
        $user = $result['data'];
        
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['role'] = $user['role'];

        // If remember me is checked, set cookies
        if ($remember_me) {
            $token = $user['user_id'] . '_' . time();
            $token_result = saveRememberToken($conn, $user['user_id'], $token);
            
            if ($token_result['success']) {
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
                setcookie('user_email', $email, time() + (30 * 24 * 60 * 60), '/');
            }
        }

        // Close database connection
        mysqli_close($conn);

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: ../view/admin_dashboard.php");
        } else {
            header("Location: ../view/user_dashboard.php");
        }
        exit();
    } else {
        $_SESSION['login_error'] = $result['message'];
        mysqli_close($conn);
        header("Location: ../view/login.php");
        exit();
    }
} else {
    $_SESSION['login_error'] = "Invalid request method.";
    header("Location: ../view/login.php");
    exit();
}
?>
