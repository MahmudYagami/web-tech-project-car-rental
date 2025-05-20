<?php
session_start();
require_once '../model/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if booking details are in session
if (!isset($_SESSION['booking_details']) || !isset($_SESSION['car_details'])) {
    $_SESSION['error'] = "Invalid booking session. Please try booking again.";
    header("Location: inventory.php");
    exit();
}

$booking = $_SESSION['booking_details'];
$car = $_SESSION['car_details'];

// Validate required data
if (!isset($booking['start_date']) || !isset($booking['end_date']) || !isset($booking['days']) || 
    !isset($booking['subtotal']) || !isset($car['image_url']) || !isset($car['brand']) || 
    !isset($car['model']) || !isset($car['daily_rate'])) {
    $_SESSION['error'] = "Missing booking information. Please try booking again.";
    header("Location: inventory.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Car Rental</title>
    <link rel="stylesheet" href="../assets/css/inventory.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .checkout-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .booking-summary {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .car-info {
            display: flex;
            gap: 2rem;
            margin-bottom: 1rem;
        }

        .car-image {
            width: 200px;
            height: 150px;
            border-radius: 8px;
            overflow: hidden;
        }

        .car-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .car-details {
            flex: 1;
        }

        .cost-breakdown {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1rem 0;
        }

        .cost-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .total-cost {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2ecc71;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #ddd;
        }

        .promo-section {
            margin: 1.5rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .promo-form {
            display: flex;
            gap: 1rem;
        }

        .promo-form input {
            flex: 1;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .promo-form button {
            padding: 0.8rem 1.5rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .promo-form button:hover {
            background: #2980b9;
        }

        .payment-section {
            margin-top: 2rem;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }

        .payment-method {
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
        }

        .payment-method.selected {
            border-color: #2ecc71;
            background: #f0fff0;
        }

        .confirm-btn {
            width: 100%;
            padding: 1rem;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            margin-top: 1rem;
        }

        .confirm-btn:hover {
            background: #27ae60;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
        }

        .user-icon {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: background-color 0.3s;
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
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Car Rental System</div>
            <div class="user-info">
                <div class="user-icon" onclick="toggleDropdown()">
                    <i class="fas fa-user-circle"></i>
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></span>
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
            </div>
        </nav>
    </header>

    <main>
        <div class="checkout-container">
            <h1>Checkout</h1>
            
            <?php if (isset($_SESSION['booking_error'])): ?>
                <div class="error-message" style="color: red; margin-bottom: 20px; padding: 10px; background: #ffe6e6; border-radius: 5px;">
                    <?php 
                        echo htmlspecialchars($_SESSION['booking_error']);
                        unset($_SESSION['booking_error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="booking-summary">
                <h2>Booking Summary</h2>
                <div class="car-info">
                    <div class="car-image">
                        <img src="<?php echo htmlspecialchars($car['image_url'] ?? ''); ?>" 
                             alt="<?php echo htmlspecialchars(($car['brand'] ?? '') . ' ' . ($car['model'] ?? '')); ?>">
                    </div>
                    <div class="car-details">
                        <h3><?php echo htmlspecialchars(($car['brand'] ?? '') . ' ' . ($car['model'] ?? '')); ?></h3>
                        <p>Rental Period: <?php echo date('M d, Y', strtotime($booking['start_date'] ?? '')); ?> - 
                                         <?php echo date('M d, Y', strtotime($booking['end_date'] ?? '')); ?></p>
                        <p>Duration: <?php echo $booking['days'] ?? 0; ?> days</p>
                    </div>
                </div>
            </div>

            <div class="cost-breakdown">
                <h3>Cost Breakdown</h3>
                <div class="cost-item">
                    <span>Daily Rate</span>
                    <span>$<?php echo number_format($car['daily_rate'] ?? 0, 2); ?></span>
                </div>
                <div class="cost-item">
                    <span>Number of Days</span>
                    <span><?php echo $booking['days'] ?? 0; ?></span>
                </div>
                <div class="cost-item">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($booking['subtotal'] ?? 0, 2); ?></span>
                </div>
                <div class="cost-item">
                    <span>Discount</span>
                    <span id="discount-amount">$0.00</span>
                </div>
                <div class="cost-item total-cost">
                    <span>Total Amount</span>
                    <span id="total-amount">$<?php echo number_format($booking['subtotal'], 2); ?></span>
                </div>
            </div>

            <div class="promo-section">
                <h3>Promo Code</h3>
                <form class="promo-form" id="promo-form">
                    <input type="text" id="promo-code" placeholder="Enter promo code">
                    <button type="button" onclick="applyPromoCode()">Apply</button>
                </form>
                <p id="promo-message"></p>
            </div>

            <div class="payment-section">
                <h3>Payment Method</h3>
                <div class="payment-methods">
                    <div class="payment-method" onclick="selectPaymentMethod(this)">
                        <h4>Credit Card</h4>
                        <p>Pay with credit card</p>
                    </div>
                    <div class="payment-method" onclick="selectPaymentMethod(this)">
                        <h4>PayPal</h4>
                        <p>Pay with PayPal</p>
                    </div>
                </div>

                <form action="../controller/process_booking.php" method="POST" id="checkout-form">
                    <input type="hidden" name="payment_method" id="payment-method">
                    <input type="hidden" name="promo_code" id="promo-code-input">
                    <button type="submit" class="confirm-btn">Confirm and Pay</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        let selectedPaymentMethod = null;
        let appliedPromoCode = null;
        let discountAmount = 0;

        function selectPaymentMethod(element) {
            // Remove selected class from all payment methods
            document.querySelectorAll('.payment-method').forEach(method => {
                method.classList.remove('selected');
            });
            
            // Add selected class to clicked payment method
            element.classList.add('selected');
            selectedPaymentMethod = element.querySelector('h4').textContent;
            document.getElementById('payment-method').value = selectedPaymentMethod;
        }

        function applyPromoCode() {
            const promoCode = document.getElementById('promo-code').value;
            
            // Here you would typically make an AJAX call to validate the promo code
            // For now, we'll use a simple example
            if (promoCode === 'WELCOME10') {
                discountAmount = <?php echo $booking['subtotal']; ?> * 0.1; // 10% discount
                document.getElementById('discount-amount').textContent = '-$' + discountAmount.toFixed(2);
                document.getElementById('total-amount').textContent = 
                    '$' + (<?php echo $booking['subtotal']; ?> - discountAmount).toFixed(2);
                document.getElementById('promo-message').textContent = 'Promo code applied successfully!';
                document.getElementById('promo-message').style.color = '#2ecc71';
                appliedPromoCode = promoCode;
                document.getElementById('promo-code-input').value = promoCode;
            } else {
                document.getElementById('promo-message').textContent = 'Invalid promo code';
                document.getElementById('promo-message').style.color = '#e74c3c';
            }
        }

        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            if (!selectedPaymentMethod) {
                alert('Please select a payment method');
                return;
            }
            this.submit();
        });

        // Add this new function for the dropdown
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.user-icon') && !event.target.matches('.user-icon *')) {
                const dropdown = document.getElementById('userDropdown');
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        }
    </script>
</body>
</html> 