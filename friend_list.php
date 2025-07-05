<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch accepted friends
$friends_sql = "
    SELECT u.id, u.name 
    FROM users u
    JOIN friends f ON (
        (f.user_id = $user_id AND f.friend_id = u.id)
        OR (f.friend_id = $user_id AND f.user_id = u.id)
    )
    WHERE f.status = 'accepted' AND u.id != $user_id
";
$friends = $conn->query($friends_sql);

// Fetch pending friend requests sent by this user
$sent_requests_sql = "
    SELECT u.id, u.name
    FROM users u
    JOIN friends f ON f.friend_id = u.id
    WHERE f.user_id = $user_id AND f.status = 'pending'
";
$sent_requests = $conn->query($sent_requests_sql);

// Fetch incoming friend requests
$incoming_requests_sql = "
    SELECT u.id, u.name
    FROM users u
    JOIN friends f ON f.user_id = u.id
    WHERE f.friend_id = $user_id AND f.status = 'pending'
";
$incoming_requests = $conn->query($incoming_requests_sql);

// Accept request handler
if (isset($_GET['accept_id'])) {
    $accept_id = (int)$_GET['accept_id'];
    $conn->query("UPDATE friends SET status='accepted' WHERE user_id=$accept_id AND friend_id=$user_id");
    header("Location: friend_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Friends</title>
    <style>
        body { font-family: Arial; padding: 20px; background-color: #f5f5f5; }
        h2 { color: #333; }
        ul { list-style: none; padding-left: 0; }
        li { padding: 10px; background: #fff; margin-bottom: 10px; border-radius: 5px; }
        .btn { padding: 6px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; }
        .btn:hover { background: #218838; }
    </style>
</head>
<body>

<h2>Accepted Friends</h2>
<ul>
<?php if ($friends->num_rows > 0): ?>
    <?php while ($row = $friends->fetch_assoc()): ?>
        <li>
            <?= htmlspecialchars($row['name']) ?>
            <a class="btn" href="chat.php?friend_id=<?= $row['id'] ?>">Chat</a>
        </li>
    <?php endwhile; ?>
<?php else: ?>
    <li>No accepted friends.</li>
<?php endif; ?>
</ul>

<h2>Sent Friend Requests</h2>
<ul>
<?php if ($sent_requests->num_rows > 0): ?>
    <?php while ($row = $sent_requests->fetch_assoc()): ?>
        <li><?= htmlspecialchars($row['name']) ?> (Pending)</li>
    <?php endwhile; ?>
<?php else: ?>
    <li>No pending sent requests.</li>
<?php endif; ?>
</ul>

<h2>Incoming Friend Requests</h2>
<ul>
<?php if ($incoming_requests->num_rows > 0): ?>
    <?php while ($row = $incoming_requests->fetch_assoc()): ?>
        <li>
            <?= htmlspecialchars($row['name']) ?>
            <a class="btn" href="friend_list.php?accept_id=<?= $row['id'] ?>">Accept</a>
        </li>
    <?php endwhile; ?>
<?php else: ?>
    <li>No incoming requests.</li>
<?php endif; ?>
</ul>

<p><a href="dashboard.php">← Back to Dashboard</a></p>

</body>
</html>
