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

// Get user preferences
$preferences_sql = "SELECT * FROM user_preferences WHERE user_id = ?";
$preferences_stmt = mysqli_prepare($conn, $preferences_sql);
mysqli_stmt_bind_param($preferences_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($preferences_stmt);
$preferences = mysqli_stmt_get_result($preferences_stmt)->fetch_assoc();

// Get booking history
$bookings_sql = "SELECT b.*, c.model, c.brand 
                 FROM bookings b 
                 JOIN cars c ON b.car_id = c.car_id 
                 WHERE b.user_id = ? 
                 ORDER BY b.booking_date DESC";
$bookings_stmt = mysqli_prepare($conn, $bookings_sql);
mysqli_stmt_bind_param($bookings_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($bookings_stmt);
$bookings = mysqli_stmt_get_result($bookings_stmt);
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
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <h1>Customer Profile</h1>
            <a href="edit_profile.php" class="edit-btn">Edit Profile</a>
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
                    <span><?php echo htmlspecialchars($user['mobile'] ?? 'Not set'); ?></span>
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
                    <img src="<?php echo htmlspecialchars($user['license_image']); ?>" alt="Driver's License" style="max-width: 300px;">
                <?php else: ?>
                    <form action="../controller/upload_license.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="license" accept="image/*" required>
                        <button type="submit" class="edit-btn">Upload License</button>
                    </form>
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
            <?php while ($booking = mysqli_fetch_assoc($bookings)): ?>
                <div class="booking-item">
                    <h3><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></h3>
                    <p>Booking Date: <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></p>
                    <p>Status: <span class="status-<?php echo strtolower($booking['status']); ?>">
                        <?php echo htmlspecialchars($booking['status']); ?>
                    </span></p>
                    <p>Total Amount: $<?php echo number_format($booking['total_amount'], 2); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        // Add any JavaScript for dynamic features here
        document.addEventListener('DOMContentLoaded', function() {
            // Example: Auto-save preferences when changed
            const preferenceInputs = document.querySelectorAll('.preferences-section input');
            preferenceInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Add AJAX call to save preferences
                });
            });
        });
    </script>
</body>
</html>
