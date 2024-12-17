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
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['review_text']);

    // Validate input
    if ($rating < 1 || $rating > 5) {
        header("Location: ../views/movie-details.php?id=$movie_id&error=invalid_rating");
        exit();
    }

    if (empty($review_text)) {
        header("Location: ../views/movie-details.php?id=$movie_id&error=empty_review");
        exit();
    }

    // Check if user has already reviewed this movie
    $check_query = "SELECT * FROM Reviews WHERE user_id = ? AND movie_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $user_id, $movie_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        // Update existing review
        $update_query = "UPDATE Reviews SET rating = ?, review_text = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ? AND movie_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("isii", $rating, $review_text, $user_id, $movie_id);
        
        if ($update_stmt->execute()) {
            header("Location: ../views/movie-details.php?id=$movie_id&success=review_updated");
        } else {
            header("Location: ../views/movie-details.php?id=$movie_id&error=update_failed");
        }
        $update_stmt->close();
    } else {
        // Add new review
        $insert_query = "INSERT INTO Reviews (user_id, movie_id, rating, review_text) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iiis", $user_id, $movie_id, $rating, $review_text);
        
        if ($insert_stmt->execute()) {
            header("Location: ../views/movie-details.php?id=$movie_id&success=review_added");
        } else {
            header("Location: ../views/movie-details.php?id=$movie_id&error=add_failed");
        }
        $insert_stmt->close();
    }
    
    $check_stmt->close();
} else {
    header("Location: ../views/user-dashboard.php");
}

$conn->close();
exit();
