<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <title>About - Flick Fusion</title>

    <style>
        /* General body styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: #fff;
            overflow-x: hidden;
        }

        /* Navigation Bar Styling */
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

        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .nav-links {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 15px;
        }

        .nav-links li {
            margin-left: 20px;
        }

        .nav-links li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
        }

        .nav-links li a:hover {
            color: #f39c12;
        }

        /* Hero Section Styling */
        .hero-section {
            position: relative;
            height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .hero-section video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        .hero-section::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        .hero-content {
            text-align: center;
            color: #fff;
            animation: fadeIn 2s ease-in-out;
        }

        .hero-content h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
        }

        .hero-content p {
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
        }

        .hero-content .cta {
            margin-top: 20px;
        }

        .hero-content .cta a {
            padding: 12px 25px;
            margin: 0 10px;
            background-color: #f39c12;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .hero-content .cta a:hover {
            background-color: #fff;
            color: #333;
        }

        .about-content {
        padding: 50px 20px;
        text-align: center;
        background-color: #1f1f1f;
        border-radius: 10px;
        max-width: 800px;
        margin: 30px auto;
        animation: slideIn 1s ease-in-out;
        }

        .about-content h2 {
        margin-bottom: 20px;
        color: #f39c12;
        font-size: 28px;
        }

        .about-content p {
        font-size: 16px;
        line-height: 1.6;
        color: #ccc;
        margin-bottom: 20px;
        }

        .about-content ul {
        text-align: left;
        margin: 0 auto;
        padding: 0;
        max-width: 600px;
        color: #ccc;
        list-style: disc;
        }

        .about-content ul li {
        margin: 10px 0;
        font-size: 16px;
        line-height: 1.6;
        }

        @keyframes slideIn {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
        }

        /* Footer Styling */
        footer {
            text-align: center;
            padding: 20px;
            background-color: #333;
            color: #fff;
            margin-top: 30px;
        }

        footer a {
            color: #f39c12;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.html" class="logo">Flick Fusion</a>
            <ul class="nav-links">
                <li><a href="../index.html">Home</a></li>
                <li><a href="#">Search</a></li>
                <li><a href="./login.php">Login</a></li>
                <li><a href="./register.php">Sign Up</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <video autoplay muted loop>
            <source src="https://videos.pexels.com/video-files/7233537/7233537-uhd_2560_1080_25fps.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="hero-content">
            <h1>About Flick Fusion</h1>
            <p>A vibrant online community for movie lovers to connect, share, and explore the magic of cinema.</p>
            <div class="cta">
                <a href="register.php">Join Now</a>
                <a href="search.html">Explore Movies</a>
            </div>
        </div>
    </section>

    <!-- About Content -->
    <section class="about-content">
        <h2>Our Story</h2>
        <p>
            Flick Fusion was born out of a shared passion for the art of cinema. What started as a simple idea to connect movie lovers
            has grown into a thriving community of film enthusiasts from all walks of life. At Flick Fusion, we believe that every movie
            has the power to inspire, entertain, and create connections between people who share a love for storytelling.
        </p>
        <h2>What Makes Us Unique</h2>
        <p>
            Unlike other platforms, Flick Fusion is not just a database for films—it's a space for you to express your thoughts, share your
            reviews, and engage in meaningful conversations about the movies that matter to you. Whether you're into blockbuster hits,
            indie films, or cult classics, there's something for everyone here.
        </p>
        <h2>Features That Inspire</h2>
        <p>
            Flick Fusion offers an array of features designed to enhance your movie experience:
        </p>
        <ul>
            <li><b>Create Personalized Collections:</b> Keep track of your favorite films, build themed playlists, and share them with the community.</li>
            <li><b>Rate & Review:</b> Share your opinions about movies and read reviews from others to discover hidden gems.</li>
            <li><b>Search & Discover:</b> Use advanced filters to find movies by genre, director, year, or even mood.</li>
            <li><b>Follow & Connect:</b> Follow other movie enthusiasts to explore their collections and engage in discussions.</li>
        </ul>
        <h2>Our Community</h2>
        <p>
            Flick Fusion is built on the idea of community. Our members come together to celebrate their love for cinema, share their
            unique perspectives, and inspire others to explore new films. Here, everyone has a voice, whether you're a seasoned cinephile
            or just discovering your passion for movies.
        </p>
        <h2>Why Join Flick Fusion?</h2>
        <p>
            Movies are better when shared. Flick Fusion is more than just a platform; it's your gateway to a world of endless cinematic
            adventures. From exclusive content to tailored recommendations, we offer tools to make your movie-watching journey
            unforgettable.
        </p>
        <p>
            So why wait? Join Flick Fusion today and be part of a global community that celebrates the magic of film!
        </p>
    </section>
    

    <!-- Footer -->
    <footer>
        <p>© 2024 Flick Fusion. All Rights Reserved.</p>
        <p><a href="#">Privacy Policy</a> | <a href="#">Contact Us</a></p>
    </footer>
</body>
</html>
