<?php
include '../db/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = intval($_POST['movie_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $release_year = intval($_POST['release_year']);
    $genre_id = intval($_POST['genre_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Handle poster upload
    $poster = null;
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $poster = file_get_contents($_FILES['poster']['tmp_name']);
        $query = "UPDATE Movies SET title = ?, release_year = ?, genre_id = ?, description = ?, poster = ? WHERE movie_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("siissi", $title, $release_year, $genre_id, $description, $poster, $movie_id);
    } else {
        $query = "UPDATE Movies SET title = ?, release_year = ?, genre_id = ?, description = ? WHERE movie_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("siisi", $title, $release_year, $genre_id, $description, $movie_id);
    }

    if ($stmt->execute()) {
        echo "Movie updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($conn);
}
?>
