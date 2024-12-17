<?php
session_start();
require_once '../db/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

// Check if movie_id is provided
if (!isset($_POST['movie_id'])) {
    header("Location: ../views/collections.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$movie_id = $_POST['movie_id'];

// Delete the movie from user's collection
$stmt = $conn->prepare("DELETE FROM Collections WHERE user_id = ? AND movie_id = ?");
$stmt->bind_param("ii", $user_id, $movie_id);

if ($stmt->execute()) {
    header("Location: ../views/collections.php?removed=success");
} else {
    header("Location: ../views/collections.php?error=failed_to_remove");
}
exit();
