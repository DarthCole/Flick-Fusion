<?php
// Include your database connection file
include '../db/connect.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize user inputs
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Check if password and confirm password match
    if ($password !== $confirmPassword) {
        echo "Passwords do not match!";
        exit();  // Stop further processing if passwords don't match
    }

    // Hash the password using a strong algorithm
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if user already exists
    $checkUserQuery = "SELECT * FROM FFUsers WHERE username='$username' OR email='$email'";
    $result = mysqli_query($conn, $checkUserQuery);

    if (mysqli_num_rows($result) > 0) {
        echo "Username or Email already exists.";
        exit();  // Stop further processing if user exists
    }

    // Insert the new user into the database
    $query = "INSERT INTO FFUsers (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";
    
    if (mysqli_query($conn, $query)) {
        echo "Registration successful!";
        // Redirect to login page or send success response
        header("Location: login.html");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
