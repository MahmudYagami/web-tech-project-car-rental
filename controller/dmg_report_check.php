<?php
include '../model/db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Set up folders
    $canvas_folder = '../assests/uploads/canvas/';
    $sign_folder = '../assests/uploads/signatures/';
    $photo_folder = '../assests/uploads/photos/';
    
    // Create folders if they don't exist
    if (!file_exists($canvas_folder)) mkdir($canvas_folder);
    if (!file_exists($sign_folder)) mkdir($sign_folder);
    if (!file_exists($photo_folder)) mkdir($photo_folder);

    // Save canvas image
    $canvas_data = $_POST['canvas_image'];
    $canvas_data = str_replace('data:image/png;base64,', '', $canvas_data);
    $canvas_data = str_replace(' ', '+', $canvas_data);
    $canvas_name = $canvas_folder . time() . '.png';
    file_put_contents($canvas_name, base64_decode($canvas_data));

    // Save signature image
    $sign_data = $_POST['signature_image'];
    $sign_data = str_replace('data:image/png;base64,', '', $sign_data);
    $sign_data = str_replace(' ', '+', $sign_data);
    $sign_name = $sign_folder . time() . '.png';
    file_put_contents($sign_name, base64_decode($sign_data));

    // Save photos
    $photo_list = [];
    if (!empty($_FILES['photos']['name'][0])) {
        foreach ($_FILES['photos']['tmp_name'] as $key => $temp_name) {
            if ($_FILES['photos']['error'][$key] == 0) {
                $photo_name = $photo_folder . time() . '_' . $_FILES['photos']['name'][$key];
                move_uploaded_file($temp_name, $photo_name);
                $photo_list[] = $photo_name;
            }
        }
    }
    $photos_json = json_encode($photo_list);

    // Save to database
    $time = date('Y-m-d H:i:s');
    $query = "INSERT INTO reports (timestamp, canvas_image, signature_image, photo_images) 
              VALUES ('$time', '$canvas_name', '$sign_name', '$photos_json')";
    
    if (mysqli_query($conn, $query)) {
        echo "Report saved";
        header("Location: ../view/damage_report.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    echo "Wrong request!";
}
?>