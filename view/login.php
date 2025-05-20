<?php
session_start();
require_once '../controller/check_remember_me.php';

// If user is already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="..\assets\css\login_style.css">
</head>
<body>
    
    <div class="wrapper">
        <form id="login-form" action="..\controller\login_check.php" method="POST">
            <h1>Login</h1>

            <?php if(isset($_SESSION['login_error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                        echo $_SESSION['login_error'];
                        unset($_SESSION['login_error']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['register_success'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo $_SESSION['register_success'];
                        unset($_SESSION['register_success']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="Email" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <div class="lower">
                <div class="remember-forgot">
                    <a href="forgot_password.php">Forgot Password?</a>
                </div>
                <div class="toggle-row">
                    <label class="switch">
                        <input type="checkbox" name="remember_me" id="remember_me">
                        <span class="slider"></span>
                    </label>
                    <span class="remember-text">Remember me</span>
                </div>
            </div>
            <button type="submit" id="submit-btn" class="btn">Login</button> 
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </form>
    </div>
<script src="..\assests\js\login_valid.js"></script>
    
</body>
</html>