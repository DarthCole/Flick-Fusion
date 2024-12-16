<?php
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
            background-color: #f4f4f4;
        }

        header {
            background-color: #333;
            padding: 20px;
        }

        header .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        header nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: flex-end;
        }

        header nav ul li {
            margin-left: 20px;
        }

        header nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
        }

        /* Sign-up page styling */
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 60px); /* Adjust for header */
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container label {
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #555;
        }

        /* Error message styling */
        .error-message {
            color: red;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Flick Fusion</div>
        <nav>
            <ul>
                <li><a href="../index.html">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Sign Up</a></li>
            </ul>
        </nav>
    </header>

    <div class="main-container">
        <div class="form-container">
            <h2>Sign Up</h2>
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

                <button type="submit">Sign Up</button>
                <div class="error-message" id="error-message"></div>
            </form>
        </div>
    </div>
    <script src="../js/signup.js" defer></script>
</body>
</html>
