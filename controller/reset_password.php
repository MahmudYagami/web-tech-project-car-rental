<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermodel.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $current_password = $_POST['recent_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_new_password'];

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'New passwords do not match']);
        exit();
    }

    // Validate password requirements
    if (!validatePasswordRequirements($new_password)) {
        echo json_encode(['status' => 'error', 'message' => 'Password does not meet requirements']);
        exit();
    }

    // Verify current password
    $verify_result = verifyCurrentPassword($conn, $email, $current_password);
    if (!$verify_result['success']) {
        echo json_encode(['status' => 'error', 'message' => $verify_result['message']]);
        exit();
    }

    // Update password
    $update_result = updateUserPassword($conn, $email, $new_password);
    echo json_encode([
        'status' => $update_result['success'] ? 'success' : 'error',
        'message' => $update_result['message']
    ]);
}

closeConnection($conn);
?> 