<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermodel.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user details
$user = getUserByEmail($conn, $_SESSION['email']);

// Get user preferences using the model function
$preferences = getUserPreferencesById($conn, $_SESSION['user_id']);

// Get booking history using the model function
$bookings_result = getUserBookingHistory($conn, $_SESSION['user_id']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Profile</title>
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

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .profile-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .profile-item {
            margin-bottom: 15px;
        }

        .profile-item label {
            font-weight: bold;
            color: #666;
            display: block;
            margin-bottom: 5px;
        }

        .profile-item span {
            color: #333;
        }

        .edit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .edit-btn:hover {
            background-color: #45a049;
        }

        .license-upload {
            margin-top: 20px;
            padding: 20px;
            border: 2px dashed #ccc;
            border-radius: 8px;
            text-align: center;
        }

        .preferences-section {
            margin-top: 20px;
        }

        .booking-history {
            margin-top: 20px;
        }

        .booking-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .booking-item:last-child {
            border-bottom: none;
        }

        .status-active {
            color: #4CAF50;
        }

        .status-completed {
            color: #666;
        }

        .status-cancelled {
            color: #f44336;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            margin-top: 5px;
        }

        .form-control:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
        }

        select.form-control {
            background-color: white;
            cursor: pointer;
        }

        #message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 4px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <h1>Customer Profile</h1>
            <div class="button-group">
                <a href="user_dashboard.php" class="edit-btn" style="background-color: #2196F3; margin-right: 10px;">Back to Dashboard</a>
                <a href="../controller/edit_profile_controller.php" class="edit-btn">Edit Profile</a>
            </div>
        </div>

        <div class="profile-section">
            <h2>Personal Information</h2>
            <div class="profile-grid">
                <div class="profile-item">
                    <label>Name</label>
                    <span><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                </div>
                <div class="profile-item">
                    <label>Email</label>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="profile-item">
                    <label>Mobile</label>
                    <span><?php echo htmlspecialchars($user['mobile']); ?></span>
                </div>
                <div class="profile-item">
                    <label>Address</label>
                    <span><?php echo htmlspecialchars($user['address'] ?? 'Not set'); ?></span>
                </div>
            </div>
        </div>

        <div class="profile-section">
            <h2>Driver's License</h2>
            <div class="license-upload">
                <?php if (isset($user['license_image'])): ?>
                    <div class="license-image-container">
                        <img src="../assets/uploads/license_4_1748355732.png?>" 
                             alt="Driver's License" 
                             style="max-width: 300px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    </div>
                <?php else: ?>
                    <form action="../controller/upload_license.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="license" accept="image/*" required>
                        <button type="submit" class="edit-btn">Upload License</button>
                    </form>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['license_success'])): ?>
                    <div class="success-message" style="color: #4CAF50; margin-top: 10px;">
                        <?php 
                            echo htmlspecialchars($_SESSION['license_success']);
                            unset($_SESSION['license_success']);
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['license_error'])): ?>
                    <div class="error-message" style="color: #f44336; margin-top: 10px;">
                        <?php 
                            echo htmlspecialchars($_SESSION['license_error']);
                            unset($_SESSION['license_error']);
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-section preferences-section">
            <h2>Car Preferences</h2>
            <div class="profile-grid">
                <div class="profile-item">
                    <label>Seat Position</label>
                    <span><?php echo htmlspecialchars($preferences['seat_position'] ?? 'Not set'); ?></span>
                </div>
                <div class="profile-item">
                    <label>Mirror Position</label>
                    <span><?php echo htmlspecialchars($preferences['mirror_position'] ?? 'Not set'); ?></span>
                </div>
                <div class="profile-item">
                    <label>Preferred Car Type</label>
                    <span><?php echo htmlspecialchars($preferences['preferred_car_type'] ?? 'Not set'); ?></span>
                </div>
            </div>
        </div>

        <div class="profile-section booking-history">
            <h2>Booking History</h2>
            <?php foreach ($bookings_result as $booking): ?>
                <div class="booking-item">
                    <h3><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></h3>
                    <p>Booking Date: <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></p>
                    <p>Status: <span class="status-<?php echo strtolower($booking['status']); ?>">
                        <?php echo htmlspecialchars($booking['status']); ?>
                    </span></p>
                    <p>Total Amount: $<?php echo number_format($booking['total_amount'], 2); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="../assets/js/customer_profile.js"></script>
</body>
</html>
