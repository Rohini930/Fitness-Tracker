<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_FILES['profile_pic'])) {
    $filename = $_FILES['profile_pic']['name'];
    $tmpname = $_FILES['profile_pic']['tmp_name'];
    $destination = 'uploads/' . $filename;

    if (move_uploaded_file($tmpname, $destination)) {
        $user_id = $_SESSION['user_id'];
        $query = "UPDATE users SET profile_pic = '$filename' WHERE id = $user_id";
        $conn->query($query);
    }
}

header('Location: dashboard.php');
exit();
?>