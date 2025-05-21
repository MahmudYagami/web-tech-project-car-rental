<?php
function verifyRememberToken($conn, $token) {
    $sql = "SELECT u.*, rt.token 
            FROM users u 
            JOIN remember_tokens rt ON u.user_id = rt.user_id 
            WHERE rt.token = ? AND rt.expires_at > NOW()";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $user;
}

function updateRememberToken($conn, $userId, $oldToken) {
    $newToken = $userId . '_' . time();
    $update_sql = "UPDATE remember_tokens SET token = ?, expires_at = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE token = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "ss", $newToken, $oldToken);
    $success = mysqli_stmt_execute($update_stmt);
    mysqli_stmt_close($update_stmt);
    
    if ($success) {
        return $newToken;
    }
    return false;
}
?> 