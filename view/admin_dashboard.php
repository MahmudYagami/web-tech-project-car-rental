<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermodel.php';
require_once '../model/adminmodel.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get admin details
$admin = getUserByEmail($conn, $_SESSION['email']);

// Get dashboard statistics
$stats = getAdminDashboardStats($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="..\assets\css\dashboard_style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .navbar {
            background: #fff;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-brand {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-link {
            color: #333;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: #f5f5f5;
            color: #4CAF50;
        }

        .nav-link.active {
            background: #4CAF50;
            color: white;
        }

        .notification-icon {
            position: relative;
            cursor: pointer;
            font-size: 24px;
            color: #333;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ff4444;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            display: none;
        }

        .notification-dropdown {
            position: absolute;
            top: 60px;
            right: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: none;
            min-width: 300px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-name {
            color: #333;
            font-weight: 500;
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
            right: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: none;
            min-width: 200px;
            z-index: 1000;
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

        .main-content {
            margin-top: 37px;
            padding: 20px;
        }

        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-box i {
            font-size: 2em;
            color: #4CAF50;
            margin-bottom: 10px;
        }

        .stat-box strong {
            display: block;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-box span {
            font-size: 1.5em;
            color: #333;
            font-weight: bold;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .quick-actions a {
            background: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            transition: background 0.3s;
        }

        .quick-actions a:hover {
            background: #45a049;
        }

        .quick-actions a.logout-btn {
            background: #dc3545;
        }

        .quick-actions a.logout-btn:hover {
            background: #c82333;
        }

        /* Responsive styles */
        @media screen and (max-width: 1024px) {
            .card.stats {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
        }

        @media screen and (max-width: 768px) {
            .navbar {
                padding: 10px 15px;
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-left, .nav-right {
                width: 100%;
                justify-content: space-between;
                margin: 5px 0;
            }

            .nav-brand {
                font-size: 20px;
            }

            .nav-links {
                display: none;
            }

            .main-content {
                margin-top: 120px;
            }

            .card.stats {
                grid-template-columns: 1fr;
            }

            .stat-box {
                padding: 15px;
            }

            .stat-box i {
                font-size: 1.5em;
            }

            .stat-box span {
                font-size: 1.2em;
            }

            .quick-actions {
                grid-template-columns: 1fr;
            }

            .notification-dropdown {
                width: 90%;
                right: 5%;
                left: 5%;
            }

            .profile-dropdown {
                width: 90%;
                right: 5%;
                left: 5%;
            }
        }

        @media screen and (max-width: 480px) {
            .navbar {
                padding: 10px;
            }

            .nav-brand {
                font-size: 18px;
            }

            .user-name {
                display: none;
            }

            .main-content {
                margin-top: -372px;
                padding: 10px;
            }

            .dashboard h2 {
                font-size: 1.5em;
                text-align: center;
            }

            .stat-box {
                padding: 10px;
            }

            .stat-box i {
                font-size: 1.2em;
            }

            .stat-box span {
                font-size: 1em;
            }

            .quick-actions a {
                padding: 12px;
                font-size: 14px;
            }
        }

        .notification-container {
            position: relative;
        }

        .notification-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-header h3 {
            margin: 0;
            color: #333;
            font-size: 16px;
            font-weight: 600;
        }

        .notification-list {
            padding: 0;
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-list::-webkit-scrollbar {
            width: 6px;
        }

        .notification-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .notification-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .notification-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #f5f5f5;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .notification-item:hover {
            background: #f9f9f9;
        }

        .notification-item.unread {
            background: #f0f7ff;
        }

        .notification-item .notification-type {
            font-size: 12px;
            color: #4CAF50;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .notification-item .notification-message {
            color: #333;
            font-size: 14px;
            line-height: 1.4;
            margin: 5px 0;
            word-wrap: break-word;
            white-space: pre-wrap;
        }

        .notification-item .notification-time {
            color: #888;
            font-size: 12px;
            margin-top: 2px;
        }

        .notification-item.booking { border-left: 4px solid #4CAF50; }
        .notification-item.damage { border-left: 4px solid #ff4444; }
        .notification-item.return { border-left: 4px solid #2196F3; }
        .notification-item.system { border-left: 4px solid #9C27B0; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="admin_dashboard.php" class="nav-brand">Car Rental Admin</a>
            <div class="nav-links"></div>
        </div>
        <div class="nav-right">
            <div class="notification-container">
                <i class='bx bx-bell notification-icon' id="notificationIcon"></i>
                <span class="notification-badge" id="notificationBadge">0</span>
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <h3>Notifications</h3>
                    </div>
                    <div class="notification-list" id="notificationList">
                        <!-- Notifications will be loaded here -->
                    </div>
                </div>
            </div>
            <div class="profile-section">
                <span class="user-name"><?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?></span>
                <div class="profile-icon" onclick="toggleProfileDropdown()">
                    <i class='bx bxs-user'></i>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <a href="view_profile.php"><i class='bx bxs-user-detail'></i> View Profile</a>
                    <a href="edit_profile.php"><i class='bx bxs-edit'></i> Edit Profile</a>
                    <a href="../controller/logout.php"><i class='bx bxs-log-out'></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="dashboard">
            <h2 style="margin-top: 100px;">Dashboard Overview</h2>

            <div class="card stats">
                <div class="stat-box">
                    <i class='bx bxs-user'></i>
                    <strong>Total Users</strong>
                    <span><?php echo $stats['total_users']; ?></span>
                </div>
                <div class="stat-box">
                    <i class='bx bxs-calendar-check'></i>
                    <strong>Total Bookings</strong>
                    <span><?php echo $stats['total_bookings']; ?></span>
                </div>
                <div class="stat-box">
                    <i class='bx bxs-car'></i>
                    <strong>Total Cars</strong>
                    <span><?php echo $stats['total_cars']; ?></span>
                </div>
                <div class="stat-box">
                    <i class='bx bxs-wallet'></i>
                    <strong>Revenue This Month</strong>
                    <span>$<?php echo number_format($stats['monthly_revenue'], 2); ?></span>
                </div>
                <div class="stat-box">
                    <i class='bx bxs-error'></i>
                    <strong>Damage Reports</strong>
                    <span><?php echo $stats['total_damage_reports']; ?></span>
                </div>
            </div>

            <div class="card">
                <h3>Quick Actions</h3>
                <div class="quick-actions">
                    <a href="role_assignment.php">Manage Users</a>
                    <a href="../controller/manage_cars_controller.php">Manage Cars</a>
                    <a href="../controller/manage_bookings_controller.php">Manage Bookings</a>
                    <a href="../controller/admin_damage_report_controller.php">Damage Reports</a>
                    <a href="../controller/logout.php" class="logout-btn">Logout</a>
                    <a href="forgot_password.php" class="logout-btn">Reset Password</a>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin_dashboard.js"></script>
</body>
</html>
