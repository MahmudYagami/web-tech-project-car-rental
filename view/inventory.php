<?php
require_once '../controller/inventory_controller.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Inventory</title>
    <link rel="stylesheet" href="../assets/css/inventory.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* User Profile Styles */
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            margin-left: auto;
        }

        .user-icon {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: background-color 0.3s;
            color: white;
        }

        .user-icon:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .user-icon i {
            font-size: 1.2rem;
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 0.5rem 0;
            min-width: 200px;
            display: none;
            z-index: 1000;
        }

        .user-dropdown.show {
            display: block;
        }

        .user-dropdown a {
            display: block;
            padding: 0.8rem 1rem;
            color: #333;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .user-dropdown a:hover {
            background-color: #f8f9fa;
        }

        .user-dropdown .divider {
            height: 1px;
            background-color: #eee;
            margin: 0.5rem 0;
        }

        .user-name {
            font-weight: 500;
            color: white;
        }

        /* Loading and No Results Styles */
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .no-results {
            display: none;
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

        /* Search Section Styles */
        .search-section {
            margin: 20px 0;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .search-form {
            width: 100%;
        }

        .search-inputs {
            display: flex;
            gap: 10px;
            align-items: center;
            position: relative;
        }

        .search-inputs input[type="text"] {
            flex: 1;
            padding: 10px 15px;
            padding-right: 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .search-icon {
            position: absolute;
            right: 120px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            pointer-events: none;
        }

        .search-inputs select {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            background: white;
        }

        .search-btn {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s;
        }

        .search-btn:hover {
            background: #0056b3;
        }

        .search-btn i {
            font-size: 16px;
        }

        /* Header and Navigation Styles */
        header {
            background: #333;
            padding: 1rem;
            color: white;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .login-link {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border: 1px solid white;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .login-link:hover {
            background: white;
            color: #333;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Car Rental System</div>
            <div class="user-info">
                <?php if (!empty($userName)): ?>
                    <div class="user-icon" onclick="toggleDropdown()">
                        <i class="fas fa-user-circle"></i>
                        <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                        <i class="fas fa-chevron-down" style="font-size: 0.8rem; margin-left: 5px;"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="user_dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="edit_profile.php">
                            <i class="fas fa-user-edit"></i> Edit Profile
                        </a>
                        <div class="divider"></div>
                        <a href="../controller/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="login-link">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <h1>Available Cars</h1>
        
        <!-- Search Section -->
        <div class="search-section">
            <form action="" method="GET" class="search-form" id="searchForm">
                <div class="search-inputs">
                    <input type="text" name="search" id="searchInput" placeholder="Search by brand or model..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <i class="fas fa-search search-icon"></i>
                    <select name="filter" id="filterSelect">
                        <option value="all" <?php echo (!isset($_GET['filter']) || $_GET['filter'] == 'all') ? 'selected' : ''; ?>>All</option>
                        <option value="brand" <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'brand') ? 'selected' : ''; ?>>Brand</option>
                        <option value="model" <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'model') ? 'selected' : ''; ?>>Model</option>
                        <option value="transmission" <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'transmission') ? 'selected' : ''; ?>>Transmission</option>
                        <option value="fuel_type" <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'fuel_type') ? 'selected' : ''; ?>>Fuel Type</option>
                    </select>
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>

        <div class="loading">Searching...</div>
        <div class="no-results">No cars found matching your search criteria.</div>

        <div class="car-grid" id="carGrid">
            <?php if ($cars && count($cars) > 0): ?>
                <?php foreach ($cars as $car): ?>
                    <div class="car-card">
                        <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
                        <div class="car-info">
                            <h2><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h2>
                            <p class="year"><?php echo htmlspecialchars($car['year']); ?></p>
                            <div class="details">
                                <p><strong>Color:</strong> <?php echo htmlspecialchars($car['color']); ?></p>
                                <p><strong>Transmission:</strong> <?php echo htmlspecialchars($car['transmission']); ?></p>
                                <p><strong>Fuel Type:</strong> <?php echo htmlspecialchars($car['fuel_type']); ?></p>
                                <p><strong>Seats:</strong> <?php echo htmlspecialchars($car['seats']); ?></p>
                                <p><strong>Mileage:</strong> <?php echo htmlspecialchars($car['mileage']); ?> km</p>
                            </div>
                            <p class="price">$<?php echo htmlspecialchars($car['daily_rate']); ?> per day</p>
                            <p class="description"><?php echo htmlspecialchars($car['description']); ?></p>
                            <button class="book-btn" data-car-id="<?php echo $car['car_id']; ?>">Book Now</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-cars">No cars available at the moment.</p>
            <?php endif; ?>
        </div>
    </main>

    <script src="../assets/js/inventory.js"></script>
</body>
</html> 