<?php

require_once 'db.php';

$sql = "SELECT * FROM vehicles WHERE availability = 1";
$result = $conn->query($sql);

$vehicles = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($vehicles);
?>
