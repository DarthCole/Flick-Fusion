<?php
session_start();
// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: user-dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <title>Login - Flick Fusion</title>

    <style>
        /* General body styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: #fff;
            min-height: 100vh;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #333;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .navbar .logo {
            color: #f39c12;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-links li a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }

        .nav-links li a:hover, .nav-links li a.active {
            color: #f39c12;
            background-color: rgba(243, 156, 18, 0.1);
        }

        /* Login Form Container */
        .login-form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
            background-color: #1f1f1f;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            width: 300px;
            margin: 120px auto;
        }

        .login-form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #f39c12;
        }

        .login-form-container label {
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
            color: #fff;
        }

        .login-form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #333;
            background-color: #2a2a2a;
            color: #fff;
        }

        .login-form-container input:focus {
            outline: none;
            border-color: #f39c12;
        }

        .login-form-container button {
            width: 100%;
            padding: 12px;
            background-color: #f39c12;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-form-container button:hover {
            background-color: #e67e22;
        }

        .login-form-container p {
            text-align: center;
            margin-top: 15px;
        }

        .login-form-container a {
            color: #f39c12;
            text-decoration: none;
        }

        .login-form-container a:hover {
            text-decoration: underline;
        }

        /* Error message styling */
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            text-align: center;
            margin-top: 10px;
        }
    </style>
    
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <a href="../index.php" class="logo">Flick Fusion</a>
        <ul class="nav-links">
            <li><a href="../index.php">Home</a></li>
            <li><a href="login.php" class="active">Login</a></li>
            <li><a href="register.php">Sign Up</a></li>
        </ul>
    </nav>    

    <!-- Login Form -->
    <div class="login-form-container">
        <form id="login-form" method="POST" action="../actions/login.php">
            <h2>Welcome Back!</h2>
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
            <p>Don't have an account? <a href="register.php">Sign up here</a></p>
            <p id="error-message" class="error-message"></p>
        </form>
    </div>
    <script src="../js/login.js" defer></script>
</body>
</html>
