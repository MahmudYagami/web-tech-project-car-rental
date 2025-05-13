<?php
// Only run validation when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $current_password = $_POST["password"] ?? "";
    $new_password = $_POST["new_password"] ?? "";

    $errors = [];

    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate current password
    if (empty($current_password)) {
        $errors[] = "Current password is required.";
    }

    // Validate new password
    if (empty($new_password)) {
        $errors[] = "New password is required.";
    } else {
        if (strlen($new_password) < 8) {
            $errors[] = "New password must be at least 8 characters long.";
        }
        if (!preg_match("/[A-Z]/", $new_password)) {
            $errors[] = "New password must include at least one uppercase letter.";
        }
        if (!preg_match("/[a-z]/", $new_password)) {
            $errors[] = "New password must include at least one lowercase letter.";
        }
        if (!preg_match("/[0-9]/", $new_password)) {
            $errors[] = "New password must include at least one number.";
        }
        if (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $new_password)) {
            $errors[] = "New password must include at least one special character.";
        }
        if ($new_password === $current_password) {
            $errors[] = "New password must be different from the current password.";
        }
    }

    // Show errors or proceed
    if (empty($errors)) {
        echo "<p style='color: green;'>Validation passed. (Now you would reset the password in DB)</p>";
        // Here, you would normally check current password from DB and update new one
    } else {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
}
?>
