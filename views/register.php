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
    <title>Sign Up - Flick Fusion</title>

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
        header {
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

        header .logo {
            color: #f39c12;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            letter-spacing: 1px;
        }

        header nav ul {
            display: flex;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        header nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }

        header nav ul li a:hover, header nav ul li a.active {
            color: #f39c12;
            background-color: rgba(243, 156, 18, 0.1);
        }

        /* Sign-up page styling */
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 80px);
            padding: 100px 20px 20px;
        }

        .form-container {
            background-color: #1f1f1f;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            width: 350px;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #f39c12;
        }

        .form-container label {
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
            color: #fff;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #333;
            background-color: #2a2a2a;
            color: #fff;
        }

        .form-container input:focus {
            outline: none;
            border-color: #f39c12;
        }

        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #f39c12;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .form-container button:hover {
            background-color: #e67e22;
        }

        .form-container p {
            text-align: center;
            margin-top: 15px;
        }

        .form-container a {
            color: #f39c12;
            text-decoration: none;
        }

        .form-container a:hover {
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
    <header>
        <a href="../index.php" class="logo">Flick Fusion</a>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php" class="active">Sign Up</a></li>
            </ul>
        </nav>
    </header>

    <div class="main-container">
        <div class="form-container">
            <h2>Create Your Account</h2>
            <form id="signUpForm" method="POST" action="../actions/register.php">
                <label for="firstname">First Name</label>
                <input type="text" name="firstname" id="firstname" required>

                <label for="lastname">Last Name</label>
                <input type="text" name="lastname" id="lastname" required>

                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>

                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>

                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>

                <button type="submit">Create Account</button>
                <p>Already have an account? <a href="login.php">Login here</a></p>
                <div class="error-message" id="error-message"></div>
            </form>
        </div>
    </div>
    <script src="../js/signup.js" defer></script>
</body>
</html>
