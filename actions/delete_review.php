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

// Check if review_id is provided
if (!isset($_POST['review_id'])) {
    echo json_encode(['success' => false, 'message' => 'Review ID not provided']);
    exit();
}

$review_id = intval($_POST['review_id']);

// Delete review
$query = "DELETE FROM Reviews WHERE review_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $review_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Review deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Review not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting review: ' . $conn->error]);
}

$stmt->close();
$conn->close();
