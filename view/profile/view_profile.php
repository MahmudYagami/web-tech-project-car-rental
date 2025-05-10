<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Profile</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
      background: #f4f4f4;
    }
    .profile-container {
      max-width: 600px;
      margin: auto;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    .profile-pic {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 15px;
    }
    .profile-info {
      text-align: left;
      margin-top: 20px;
    }
    .btn {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #007bff;
      border: none;
      color: white;
      border-radius: 4px;
      text-decoration: none;
      display: inline-block;
    }
    .btn:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

<div class="profile-container">
  <h2>Profile Details</h2>
  <img src="../../assets/images/default-avatar.png" alt="Profile Picture" class="profile-pic">

  <div class="profile-info">
    <p><strong>Full Name:</strong> John Doe</p>
    <p><strong>Email:</strong> john@example.com</p>
    <!-- Add other fields if needed -->
  </div>

  <a href="edit_profile.php" class="btn">Edit Profile</a>
</div>

</body>
</html>
