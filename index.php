<?php
session_start();
// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: views/user-dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flick Fusion</title>
    <link rel="icon" href="assets/icons/favicon.ico" type="image/x-icon">
    
    <!-- Internal CSS Styles -->
    <style>
        /* General Styles */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            color: #f39c12;
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

        header nav ul li a:hover {
            color: #f39c12;
            background-color: rgba(243, 156, 18, 0.1);
        }

        /* Hero Video */
        .hero-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        /* Main Content Padding */
        .main-overlay {
            padding-top: 80px;
            position: relative;
            z-index: 1;
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 100px 20px;
            background-color: rgba(0, 0, 0, 0.6);
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            z-index: 1;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            z-index: 1;
        }

        .hero .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #f39c12;
            color: #fff;
            border-radius: 4px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            margin: 0 10px;
        }

        .hero .btn:hover {
            background-color: #e67e22;
        }

        .hero .btn-secondary {
            background-color: #555;
        }

        .hero .btn-secondary:hover {
            background-color: #666;
        }

        /* Features Section */
        .features {
            padding: 50px 20px;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.6);
            border-radius: 10px;
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
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        /* Trending Section */
        .trending {
            padding: 50px 20px;
            background-color: rgba(0, 0, 0, 0.6);
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
            background-color: rgba(255, 255, 255, 0.1);
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
        <a href="index.php" class="logo">Flick Fusion</a>
        <nav>
            <ul>
                <li><a href="views/user-dashboard.php">Explore</a></li>
                <li><a href="views/login.php">Login</a></li>
                <li><a href="views/register.php">Sign Up</a></li>
            </ul>
        </nav>
    </header>
    <main class="main-overlay">
        <!-- Hero Section -->
        <video autoplay muted loop class="hero-video">
            <source src="https://videos.pexels.com/video-files/7988642/7988642-uhd_2732_1440_25fps.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <section class="hero">
            <h1>Welcome to Flick Fusion</h1>
            <p>Connect with movie lovers, create collections, and share your cinematic journey!</p>
            <div class="cta-buttons">
                <a href="views/register.php" class="btn">Get Started</a>
                <a href="views/login.php" class="btn btn-secondary">Login</a>
            </div>
        </section>
    
        <!-- Features Section -->
        <section class="features">
            <h2>Features</h2>
            <div class="features-grid">
                <div class="feature-item">
                    <h3>Create Collections</h3>
                    <p>Organize your favorite movies into personalized collections.</p>
                </div>
                <div class="feature-item">
                    <h3>Share Reviews</h3>
                    <p>Share your thoughts and read reviews from other movie enthusiasts.</p>
                </div>
                <div class="feature-item">
                    <h3>Discover Movies</h3>
                    <p>Explore new films and get personalized recommendations.</p>
                </div>
                <div class="feature-item">
                    <h3>Connect with Others</h3>
                    <p>Join a community of passionate movie lovers.</p>
                </div>
            </div>
        </section>
    </main>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Film Fan Club. All Rights Reserved.</p>
        <p><a href="#">Privacy Policy</a> | <a href="#">Contact Us</a></p>
    </footer>
</body>
</html>
