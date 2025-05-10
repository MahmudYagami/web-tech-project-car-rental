<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 40px;
    }
    .dashboard {
      max-width: 1200px;
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
      min-width: 250px;
      margin: 10px;
      padding: 15px;
      background: #ffe0e0;
      border-left: 6px solid #dc3545;
      border-radius: 5px;
    }
    .quick-actions a {
      display: inline-block;
      margin-right: 10px;
      padding: 10px 15px;
      background: #dc3545;
      color: white;
      border-radius: 5px;
      text-decoration: none;
    }
    .quick-actions a:hover {
      background: #a92828;
    }
  </style>
</head>
<body>

<div class="dashboard">
  <h2>Admin Dashboard</h2>

  <div class="card stats">
    <div class="stat-box">
      <strong>Total Users:</strong> 150
    </div>
    <div class="stat-box">
      <strong>Total Bookings:</strong> 320
    </div>
    <div class="stat-box">
      <strong>Total Cars:</strong> 45
    </div>
    <div class="stat-box">
      <strong>Revenue This Month:</strong> $12,500
    </div>
  </div>

  <div class="card">
    <h3>Quick Actions</h3>
    <div class="quick-actions">
      <a href="..\roles\role_assigment.php">Manage Users</a>
      <a href="../cars/manage_cars.php">Manage Cars</a>
      <a href="../bookings/manage_bookings.php">Manage Bookings</a>
    </div>
  </div>
</div>

</body>
</html>
