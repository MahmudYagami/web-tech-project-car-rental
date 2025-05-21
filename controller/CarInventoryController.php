<?php
session_start();
require_once '../model/db.php';
require_once '../model/usermodel.php';

// Get data
$cars = getAllCars($conn);
$username = getUsername($conn);
?>