<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['req_id'])) {
    $req_id = intval($_POST['req_id']);
    $conn->query("UPDATE friends SET status = 'accepted' WHERE id = $req_id");
}
header("Location: dashboard.php");
exit();
