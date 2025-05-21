<?php
require_once '../model/db.php';
require_once '../model/dmgreport_model.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Use model function to delete the report
    $result = deleteDamageReport($conn, $id);
    echo $result['message'];
    
    mysqli_close($conn);
} else {
    echo "Invalid request.";
}
?>