<?php
session_start();
require_once '../db/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in to follow users']);
    exit();
}

// Get POST data
$follower_id = $_SESSION['user_id'];
$following_id = isset($_POST['following_id']) ? intval($_POST['following_id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Validate input
if ($following_id === 0 || empty($action)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Prevent following yourself
if ($follower_id === $following_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'You cannot follow yourself']);
    exit();
}

try {
    if ($action === 'follow') {
        // Check if already following
        $check_query = "SELECT * FROM Followers WHERE follower_id = ? AND following_id = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ii", $follower_id, $following_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'You are already following this user']);
            exit();
        }

        // Add follow relationship
        $query = "INSERT INTO Followers (follower_id, following_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $follower_id, $following_id);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Successfully followed user']);
    } 
    elseif ($action === 'unfollow') {
        // Remove follow relationship
        $query = "DELETE FROM Followers WHERE follower_id = ? AND following_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $follower_id, $following_id);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Successfully unfollowed user']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
    error_log($e->getMessage());
}
?>
