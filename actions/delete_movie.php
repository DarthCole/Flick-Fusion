<?php
include '../db/db_connect.php';

if (isset($_GET['movie_id'])) {
    $movie_id = intval($_GET['movie_id']);

    $query = "DELETE FROM Movies WHERE movie_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $movie_id);

    if ($stmt->execute()) {
        echo "Movie deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($conn);
}
?>
