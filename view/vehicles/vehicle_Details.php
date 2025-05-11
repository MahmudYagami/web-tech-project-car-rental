<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vehicle Details</title>
  <link rel="stylesheet" href="details.css">
  <style>
    body {
  font-family: 'Segoe UI', sans-serif;
  background-color: #f4f4f4;
  margin: 0;
  padding: 0;
}

header {
  background-color: #444;
  color: #fff;
  padding: 20px;
  text-align: center;
}

.details-container {
  display: flex;
  flex-wrap: wrap;
  padding: 30px;
  gap: 30px;
  max-width: 1200px;
  margin: auto;
}

.image-section img {
  max-width: 500px;
  width: 100%;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.info-section {
  flex: 1;
  min-width: 300px;
}

.info-section h2 {
  font-size: 28px;
  margin-bottom: 15px;
}

.info-section p {
  font-size: 18px;
  margin: 10px 0;
}

footer {
  background: #222;
  color: white;
  text-align: center;
  padding: 15px;
}




    .actions {
  margin-top: 20px;
}

.book-btn, .actions button {
  padding: 12px 20px;
  margin-right: 10px;
  font-size: 16px;
  cursor: pointer;
  border: none;
  background-color: #007bff;
  color: white;
  border-radius: 5px;
  text-decoration: none;
}

.book-btn:hover, .actions button:hover {
  background-color: #0056b3;
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
  background-color: rgba(0,0,0,0.4);
}

.modal-content {
  background-color: white;
  margin: auto;
  padding: 25px;
  border-radius: 10px;
  width: 400px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.modal-content h2 {
  margin-top: 0;
}

.close {
  float: right;
  font-size: 28px;
  font-weight: bold;
  color: #aaa;
  cursor: pointer;
}

.breakdown p {
  margin: 8px 0;
}


  </style>
</head>
<body>
  <header>
    <h1>Vehicle Details</h1>
  </header>

  <main class="details-container">
   <div class="image-section">
      <img src="placeholder-car.jpg" alt="Vehicle Image" id="vehicleImage">
    </div>
    <div class="info-section">
      <h2 id="vehicleName">Vehicle Name</h2>
      <p><strong>Type:</strong> <span id="vehicleType">Type</span></p>
      <p><strong>Price per Day:</strong> <span id="vehiclePrice">$0</span></p>
      <p><strong>Status:</strong> <span id="vehicleStatus">Unavailable</span></p>
      <p><strong>Description:</strong> <span id="vehicleDesc">Vehicle details and specifications go here.</span></p>

      <!-- Booking Button -->
      <div class="actions">
        <a href="..\..\view\booking\booking.php" class="book-btn">Book Now</a>
      </div>
    </div>
  </main>

  <footer>
    <p>© 2025 CarRental Inc.</p>
  </footer>
</body>
</html>
