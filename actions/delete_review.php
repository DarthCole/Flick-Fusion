<?php
include '../db/db_connect.php';

// Delete review
if (isset($_GET['review_id'])) {
    $review_id = intval($_GET['review_id']);
    $query = "DELETE FROM Reviews WHERE review_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $review_id);

    if ($stmt->execute()) {
        header("Location: ../views/reviews.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
