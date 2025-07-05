<?php
$host = "localhost";
$port = "3307";
$user = "root";
$pass = "";
$db = "fitness_tracker_db";

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
