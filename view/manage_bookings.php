<?php
session_start();
require_once '../model/db.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if payment_status column exists
$check_column = "SHOW COLUMNS FROM bookings LIKE 'payment_status'";
$column_result = mysqli_query($conn, $check_column);
$has_payment_status = mysqli_num_rows($column_result) > 0;

// Get all bookings with user and car details
$query = "SELECT b.*, 
          u.first_name, u.last_name, u.email,
          c.brand, c.model, c.image_url
          FROM bookings b
          JOIN users u ON b.user_id = u.user_id
          JOIN cars c ON b.car_id = c.car_id
          ORDER BY b.booking_date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="../assets/css/dashboard_style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            color: #333;
        }

        .back-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .back-btn:hover {
            background-color: #45a049;
        }

        .bookings-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .bookings-table th,
        .bookings-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .bookings-table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
        }

        .bookings-table tr:hover {
            background-color: #f5f5f5;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
            font-weight: 500;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 5px;
            font-size: 0.9em;
        }

        .view-btn {
            background-color: #2196F3;
            color: white;
        }

        .view-btn:hover {
            background-color: #1976D2;
        }

        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }

        .edit-btn:hover {
            background-color: #45a049;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
        }

        .delete-btn:hover {
            background-color: #d32f2f;
        }

        .search-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .search-bar input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            flex: 1;
        }

        .search-bar button {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #45a049;
        }

        .car-image {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .status-select {
            padding: 5px 10px;
            border-radius: 15px;
            border: 1px solid #ddd;
            background-color: white;
            cursor: pointer;
            font-size: 0.9em;
            font-weight: 500;
        }

        .status-select:disabled {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }

        .status-select option[value="pending"] {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-select option[value="confirmed"] {
            background-color: #d4edda;
            color: #155724;
        }

        .status-select option[value="cancelled"] {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-select option[value="completed"] {
            background-color: #cce5ff;
            color: #004085;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Bookings</h1>
            <a href="admin_dashboard.php" class="back-btn">
                <i class='bx bx-arrow-back'></i>
                Back to Dashboard
            </a>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search by booking ID, user name, or car model...">
            <button onclick="searchBookings()">Search</button>
        </div>

        <table class="bookings-table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User</th>
                    <th>Car</th>
                    <th>Image</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <?php if ($has_payment_status): ?>
                    <th>Payment Status</th>
                    <?php endif; ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $booking['booking_id']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?><br>
                            <small><?php echo htmlspecialchars($booking['email']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></td>
                        <td>
                            <img src="../<?php echo htmlspecialchars($booking['image_url']); ?>" 
                                 alt="Car Image" class="car-image">
                        </td>
                        <td><?php echo date('M d, Y', strtotime($booking['start_date'])); ?></td>
                        <td><?php echo date('M d, Y', strtotime($booking['end_date'])); ?></td>
                        <td>$<?php echo number_format($booking['total_amount'], 2); ?></td>
                        <td>
                            <select class="status-select" data-booking-id="<?php echo $booking['booking_id']; ?>" 
                                    onchange="updateBookingStatus(this)">
                                <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                <option value="completed" <?php echo $booking['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </td>
                        <?php if ($has_payment_status): ?>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($booking['payment_status'] ?? 'pending'); ?>">
                                <?php echo ucfirst($booking['payment_status'] ?? 'Pending'); ?>
                            </span>
                        </td>
                        <?php endif; ?>
                        <td>
                            <a href="view_booking.php?id=<?php echo $booking['booking_id']; ?>" class="action-btn view-btn">
                                <i class='bx bx-show'></i> View
                            </a>
                            <a href="edit_booking.php?id=<?php echo $booking['booking_id']; ?>" class="action-btn edit-btn">
                                <i class='bx bx-edit'></i> Edit
                            </a>
                            <a href="../controller/delete_booking.php?id=<?php echo $booking['booking_id']; ?>" 
                               class="action-btn delete-btn"
                               onclick="return confirm('Are you sure you want to delete this booking?')">
                                <i class='bx bx-trash'></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        function updateBookingStatus(selectElement) {
            const bookingId = selectElement.dataset.bookingId;
            const newStatus = selectElement.value;
            
            // Show loading state
            selectElement.disabled = true;
            
            // Send AJAX request to update status
            fetch('../controller/update_booking_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `booking_id=${bookingId}&status=${newStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert('Status updated successfully');
                    // Update the status badge
                    const statusBadge = selectElement.closest('tr').querySelector('.status-badge');
                    if (statusBadge) {
                        statusBadge.className = `status-badge status-${newStatus}`;
                        statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    }
                } else {
                    // Show error message
                    alert(data.message || 'Error updating status');
                    // Reset select to previous value
                    selectElement.value = selectElement.dataset.previousValue;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status');
                // Reset select to previous value
                selectElement.value = selectElement.dataset.previousValue;
            })
            .finally(() => {
                // Re-enable select
                selectElement.disabled = false;
            });
        }

        // Store previous value when select changes
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('focus', function() {
                this.dataset.previousValue = this.value;
            });
        });

        function searchBookings() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.querySelector('.bookings-table');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    const cell = cells[j];
                    if (cell) {
                        const text = cell.textContent || cell.innerText;
                        if (text.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }

                row.style.display = found ? '' : 'none';
            }
        }
    </script>
</body>
</html> 