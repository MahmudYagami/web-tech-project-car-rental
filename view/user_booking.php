<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if we have the bookings data in session
if (!isset($_SESSION['user_bookings_data'])) {
    header("Location: ../controller/user_bookings_controller.php");
    exit();
}

// Get data from session
$bookings = $_SESSION['user_bookings_data']['bookings'];
$has_payment_status = $_SESSION['user_bookings_data']['has_payment_status'];

// Clear the session data after retrieving it
unset($_SESSION['user_bookings_data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

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

        .booking-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
            display: flex;
        }

        .car-image {
            width: 200px;
            height: 150px;
            object-fit: cover;
        }

        .booking-details {
            padding: 20px;
            flex: 1;
        }

        .car-info {
            margin-bottom: 15px;
        }

        .car-info h2 {
            margin: 0 0 5px 0;
            color: #333;
        }

        .booking-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .info-item i {
            color: #4CAF50;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }

        .status-pending {
            background-color: #ffd700;
            color: #000;
        }

        .status-confirmed {
            background-color: #4CAF50;
            color: white;
        }

        .status-completed {
            background-color: #2196F3;
            color: white;
        }

        .status-cancelled {
            background-color: #f44336;
            color: white;
        }

        .payment-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
            margin-left: 10px;
        }

        .payment-pending {
            background-color: #ff9800;
            color: white;
        }

        .payment-paid {
            background-color: #4CAF50;
            color: white;
        }

        .payment-refunded {
            background-color: #2196F3;
            color: white;
        }

        .no-bookings {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .no-bookings i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 20px;
        }

        .no-bookings p {
            color: #666;
            margin: 10px 0;
        }

        .browse-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        .browse-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>My Bookings</h1>
            <a href="user_dashboard.php" class="back-btn">
                <i class='bx bx-arrow-back'></i>
                Back to Dashboard
            </a>
        </div>

        <?php if (empty($bookings)): ?>
            <div class="no-bookings">
                <i class='bx bx-car'></i>
                <h2>No Bookings Found</h2>
                <p>You haven't made any bookings yet.</p>
                <a href="inventory.php" class="browse-btn">Browse Cars</a>
            </div>
        <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card">
                    <img src="../<?php echo htmlspecialchars($booking['image_url']); ?>" alt="Car Image" class="car-image">
                    <div class="booking-details">
                        <div class="car-info">
                            <h2><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></h2>
                        </div>
                        <div class="booking-info">
                            <div class="info-item">
                                <i class='bx bx-calendar'></i>
                                <span>Booking Date: <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></span>
                            </div>
                            <div class="info-item">
                                <i class='bx bx-calendar-check'></i>
                                <span>Start Date: <?php echo date('M d, Y', strtotime($booking['start_date'])); ?></span>
                            </div>
                            <div class="info-item">
                                <i class='bx bx-calendar-x'></i>
                                <span>End Date: <?php echo date('M d, Y', strtotime($booking['end_date'])); ?></span>
                            </div>
                            <div class="info-item">
                                <i class='bx bx-dollar'></i>
                                <span>Total Amount: $<?php echo number_format($booking['total_amount'], 2); ?></span>
                            </div>
                        </div>
                        <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                        <?php if ($has_payment_status): ?>
                        <span class="payment-badge payment-<?php echo strtolower($booking['payment_status'] ?? 'pending'); ?>">
                            <?php echo ucfirst($booking['payment_status'] ?? 'Pending'); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html> 