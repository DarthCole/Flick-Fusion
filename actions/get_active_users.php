<?php
session_start();
require '../db/db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
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

$query = "SELECT u.username, 
                 COUNT(DISTINCT r.review_id) AS review_count, 
                 COUNT(DISTINCT c.collection_id) AS collection_count, 
                 (COUNT(DISTINCT r.review_id) + COUNT(DISTINCT c.collection_id)) AS total_activity
          FROM FFUsers u
          LEFT JOIN Reviews r ON u.user_id = r.user_id
          LEFT JOIN Collections c ON u.user_id = c.user_id
          WHERE u.role = 2
          GROUP BY u.user_id
          ORDER BY total_activity DESC
          LIMIT 5";

$result = mysqli_query($conn, $query);

$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error fetching active users']);
}

mysqli_close($conn);
?>
