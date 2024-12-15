<?php
include '../db/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $release_year = intval($_POST['release_year']);
    $genre_id = intval($_POST['genre_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Handle poster upload
    $poster = null;
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $poster = file_get_contents($_FILES['poster']['tmp_name']);
    }

    $query = "INSERT INTO Movies (title, release_year, genre_id, description, poster) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siiss", $title, $release_year, $genre_id, $description, $poster);

    if ($stmt->execute()) {
        echo "Movie added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($conn);
}
?>
