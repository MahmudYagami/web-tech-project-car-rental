<?php
session_start();
require_once '../model/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_POST['notification_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Notification ID is required']);
    exit();
}

$notification_id = (int)$_POST['notification_id'];

// Mark notification as read
$sql = "UPDATE notifications SET is_read = TRUE WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $notification_id);
mysqli_stmt_execute($stmt);

// Get updated unread count
$count_sql = "SELECT COUNT(*) as unread_count FROM notifications WHERE is_read = FALSE";
$count_result = mysqli_query($conn, $count_sql);
$unread_count = mysqli_fetch_assoc($count_result)['unread_count'];

echo json_encode([
    'success' => true,
    'unread_count' => $unread_count
]);

mysqli_close($conn);
?> 