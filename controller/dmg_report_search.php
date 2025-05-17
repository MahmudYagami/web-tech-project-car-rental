<?php
require_once '../model/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search = mysqli_real_escape_string($conn, $_POST['search']);
    
    // Search by ID or timestamp
    $query = "SELECT id, timestamp, canvas_image, signature_image, photo_images FROM reports 
              WHERE id = ? OR timestamp LIKE ? 
              ORDER BY timestamp DESC";
    $stmt = mysqli_prepare($conn, $query);
    $search_like = "%$search%";
    mysqli_stmt_bind_param($stmt, 'is', $search, $search_like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['timestamp']; ?></td>
            <td><img src="../<?php echo $row['canvas_image']; ?>" alt="Canvas" class="thumbnail"></td>
            <td><img src="../<?php echo $row['signature_image']; ?>" alt="Signature" class="thumbnail"></td>
            <td>
                <?php
                $photos = json_decode($row['photo_images'], true);
                if ($photos) {
                    foreach ($photos as $photo) {
                        echo '<img src="../' . $photo . '" alt="Photo" class="thumbnail">';
                    }
                } else {
                    echo 'No photos';
                }
                ?>
            </td>
            <td>
                <a href="view_report.php?id=<?php echo $row['id']; ?>" class="btn view-btn">View</a>
                <button onclick="deleteReport(<?php echo $row['id']; ?>)" class="btn delete-btn">Delete</button>
            </td>
        </tr>
        <?php
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo '<tr><td colspan="6">Invalid search request.</td></tr>';
}

mysqli_close($conn);
?>