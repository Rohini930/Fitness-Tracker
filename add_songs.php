<?php
include('db.php');
$user_id = $_SESSION['user_id'];

$song_names = $_POST['song_name'];
$artists = $_POST['artist'];

for ($i = 0; $i < count($song_names); $i++) {
    $name = $song_names[$i];
    $artist = $artists[$i];

    $sql = "INSERT INTO songs (user_id, song_name, artist) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $name, $artist);
    $stmt->execute();
}

echo "Songs added successfully.";
?>
