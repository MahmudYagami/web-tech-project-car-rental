<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
      background: #f4f4f4;
    }
    .profile-container {
      max-width: 800px;
      margin: auto;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      border-bottom: 2px solid #ccc;
      padding-bottom: 10px;
    }
    form {
      margin-bottom: 30px;
    }
    label {
      display: block;
      margin-top: 10px;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .btn {
      margin-top: 15px;
      padding: 10px 20px;
      background-color: #007bff;
      border: none;
      color: white;
      border-radius: 4px;
      cursor: pointer;
    }
    .btn:hover {
      background-color: #0056b3;
    }
    .profile-pic {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>

<div class="profile-container">
  <h2>Edit Profile</h2>
  <img src="../../assets/images/default-avatar.png" alt="Profile Picture" class="profile-pic" id="avatarPreview">

  <!-- Edit Profile -->
  <form id="editProfileForm">
    <h3>Personal Details</h3>
    <label for="fullName">Full Name</label>
    <input type="text" id="fullName" name="fullName" value="John Doe">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="john@example.com">

    <button type="submit" class="btn">Save Changes</button>
  </form>

  <!-- Change Avatar -->
  <form id="changeAvatarForm" enctype="multipart/form-data">
    <h3>Change Profile Picture</h3>
    <label for="avatar">Upload New Avatar</label>
    <input type="file" id="avatar" name="avatar" accept="image/*">

    <button type="submit" class="btn">Set as Profile Picture</button>
  </form>

  <!-- Update Password -->
  <form id="updatePasswordForm">
    <h3>Update Password</h3>
    <label for="currentPassword">Current Password</label>
    <input type="password" id="currentPassword" name="currentPassword">

    <label for="newPassword">New Password</label>
    <input type="password" id="newPassword" name="newPassword">

    <label for="confirmPassword">Confirm New Password</label>
    <input type="password" id="confirmPassword" name="confirmPassword">

    <button type="submit" class="btn">Update Password</button>
  </form>

  <!-- Delete Profile -->
  <form id="deleteProfileForm">
    <h3>Delete Profile</h3>
    <p>This action is irreversible. Proceed with caution.</p>
    <button type="submit" class="btn" style="background-color: #dc3545;">Delete Profile</button>
  </form>
</div>

</body>
</html>
