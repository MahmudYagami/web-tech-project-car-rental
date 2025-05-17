<?php
require_once '../model/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Directory setup
    $canvasDir = 'uploads/canvas/';
    $signatureDir = 'uploads/signatures/';
    $photoDir = 'uploads/photos/';
    
    if (!file_exists($canvasDir)) mkdir($canvasDir, 0777, true);
    if (!file_exists($signatureDir)) mkdir($signatureDir, 0777, true);
    if (!file_exists($photoDir)) mkdir($photoDir, 0777, true);

    // Save Canvas Image (includes uploaded image + annotations)
    $canvasImage = $_POST['canvas_image'];
    $canvasImage = str_replace('data:image/png;base64,', '', $canvasImage);
    $canvasImage = str_replace(' ', '+', $canvasImage);
    $canvasFileName = $canvasDir . uniqid() . '.png';
    file_put_contents($canvasFileName, base64_decode($canvasImage));

    // Save Signature Image
    $signatureImage = $_POST['signature_image'];
    $signatureImage = str_replace('data:image/png;base64,', '', $signatureImage);
    $signatureImage = str_replace(' ', '+', $signatureImage);
    $signatureFileName = $signatureDir . uniqid() . '.png';
    file_put_contents($signatureFileName, base64_decode($signatureImage));

    // Save Photos
    $photoPaths = [];
    if (!empty($_FILES['photos']['name'][0])) {
        foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {
            if ($_FILES['photos']['error'][$index] === UPLOAD_ERR_OK) {
                $photoFileName = $photoDir . uniqid() . '_' . $_FILES['photos']['name'][$index];
                move_uploaded_file($tmpName, $photoFileName);
                $photoPaths[] = $photoFileName;
            }
        }
    }
    $photoPathsJson = json_encode($photoPaths);

    // Insert into Database
    $timestamp = date('Y-m-d H:i:s');
    $query = "INSERT INTO reports (timestamp, canvas_image, signature_image, photo_images) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssss', $timestamp, $canvasFileName, $signatureFileName, $photoPathsJson);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Report saved successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo "Invalid request.";
}
?>