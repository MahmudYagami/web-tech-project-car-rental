<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Get data from session
$total_reports = $_SESSION['total_reports'] ?? 0;
$reports = $_SESSION['damage_reports'] ?? [];
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
        <p>Total Reports: <span id="totalReports"><?php echo htmlspecialchars($total_reports); ?></span></p>

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
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report['id']); ?></td>
                        <td><?php echo htmlspecialchars($report['timestamp']); ?></td>
                        <td>
                            <img src="../<?php echo htmlspecialchars($report['canvas_image']); ?>" 
                                 alt="Canvas" class="thumbnail">
                        </td>
                        <td>
                            <img src="../<?php echo htmlspecialchars($report['signature_image']); ?>" 
                                 alt="Signature" class="thumbnail">
                        </td>
                        <td>
                            <?php if ($report['photo_images']): ?>
                                <?php foreach ($report['photo_images'] as $photo): ?>
                                    <img src="../<?php echo htmlspecialchars($photo); ?>" 
                                         alt="Photo" class="thumbnail">
                                <?php endforeach; ?>
                            <?php else: ?>
                                No photos
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="view_Admin_dmg_report.php?id=<?php echo $report['id']; ?>" 
                               class="btn view-btn">View</a>
                            <button onclick="deleteReport(<?php echo $report['id']; ?>)" 
                                    class="btn delete-btn">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function searchReports() {
            const searchTerm = document.getElementById('searchInput').value;
            
            fetch(`../controller/admin_damage_report_controller.php?search=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateReportsTable(data.data);
                    } else {
                        alert('Error searching reports: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while searching reports');
                });
        }

        function updateReportsTable(reports) {
            const tbody = document.getElementById('reportsBody');
            tbody.innerHTML = '';

            reports.forEach(report => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${report.id}</td>
                    <td>${report.timestamp}</td>
                    <td>
                        <img src="../${report.canvas_image}" alt="Canvas" class="thumbnail">
                    </td>
                    <td>
                        <img src="../${report.signature_image}" alt="Signature" class="thumbnail">
                    </td>
                    <td>
                        ${report.photo_images ? 
                            report.photo_images.map(photo => 
                                `<img src="../${photo}" alt="Photo" class="thumbnail">`
                            ).join('') : 
                            'No photos'}
                    </td>
                    <td>
                        <a href="view_Admin_dmg_report.php?id=${report.id}" class="btn view-btn">View</a>
                        <button onclick="deleteReport(${report.id})" class="btn delete-btn">Delete</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function deleteReport(reportId) {
            if (confirm('Are you sure you want to delete this report?')) {
                fetch('../controller/admin_damage_report_controller.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&report_id=${reportId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting report: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the report');
                });
            }
        }
    </script>
</body>
</html>