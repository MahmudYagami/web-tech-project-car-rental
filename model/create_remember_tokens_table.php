<?php
require_once 'db.php';

// Create remember_tokens table
$sql = "CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_token (token)
)";

if (mysqli_query($conn, $sql)) {
    echo "Remember tokens table created successfully";
} else {
    echo "Error creating remember tokens table: " . mysqli_error($conn);
}

mysqli_close($conn);
?> 