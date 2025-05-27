<?php
session_start();
require_once '../model/db.php';
require_once '../model/dmgreport_model.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate required fields
    if (!isset($_POST['canvas_image']) || !isset($_POST['signature_image'])) {
        $_SESSION['error'] = "Missing required fields";
        header("Location: ../view/damage_report.php");
        exit();
    }

    // Set up folders
    $canvas_folder = '../assets/uploads/canvas/';
    $sign_folder = '../assets/uploads/signatures/';
    $photo_folder = '../assets/uploads/photos/';
    
    // Create folders if they don't exist
    if (!file_exists($canvas_folder)) mkdir($canvas_folder, 0777, true);
    if (!file_exists($sign_folder)) mkdir($sign_folder, 0777, true);
    if (!file_exists($photo_folder)) mkdir($photo_folder, 0777, true);

    // Handle canvas image
    $canvas_name = handleBase64Image($_POST['canvas_image'], $canvas_folder);
    if (!$canvas_name) {
        $_SESSION['error'] = "Error saving canvas image";
        header("Location: ../view/damage_report.php");
        exit();
    }

    // Handle signature image
    $sign_name = handleBase64Image($_POST['signature_image'], $sign_folder);
    if (!$sign_name) {
        $_SESSION['error'] = "Error saving signature image";
        header("Location: ../view/damage_report.php");
        exit();
    }

    // Handle photos
    $photo_list = [];
    if (!empty($_FILES['photos']['name'][0])) {
        foreach ($_FILES['photos']['tmp_name'] as $key => $temp_name) {
            if ($_FILES['photos']['error'][$key] == 0) {
                $file_data = [
                    'name' => $_FILES['photos']['name'][$key],
                    'tmp_name' => $temp_name,
                    'error' => $_FILES['photos']['error'][$key]
                ];
                $photo_name = handleFileUpload($file_data, $photo_folder);
                if ($photo_name) {
                    $photo_list[] = $photo_name;
                }
            }
        }
    }
    $photos_json = json_encode($photo_list);

    // Get user ID from session
    $user_id = $_SESSION['user_id'];

    // Save to database using model function
    $result = saveDamageReport($conn, $user_id, $canvas_name, $sign_name, $photos_json);
    
    if ($result['success']) {
        $_SESSION['success'] = "Damage report submitted successfully";
        header("Location: ../view/damage_report.php");
    } else {
        $_SESSION['error'] = $result['message'];
        header("Location: ../view/damage_report.php");
    }
    exit();
} else {
    header("Location: ../view/damage_report.php");
    exit();
}
?>