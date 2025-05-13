<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Hardcoded valid credentials
    $valid_email = "admin@example.com";
    $valid_password = "Admin@123"; // Plain text, for demo only

    // Get submitted credentials
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Basic empty check
    if (empty($email) || empty($password)) {
        echo "Please enter both email and password.";
        exit;
    }

    // Validate credentials
    if ($email === $valid_email && $password === $valid_password) {
        $_SESSION["email"] = $email;

        // Redirect to dashboard
        header("Location: view/dashboard/user_dashboard.php");
        exit;
    } else {
        echo "Invalid email or password.";
        exit;
    }
} else {
    echo "Invalid request method.";
    exit;
}
?>
