<?php
session_start();
require_once '../model/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_car':
                if (isset($_POST['brand']) && isset($_POST['model']) && isset($_POST['year']) && 
                    isset($_POST['color']) && isset($_POST['transmission']) && isset($_POST['fuel_type']) && 
                    isset($_POST['seats']) && isset($_POST['mileage']) && isset($_POST['daily_rate']) && 
                    isset($_POST['description'])) {
                    
                    $sql = "INSERT INTO cars (brand, model, year, color, transmission, fuel_type, seats, 
                            mileage, daily_rate, description, status, image_url) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available', ?)";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "ssisssiidss", 
                        $_POST['brand'], 
                        $_POST['model'], 
                        $_POST['year'], 
                        $_POST['color'], 
                        $_POST['transmission'], 
                        $_POST['fuel_type'], 
                        $_POST['seats'], 
                        $_POST['mileage'], 
                        $_POST['daily_rate'], 
                        $_POST['description'],
                        $_POST['image_url']
                    );
                    mysqli_stmt_execute($stmt);
                }
                break;
            
            case 'update_car':
                if (isset($_POST['car_id'])) {
                    $sql = "UPDATE cars SET 
                            brand = ?, 
                            model = ?, 
                            year = ?, 
                            color = ?, 
                            transmission = ?, 
                            fuel_type = ?, 
                            seats = ?, 
                            mileage = ?, 
                            daily_rate = ?, 
                            description = ?,
                            image_url = ?,
                            status = ?
                            WHERE car_id = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "ssisssiidsssi", 
                        $_POST['brand'], 
                        $_POST['model'], 
                        $_POST['year'], 
                        $_POST['color'], 
                        $_POST['transmission'], 
                        $_POST['fuel_type'], 
                        $_POST['seats'], 
                        $_POST['mileage'], 
                        $_POST['daily_rate'], 
                        $_POST['description'],
                        $_POST['image_url'],
                        $_POST['status'],
                        $_POST['car_id']
                    );
                    mysqli_stmt_execute($stmt);
                }
                break;
            
            case 'delete_car':
                if (isset($_POST['car_id'])) {
                    $sql = "DELETE FROM cars WHERE car_id = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $_POST['car_id']);
                    mysqli_stmt_execute($stmt);
                }
                break;
        }
    }
}

// Get all cars
$sql = "SELECT * FROM cars ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$cars = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cars - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h2 {
            color: #333;
        }

        .back-btn {
            background-color: #666;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .back-btn:hover {
            background-color: #555;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }

        .card-header h4 {
            color: #333;
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: #4CAF50;
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background-color: #45a049;
        }

        .btn-danger {
            background-color: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background-color: #da190b;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            color: #333;
        }

        .car-image {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-available {
            background-color: #4CAF50;
            color: white;
        }

        .status-booked {
            background-color: #ff9800;
            color: white;
        }

        .status-maintenance {
            background-color: #f44336;
            color: white;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            margin: 50px auto;
            padding: 20px;
            width: 90%;
            max-width: 800px;
            border-radius: 8px;
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h5 {
            margin: 0;
            color: #333;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #666;
        }

        .modal-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            text-align: right;
        }

        .modal-footer .btn {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Manage Cars</h2>
            <a href="admin_dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <!-- Add New Car Form -->
        <div class="card">
            <div class="card-header">
                <h4>Add New Car</h4>
            </div>
            <div class="card-body">
                <form method="POST" id="addCarForm">
                    <input type="hidden" name="action" value="add_car">
                    <div class="form-grid">
                        <div class="form-group">
                            <input type="text" class="form-control" name="brand" placeholder="Brand" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="model" placeholder="Model" required>
                        </div>
                        <div class="form-group">
                            <input type="number" class="form-control" name="year" placeholder="Year" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="color" placeholder="Color" required>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="transmission" required>
                                <option value="">Transmission</option>
                                <option value="Automatic">Automatic</option>
                                <option value="Manual">Manual</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="fuel_type" required>
                                <option value="">Fuel Type</option>
                                <option value="Petrol">Petrol</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Electric">Electric</option>
                                <option value="Hybrid">Hybrid</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="number" class="form-control" name="seats" placeholder="Seats" required>
                        </div>
                        <div class="form-group">
                            <input type="number" class="form-control" name="mileage" placeholder="Mileage" required>
                        </div>
                        <div class="form-group">
                            <input type="number" class="form-control" name="daily_rate" placeholder="Daily Rate" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="image_url" placeholder="Image URL" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="description" placeholder="Description" required></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Add Car</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cars Table -->
        <div class="card">
            <div class="card-header">
                <h4>Car List</h4>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Year</th>
                                <th>Color</th>
                                <th>Transmission</th>
                                <th>Fuel Type</th>
                                <th>Seats</th>
                                <th>Mileage</th>
                                <th>Daily Rate</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cars as $car): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($car['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>"
                                         class="car-image">
                                </td>
                                <td><?php echo htmlspecialchars($car['brand']); ?></td>
                                <td><?php echo htmlspecialchars($car['model']); ?></td>
                                <td><?php echo htmlspecialchars($car['year']); ?></td>
                                <td><?php echo htmlspecialchars($car['color']); ?></td>
                                <td><?php echo htmlspecialchars($car['transmission']); ?></td>
                                <td><?php echo htmlspecialchars($car['fuel_type']); ?></td>
                                <td><?php echo htmlspecialchars($car['seats']); ?></td>
                                <td><?php echo htmlspecialchars($car['mileage']); ?></td>
                                <td>$<?php echo htmlspecialchars($car['daily_rate']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $car['status']; ?>">
                                        <?php echo htmlspecialchars($car['status']); ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <button type="button" class="btn btn-primary" 
                                            onclick="editCar(<?php echo htmlspecialchars(json_encode($car)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this car?');">
                                        <input type="hidden" name="action" value="delete_car">
                                        <input type="hidden" name="car_id" value="<?php echo $car['car_id']; ?>">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Car Modal -->
    <div id="editCarModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Edit Car</h5>
                <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="editCarForm">
                    <input type="hidden" name="action" value="update_car">
                    <input type="hidden" name="car_id" id="edit_car_id">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Brand</label>
                            <input type="text" class="form-control" name="brand" id="edit_brand" required>
                        </div>
                        <div class="form-group">
                            <label>Model</label>
                            <input type="text" class="form-control" name="model" id="edit_model" required>
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <input type="number" class="form-control" name="year" id="edit_year" required>
                        </div>
                        <div class="form-group">
                            <label>Color</label>
                            <input type="text" class="form-control" name="color" id="edit_color" required>
                        </div>
                        <div class="form-group">
                            <label>Transmission</label>
                            <select class="form-control" name="transmission" id="edit_transmission" required>
                                <option value="Automatic">Automatic</option>
                                <option value="Manual">Manual</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fuel Type</label>
                            <select class="form-control" name="fuel_type" id="edit_fuel_type" required>
                                <option value="Petrol">Petrol</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Electric">Electric</option>
                                <option value="Hybrid">Hybrid</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Seats</label>
                            <input type="number" class="form-control" name="seats" id="edit_seats" required>
                        </div>
                        <div class="form-group">
                            <label>Mileage</label>
                            <input type="number" class="form-control" name="mileage" id="edit_mileage" required>
                        </div>
                        <div class="form-group">
                            <label>Daily Rate</label>
                            <input type="number" class="form-control" name="daily_rate" id="edit_daily_rate" required>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status" id="edit_status" required>
                                <option value="available">Available</option>
                                <option value="booked">Rented</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Image URL</label>
                            <input type="text" class="form-control" name="image_url" id="edit_image_url" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="edit_description" required></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Close</button>
                <button type="submit" form="editCarForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>

    <script>
        function editCar(car) {
            document.getElementById('edit_car_id').value = car.car_id;
            document.getElementById('edit_brand').value = car.brand;
            document.getElementById('edit_model').value = car.model;
            document.getElementById('edit_year').value = car.year;
            document.getElementById('edit_color').value = car.color;
            document.getElementById('edit_transmission').value = car.transmission;
            document.getElementById('edit_fuel_type').value = car.fuel_type;
            document.getElementById('edit_seats').value = car.seats;
            document.getElementById('edit_mileage').value = car.mileage;
            document.getElementById('edit_daily_rate').value = car.daily_rate;
            document.getElementById('edit_status').value = car.status;
            document.getElementById('edit_image_url').value = car.image_url;
            document.getElementById('edit_description').value = car.description;
            
            document.getElementById('editCarModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editCarModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editCarModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html> 