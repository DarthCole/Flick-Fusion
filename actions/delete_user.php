<?php
include '../db/db_connect.php';

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    $query = "DELETE FROM FFUsers WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "User deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($conn);
}
?>
