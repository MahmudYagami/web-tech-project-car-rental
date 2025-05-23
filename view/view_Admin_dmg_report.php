<?php
require_once '../model/db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Static base paths
define('CANVAS_BASE_PATH', 'controller/assests/uploads/canvas/');
define('SIGNATURE_BASE_PATH', 'controller/assests/uploads/signatures/');
define('PHOTO_BASE_PATH', 'controller/assests/uploads/photos/');

if (!isset($_GET['id'])) {
    die("Invalid report ID.");
}

$id = intval($_GET['id']);
$query = "SELECT id, timestamp, canvas_image, signature_image, photo_images FROM reports WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View Report #<?php echo $row['id']; ?></title>
        <link rel="stylesheet" href="../css/admin_style.css">


        <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    background-color: #f4f4f4;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #333;
}

p {
    text-align: center;
    font-size: 18px;
    margin-bottom: 20px;
}

.search-bar {
    margin-bottom: 20px;
    text-align: center;
}

.search-bar input {
    padding: 8px;
    width: 200px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.search-bar button {
    padding: 8px 16px;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.search-bar button:hover {
    background: #0056b3;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background: #007bff;
    color: #fff;
}

.thumbnail {
    max-width: 50px;
    height: auto;
    margin-right: 5px;
}

.btn {
    padding: 6px 12px;
    text-decoration: none;
    color: #fff;
    border-radius: 4px;
    margin-right: 5px;
}

.view-btn {
    background: #28a745;
}

.view-btn:hover {
    background: #218838;
}

.delete-btn {
    background: #dc3545;
    border: none;
    cursor: pointer;
}

.delete-btn:hover {
    background: #c82333;
}

.view-container {
    text-align: center;
}

.view-container img {
    max-width: 300px;
    height: auto;
    margin: 10px;
}

.view-container p {
    font-size: 16px;
    margin: 10px 0;
}
    </style>
    </head>
    <body>
        <div class="container view-container">
            <h1>Damage Report #<?php echo $row['id']; ?></h1>
            <p><strong>Timestamp:</strong> <?php echo $row['timestamp']; ?></p>

            <!-- Canvas Image -->
            <div class="image-container">
                <h3>Canvas Image</h3>
                <img src="<?php echo $row['canvas_image']; ?>" alt="Canvas Image" class="report-image">
            </div>

            <!-- Signature Image -->
            <div class="image-container">
                <h3>Signature</h3>
                <img src="<?php echo $row['signature_image']; ?>" alt="Signature" class="report-image">
            </div>

            <!-- Photos -->
            <div class="image-container">
                <h3>Photos</h3>
                <?php
                $photos = json_decode($row['photo_images'], true);
                if ($photos) {
                    foreach ($photos as $photo) {
                        echo '<img src="' . $photo . '" alt="Photo" class="report-image">';
                    }
                } else {
                    echo '<p>No photos uploaded</p>';
                }
                ?>
            </div>

            <!-- Back Button -->
            <p><a href="admin_damage_report.php" class="btn view-btn">Back to Admin Panel</a></p>
        </div>
    </body>
    </html>
    <?php
} else {
    echo "Report not found.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>