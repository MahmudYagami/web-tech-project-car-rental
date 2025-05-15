<?php
session_start();
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vehicle Inventory</title>
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #f1f3f6;
        }

        header {
            background-color: #2c3e50;
            padding: 20px;
            color: white;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            padding: 8px 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .profile-dropdown {
            position: relative;
        }

        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid white;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 50px;
            background-color: white;
            color: #2c3e50;
            border-radius: 6px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            padding: 10px;
            z-index: 100;
            min-width: 140px;
            flex-direction: column;
        }

        .profile-dropdown:hover .dropdown-menu {
            display: flex;
        }

        .dropdown-menu a {
            padding: 8px;
            text-decoration: none;
            color: #2c3e50;
        }

        .dropdown-menu a:hover {
            background-color: #f0f0f0;
        }

        header h1 {
            margin: 0;
            font-size: 2rem;
        }

        .controls {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .controls input, .controls select {
            padding: 10px;
            border-radius: 5px;
            border: none;
            width: 200px;
            font-size: 1rem;
        }

        main {
            padding: 30px;
        }

        #vehicle-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .vehicle-card {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }

        .vehicle-card:hover {
            transform: translateY(-5px);
        }

        .vehicle-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .vehicle-card-content {
            padding: 15px;
        }

        .vehicle-card h3 {
            margin: 0 0 10px;
            font-size: 1.3rem;
            color: #2c3e50;
        }

        .vehicle-card p {
            margin: 5px 0;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-top">
            <h1>🚗 Vehicle Inventory</h1>
            <div class="user-controls">
                <?php if ($user): ?>
                    <div class="profile-dropdown">
                        <img src="images/profile_icon.png" alt="Profile" class="profile-icon">
                        <div class="dropdown-menu">
                            <span style="padding: 8px;">👋 <?= htmlspecialchars($user['username']) ?></span>
                            <a href="profile.php">Profile</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn">Login</a>
                    <a href="signup.php" class="btn">Signup</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="controls">
            <input type="text" id="searchInput" placeholder="Search by model...">
            <select id="sortSelect">
                <option value="default">Sort by</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
                <option value="name_asc">Model A-Z</option>
                <option value="name_desc">Model Z-A</option>
            </select>
        </div>
    </header>

    <main>
        <div id="vehicle-container"></div>
    </main>

    <script src="vechicel_list.js"></script>
</body>
</html>
