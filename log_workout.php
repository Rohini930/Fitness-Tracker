<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['workout_type'];
    $duration = intval($_POST['duration']);
    $calories = intval($_POST['calories_burned']);
    $date = $_POST['workout_date'];

    $stmt = $conn->prepare("INSERT INTO workouts (user_id, workout_type, duration, calories_burned, workout_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isiis", $user_id, $type, $duration, $calories, $date);
    if ($stmt->execute()) {
        $msg = "Workout logged successfully!";
    } else {
        $msg = "Error logging workout.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Log Workout</title>
    <style>
    html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}
  body {
    background-image: url('/fitness_tracker/uploads/bg/bg4.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
  }
  body {
  color: white;
</style>
    <link rel="stylesheet" href="style.css"type="text/css">
</head>
<body>
    <div class="container">
        <h2>Log Workout</h2>
        <?php if ($msg) echo "<p>$msg</p>"; ?>
        <form method="POST">
    <label>Workout Type:</label>
    <input type="text" name="workout_type" required><br><br>

    <label>Duration (minutes):</label>
    <input type="number" name="duration" required><br><br>

    <label>Calories Burned:</label>
    <input type="number" name="calories_burned" required><br><br>

    <label>Date:</label>
    <input type="date" name="workout_date" required><br><br>

    <button type="submit">Log Workout</button>
</form>

        <br>
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</body>
</html>
