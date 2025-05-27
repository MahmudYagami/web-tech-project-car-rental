<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if we have the profile data in session
if (!isset($_SESSION['edit_profile_data'])) {
    header("Location: ../controller/edit_profile_controller.php");
    exit();
}

// Get data from session
$user = $_SESSION['edit_profile_data']['user'];
$preferences = $_SESSION['edit_profile_data']['preferences'];

// Clear the session data after retrieving it
unset($_SESSION['edit_profile_data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .profile-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #666;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .btn-secondary {
            background-color: #666;
        }

        .btn-secondary:hover {
            background-color: #555;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-section">
            <h2>Edit Profile</h2>
            <form action="../controller/update_profile.php" method="POST">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile</label>
                    <input type="tel" id="mobile" name="mobile" value="<?php echo htmlspecialchars($user['mobile'] ?? ''); ?>" pattern="[0-9]{11}">
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn">Save Changes</button>
                    <a href="customer_profile.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <div class="profile-section">
            <h2>Car Preferences</h2>
            <form action="../controller/update_preferences.php" method="POST">
                <div class="form-group">
                    <label for="seat_position">Seat Position</label>
                    <select id="seat_position" name="seat_position">
                        <option value="">Select position</option>
                        <option value="front" <?php echo ($preferences['seat_position'] ?? '') === 'front' ? 'selected' : ''; ?>>Front</option>
                        <option value="middle" <?php echo ($preferences['seat_position'] ?? '') === 'middle' ? 'selected' : ''; ?>>Middle</option>
                        <option value="rear" <?php echo ($preferences['seat_position'] ?? '') === 'rear' ? 'selected' : ''; ?>>Rear</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="mirror_position">Mirror Position</label>
                    <input type="text" id="mirror_position" name="mirror_position" value="<?php echo htmlspecialchars($preferences['mirror_position'] ?? ''); ?>" placeholder="e.g., 45° inward">
                </div>

                <div class="form-group">
                    <label for="preferred_car_type">Preferred Car Type</label>
                    <select id="preferred_car_type" name="preferred_car_type">
                        <option value="">Select type</option>
                        <option value="sedan" <?php echo ($preferences['preferred_car_type'] ?? '') === 'sedan' ? 'selected' : ''; ?>>Sedan</option>
                        <option value="suv" <?php echo ($preferences['preferred_car_type'] ?? '') === 'suv' ? 'selected' : ''; ?>>SUV</option>
                        <option value="sports" <?php echo ($preferences['preferred_car_type'] ?? '') === 'sports' ? 'selected' : ''; ?>>Sports</option>
                        <option value="luxury" <?php echo ($preferences['preferred_car_type'] ?? '') === 'luxury' ? 'selected' : ''; ?>>Luxury</option>
                    </select>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn">Save Preferences</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
