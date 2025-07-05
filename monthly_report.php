<?php
session_start();
include("db.php");

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view the report.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Get selected month and year or default to current
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Get month name
$monthName = date("F", mktime(0, 0, 0, $month, 1));

// Query to get total calories burned grouped by workout type
$sql = "
    SELECT workout_type, SUM(calories_burned) AS total_calories
    FROM workouts
    WHERE user_id = $user_id
      AND MONTH(workout_date) = $month
      AND YEAR(workout_date) = $year
    GROUP BY workout_type
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Monthly Workout Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            
            text-align: center;
            background-image: url('/fitness_tracker/uploads/bg/bg4.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    color: white;
        }
        h2 {
            margin-top: 20px;
        }
        form {
            margin: 20px auto;
        }
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 60%;
            background-color: black;
        }
        th, td {
            border: 1px solid #999;
            padding: 12px;
        }
        th {
            background-color: black;
        }
    </style>
</head>
<body>

<h2>Workout Summary for <?php echo "$monthName $year"; ?></h2>

<form method="GET">
    <label for="month">Month:</label>
    <select name="month" id="month">
        <?php
        for ($m = 1; $m <= 12; $m++) {
            $selected = ($m == $month) ? 'selected' : '';
            echo "<option value='$m' $selected>" . date("F", mktime(0, 0, 0, $m, 1)) . "</option>";
        }
        ?>
    </select>

    <label for="year">Year:</label>
    <select name="year" id="year">
        <?php
        $currentYear = date('Y');
        for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
            $selected = ($y == $year) ? 'selected' : '';
            echo "<option value='$y' $selected>$y</option>";
        }
        ?>
    </select>

    <input type="submit" value="Generate Report">
</form>

<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Workout Type</th>
                <th>Total Calories Burned</th>
            </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['workout_type']) . "</td>
                <td>" . htmlspecialchars($row['total_calories']) . "</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "<p>No workouts logged for $monthName $year.</p>";
}
?>
<a href="dashboard.php">← Back to Dashboard</a>
</body>
</html>
