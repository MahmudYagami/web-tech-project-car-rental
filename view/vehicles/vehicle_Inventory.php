<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vehicle Inventory</title>
  <link rel="stylesheet" href="inventory.css">
  <style>
    body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  background: #f9f9f9;
}

header {
  background: #333;
  color: white;
  padding: 20px;
  text-align: center;
}

.filters {
  margin-top: 15px;
  display: flex;
  justify-content: center;
  gap: 10px;
}

.filters input, .filters select {
  padding: 8px;
  font-size: 16px;
}

.inventory-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
  padding: 20px;
}

.vehicle-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  overflow: hidden;
  transition: transform 0.2s ease-in-out;
}

.vehicle-card:hover {
  transform: scale(1.02);
}

.vehicle-card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

.vehicle-info {
  padding: 15px;
}

.vehicle-info h2 {
  margin: 0 0 10px;
}

.vehicle-info p {
  margin: 5px 0;
}

.available {
  color: green;
  font-weight: bold;
}

footer {
  background: #222;
  color: white;
  text-align: center;
  padding: 15px;
}

  </style>
</head>
<body>
  <header>
    <h1>Fleet Gallery</h1>
    <div class="filters">
      <input type="text" placeholder="Search by model or type" id="searchBox">
      <select id="typeFilter">
        <option value="">All Types</option>
        <option value="SUV">SUV</option>
        <option value="Sedan">Sedan</option>
        <option value="Truck">Truck</option>
      </select>
      <select id="priceFilter">
        <option value="">Price Range</option>
        <option value="0-50">0 - 5000/day</option>
        <option value="51-100">5100 - 10000/day</option>
        <option value="101+">10100+/day</option>
      </select>
    </div>
  </header>

  <main class="inventory-grid">
    <!-- Sample Vehicle Card -->
    <div class="vehicle-card">
      <a href="vehicle_Details.php?id=1">
        <img src="placeholder-car.jpg" alt="Car Image">
        <div class="vehicle-info">
          <h2>Toyota Corolla</h2>
          <p>Type: Sedan</p>
          <p>Price: 4500/day</p>
          <p class="available">Available</p>
        </div>
      </a>
    </div>

    <!-- More vehicle cards will be dynamically loaded via PHP/JS -->
  </main>

  <footer>
    <p>© 2025 CarRental Inc.</p>
  </footer>
</body>
</html>
