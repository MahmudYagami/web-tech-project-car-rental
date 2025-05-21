<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Get cars from session
$cars = $_SESSION['cars'] ?? [];
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

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php 
                echo htmlspecialchars($_SESSION['message']); 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h4>Add New Car</h4>
            </div>
            <div class="card-body">
                <form action="../controller/manage_cars_controller.php" method="POST" class="form-grid">
                    <input type="hidden" name="action" value="add_car">
                    
                    <div class="form-group">
                        <label for="brand">Brand</label>
                        <input type="text" id="brand" name="brand" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="model">Model</label>
                        <input type="text" id="model" name="model" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="year">Year</label>
                        <input type="number" id="year" name="year" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="color">Color</label>
                        <input type="text" id="color" name="color" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="transmission">Transmission</label>
                        <select id="transmission" name="transmission" class="form-control" required>
                            <option value="automatic">Automatic</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fuel_type">Fuel Type</label>
                        <select id="fuel_type" name="fuel_type" class="form-control" required>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="hybrid">Hybrid</option>
                            <option value="electric">Electric</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="seats">Number of Seats</label>
                        <input type="number" id="seats" name="seats" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="mileage">Mileage</label>
                        <input type="number" id="mileage" name="mileage" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="daily_rate">Daily Rate ($)</label>
                        <input type="number" id="daily_rate" name="daily_rate" step="0.01" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="image_url">Image URL</label>
                        <input type="text" id="image_url" name="image_url" class="form-control" required>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" required></textarea>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <button type="submit" class="btn btn-primary">Add Car</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Car Inventory</h4>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
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
                                    <td><?php echo htmlspecialchars($car['car_id']); ?></td>
                                    <td><?php echo htmlspecialchars($car['brand']); ?></td>
                                    <td><?php echo htmlspecialchars($car['model']); ?></td>
                                    <td><?php echo htmlspecialchars($car['year']); ?></td>
                                    <td><?php echo htmlspecialchars($car['color']); ?></td>
                                    <td><?php echo htmlspecialchars($car['transmission']); ?></td>
                                    <td><?php echo htmlspecialchars($car['fuel_type']); ?></td>
                                    <td><?php echo htmlspecialchars($car['seats']); ?></td>
                                    <td><?php echo htmlspecialchars($car['mileage']); ?></td>
                                    <td>$<?php echo htmlspecialchars(number_format($car['daily_rate'], 2)); ?></td>
                                    <td>
                                        <select class="form-control status-select" 
                                                onchange="updateCarStatus(<?php echo $car['car_id']; ?>, this.value)">
                                            <option value="available" <?php echo $car['status'] === 'available' ? 'selected' : ''; ?>>
                                                Available
                                            </option>
                                            <option value="maintenance" <?php echo $car['status'] === 'maintenance' ? 'selected' : ''; ?>>
                                                Maintenance
                                            </option>
                                            <option value="booked" <?php echo $car['status'] === 'booked' ? 'selected' : ''; ?>>
                                                Booked
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <button onclick="editCar(<?php echo htmlspecialchars(json_encode($car)); ?>)" 
                                                class="btn btn-primary">Edit</button>
                                        <button onclick="deleteCar(<?php echo $car['car_id']; ?>)" 
                                                class="btn btn-danger">Delete</button>
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
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Car</h2>
            <form id="editForm" action="../controller/manage_cars_controller.php" method="POST">
                <input type="hidden" name="action" value="update_car">
                <input type="hidden" name="car_id" id="edit_car_id">
                
                <div class="form-grid">
                    <!-- Same fields as add car form -->
                    <div class="form-group">
                        <label for="edit_brand">Brand</label>
                        <input type="text" id="edit_brand" name="brand" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_model">Model</label>
                        <input type="text" id="edit_model" name="model" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_year">Year</label>
                        <input type="number" id="edit_year" name="year" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_color">Color</label>
                        <input type="text" id="edit_color" name="color" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_transmission">Transmission</label>
                        <select id="edit_transmission" name="transmission" class="form-control" required>
                            <option value="automatic">Automatic</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_fuel_type">Fuel Type</label>
                        <select id="edit_fuel_type" name="fuel_type" class="form-control" required>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="hybrid">Hybrid</option>
                            <option value="electric">Electric</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_seats">Number of Seats</label>
                        <input type="number" id="edit_seats" name="seats" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_mileage">Mileage</label>
                        <input type="number" id="edit_mileage" name="mileage" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_daily_rate">Daily Rate ($)</label>
                        <input type="number" id="edit_daily_rate" name="daily_rate" step="0.01" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_image_url">Image URL</label>
                        <input type="text" id="edit_image_url" name="image_url" class="form-control" required>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="edit_description">Description</label>
                        <textarea id="edit_description" name="description" class="form-control" required></textarea>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <button type="submit" class="btn btn-primary">Update Car</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Get the modal
        const modal = document.getElementById('editModal');
        const span = document.getElementsByClassName('close')[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = 'none';
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        function editCar(car) {
            // Populate the edit form with car data
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
            document.getElementById('edit_image_url').value = car.image_url;
            document.getElementById('edit_description').value = car.description;

            // Show the modal
            modal.style.display = 'block';
        }

        function deleteCar(carId) {
            if (confirm('Are you sure you want to delete this car?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../controller/manage_cars_controller.php';

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_car';

                const carIdInput = document.createElement('input');
                carIdInput.type = 'hidden';
                carIdInput.name = 'car_id';
                carIdInput.value = carId;

                form.appendChild(actionInput);
                form.appendChild(carIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function updateCarStatus(carId, status) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../controller/manage_cars_controller.php';

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'update_car';

            const carIdInput = document.createElement('input');
            carIdInput.type = 'hidden';
            carIdInput.name = 'car_id';
            carIdInput.value = carId;

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;

            form.appendChild(actionInput);
            form.appendChild(carIdInput);
            form.appendChild(statusInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html> 