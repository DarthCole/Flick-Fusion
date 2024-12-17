<?php
session_start();
require_once '../db/db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Check admin role
$user_id = $_SESSION['user_id'];
$admin_check_query = "SELECT role FROM FFUsers WHERE user_id = ?";
$stmt = $conn->prepare($admin_check_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['role'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if movie_id is provided
if (!isset($_POST['movie_id'])) {
    echo json_encode(['success' => false, 'message' => 'Movie ID not provided']);
    exit();
}

$movie_id = intval($_POST['movie_id']);

// Begin transaction
$conn->begin_transaction();

try {
    // Delete associated reviews first
    $delete_reviews = "DELETE FROM Reviews WHERE movie_id = ?";
    $stmt = $conn->prepare($delete_reviews);
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();

    // Delete from collections
    $delete_collections = "DELETE FROM CollectionMovies WHERE movie_id = ?";
    $stmt = $conn->prepare($delete_collections);
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();

    // Finally, delete the movie
    $delete_movie = "DELETE FROM Movies WHERE movie_id = ?";
    $stmt = $conn->prepare($delete_movie);
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Movie deleted successfully']);
    } else {
        throw new Exception('Movie not found');
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error deleting movie: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
