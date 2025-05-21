<?php
session_start();
require_once '../model/db.php';
require_once '../model/vehicle_model.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../view/login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => 'Invalid action'];
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_car':
                // Validate car data
                $validation = validateCarData($_POST);
                if (!$validation['success']) {
                    $response = $validation;
                    break;
                }
                
                // Add car
                $response = addCar($conn, $_POST);
                break;
            
            case 'update_car':
                if (!isset($_POST['car_id'])) {
                    $response = ['success' => false, 'message' => 'Car ID is required'];
                    break;
                }
                
                // Validate car data
                $validation = validateCarData($_POST);
                if (!$validation['success']) {
                    $response = $validation;
                    break;
                }
                
                // Update car
                $response = updateCar($conn, $_POST);
                break;
            
            case 'delete_car':
                if (!isset($_POST['car_id'])) {
                    $response = ['success' => false, 'message' => 'Car ID is required'];
                    break;
                }
                
                // Delete car
                $response = deleteCar($conn, $_POST['car_id']);
                break;
        }
    }
    
    // If it's an AJAX request, return JSON response
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // For regular form submissions, set message in session and redirect
    $_SESSION['message'] = $response['message'];
    $_SESSION['message_type'] = $response['success'] ? 'success' : 'error';
    header('Location: ../view/manage_cars.php');
    exit();
}

// Get all cars for display
$cars_result = getAllCars($conn);
$_SESSION['cars'] = $cars_result['data'];

// For initial page load, redirect to view
header('Location: ../view/manage_cars.php');
exit();
?> 