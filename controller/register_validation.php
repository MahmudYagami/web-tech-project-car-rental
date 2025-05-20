<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermodel.php';

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

    // Check if email already exists
    $existingUser = getUserByEmail($conn, $email);
    if ($existingUser) {
        $_SESSION['register_error'] = "An account with this email already exists. Please use a different email or login.";
        mysqli_close($conn);
        header("Location: ../view/register.php");
        exit();
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
        $_SESSION['register_errors'] = $errors;
        mysqli_close($conn);
        header("Location: ../view/register.php");
        exit();
    } else {
        // If no errors, proceed with registration
        $result = createUser($conn, $email, $password, $firstName, $lastName, 'user');
        
        if ($result) {
            // Insert additional user details into user_details table
            $sql = "INSERT INTO user_details (user_id, mobile, country, address, date_of_birth) 
                    SELECT user_id, ?, ?, ?, ? FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssss", $mobile, $country, $address, $dob, $email);
                $detailsResult = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                
                if ($detailsResult) {
                    $_SESSION['register_success'] = "Registration successful! You can now login with your credentials.";
                    mysqli_close($conn);
                    header("Location: ../view/login.php");
                    exit();
                } else {
                    $_SESSION['register_error'] = "There was an error creating your account. Please try again.";
                    mysqli_close($conn);
                    header("Location: ../view/register.php");
                    exit();
                }
            } else {
                $_SESSION['register_error'] = "There was an error creating your account. Please try again.";
                mysqli_close($conn);
                header("Location: ../view/register.php");
                exit();
            }
        } else {
            $_SESSION['register_error'] = "There was an error creating your account. Please try again.";
            mysqli_close($conn);
            header("Location: ../view/register.php");
            exit();
        }
    }
} else {
    // If not POST
    mysqli_close($conn);
    header("Location: ../view/register.php");
    exit();
}
?>
