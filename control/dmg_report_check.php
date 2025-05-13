<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    // Validate photos
    if (!isset($_FILES['photos']) || count($_FILES['photos']['name']) == 0 || empty($_FILES['photos']['name'][0])) {
        $errors[] = "Please upload at least one photo.";
    } else {
        foreach ($_FILES['photos']['type'] as $type) {
            if (!in_array($type, ['image/jpeg', 'image/png', 'image/jpg'])) {
                $errors[] = "All photos must be JPG or PNG files.";
                break;
            }
        }
    }

    // Validate signature
    if (!isset($_FILES['signature']) || $_FILES['signature']['error'] != 0) {
        $errors[] = "Please upload a signature image.";
    } else {
        $sigType = $_FILES['signature']['type'];
        if (!in_array($sigType, ['image/jpeg', 'image/png', 'image/jpg'])) {
            $errors[] = "Signature must be JPG or PNG.";
        }
    }

    // Show errors or process
    if (empty($errors)) {
        echo "<p style='color: green;'>Report successfully submitted (processing logic not added).</p>";
        // You can now move_uploaded_file() to save images
    } else {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
}
?>
