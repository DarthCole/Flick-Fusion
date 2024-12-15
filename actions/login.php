<?php
session_start();
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Input sanitization and validation
    $usernameOrEmail = trim($_POST['username']);
    $password = $_POST['password'];

    // Regex patterns
    $emailPattern = "/^[\w\-\.]+@([\w-]+\.)+[\w-]{2,4}$/"; // Email format
    $usernamePattern = "/^[a-zA-Z0-9_]{3,30}$/";          // Username: 3-30 chars, alphanumeric + underscores

    // Validate username/email format
    if (!preg_match($emailPattern, $usernameOrEmail) && !preg_match($usernamePattern, $usernameOrEmail)) {
        echo "Invalid username or email format.";
        exit;
    }

    // Validate password length (at least 8 characters)
    if (strlen($password) < 8) {
        echo "Password must be at least 8 characters long.";
        exit;
    }

    // Secure SQL query with prepared statements
    $query = "SELECT * FROM FFUsers WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail); // Bind sanitized input
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store user info in session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 1) {
                header("Location: ../views/admin-dashboard.php"); // Admin dashboard
            } else {
                header("Location: ../views/user-dashboard.php"); // Regular user dashboard
            }
            exit;
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid username or email.";
    }

    // Close statement
    $stmt->close();
} else {
    echo "Invalid request method.";
}

// Close database connection
$conn->close();
?>
