<?php
require '../db/db_connect.php';

if (isset($_GET['collection_id'])) {
    $collection_id = intval($_GET['collection_id']);

    $query = "DELETE FROM Collections WHERE collection_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $collection_id);

    if ($stmt->execute()) {
        echo "Collection deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($conn);
}
?>
