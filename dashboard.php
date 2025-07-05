<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "fitness_tracker_db", 3307);

// Get user data
$user_id = $_SESSION['user_id'];
$user_result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_result->fetch_assoc();

// Handle update info
if (isset($_POST['update_info'])) {
    $new_age = $_POST['age'];
    $new_height = $_POST['height'];
    $new_weight = $_POST['weight'];
    $conn->query("UPDATE users SET age = '$new_age', height = '$new_height', weight = '$new_weight' WHERE id = $user_id");
    header("Location: dashboard.php");
    exit();
}

// Handle profile picture upload
if (isset($_POST['upload_pic'])) {
    $target = "uploads/" . basename($_FILES['profile_pic']['name']);
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
        $conn->query("UPDATE users SET profile_pic = '$target' WHERE id = $user_id");
        header("Location: dashboard.php");
        exit();
    }
}

// Handle music upload
if (isset($_POST['upload_music'])) {
    $music_file = $_FILES['music_file'];
    $file_name = basename($music_file['name']);
    $upload_dir = "uploads/music/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_path = $upload_dir . time() . "_" . $file_name;

    if (move_uploaded_file($music_file['tmp_name'], $file_path)) {
        $conn->query("INSERT INTO user_music (user_id, file_name, file_path, uploaded_at) VALUES ($user_id, '$file_name', '$file_path', NOW())");
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<p style='color:red;'>Music upload failed.</p>";
    }
}

// Handle friend request
if (isset($_POST['friend_email'])) {
    $friend_email = $_POST['friend_email'];
    $friend_result = $conn->query("SELECT id FROM users WHERE email = '$friend_email'");
    if ($friend_result->num_rows > 0) {
        $friend = $friend_result->fetch_assoc();
        $friend_id = $friend['id'];
        $check = $conn->query("SELECT * FROM friends WHERE user_id=$user_id AND friend_id=$friend_id");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO friends (user_id, friend_id, status) VALUES ($user_id, $friend_id, 'pending')");
        }
    }
}

// Accept friend request
if (isset($_GET['accept'])) {
    $fid = $_GET['accept'];
    $conn->query("UPDATE friends SET status='accepted' WHERE user_id=$fid AND friend_id=$user_id");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial;
            color: white;
            padding: 20px;
            background-image: url('/fitness_tracker/uploads/bg/bg4.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .btn {
            padding: 10px 20px;
            margin: 5px;
            display: inline-block;
            border: none;
            color: white;
            cursor: pointer;
        }

        .green { background-color: #28a745; }
        .red { background-color: #dc3545; }
        .btn-small { padding: 6px 12px; font-size: 0.9em; }

        img.profile {
            width: 200px;
            height: 200px;
            border-radius: 50%;
        }

        input[type="number"], input[type="file"], input[type="email"] {
            padding: 6px;
            margin-bottom: 10px;
            width: 200px;
        }
    </style>
</head>
<body>

<h2>Welcome, <?= htmlspecialchars($user['name']) ?></h2>

<?php if ($user['profile_pic']): ?>
    <img src="<?= $user['profile_pic'] ?>" class="profile">
<?php endif; ?>

<!-- Profile Info -->
<div class="info-box">
    <h3>My Profile Info</h3>

    <div id="infoDisplay">
        <p>Age: <?= htmlspecialchars($user['age']) ?></p>
        <p>Weight: <?= htmlspecialchars($user['weight']) ?> kg</p>
        <p>Height: <?= htmlspecialchars($user['height']) ?> ft</p>
        <button onclick="toggleEdit()" class="btn green btn-small">Update Info</button>
    </div>

    <form method="POST" id="editForm" style="display: none;">
        <label>Age:</label><br>
        <input type="number" name="age" value="<?= $user['age'] ?>" required><br>

        <label>Weight (kg):</label><br>
        <input type="number" name="weight" value="<?= $user['weight'] ?>" required><br>

        <label>Height (feet):</label><br>
        <input type="number" name="height" step="0.01" value="<?= $user['height'] ?>" required><br><br>

        <button type="submit" name="update_info" class="btn green btn-small">Save</button>
    </form>
</div>

<script>
function toggleEdit() {
    document.getElementById('infoDisplay').style.display = 'none';
    document.getElementById('editForm').style.display = 'block';
}
</script>

<!-- Upload Profile Pic -->
<form method="post" enctype="multipart/form-data">
    <input type="file" name="profile_pic" required>
    <button class="btn green btn-small" type="submit" name="upload_pic">Upload Profile Picture</button>
</form>

<!-- Navigation -->
<a href="log_workout.php" class="btn green btn-small">Log Workout</a>
<a href="workout_history.php" class="btn green btn-small">Workout History</a>
<a href="monthly_report.php" class="btn green btn-small">Monthly Report</a>
<a href="logout.php" class="btn red btn-small">Logout</a>

<!-- Friends -->
<h3>Add Friend</h3>
<form method="post">
    <input type="email" name="friend_email" required placeholder="Friend's Email">
    <button class="btn green btn-small">Add Friend</button>
</form>

<h3>Friend Requests Sent</h3>
<ul>
<?php
$sent = $conn->query("SELECT u.name FROM friends f JOIN users u ON f.friend_id=u.id WHERE f.user_id=$user_id AND f.status='pending'");
while ($row = $sent->fetch_assoc()) {
    echo "<li>" . htmlspecialchars($row['name']) . "</li>";
}
?>
</ul>

<h3>Friend Requests Received</h3>
<ul>
<?php
$received = $conn->query("SELECT u.id, u.name FROM friends f JOIN users u ON f.user_id=u.id WHERE f.friend_id=$user_id AND f.status='pending'");
while ($row = $received->fetch_assoc()) {
    echo "<li>" . htmlspecialchars($row['name']) . " 
        <a href='dashboard.php?accept={$row['id']}' class='btn green btn-small'>Accept</a></li>";
}
?>
</ul>

<h3>Friends</h3>
<ul>
<?php
$friends = $conn->query("
    SELECT u.name, u.id FROM friends f 
    JOIN users u ON 
    (f.friend_id=u.id AND f.user_id=$user_id) OR 
    (f.user_id=u.id AND f.friend_id=$user_id)
    WHERE f.status='accepted' AND u.id != $user_id
");
while ($row = $friends->fetch_assoc()) {
    $friend_id = $row['id'];
    $friend_name = htmlspecialchars($row['name']);
    echo "<li>{$friend_name} 
            <a href='chat.php?friend_id={$friend_id}' class='btn green btn-small'>Chat</a>
          </li>";
}
?>
</ul>

<!-- Songs -->
<h2>My Songs</h2>
<?php
$song_result = $conn->query("SELECT * FROM user_music WHERE user_id = $user_id ORDER BY uploaded_at DESC");
?>

<?php if ($song_result && $song_result->num_rows > 0): ?>
<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
  <tr style="background-color: #444; color: #fff;">
    <th style="padding: 10px;">File Name</th>
    <th>Play</th>
    <th>Uploaded At</th>
    <th>Action</th>
  </tr>
  <?php while ($row = $song_result->fetch_assoc()): ?>
  <tr style="background-color: #222; color: #fff;">
    <td style="padding: 10px;"><?= htmlspecialchars($row['file_name']) ?></td>
    <td>
      <audio controls>
        <source src="<?= $row['file_path'] ?>" type="audio/mpeg">
        Your browser does not support the audio element.
      </audio>
    </td>
    <td><?= $row['uploaded_at'] ?></td>
    <td>
      <form method="POST" action="delete_song.php" onsubmit="return confirm('Delete this song?');">
        <input type="hidden" name="song_id" value="<?= $row['id'] ?>">
        <button type="submit" style="background-color: red; color: white;">Delete</button>
      </form>
    </td>
  </tr>
  <?php endwhile; ?>
</table>
<?php else: ?>
  <p>No songs uploaded yet.</p>
<?php endif; ?>

<h3>Upload Music</h3>
<form method="POST" enctype="multipart/form-data">
  <label>Select Audio File:
    <input type="file" name="music_file" accept=".mp3,.wav" required>
  </label><br><br>
  <button class="btn green btn-small" type="submit" name="upload_music">Upload</button>
</form>

</body>
</html>
