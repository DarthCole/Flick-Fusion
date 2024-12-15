<?php
require_once("../actions/login.php");
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
            background-color: #f4f4f4;
        }

        .navbar {
            background-color: #333;
            padding: 20px;
        }

        .navbar .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }

        .navbar-container .nav-links {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: flex-end;
        }

        .nav-links li {
            margin-left: 20px;
        }

        .nav-links li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
        }

        /* Login Form Container */
        .login-form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 50px auto; /* Center form with a top margin */
        }

        .login-form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-form-container label {
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
        }

        .login-form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .login-form-container button {
            width: 100%;
            padding: 12px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .login-form-container button:hover {
            background-color: #555;
        }

        /* Error message styling */
        .error-message {
            color: red;
            font-size: 14px;
            text-align: center;
        }
    </style>
    
    <script src="../js/login.js" defer></script>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.html" class="logo">Flick Fusion</a>
            <ul class="nav-links">
                <li><a href="../index.html">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="register.php">Sign Up</a></li>
            </ul>
        </div>
    </nav>    

    <!-- Login Form -->
    <div class="login-form-container">
        <form id="login-form" method="POST" action="actions/login.php">
            <h2>Login</h2>
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
</body>
</html>
