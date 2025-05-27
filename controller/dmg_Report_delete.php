<?php
session_start();
require_once '../model/db.php';
require_once '../model/dmgreport_model.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Unauthorized access.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    if ($id <= 0) {
        echo "Invalid report ID.";
        exit();
    }
    
    // Use model function to delete the report
    $result = deleteDamageReport($conn, $id);
    
    if ($result['success']) {
        echo "Report deleted successfully";
    } else {
        echo "Error: " . $result['message'];
    }
    
    mysqli_close($conn);
} else {
    echo "Invalid request method or missing report ID.";
}
?>