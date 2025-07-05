<?php
include 'db.php';
session_start();
$user_id = $_SESSION['user_id'];

$result = $conn->query("SELECT * FROM workouts WHERE user_id = $user_id ORDER BY date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Workouts</title>
    <link rel="stylesheet" href="css/style.css"type="text/css" >
</head>


<body>
    <h2>Your Workouts</h2>
    <table border="1">
        <tr>
            <th>Date</th>
            <th>Exercise</th>
            <th>Duration (minutes)</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['date']; ?></td>
                <td><?php echo $row['exercise']; ?></td>
                <td><?php echo $row['duration']; ?></td>
            </tr>
        <?php } ?>
    </table>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
