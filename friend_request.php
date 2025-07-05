<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT users.id, users.name FROM friends 
        JOIN users ON friends.user_id = users.id 
        WHERE friends.friend_id = $user_id AND friends.status = 'pending'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Friend Requests</title>
    <link rel="stylesheet" href="css/style.css"type="text/css">
</head>
<body>
    <div class="container">
        <h2>Incoming Friend Requests</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="friend-request">
                    <p><strong><?php echo $row['name']; ?></strong> sent you a friend request.</p>
                    <form method="POST" action="accept_request.php">
                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="button">Accept</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No pending friend requests.</p>
        <?php endif; ?>

        <a class="button" href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
