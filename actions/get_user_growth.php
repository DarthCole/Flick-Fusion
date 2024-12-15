<?php
include '../db/connect.php';

header('Content-Type: application/json');

$query = "SELECT DATE(created_at) AS registration_date, COUNT(*) AS daily_count 
          FROM FFUsers 
          WHERE role = 2 AND created_at >= CURDATE() - INTERVAL 30 DAY 
          GROUP BY registration_date 
          ORDER BY registration_date";

$result = mysqli_query($conn, $query);

$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error fetching user growth']);
}

mysqli_close($conn);
?>
