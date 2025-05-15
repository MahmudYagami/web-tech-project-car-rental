<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carrentalsystem";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function closeConnection($conn) {
    mysqli_close($conn);
}
?>