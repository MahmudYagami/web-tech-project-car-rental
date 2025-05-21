<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if car data is available in session
if (!isset($_SESSION['booking_car'])) {
    header("Location: ../controller/booking_controller.php?car_id=" . ($_GET['car_id'] ?? ''));
    exit();
}

$car = $_SESSION['booking_car'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Car - <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></title>
    <link rel="stylesheet" href="../assets/css/inventory.css">
    <link rel="stylesheet" href="../assets/css/booking.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <header>
        <div class="container">
            <h1>Book Your Car</h1>
            <div class="user-info">
                <i class='bx bxs-user'></i>
                <span><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></span>
                <a href="user_dashboard.php" class="dashboard-link">Dashboard</a>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="booking-container">
            <div class="car-summary">
                <div class="car-image">
                    <img src="<?php echo htmlspecialchars($car['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
                </div>
                <div class="car-details">
                    <h2><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h2>
                    <p class="year"><?php echo htmlspecialchars($car['year']); ?></p>
                    <div class="specs">
                        <span><i class='bx bxs-color'></i> <?php echo htmlspecialchars($car['color']); ?></span>
                        <span><i class='bx bxs-car'></i> <?php echo htmlspecialchars($car['transmission']); ?></span>
                        <span><i class='bx bxs-gas-pump'></i> <?php echo htmlspecialchars($car['fuel_type']); ?></span>
                        <span><i class='bx bxs-user'></i> <?php echo htmlspecialchars($car['seats']); ?> seats</span>
                    </div>
                </div>
            </div>

            <form action="../controller/process_booking.php" method="POST" class="booking-form">
                <input type="hidden" name="car_id" value="<?php echo $car['car_id']; ?>">
                
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="total-price">
                    <h3>Total Price: <span id="total_price">$0.00</span></h3>
                    <p>Daily Rate: $<?php echo number_format($car['daily_rate'], 2); ?></p>
                </div>

                <button type="submit" class="submit-btn">Proceed to Checkout</button>
            </form>
        </div>
    </main>

    <script>
        // Calculate total price based on selected dates
        function calculateTotal() {
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);
            const dailyRate = <?php echo $car['daily_rate']; ?>;
            
            if (startDate && endDate && startDate <= endDate) {
                const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                const total = days * dailyRate;
                document.getElementById('total_price').textContent = '$' + total.toFixed(2);
            }
        }

        document.getElementById('start_date').addEventListener('change', calculateTotal);
        document.getElementById('end_date').addEventListener('change', calculateTotal);
    </script>
</body>
</html>
