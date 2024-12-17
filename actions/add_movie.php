<?php
session_start();
require_once '../db/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
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
    header("Location: ../views/user-dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $release_year = intval($_POST['release_year']);
    $genre_id = intval($_POST['genre_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $error = null;

    // Validate input
    if (empty($title) || empty($release_year) || empty($genre_id)) {
        $error = "All fields are required";
    } elseif ($release_year < 1888 || $release_year > date("Y") + 5) {
        $error = "Invalid release year";
    }

    // Handle poster upload
    if (!isset($_FILES['poster']) || $_FILES['poster']['error'] !== UPLOAD_ERR_OK) {
        $error = "Movie poster is required";
    } else {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['poster']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $error = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        } elseif ($_FILES['poster']['size'] > 5000000) { // 5MB limit
            $error = "File is too large. Maximum size is 5MB.";
        }
    }

    if ($error === null) {
        try {
            $poster = file_get_contents($_FILES['poster']['tmp_name']);
            
            $query = "INSERT INTO Movies (title, release_year, genre_id, description, poster, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("siiss", $title, $release_year, $genre_id, $description, $poster);

            if ($stmt->execute()) {
                header("Location: ../views/admin_movies.php?success=1");
                exit();
            } else {
                $error = "Error adding movie: " . $conn->error;
            }
        } catch (Exception $e) {
            $error = "Error processing movie: " . $e->getMessage();
        }
    }

    if ($error !== null) {
        header("Location: ../views/admin_movies.php?error=" . urlencode($error));
        exit();
    }
} else {
    header("Location: ../views/admin_movies.php");
    exit();
}
