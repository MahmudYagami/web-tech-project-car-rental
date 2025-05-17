<?php
session_start();
require_once '../model/db.php'; // Make sure this defines $conn

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        echo "Please enter both email and password.";
        exit;
    }

    // Prepare SQL query
    $sql = "SELECT user_id, email, password, first_name, last_name, role FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        echo "Database error: " . mysqli_error($conn);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Use password_verify if password is hashed
        if ($password === $user['password']) { // change this to password_verify() if needed
            $_SESSION["user_id"] = $user['user_id'];
            $_SESSION["email"] = $user['email'];
            $_SESSION["first_name"] = $user['first_name'];
            $_SESSION["last_name"] = $user['last_name'];
            $_SESSION["role"] = $user['role'];

            // 🔥 Correct role check
            if ($user['role'] === 'admin') {
                header("Location: ../view/admin_dashboard.php");
            } else {
                header("Location: ../view/user_dashboard.php");
            }
            exit;

        } else {
            echo "Invalid email or password.";
        }
    } else {
        echo "Invalid email or password.";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Invalid request method.";
}

mysqli_close($conn);
?>
