<?php
require_once '../model/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Fetch report to get file paths
    $query = "SELECT canvas_image, signature_image, photo_images FROM reports WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // Delete associated files
        if (file_exists('../' . $row['canvas_image'])) {
            unlink('../' . $row['canvas_image']);
        }
        if (file_exists('../' . $row['signature_image'])) {
            unlink('../' . $row['signature_image']);
        }
        $photos = json_decode($row['photo_images'], true);
        if ($photos) {
            foreach ($photos as $photo) {
                if (file_exists('../' . $photo)) {
                    unlink('../' . $photo);
                }
            }
        }
        
        // Delete report from database
        $delete_query = "DELETE FROM reports WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, 'i', $id);
        
        if (mysqli_stmt_execute($delete_stmt)) {
            echo "Report deleted successfully!";
        } else {
            echo "Error deleting report: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($delete_stmt);
    } else {
        echo "Report not found.";
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Invalid request.";
}

mysqli_close($conn);
?>