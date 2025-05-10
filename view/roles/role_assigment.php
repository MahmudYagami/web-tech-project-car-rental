<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users & Roles</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
      background-color: #f4f4f4;
    }
    .container {
      max-width: 1200px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    h2 {
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    th {
      background-color: #007bff;
      color: white;
    }
    select {
      padding: 5px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }
    .btn {
      padding: 8px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      color: #fff;
    }
    .btn-update {
      background-color: #28a745;
    }
    .btn-update:hover {
      background-color: #218838;
    }
    .btn-delete {
      background-color: #dc3545;
    }
    .btn-delete:hover {
      background-color: #c82333;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Manage Users & Roles</h2>

  <table>
    <thead>
      <tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Change Role</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <!-- Example User Row -->
      <tr>
        <td>1</td>
        <td>John Doe</td>
        <td>john@example.com</td>
        <td>User</td>
        <td>
          <select name="role">
            <option value="User" selected>User</option>
            <option value="Editor">Editor</option>
            <option value="Admin">Admin</option>
          </select>
          <button class="btn btn-update">Update</button>
        </td>
        <td>
          <button class="btn btn-delete">Delete</button>
        </td>
      </tr>

      <!-- More rows can be added dynamically with PHP/SQL later -->
    </tbody>
  </table>
</div>

</body>
</html>
