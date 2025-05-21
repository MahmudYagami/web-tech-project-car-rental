<?php
require_once '../model/db.php';
require_once '../model/dmgreport_model.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Set up folders
    $canvas_folder = '../assets/uploads/canvas/';
    $sign_folder = '../assets/uploads/signatures/';
    $photo_folder = '../assets/uploads/photos/';
    
    // Create folders if they don't exist
    if (!file_exists($canvas_folder)) mkdir($canvas_folder);
    if (!file_exists($sign_folder)) mkdir($sign_folder);
    if (!file_exists($photo_folder)) mkdir($photo_folder);

    // Handle canvas image
    $canvas_name = handleBase64Image($_POST['canvas_image'], $canvas_folder);
    if (!$canvas_name) {
        echo "Error saving canvas image";
        exit();
    }

    // Handle signature image
    $sign_name = handleBase64Image($_POST['signature_image'], $sign_folder);
    if (!$sign_name) {
        echo "Error saving signature image";
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

    // Save to database using model function
    $result = saveDamageReport($conn, $canvas_name, $sign_name, $photos_json);
    
    if ($result['success']) {
        header("Location: ../view/damage_report.php");
    } else {
        echo $result['message'];
    }

    mysqli_close($conn);
} else {
    echo "Wrong request!";
}
?>