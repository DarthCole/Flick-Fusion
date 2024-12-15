<?php
// Include the database configuration file
require '../db/db_connect.php';

// Initialize variables for error and success messages
$error = '';
$success = '';

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize inputs
    $firstName = htmlspecialchars(trim($_POST['firstname']));
    $lastName = htmlspecialchars(trim($_POST['lastname']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Basic validation for empty fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        // Check database connection
        if ($conn->connect_error) {
            $error = "Unable to connect to the database. Please try again later.";
        } else {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT * FROM FFUsers WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = 'An account with this email already exists.';
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert the new user into the database
                $stmt = $conn->prepare("INSERT INTO FFUsers (first_name, last_name, username, email, password, role) VALUES (?, ?, ?, ?, ?, 2)");
                $username = $firstName . ' ' . $lastName; // Generate a username from first and last name
                $stmt->bind_param("sssss", $firstName, $lastName, $username, $email, $hashedPassword);

                if ($stmt->execute()) {
                    $success = 'Account created successfully!';
                    // Redirect to the login page
                    header("Location: ../views/login.php");
                    exit();
                } else {
                    $error = 'An error occurred while creating your account. Please try again.';
                }
            }
            // Close the statement
            $stmt->close();
        }
    }
    // Close the database connection
    $conn->close();
}
?>
