<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flick Fusion - Home</title>
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <!-- Internal CSS Styles -->
    <style>
        /* General Styles */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
        }

        a {
            text-decoration: none;
            color: #f39c12;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #1f1f1f;
        }

        header .logo {
            font-size: 24px;
            font-weight: bold;
        }

        header nav ul {
            list-style: none;
            display: flex;
            gap: 15px;
        }

        header nav ul li {
            display: inline;
        }

        /* Hero Video */
        .hero-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensures video fits without distortion */
            z-index: -1; /* Keeps the video in the background */
        }

        /* Main Overlay */
        .main {
            position: relative; /* Allows content to overlay the video */
            z-index: 1; /* Ensures text and other content appear above the video */
            color: #fff; /* Keeps text visible */
            padding: 20px;
        }

        .main h1, .main h2, .main p {
            color: #fff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8); /* Adds contrast for readability */
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 100px 20px;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            z-index: 1; /* Content above video */
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            z-index: 1;
        }

        .hero .cta-buttons a {
            padding: 10px 20px;
            margin: 0 10px;
            font-size: 18px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            z-index: 1;
        }

        .hero .btn {
            background-color: #f39c12;
        }

        .hero .btn-secondary {
            background-color: #555;
        }

        /* Features Section */
        .features {
            padding: 50px 20px;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.6); /* Semi-transparent background */
            border-radius: 10px; /* Soft corners */
            margin: 20px auto;
            max-width: 800px;
        }

        .features h2 {
            margin-bottom: 30px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .feature-item {
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.1); /* Slightly transparent */
            border-radius: 5px;
        }

        /* Trending Section */
        .trending {
            padding: 50px 20px;
            background-color: rgba(0, 0, 0, 0.6); /* Semi-transparent background */
            border-radius: 10px;
            margin: 20px auto;
            max-width: 800px;
        }

        .trending h2 {
            margin-bottom: 30px;
            text-align: center;
        }

        .movie-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .movie-card {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.1); /* Slightly transparent */
            padding: 10px;
            border-radius: 5px;
        }

        .movie-card img {
            width: 100%;
            border-radius: 5px;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            margin-top: 20px;
        }

        footer a {
            color: #f39c12;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Flick Fusion</div>
        <nav>
            <ul>
                <li><a href="about.php">About</a></li>
                <li><a href="#">Search</a></li>
                <li><a href="collections.php">Collections</a></li> <!-- New link -->
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main class="main-overlay">
        <section class="hero">
            <h1>Welcome to Flick Fusion</h1>
            <p>Enjoy your personalized dashboard for managing collections!</p>
        </section>
    </main>
    <footer>
        <p>© 2024 Flick Fusion. All Rights Reserved.</p>
    </footer>
</body>
</html>
