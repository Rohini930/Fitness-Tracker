<!-- workout_list.php -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Workout List</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Workout List</h2>

        <div class="workout-card">
            <h3>Push-Ups</h3>
            <p>3 sets of 15 reps</p>
        </div>
        <div class="workout-card">
            <h3>Squats</h3>
            <p>3 sets of 20 reps</p>
        </div>
        <div class="workout-card">
            <h3>Plank</h3>
            <p>3 sets of 60 seconds</p>
        </div>
        <div class="workout-card">
            <h3>Burpees</h3>
            <p>3 sets of 10 reps</p>
        </div>
        <div class="workout-card">
            <h3>Jumping Jacks</h3>
            <p>3 sets of 30 seconds</p>
        </div>

        <div style="text-align:center;">
            <a class="button" href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
