<?php
session_start();
require_once '../model/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// Get user's active bookings
$user_id = $_SESSION['user_id'];
$sql = "SELECT b.*, c.brand, c.model 
        FROM bookings b 
        JOIN cars c ON b.car_id = c.car_id 
        WHERE b.user_id = ? AND b.status = 'confirmed'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$active_bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    
    // Verify the booking belongs to the user
    $verify_sql = "SELECT * FROM bookings WHERE booking_id = ? AND user_id = ? AND status = 'confirmed'";
    $verify_stmt = mysqli_prepare($conn, $verify_sql);
    mysqli_stmt_bind_param($verify_stmt, "ii", $booking_id, $user_id);
    mysqli_stmt_execute($verify_stmt);
    $verify_result = mysqli_stmt_get_result($verify_stmt);
    
    if (mysqli_num_rows($verify_result) > 0) {
        // Delete the booking
        $delete_sql = "DELETE FROM bookings WHERE booking_id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($delete_stmt, "i", $booking_id);
        
        if (mysqli_stmt_execute($delete_stmt)) {
            $_SESSION['success_message'] = "Car has been successfully returned!";
        } else {
            $_SESSION['error_message'] = "Error returning the car. Please try again.";
        }
        mysqli_stmt_close($delete_stmt);
    } else {
        $_SESSION['error_message'] = "Invalid booking or unauthorized action.";
    }
    mysqli_stmt_close($verify_stmt);
    
    header("Location: ../view/user_dashboard.php");
    exit();
}
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