<?php
require_once 'db.php';

function getAvailableVehicles($conn) {
    // Prepare the statement
    $query = "SELECT * FROM cars WHERE status = 'available'";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Error preparing statement: ' . mysqli_error($conn)];
    }
    
    // Execute the statement
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Error executing query: ' . mysqli_error($conn)];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $vehicles = [];
    
    // Fetch all vehicles
    while ($row = mysqli_fetch_assoc($result)) {
        $vehicles[] = [
            'id' => $row['id'],
            'model' => $row['model'],
            'brand' => $row['brand'],
            'year' => $row['year'],
            'price_per_day' => $row['price_per_day'],
            'status' => $row['status'],
            'image' => $row['image']
        ];
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => true, 'data' => $vehicles];
}

function getAllCars($conn) {
    $sql = "SELECT * FROM cars ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        return ['success' => false, 'message' => 'Failed to fetch cars', 'data' => []];
    }
    
    $cars = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return ['success' => true, 'data' => $cars];
}

function searchCars($conn, $search = '', $filter = 'all') {
    // Base query
    $query = "SELECT * FROM cars WHERE status = 'available'";
    $params = [];
    $types = "";

    // Add search conditions if search term is provided
    if (!empty($search)) {
        $searchTerm = "%$search%";
        
        switch ($filter) {
            case 'brand':
                $query .= " AND brand LIKE ?";
                $params[] = $searchTerm;
                $types .= "s";
                break;
            case 'model':
                $query .= " AND model LIKE ?";
                $params[] = $searchTerm;
                $types .= "s";
                break;
            case 'transmission':
                $query .= " AND transmission LIKE ?";
                $params[] = $searchTerm;
                $types .= "s";
                break;
            case 'fuel_type':
                $query .= " AND fuel_type LIKE ?";
                $params[] = $searchTerm;
                $types .= "s";
                break;
            default:
                $query .= " AND (brand LIKE ? OR model LIKE ? OR transmission LIKE ? OR fuel_type LIKE ?)";
                $params = array_fill(0, 4, $searchTerm);
                $types .= "ssss";
                break;
        }
    }

    // Prepare statement
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        error_log("Failed to prepare statement: " . mysqli_error($conn));
        return ['success' => false, 'message' => 'Failed to prepare query'];
    }

    // Bind parameters if we have any
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    // Execute the query
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Failed to execute statement: " . mysqli_error($conn));
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to execute query'];
    }

    // Get results
    $result = mysqli_stmt_get_result($stmt);
    $cars = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $cars[] = $row;
    }

    mysqli_stmt_close($stmt);
    return ['success' => true, 'data' => $cars];
}

function getUserName($conn, $user_id) {
    $sql = "SELECT first_name, last_name FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Failed to prepare query'];
    }
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to execute query'];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $userName = $row['first_name'] . ' ' . $row['last_name'];
        mysqli_stmt_close($stmt);
        return ['success' => true, 'data' => $userName];
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => false, 'message' => 'User not found'];
}

function addCar($conn, $carData) {
    $sql = "INSERT INTO cars (brand, model, year, color, transmission, fuel_type, seats, 
            mileage, daily_rate, description, status, image_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available', ?)";
            
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return ['success' => false, 'message' => 'Failed to prepare statement'];
    }
    
    mysqli_stmt_bind_param($stmt, "ssisssiidss", 
        $carData['brand'], 
        $carData['model'], 
        $carData['year'], 
        $carData['color'], 
        $carData['transmission'], 
        $carData['fuel_type'], 
        $carData['seats'], 
        $carData['mileage'], 
        $carData['daily_rate'], 
        $carData['description'],
        $carData['image_url']
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to add car'];
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => true, 'message' => 'Car added successfully'];
}

function updateCar($conn, $carData) {
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
    if (!$stmt) {
        return ['success' => false, 'message' => 'Failed to prepare statement'];
    }
    
    mysqli_stmt_bind_param($stmt, "ssisssiidsssi", 
        $carData['brand'], 
        $carData['model'], 
        $carData['year'], 
        $carData['color'], 
        $carData['transmission'], 
        $carData['fuel_type'], 
        $carData['seats'], 
        $carData['mileage'], 
        $carData['daily_rate'], 
        $carData['description'],
        $carData['image_url'],
        $carData['status'],
        $carData['car_id']
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to update car'];
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => true, 'message' => 'Car updated successfully'];
}

function deleteCar($conn, $carId) {
    $sql = "DELETE FROM cars WHERE car_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Failed to prepare statement'];
    }
    
    mysqli_stmt_bind_param($stmt, "i", $carId);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Failed to delete car'];
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => true, 'message' => 'Car deleted successfully'];
}

function validateCarData($carData) {
    $required_fields = ['brand', 'model', 'year', 'color', 'transmission', 
                       'fuel_type', 'seats', 'mileage', 'daily_rate', 'description'];
    
    foreach ($required_fields as $field) {
        if (!isset($carData[$field]) || empty($carData[$field])) {
            return ['success' => false, 'message' => ucfirst($field) . ' is required'];
        }
    }
    
    if (!is_numeric($carData['year']) || $carData['year'] < 1900 || $carData['year'] > date('Y') + 1) {
        return ['success' => false, 'message' => 'Invalid year'];
    }
    
    if (!is_numeric($carData['seats']) || $carData['seats'] < 1) {
        return ['success' => false, 'message' => 'Invalid number of seats'];
    }
    
    if (!is_numeric($carData['mileage']) || $carData['mileage'] < 0) {
        return ['success' => false, 'message' => 'Invalid mileage'];
    }
    
    if (!is_numeric($carData['daily_rate']) || $carData['daily_rate'] <= 0) {
        return ['success' => false, 'message' => 'Invalid daily rate'];
    }
    
    return ['success' => true];
}

function getCarForBooking($conn, $car_id) {
    // Prepare the statement
    $query = "SELECT * FROM cars WHERE car_id = ? AND status = 'available'";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Error preparing statement: ' . mysqli_error($conn)];
    }
    
    // Bind the car ID parameter
    mysqli_stmt_bind_param($stmt, "i", $car_id);
    
    // Execute the statement
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Error executing query: ' . mysqli_error($conn)];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    
    // Check if car exists and is available
    if ($car = mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($stmt);
        return ['success' => true, 'data' => $car];
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => false, 'message' => 'Car not found or not available'];
}
?> 