<?php
session_start();
include('db.php');

$user_id = $_SESSION['user_id'];

// Fetch all songs added by this user
$sql = "SELECT * FROM songs WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Songs</title>
  <style>
    body {
      background: #121212;
      color: white;
      font-family: Arial, sans-serif;
      padding: 20px;
    }
    h2 {
      color: #4CAF50;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #444;
    }
    audio {
      width: 200px;
    }
  </style>
</head>
<body>

  <h2>All Songs You've Added</h2>

  <?php if ($result->num_rows > 0): ?>
    <table>
      <tr>
        <th>Song Name</th>
        <th>Artist</th>
        <th>Preview</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['song_name']) ?></td>
          <td><?= htmlspecialchars($row['artist']) ?></td>
          <td>
            <?php if (!empty($row['file_path'])): ?>
              <audio controls>
                <source src="<?= $row['file_path'] ?>" type="audio/mpeg">
                Your browser does not support the audio element.
              </audio>
            <?php else: ?>
              Not available
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p>No songs found.</p>
  <?php endif; ?>

</body>
</html>
