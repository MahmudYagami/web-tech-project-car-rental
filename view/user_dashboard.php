<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="../../assets/css/dashboard_style.css">
  <style>
    /* dashboard_style.css */

body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f9f9f9;
}

.dashboard-container {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 250px;
  background: #fff;
  box-shadow: 2px 0 5px rgba(0,0,0,0.1);
  padding-top: 30px;
}

.sidebar ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar li {
  padding: 15px 30px;
  cursor: pointer;
  color: #333;
  font-weight: 500;
}

.sidebar li.active, .sidebar li:hover {
  background-color: #e8f4f9;
  color: #007bff;
}

.main-content {
  flex: 1;
  padding: 40px;
  background-color: #fff;
}

.breadcrumb {
  font-size: 14px;
  color: #888;
  margin-bottom: 20px;
}

.profile-section {
  background-color: #fff;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.profile-section h2 {
  font-size: 24px;
  margin-bottom: 25px;
}

.profile-card {
  display: flex;
  align-items: center;
}

.avatar-container {
  position: relative;
  width: 120px;
  height: 120px;
  margin-right: 40px;
}

.avatar-container img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  border: 3px solid #ccc;
}

.edit-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  background: #fff;
  border: 2px solid #007bff;
  color: #007bff;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  cursor: pointer;
  font-size: 16px;
  font-weight: bold;
}

.info-fields {
  font-size: 16px;
  line-height: 2.2;
}

  </style>
</head>
<body>
  <div class="dashboard-container">
    <nav class="sidebar">
      <ul>
        <li class="active">My Account</li>
        <li>My Addresses</li>
        <li>My Offers</li>
        <li>My Promotions</li>
        <li>Free Services</li>
        <li>Sheba Credit</li>
      </ul>
    </nav>

    <div class="main-content">
      <div class="breadcrumb">Home &gt; <span>My Account</span></div>
      <div class="profile-section">
        <h2>Personal Info</h2>
        <div class="profile-card">
          <div class="avatar-container">
            <img src="../../assets/images/avatar-placeholder.png" alt="Avatar">
            <button class="edit-btn">✎</button>
          </div>
          <div class="info-fields">
            <div><strong>Name:</strong> Akid Mahmud</div>
            <div><strong>Phone:</strong> +8801840193060</div>
            <div><strong>Email:</strong> N/A</div>
            <div><strong>Date of Birth:</strong> N/A</div>
            <div><strong>Gender:</strong> N/A</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
