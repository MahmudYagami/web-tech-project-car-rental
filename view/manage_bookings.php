<?php
session_start();
require_once '../model/db.php';
require_once '../model/bookingmanagementmodel.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get data from session
$has_payment_status = $_SESSION['has_payment_status'] ?? false;
$bookings = $_SESSION['bookings'] ?? [];
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
                <?php foreach ($bookings as $booking): ?>
                    <tr data-booking-id="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                        <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?><br>
                            <small><?php echo htmlspecialchars($booking['email']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></td>
                        <td>
                            <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" alt="Car Image" class="car-image">
                        </td>
                        <td><?php echo htmlspecialchars($booking['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($booking['end_date']); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($booking['total_amount'], 2)); ?></td>
                        <td>
                            <select class="status-select" onchange="updateStatus(this.value, <?php echo $booking['booking_id']; ?>)">
                                <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                <option value="completed" <?php echo $booking['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </td>
                        <?php if ($has_payment_status): ?>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($booking['payment_status']); ?>">
                                    <?php echo htmlspecialchars($booking['payment_status']); ?>
                                </span>
                            </td>
                        <?php endif; ?>
                        <td>
                            <button onclick="deleteBooking(<?php echo $booking['booking_id']; ?>)" 
                                    class="btn btn-danger btn-sm">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="../assets/js/booking.js"></script>
</body>
</html> 