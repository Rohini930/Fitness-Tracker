<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT workout_type, duration, calories_burned, workout_date FROM workouts WHERE user_id = ? ORDER BY workout_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Workout History</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { margin-bottom: 20px; }
        ul { list-style-type: none; padding: 0; }
        li { margin-bottom: 10px; background: #f0f0f0; padding: 10px; border-radius: 5px; }
    </style>
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
</style>
</head>
<body>

<p style="color: white;">YOUR WORKOUT HISTORY</p>


<?php if ($result->num_rows > 0): ?>
    <ul>
    <?php while ($row = $result->fetch_assoc()): ?>
        <li>
            <?php echo htmlspecialchars($row['workout_type']); ?> - 
            <?php echo htmlspecialchars($row['duration']); ?> mins - 
            <?php echo htmlspecialchars($row['calories_burned']); ?> calories burned on 
            <?php echo htmlspecialchars($row['workout_date']); ?>
        </li>
    <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No workouts logged yet.</p>
<?php endif; ?>
<a href="dashboard.php">← Back to Dashboard</a>
</body>
</html>
