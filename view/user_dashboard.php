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
if (!$user) {
    // If user data cannot be found, redirect to login
    session_destroy();
    header("Location: login.php");
    exit();
}
$loyaltyPoints = getUserLoyaltyPoints($conn, $_SESSION['user_id']);
$totalDamageReports = getUserDamageReports($conn, $_SESSION['user_id']);
$bookingStats = getUserBookingStats($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="..\assets\css\dashboard_style.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
    .profile-section {
      position: absolute;
      top: 20px;
      right: 20px;
      display: flex;
      align-items: center;
    }

    .profile-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: #4CAF50;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      cursor: pointer;
      font-size: 20px;
    }

    .profile-dropdown {
      position: absolute;
      top: 60px;
      right: 0;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      display: none;
      min-width: 200px;
      z-index: 1000;
    }

    .profile-dropdown.show {
      display: block;
    }

    .profile-dropdown a {
      display: block;
      padding: 12px 20px;
      color: #333;
      text-decoration: none;
      transition: background 0.3s;
    }

    .profile-dropdown a:hover {
      background: #f5f5f5;
    }

    .user-name {
      margin-right: 15px;
      color: #333;
    }

    .loyalty-points {
      background: linear-gradient(135deg, #FFD700, #FFA500);
      padding: 15px 25px;
      border-radius: 8px;
      color: white;
      font-weight: bold;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      display: inline-block;
    }

    .loyalty-points i {
      margin-right: 8px;
    }

    .stat-box {
      background: white;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      margin: 10px;
      flex: 1;
      min-width: 200px;
      text-align: center;
    }

    .stat-box i {
      font-size: 24px;
      margin-bottom: 10px;
      color: #4CAF50;
    }

    .stat-box strong {
      display: block;
      margin-bottom: 5px;
      color: #666;
    }

    .stat-box span {
      font-size: 20px;
      color: #333;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="profile-section">
    <span class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
    <div class="profile-icon" onclick="toggleProfileDropdown()">
      <i class='bx bxs-user'></i>
    </div>
    <div class="profile-dropdown" id="profileDropdown">
      <a href="customer_profile.php"><i class='bx bxs-user-detail'></i> View Profile</a>
      <a href="edit_profile.php"><i class='bx bxs-edit'></i> Edit Profile</a>
      <a href="../controller/logout.php"><i class='bx bxs-log-out'></i> Logout</a>
    </div>
  </div>

  <div class="dashboard">
    <h2>User Dashboard</h2>

    <div class="loyalty-points">
      <i class='bx bxs-star'></i>
      Loyalty Points: <?php echo number_format($loyaltyPoints); ?>
    </div>

    <div class="card stats">
      <div class="stat-box">
        <i class='bx bxs-car'></i>
        <strong>Your Bookings</strong>
        <span><?php echo $bookingStats['total_bookings']; ?></span>
      </div>
      <div class="stat-box">
        <i class='bx bxs-time'></i>
        <strong>Active Bookings</strong>
        <span><?php echo $bookingStats['active_bookings']; ?></span>
      </div>
      <div class="stat-box">
        <i class='bx bxs-wallet'></i>
        <strong>Total Spent</strong>
        <span>$<?php echo number_format($bookingStats['total_spent'], 2); ?></span>
      </div>
      <div class="stat-box">
        <i class='bx bxs-error'></i>
        <strong>Damage Reports</strong>
        <span><?php echo $totalDamageReports; ?></span>
      </div>
      <div class="stat-box">
        <i class='bx bxs-bell'></i>
        <strong>Pending Returns</strong>
        <span><?php echo $bookingStats['pending_returns']; ?></span>
      </div>
    </div>

    <div class="card">
      <h3>Quick Actions</h3>
      <div class="quick-actions">
        <a href="../controller/user_bookings_controller.php">View My Bookings</a>
        <a href="inventory.php">Browse Cars</a>
        <a href="damage_report.php">Report Damage</a>
        <a href="../controller/return_car.php" class="return-btn">Return Car</a>
      </div>
    </div>
  </div>

  <script>
    function toggleProfileDropdown() {
      document.getElementById('profileDropdown').classList.toggle('show');
    }

    // Close dropdown when clicking outside
    window.onclick = function(event) {
      if (!event.target.matches('.profile-icon') && !event.target.matches('.profile-icon *')) {
        var dropdowns = document.getElementsByClassName("profile-dropdown");
        for (var i = 0; i < dropdowns.length; i++) {
          var openDropdown = dropdowns[i];
          if (openDropdown.classList.contains('show')) {
            openDropdown.classList.remove('show');
          }
        }
      }
    }
  </script>
</body>
</html>
