<?php
require_once 'db.php';

function saveDamageReport($conn, $user_id, $canvas_name, $sign_name, $photos_json) {
    $time = date('Y-m-d H:i:s');
    
    $query = "INSERT INTO reports (user_id, timestamp, canvas_image, signature_image, photo_images) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issss", $user_id, $time, $canvas_name, $sign_name, $photos_json);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return ['success' => true, 'message' => 'Report saved successfully'];
        } else {
            $error = mysqli_error($conn);
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Error: ' . $error];
        }
    } else {
        return ['success' => false, 'message' => 'Error preparing statement: ' . mysqli_error($conn)];
    }
}

function getDamageReportById($conn, $id) {
    $query = "SELECT canvas_image, signature_image, photo_images FROM reports WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($stmt);
        return ['success' => true, 'data' => $row];
    } else {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Report not found'];
    }
}

function deleteDamageReport($conn, $id) {
    // First get the report details
    $report = getDamageReportById($conn, $id);
    if (!$report['success']) {
        return $report;
    }
    
    $row = $report['data'];
    
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
        mysqli_stmt_close($delete_stmt);
        return ['success' => true, 'message' => 'Report deleted successfully'];
    } else {
        $error = mysqli_error($conn);
        mysqli_stmt_close($delete_stmt);
        return ['success' => false, 'message' => 'Error deleting report: ' . $error];
    }
}

function searchDamageReports($conn, $search_term) {
    $search_term = mysqli_real_escape_string($conn, $search_term);
    
    $query = "SELECT r.id, r.timestamp, r.canvas_image, r.signature_image, r.photo_images, u.email 
              FROM reports r 
              JOIN users u ON r.user_id = u.user_id 
              WHERE r.id LIKE '%$search_term%' 
              OR DATE(r.timestamp) LIKE '%$search_term%'
              OR u.email LIKE '%$search_term%'
              ORDER BY r.timestamp DESC";
              
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return ['success' => false, 'message' => 'Failed to search reports', 'data' => []];
    }
    
    $reports = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['photo_images'] = json_decode($row['photo_images'], true);
        $reports[] = $row;
    }
    
    return ['success' => true, 'data' => $reports];
}

function formatReportRow($report) {
    $html = '<tr>';
    $html .= '<td>' . htmlspecialchars($report['id']) . '</td>';
    $html .= '<td class="email-cell" title="' . htmlspecialchars($report['email']) . '">' . htmlspecialchars($report['email']) . '</td>';
    $html .= '<td>' . htmlspecialchars($report['timestamp']) . '</td>';
    $html .= '<td><img src="../' . htmlspecialchars($report['canvas_image']) . '" alt="Canvas" class="thumbnail"></td>';
    $html .= '<td><img src="../' . htmlspecialchars($report['signature_image']) . '" alt="Signature" class="thumbnail"></td>';
    $html .= '<td>';
    
    $photos = json_decode($report['photo_images'], true);
    if ($photos) {
        foreach ($photos as $photo) {
            $html .= '<img src="../' . htmlspecialchars($photo) . '" alt="Photo" class="thumbnail">';
        }
    } else {
        $html .= 'No photos';
    }
    
    $html .= '</td>';
    $html .= '<td>';
    $html .= '<a href="view_Admin_dmg_report.php?id=' . $report['id'] . '" class="btn view-btn">View</a>';
    $html .= '<button onclick="deleteReport(' . $report['id'] . ')" class="btn delete-btn">Delete</button>';
    $html .= '</td>';
    $html .= '</tr>';
    
    return $html;
}

function handleFileUpload($file_data, $folder_path) {
    if (!file_exists($folder_path)) {
        mkdir($folder_path, 0777, true);
    }
    
    $file_name = $folder_path . time() . '_' . basename($file_data['name']);
    if (move_uploaded_file($file_data['tmp_name'], $file_name)) {
        return $file_name;
    }
    return false;
}

function handleBase64Image($base64_data, $folder_path) {
    if (!file_exists($folder_path)) {
        mkdir($folder_path, 0777, true);
    }
    
    $image_data = str_replace('data:image/png;base64,', '', $base64_data);
    $image_data = str_replace(' ', '+', $image_data);
    $file_name = $folder_path . time() . '.png';
    
    if (file_put_contents($file_name, base64_decode($image_data))) {
        return $file_name;
    }
    return false;
}

function getTotalReports($conn) {
    $total_query = "SELECT COUNT(*) as total FROM reports";
    $total_result = mysqli_query($conn, $total_query);
    
    if (!$total_result) {
        return ['success' => false, 'message' => 'Failed to fetch total reports', 'data' => 0];
    }
    
    $total_row = mysqli_fetch_assoc($total_result);
    return ['success' => true, 'data' => $total_row['total']];
}

function getAllDamageReports($conn) {
    $query = "SELECT r.id, r.timestamp, r.canvas_image, r.signature_image, r.photo_images, u.email 
              FROM reports r 
              JOIN users u ON r.user_id = u.user_id 
              ORDER BY r.timestamp DESC";
              
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return ['success' => false, 'message' => 'Failed to fetch reports', 'data' => []];
    }
    
    $reports = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['photo_images'] = json_decode($row['photo_images'], true);
        $reports[] = $row;
    }
    
    return ['success' => true, 'data' => $reports];
}
?>
