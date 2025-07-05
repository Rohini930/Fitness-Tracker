<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$friend_id = $_GET['friend_id'] ?? null;

if (!$friend_id) {
    echo "No friend selected.";
    exit();
}

// Mark received messages as seen
$conn->query("UPDATE messages SET status='seen' WHERE receiver_id=$user_id AND sender_id=$friend_id");

// Handle message sending
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    if ($msg !== '') {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $friend_id, $msg);
        $stmt->execute();
    }
}

// Fetch chat history
$sql = "SELECT * FROM messages 
        WHERE (sender_id=$user_id AND receiver_id=$friend_id) 
           OR (sender_id=$friend_id AND receiver_id=$user_id)
        ORDER BY sent_at ASC";
$messages = $conn->query($sql);

// Get friend's name
$friend = $conn->query("SELECT name FROM users WHERE id=$friend_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat with <?= htmlspecialchars($friend['name']) ?></title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .chat-box { max-width: 600px; margin: auto; border: 1px solid #ccc; padding: 10px; }
        .msg { margin: 10px 0; }
        .sent { text-align: right; }
        .received { text-align: left; }
        .timestamp { font-size: 10px; color: gray; }
        .status { font-size: 10px; color: green; }
    </style>
    <style>
    
  body {
    background-image: url('/fitness_tracker/uploads/bg/bg8.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
  }
  
        body {
  color: white;
}
</style>

</head>
<body>
<div class="chat-box">
    <h2>Chat with <?= htmlspecialchars($friend['name']) ?></h2>

    <div>
        <?php while ($row = $messages->fetch_assoc()): ?>
            <div class="msg <?= $row['sender_id'] == $user_id ? 'sent' : 'received' ?>">
                <strong><?= $row['sender_id'] == $user_id ? 'You' : $friend['name'] ?>:</strong>
                <?= htmlspecialchars($row['message']) ?><br>
                <span class="timestamp"><?= $row['sent_at'] ?></span>
                <?php if ($row['sender_id'] == $user_id): ?>
                    <span class="status"> - <?= $row['status'] ?></span>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>

    <form method="POST">
        <textarea name="message" rows="3" style="width: 100%;" placeholder="Type your message here..."></textarea><br>
        <button type="submit">Send</button>
    </form>

    <p><a href="dashboard.php">← Back to Dashboard</a></p>
</div>
</body>
</html>
