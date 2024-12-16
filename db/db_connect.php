<?php
// Database connection settings
$servername = "localhost";  // Typically 'localhost' on a local server
$username = "root";         // Default XAMPP MySQL username
$password = "";             // Default XAMPP MySQL password (empty by default)
$dbname = "flick fusion";   // Name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // If there's a connection error, output the error message
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connection successful";
}

// Close the connection after use (not required unless you want to close manually)
$conn->close();
?>
