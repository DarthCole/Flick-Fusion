<?php
require '../db/db_connect.php';

header('Content-Type: application/json');

$query = "SELECT m.title, 
                 AVG(r.rating) AS average_rating, 
                 COUNT(r.review_id) AS review_count
          FROM Movies m
          JOIN Reviews r ON m.movie_id = r.movie_id
          WHERE r.rating = 5 AND r.created_at >= CURDATE() - INTERVAL 7 DAY
          GROUP BY m.movie_id
          ORDER BY average_rating DESC, review_count DESC
          LIMIT 1";

$result = mysqli_query($conn, $query);

$data = [];
if ($result && mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => 'No data available']);
}

mysqli_close($conn);
?>
