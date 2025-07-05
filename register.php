<?php
include 'db.php'; // your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $age      = $_POST['age'];
    $gender   = $_POST['gender'];
    $weight   = $_POST['weight'];
    $height   = $_POST['height'];

    // Basic SQL Insert
    $sql = "INSERT INTO users (name, email, password, age, gender, weight, height)
            VALUES ('$name', '$email', '$password', '$age', '$gender', '$weight', '$height')";

    if (mysqli_query($conn, $sql)) {
        echo "Registration successful.";
        header("Location: login.php"); // redirect after successful insert
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css"type="text/css" >
</head>
<style>
  body {
    background-image: url('/fitness_tracker/uploads/bg/bg1.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    height: 100vh;
    margin: 0;
  }
</style>


<body>
    <form method="POST" action="">
        <h2>Register</h2>
        <input type="text" name="name" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="number" name="weight" step="0.1" placeholder="weight(kg)"required><br>
        <input type="number" name="height" step="0.1"placeholder="height(feet)" required><br>

<label>Gender:</label>
<select name="gender" required>
  <option value="">Select</option>
  <option value="Male">Male</option>
  <option value="Female">Female</option>
  <option value="Other">Other</option>
</select><br>


<input type="number" name="age" placeholder="age"required><br>

        
        <button type="submit">Register</button>
        <!-- Add below Email & Password fields -->

    </form>
    <a href="login.php">Already have an account? Login</a>
</body>
</html>
