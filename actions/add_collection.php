<?php
session_start();
require_once '../db/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $movie_id = intval($_POST['movie_id']);

    // Check if movie is already in collection
    $check_query = "SELECT * FROM Collections WHERE user_id = ? AND movie_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $user_id, $movie_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: ../views/user-dashboard.php?error=already_in_collection");
        exit();
    }

    // Add to collection
    $query = "INSERT INTO Collections (user_id, movie_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $movie_id);

    if ($stmt->execute()) {
        header("Location: ../views/user-dashboard.php?success=added_to_collection");
    } else {
        header("Location: ../views/user-dashboard.php?error=add_failed");
    }

    $stmt->close();
    $check_stmt->close();
    $conn->close();
} else {
    header("Location: ../views/user-dashboard.php");
}
exit();
