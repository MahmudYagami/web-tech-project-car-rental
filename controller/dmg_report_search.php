<?php
require_once '../model/db.php';

if (isset($_POST['search'])) {
    $search = mysqli_real_escape_string($conn, $_POST['search']);
    
    // Search in reports table and join with users table to search by email
    $query = "SELECT r.id, r.timestamp, r.canvas_image, r.signature_image, r.photo_images, u.email 
              FROM reports r 
              LEFT JOIN users u ON r.user_id = u.user_id 
              WHERE r.id LIKE '%$search%' 
              OR r.timestamp LIKE '%$search%' 
              OR u.email LIKE '%$search%'
              ORDER BY r.timestamp DESC";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['id']) . '</td>';
            echo '<td class="email-cell" title="' . htmlspecialchars($row['email']) . '">' . htmlspecialchars($row['email']) . '</td>';
            echo '<td>' . htmlspecialchars($row['timestamp']) . '</td>';
            echo '<td><img src="../' . htmlspecialchars($row['canvas_image']) . '" alt="Canvas" class="thumbnail"></td>';
            echo '<td><img src="../' . htmlspecialchars($row['signature_image']) . '" alt="Signature" class="thumbnail"></td>';
            echo '<td>';
            
            $photos = json_decode($row['photo_images'], true);
            if ($photos) {
                foreach ($photos as $photo) {
                    echo '<img src="../' . htmlspecialchars($photo) . '" alt="Photo" class="thumbnail">';
                }
            } else {
                echo 'No photos';
            }
            
            echo '</td>';
            echo '<td>';
            echo '<a href="view_Admin_dmg_report.php?id=' . $row['id'] . '" class="btn view-btn">View</a>';
            echo '<button onclick="deleteReport(' . $row['id'] . ')" class="btn delete-btn">Delete</button>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="7">Error searching reports</td></tr>';
    }
} else {
    echo '<tr><td colspan="7">No search term provided</td></tr>';
}

mysqli_close($conn);
?>