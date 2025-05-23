<?php
session_start();
require_once '../model/db.php';
require_once '../model/booking_model.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// Handle POST request for car return
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    
    // Verify booking ownership
    $verify_result = verifyBookingOwnership($conn, $booking_id, $_SESSION['user_id']);
    
    if ($verify_result['success']) {
        // Process the car return
        $return_result = processCarReturn($conn, $booking_id);
        
        if ($return_result['success']) {
            $_SESSION['success_message'] = $return_result['message'];
        } else {
            $_SESSION['error_message'] = $return_result['message'];
        }
    } else {
        $_SESSION['error_message'] = $verify_result['message'];
    }
    
    header("Location: ../view/user_dashboard.php");
    exit();
}

// Handle GET request to display return car page
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get user's active bookings
    $bookings_result = getActiveBookings($conn, $_SESSION['user_id']);
    
    // Store bookings in session for the view
    $_SESSION['active_bookings'] = $bookings_result['data'];
    
    // Redirect to the view
    header("Location: ../view/return_car.php");
    exit();
}

// If neither POST nor GET, redirect to dashboard
header("Location: ../view/user_dashboard.php");
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Car</title>
    <link rel="stylesheet" href="../assets/css/dashboard_style.css">
    <style>
        .return-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .booking-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .booking-info {
            flex-grow: 1;
        }
        
        .return-form {
            margin-left: 20px;
        }
        
        .return-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .return-btn:hover {
            background-color: #45a049;
        }
        
        .no-bookings {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>
    <div class="return-container">
        <h2>Return Car</h2>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message success">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="message error">
                <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($active_bookings)): ?>
            <div class="no-bookings">
                <p>You don't have any active bookings to return.</p>
                <a href="../view/user_dashboard.php" class="return-btn">Back to Dashboard</a>
            </div>
        <?php else: ?>
            <?php foreach ($active_bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-info">
                        <h3><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></h3>
                        <p>Booking ID: <?php echo htmlspecialchars($booking['booking_id']); ?></p>
                        <p>Start Date: <?php echo htmlspecialchars($booking['start_date']); ?></p>
                        <p>End Date: <?php echo htmlspecialchars($booking['end_date']); ?></p>
                    </div>
                    <form class="return-form" method="POST">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                        <button type="submit" class="return-btn">Return Car</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html> 