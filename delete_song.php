<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['song_id'])) {
    $song_id = intval($_POST['song_id']);

    // Delete file from disk
    $res = $conn->query("SELECT file_path FROM user_music WHERE id=$song_id AND user_id=$user_id");
    if ($res->num_rows > 0) {
        $song = $res->fetch_assoc();
        $file_path = $song['file_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete from DB
        $conn->query("DELETE FROM user_music WHERE id=$song_id AND user_id=$user_id");
    }
}

header("Location: dashboard.php");
exit();
?>
