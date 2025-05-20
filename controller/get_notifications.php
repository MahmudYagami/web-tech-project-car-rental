<?php
session_start();
require_once '../model/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

// Get unread notifications count
$count_sql = "SELECT COUNT(*) as unread_count FROM notifications WHERE is_read = FALSE";
$count_result = mysqli_query($conn, $count_sql);
$unread_count = mysqli_fetch_assoc($count_result)['unread_count'];

// Get new notifications
$sql = "SELECT n.*, b.booking_id, b.start_date, b.end_date, u.first_name, u.last_name, c.model 
        FROM notifications n 
        LEFT JOIN bookings b ON n.booking_id = b.booking_id 
        LEFT JOIN users u ON b.user_id = u.user_id 
        LEFT JOIN cars c ON b.car_id = c.car_id 
        WHERE n.id > ? 
        ORDER BY n.created_at DESC 
        LIMIT 10";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $last_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$notifications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = [
        'id' => $row['id'],
        'message' => $row['message'],
        'is_read' => (bool)$row['is_read'],
        'created_at' => $row['created_at'],
        'booking_id' => $row['booking_id']
    ];
}

echo json_encode([
    'notifications' => $notifications,
    'unread_count' => $unread_count
]);

mysqli_close($conn);
?> 