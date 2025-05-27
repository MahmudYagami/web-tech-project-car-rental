<?php
require_once 'db.php';

function getAllUsers($conn) {
    $query = "SELECT * FROM users ORDER BY user_id DESC";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function searchUsers($conn, $searchTerm) {
    $searchTerm = "%" . mysqli_real_escape_string($conn, $searchTerm) . "%";
    $query = "SELECT * FROM users 
              WHERE first_name LIKE ? 
              OR last_name LIKE ? 
              OR email LIKE ? 
              ORDER BY user_id DESC";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $searchTerm, $searchTerm, $searchTerm);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function updateUserRole($conn, $userId, $role) {
    $query = "UPDATE users SET role = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $role, $userId);
    return mysqli_stmt_execute($stmt);
}

function deleteUser($conn, $userId) {
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    return mysqli_stmt_execute($stmt);
}

function createUser($conn, $email, $password, $firstName, $lastName, $role) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (email, password, first_name, last_name, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssss", $email, $hashedPassword, $firstName, $lastName, $role);
    return mysqli_stmt_execute($stmt);
} 