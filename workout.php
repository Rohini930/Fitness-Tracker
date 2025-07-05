<?php
include 'db.php';
session_start();
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $exercise = $_POST['exercise'];
    $duration = $_POST['duration'];

    $conn->query("INSERT INTO workouts (user_id, date, exercise, duration) VALUES ($user_id, '$date', '$exercise', $duration)");
    echo "Workout logged!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Log Workout</title>
    <link rel="stylesheet" href="css/style.css"type="text/css">
</head>


<body>
    <form method="POST">
        <h3>Log Workout</h3>
        <input type="date" name="date" required><br>
        <input type="text" name="exercise" placeholder="Exercise (e.g. Pushups)" required><br>
        <input type="number" name="duration" placeholder="Duration (minutes)" required><br>
        <button type="submit">Save Workout</button>
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
