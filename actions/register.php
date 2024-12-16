<?php
// Include the database connection file
require '../db/db_connect.php';

// Initialize response array
$response = ['success' => false, 'message' => ''];

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize inputs
    $firstName = htmlspecialchars(trim($_POST['firstname']));
    $lastName = htmlspecialchars(trim($_POST['lastname']));
    $username = htmlspecialchars(trim($_POST['username']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Input validation
    if (empty($firstName) || empty($lastName) || empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $response['message'] = 'All fields are required.';
    } elseif (!preg_match("/^[a-zA-Z\s]{1,50}$/", $firstName)) {
        $response['message'] = 'Invalid first name.';
    } elseif (!preg_match("/^[a-zA-Z\s]{1,50}$/", $lastName)) {
        $response['message'] = 'Invalid last name.';
    } elseif (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
        $response['message'] = 'Invalid username.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format.';
    } elseif (strlen($password) < 8) {
        $response['message'] = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirmPassword) {
        $response['message'] = 'Passwords do not match.';
    } else {
        try {
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT * FROM FFUsers WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $response['message'] = 'An account with this username or email already exists.';
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert user into the database
                $stmt = $conn->prepare("INSERT INTO FFUsers (username, email, password, role) VALUES (?, ?, ?, 2)");
                $stmt->bind_param("sss", $username, $email, $hashedPassword);

                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Account created successfully! Redirecting...';
                } else {
                    $response['message'] = 'An error occurred while creating your account. Please try again.';
                }
            }
        } catch (Exception $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        } finally {
            // Close statement and connection
            if (isset($stmt)) $stmt->close();
            $conn->close();
        }
    }
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
