<?php
include '../db/db_connect.php';

header('Content-Type: application/json');

$query = "SELECT m.title, 
                 COUNT(r.review_id) AS review_count 
          FROM Movies m
          JOIN Reviews r ON m.movie_id = r.movie_id
          GROUP BY m.movie_id
          ORDER BY review_count DESC
          LIMIT 3";

$result = mysqli_query($conn, $query);

$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error fetching popular movies']);
}

mysqli_close($conn);
?>
