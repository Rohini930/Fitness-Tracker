<?php
session_start();
include('db.php');  // your database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please login to view this page.");
}

$user_id = $_SESSION['user_id'];

// Prepare and execute query to get pending friend requests sent by this user
$sql = "SELECT u.user_id, u.name 
        FROM friend_requests fr
        JOIN users u ON fr.receiver_id = u.user_id
        WHERE fr.sender_id = ? AND fr.status = 'pending'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Friend Requests Sent (Pending)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #222;
            color: #fff;
            padding: 20px;
        }
        h3 {
            color: #4CAF50;
        }
        ul {
            list-style-type: disc;
            padding-left: 20px;
        }
        li {
            padding: 5px 0;
        }
        p {
            font-style: italic;
            color: #ccc;
        }
    </style>
</head>
<body>

    <h3>Friend Requests Sent</h3>

    <?php
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($row['name']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No pending friend requests sent.</p>";
    }
    ?>

</body>
</html>
