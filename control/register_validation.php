<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $errors = [];

    // Get all values
    $firstName = trim($_POST["first_name"] ?? '');
    $lastName = trim($_POST["last_name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $mobile = trim($_POST["mobile"] ?? '');
    $password = $_POST["password"] ?? '';
    $repassword = $_POST["retype_password"] ?? '';
    $country = $_POST["country"] ?? '';
    $address = trim($_POST["address"] ?? '');
    $dob = $_POST["dob"] ?? '';

    // Basic empty check
    if (empty($firstName)) $errors[] = "First name is required.";
    if (empty($lastName)) $errors[] = "Last name is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($mobile)) $errors[] = "Mobile is required.";
    if (empty($password)) $errors[] = "Password is required.";
    if (empty($repassword)) $errors[] = "Retype Password is required.";
    if (empty($country)) $errors[] = "Country is required.";
    if (empty($address)) $errors[] = "Address is required.";
    if (empty($dob)) $errors[] = "Date of birth is required.";

    // Email format check (basic)
    if (!str_contains($email, '@') || !str_contains($email, '.')) {
        $errors[] = "Invalid email format.";
    }

    // Password complexity check
    $hasUpper = $hasLower = $hasDigit = $hasSpecial = false;
    $specialChars = "!@#$%^&*()_+-=[]{};:'\",.<>/?";

    for ($i = 0; $i < strlen($password); $i++) {
        $ch = $password[$i];
        if (ctype_upper($ch)) $hasUpper = true;
        elseif (ctype_lower($ch)) $hasLower = true;
        elseif (ctype_digit($ch)) $hasDigit = true;
        elseif (strpos($specialChars, $ch) !== false) $hasSpecial = true;
    }

    if (strlen($password) < 8 || !$hasUpper || !$hasLower || !$hasDigit || !$hasSpecial) {
        $errors[] = "Password must be at least 8 characters long and include uppercase, lowercase, digit, and special character.";
    }

    // Confirm password
    if ($password !== $repassword) {
        $errors[] = "Passwords do not match.";
    }

    // Mobile number check
    if (strlen($mobile) !== 11 || !ctype_digit($mobile)) {
        $errors[] = "Mobile number must be exactly 11 digits and numeric.";
    }

    // Final output
    if (!empty($errors)) {
        echo "<h2>Validation Errors:</h2><ul>";
        foreach ($errors as $err) {
            echo "<li>" . htmlspecialchars($err) . "</li>";
        }
        echo "</ul><a href='register.php'>Go Back</a>";
    } else {
        echo "<h2>Registration Successful (Validated in PHP)!</h2>";
        echo "<a href='login.php'>Go to Login</a>";
    }
} else {
    // If not POST
    header("Location: register.php");
    exit();
}
?>
