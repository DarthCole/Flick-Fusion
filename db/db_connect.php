<?php
// Database connection settings
$servername = "localhost"; // Typically 'localhost' for local development
$username = "root";        // Default XAMPP MySQL username
$password = "";            // Default XAMPP MySQL password
$dbname = "flick_fusion";  // Name of your database (fixed the space issue)

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Log error and stop execution
    error_log("Connection failed: " . $conn->connect_error); // Log error for debugging
    die("Database connection failed. Please try again later.");
}

// Uncomment the line below for debugging purposes only
// echo "Connection successful";
?>
