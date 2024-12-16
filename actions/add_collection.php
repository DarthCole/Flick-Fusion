<?php
require '../db/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $movie_id = intval($_POST['movie_id']);

    $query = "INSERT INTO Collections (user_id, movie_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $movie_id);

    if ($stmt->execute()) {
        echo "Collection added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($conn);
}
?>
