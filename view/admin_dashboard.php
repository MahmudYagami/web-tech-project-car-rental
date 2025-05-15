<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="..\..\assests\css\dashboard_style.css">
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
    <div class="stat-box">
      <strong>Damege Report:</strong> 2
    </div>
  </div>

  <div class="card">
    <h3>Quick Actions</h3>
    <div class="quick-actions">
      <a href="..\roles\role_assigment.php">Manage Users</a>
      <a href="../cars/manage_cars.php">Manage Cars</a>
      <a href="../bookings/manage_bookings.php">Manage Bookings</a>
      <a href="../bookings/manage_bookings.php">Damege Report</a>
    </div>
  </div>
</div>

</body>
</html>
