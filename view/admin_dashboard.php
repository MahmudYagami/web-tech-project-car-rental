<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermodel.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get admin details
$admin = getUserByEmail($conn, $_SESSION['email']);

// Get total users
$users_query = "SELECT COUNT(*) as total FROM users";
$users_result = mysqli_query($conn, $users_query);
$total_users = mysqli_fetch_assoc($users_result)['total'];

// Get total bookings
$bookings_query = "SELECT COUNT(*) as total FROM bookings";
$bookings_result = mysqli_query($conn, $bookings_query);
$total_bookings = mysqli_fetch_assoc($bookings_result)['total'];

// Get total cars
$cars_query = "SELECT COUNT(*) as total FROM cars";
$cars_result = mysqli_query($conn, $cars_query);
$total_cars = mysqli_fetch_assoc($cars_result)['total'];

// Check if payment_status column exists
$check_column = "SHOW COLUMNS FROM bookings LIKE 'payment_status'";
$column_result = mysqli_query($conn, $check_column);
$has_payment_status = mysqli_num_rows($column_result) > 0;

// Get revenue for current month
if ($has_payment_status) {
    $revenue_query = "SELECT SUM(total_amount) as total FROM bookings 
                     WHERE MONTH(booking_date) = MONTH(CURRENT_DATE()) 
                     AND YEAR(booking_date) = YEAR(CURRENT_DATE())
                     AND payment_status = 'paid'";
} else {
    $revenue_query = "SELECT SUM(total_amount) as total FROM bookings 
                     WHERE MONTH(booking_date) = MONTH(CURRENT_DATE()) 
                     AND YEAR(booking_date) = YEAR(CURRENT_DATE())";
}
$revenue_result = mysqli_query($conn, $revenue_query);
$monthly_revenue = mysqli_fetch_assoc($revenue_result)['total'] ?? 0;

// Get total damage reports
$damage_query = "SELECT COUNT(*) as total FROM reports";
$damage_result = mysqli_query($conn, $damage_query);
$total_damage_reports = mysqli_fetch_assoc($damage_result)['total'];
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
            margin-top: 80px;
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
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="admin_dashboard.php" class="nav-brand">Car Rental Admin</a>
            <div class="nav-links"></div>
            </div>
        </div>
        <div class="nav-right">
            <div class="notification-icon" onclick="toggleNotifications()">
                <i class='bx bxs-bell'></i>
                <span class="notification-badge" id="notificationBadge">0</span>
            </div>
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="no-notifications">No new notifications</div>
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
                    <span><?php echo $total_users; ?></span>
                </div>
                <div class="stat-box">
                    <i class='bx bxs-calendar-check'></i>
                    <strong>Total Bookings</strong>
                    <span><?php echo $total_bookings; ?></span>
                </div>
                <div class="stat-box">
                    <i class='bx bxs-car'></i>
                    <strong>Total Cars</strong>
                    <span><?php echo $total_cars; ?></span>
                </div>
                <div class="stat-box">
                    <i class='bx bxs-wallet'></i>
                    <strong>Revenue This Month</strong>
                    <span>$<?php echo number_format($monthly_revenue, 2); ?></span>
                </div>
                <div class="stat-box">
                    <i class='bx bxs-error'></i>
                    <strong>Damage Reports</strong>
                    <span><?php echo $total_damage_reports; ?></span>
                </div>
            </div>

            <div class="card">
                <h3>Quick Actions</h3>
                <div class="quick-actions">
                    <a href="role_assignment.php">Manage Users</a>
                    <a href="manage_cars.php">Manage Cars</a>
                    <a href="manage_bookings.php">Manage Bookings</a>
                    <a href="admin_damage_report.php">Damage Reports</a>
                    <a href="../controller/logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        let lastNotificationId = 0;
        let notificationCheckInterval;

        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            
            if (dropdown.style.display === 'block') {
                startNotificationCheck();
            } else {
                stopNotificationCheck();
            }
        }

        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        function startNotificationCheck() {
            notificationCheckInterval = setInterval(checkNewNotifications, 10000);
        }

        function stopNotificationCheck() {
            clearInterval(notificationCheckInterval);
        }

        function checkNewNotifications() {
            fetch('../controller/get_notifications.php?last_id=' + lastNotificationId)
                .then(response => response.json())
                .then(data => {
                    if (data.notifications.length > 0) {
                        updateNotifications(data.notifications);
                        updateNotificationBadge(data.unread_count);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function updateNotifications(notifications) {
            const dropdown = document.getElementById('notificationDropdown');
            let html = '';

            notifications.forEach(notification => {
                if (notification.id > lastNotificationId) {
                    lastNotificationId = notification.id;
                }

                html += `
                    <div class="notification-item ${notification.is_read ? '' : 'unread'}" 
                         onclick="markAsRead(${notification.id})">
                        <div class="notification-content">${notification.message}</div>
                        <div class="notification-time">${notification.created_at}</div>
                    </div>
                `;
            });

            if (html === '') {
                html = '<div class="no-notifications">No new notifications</div>';
            }

            dropdown.innerHTML = html;
        }

        function updateNotificationBadge(count) {
            const badge = document.getElementById('notificationBadge');
            if (count > 0) {
                badge.style.display = 'block';
                badge.textContent = count;
            } else {
                badge.style.display = 'none';
            }
        }

        function markAsRead(notificationId) {
            fetch('../controller/mark_notification_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'notification_id=' + notificationId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = document.querySelector(`[onclick="markAsRead(${notificationId})"]`);
                    if (item) {
                        item.classList.remove('unread');
                    }
                    updateNotificationBadge(data.unread_count);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Start checking for notifications when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            checkNewNotifications();
        });

        // Close dropdowns when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.notification-icon') && !event.target.matches('.notification-icon *')) {
                const notificationDropdown = document.getElementById('notificationDropdown');
                if (notificationDropdown.style.display === 'block') {
                    notificationDropdown.style.display = 'none';
                    stopNotificationCheck();
                }
            }
            if (!event.target.matches('.profile-icon') && !event.target.matches('.profile-icon *')) {
                const profileDropdown = document.getElementById('profileDropdown');
                if (profileDropdown.style.display === 'block') {
                    profileDropdown.style.display = 'none';
                }
            }
        }

        // Set active nav link based on current page
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
