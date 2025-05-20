<?php
session_start();
require_once '../model/db.php';

class CarInventoryController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllCars() {
        $query = "SELECT * FROM cars WHERE status = 'available' ORDER BY created_at DESC";
        $result = mysqli_query($this->conn, $query);
        $cars = array();
        
        while ($row = mysqli_fetch_assoc($result)) {
            $cars[] = $row;
        }
        
        return $cars;
    }

    public function getUsername() {
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $query = "SELECT first_name, last_name FROM users WHERE id = '$userId'";
            $result = mysqli_query($this->conn, $query);
            if ($user = mysqli_fetch_assoc($result)) {
                return $user['first_name'] . ' ' . $user['last_name'];
            }
        }
        return 'Guest';
    }
}

// Initialize controller
$controller = new CarInventoryController($conn);

// Get cars data
$cars = $controller->getAllCars();
$username = $controller->getUsername();
?> 