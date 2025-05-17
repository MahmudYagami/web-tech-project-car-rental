<?php
require_once '../model/db.php';

// Fetch total number of reports
$total_query = "SELECT COUNT(*) as total FROM reports";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_reports = $total_row['total'];

// Fetch all reports (initial load)
$query = "SELECT id, timestamp, canvas_image, signature_image, photo_images FROM reports ORDER BY timestamp DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Damage Reports Admin Panel</title>
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
    <div class="container">
        <h1>Damage Reports Admin Panel</h1>
        <p>Total Reports: <span id="totalReports"><?php echo $total_reports; ?></span></p>

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search by ID or Timestamp (YYYY-MM-DD)">
            <button onclick="searchReports()">Search</button>
        </div>

        <!-- Reports Table -->
        <table id="reportsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Timestamp</th>
                    <th>Canvas Image</th>
                    <th>Signature Image</th>
                    <th>Photos</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="reportsBody">
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
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
                            <a href="view_Admin_dmg_report.php?id=<?php echo $row['id']; ?>" class="btn view-btn">View</a>
                            <button onclick="deleteReport(<?php echo $row['id']; ?>)" class="btn delete-btn">Delete</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="..\assests\js\admin_dmg_report.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>