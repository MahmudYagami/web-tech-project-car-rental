<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Book Your Ride</title>
  <link rel="stylesheet" href="booking.css" />
  <style>
    body {
  font-family: Arial, sans-serif;
  background-color: #f8f8f8;
  margin: 0;
  padding: 0;
}

header {
  background: #2d2d2d;
  color: white;
  padding: 20px;
  text-align: center;
}

.booking-form {
  max-width: 500px;
  margin: 30px auto;
  padding: 25px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.booking-form label {
  display: block;
  margin-top: 15px;
  font-weight: bold;
}

.booking-form input {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  box-sizing: border-box;
}

.rate-calendar {
  margin-top: 20px;
  font-size: 18px;
}

.confirm-btn {
  margin-top: 20px;
  padding: 12px;
  width: 100%;
  background: #007bff;
  color: white;
  border: none;
  font-size: 16px;
  border-radius: 5px;
  cursor: pointer;
}

.confirm-btn:hover {
  background: #0056b3;
}

footer {
  background: #2d2d2d;
  color: white;
  text-align: center;
  padding: 15px;
}

    .buttons {
  margin-top: 20px;
  display: flex;
  gap: 15px;
}

.calc-btn {
  background: #28a745;
  color: white;
  padding: 12px;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
}

.calc-btn:hover {
  background: #218838;
}

.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  padding-top: 80px;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.5);
}

.modal-content {
  background-color: #fff;
  margin: auto;
  padding: 25px;
  border-radius: 10px;
  width: 400px;
  max-width: 90%;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.modal-content label {
  display: block;
  margin-top: 15px;
  font-weight: bold;
}

.modal-content input {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
}

.close {
  float: right;
  font-size: 24px;
  font-weight: bold;
  color: #aaa;
  cursor: pointer;
}

.breakdown p {
  margin: 8px 0;
  font-size: 16px;
}

  </style>
</head>
<body>
  <header>
    <h1>Book Your Vehicle</h1>
  </header>

  <main class="booking-form">
    <form>
      <label for="pickupDate">Pickup Date & Time:</label>
      <input type="datetime-local" id="pickupDate" name="pickupDate" required />

      <label for="returnDate">Return Date & Time:</label>
      <input type="datetime-local" id="returnDate" name="returnDate" required />

      <div class="rate-calendar">
        <h3>Estimated Total: <span id="priceEstimate">$0.00</span></h3>
      </div>

      <div class="buttons">
        <button type="submit" class="confirm-btn">Reserve Now</button>
        <button type="button" class="calc-btn" onclick="openCalculator()">Pricing Calculator</button>
      </div>
    </form>
  </main>

  <!-- Pricing Calculator Modal -->
  <div id="calcModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeCalculator()">&times;</span>
      <h2>Pricing Calculator</h2>

      <label for="calcPickup">Pickup Date:</label>
      <input type="date" id="calcPickup" />

      <label for="calcReturn">Return Date:</label>
      <input type="date" id="calcReturn" />

      <label for="promo">Promo Code:</label>
      <input type="text" id="promo" placeholder="Enter Code (e.g., SAVE10)" />

      <div class="breakdown">
        <p>Base Rate (per day): $<span id="baseRate">50</span></p>
        <p>Rental Days: <span id="rentalDays">0</span></p>
        <p>Subtotal: $<span id="subtotal">0</span></p>
        <p>Taxes & Fees (15%): $<span id="taxes">0</span></p>
        <p>Discount: -$<span id="discount">0</span></p>
        <hr />
        <p><strong>Total: $<span id="finalTotal">0</span></strong></p>
      </div>
    </div>
  </div>

  <footer>
    <p>© 2025 CarRental Inc.</p>
  </footer>

  <script>
    function openCalculator() {
      document.getElementById("calcModal").style.display = "block";
    }

    function closeCalculator() {
      document.getElementById("calcModal").style.display = "none";
    }

    function calculateQuote() {
      const baseRate = 50;
      const pickup = new Date(document.getElementById("calcPickup").value);
      const returnDate = new Date(document.getElementById("calcReturn").value);
      const promo = document.getElementById("promo").value.toLowerCase();

      let days = Math.ceil((returnDate - pickup) / (1000 * 60 * 60 * 24));
      if (isNaN(days) || days < 1) days = 0;

      const subtotal = baseRate * days;
      const taxes = subtotal * 0.15;
      let discount = 0;
      if (promo === "save10") discount = 10;
      const total = subtotal + taxes - discount;

      document.getElementById("rentalDays").textContent = days;
      document.getElementById("subtotal").textContent = subtotal.toFixed(2);
      document.getElementById("taxes").textContent = taxes.toFixed(2);
      document.getElementById("discount").textContent = discount.toFixed(2);
      document.getElementById("finalTotal").textContent = total.toFixed(2);
    }

    document.getElementById("calcPickup").addEventListener("change", calculateQuote);
    document.getElementById("calcReturn").addEventListener("change", calculateQuote);
    document.getElementById("promo").addEventListener("input", calculateQuote);
  </script>
</body>
</html>
