<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    die("Login required.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $file_name = basename($_FILES["audio_file"]["name"]);
    $upload_dir = "uploads/music/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $target_file = $upload_dir . time() . "_" . $file_name;

    if (move_uploaded_file($_FILES["audio_file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO user_music (user_id, file_name, file_path, uploaded_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $file_name, $target_file);
        $stmt->execute();

        header("Location: dashboard.php?upload=success");
        exit;
    } else {
        echo "Error uploading file.";
    }
}
?>
