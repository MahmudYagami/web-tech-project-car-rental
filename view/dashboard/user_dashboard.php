<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 40px;
    }
    .dashboard {
      max-width: 1000px;
      margin: auto;
    }
    .card {
      background: white;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .card h3 {
      margin-bottom: 10px;
    }
    .stats {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
    }
    .stat-box {
      flex: 1;
      min-width: 200px;
      margin: 10px;
      padding: 15px;
      background: #e0f0ff;
      border-left: 6px solid #007bff;
      border-radius: 5px;
    }
    .quick-actions a {
      display: inline-block;
      margin-right: 10px;
      padding: 10px 15px;
      background: #007bff;
      color: white;
      border-radius: 5px;
      text-decoration: none;
    }
    .quick-actions a:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>

<div class="dashboard">
  <h2>Welcome, [User Name]</h2>

  <div class="card stats">
    <div class="stat-box">
      <strong>Upcoming Bookings:</strong> 2
    </div>
    <div class="stat-box">
      <strong>Total Rentals:</strong> 7
    </div>
    <div class="stat-box">
      <strong>Outstanding Payments:</strong> $150
    </div>
  </div>

  <div class="card">
    <h3>Quick Actions</h3>
    <div class="quick-actions">
      <a href="../booking/book_car.php">Rent a Car</a>
      <a href="../profile/view_profile.php">View Profile</a>
      <a href="../history/rental_history.php">Rental History</a>
    </div>
  </div>
</div>

</body>
</html>
