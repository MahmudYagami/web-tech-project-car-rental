<?php
require_once 'db.php';

// Create notifications table
$sql = "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    booking_id INT,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $sql)) {
    echo "Notifications table created successfully";
} else {
    echo "Error creating notifications table: " . mysqli_error($conn);
}

mysqli_close($conn);
?> 