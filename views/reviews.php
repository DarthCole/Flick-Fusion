<?php
session_start();
require_once '../db/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user's reviews and all reviews
$user_id = $_SESSION['user_id'];
$query = "
    SELECT r.*, m.title as movie_title, u.username 
    FROM Reviews r
    INNER JOIN Movies m ON r.movie_id = m.movie_id
    INNER JOIN FFUsers u ON r.user_id = u.user_id
    ORDER BY r.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <title>Movie Reviews - Flick Fusion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
            margin: 0;
            padding: 0;
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

        .logo {
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

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }

        .nav-links a:hover {
            color: #f39c12;
            background-color: rgba(243, 156, 18, 0.1);
        }

        .nav-links a.active {
            color: #f39c12;
            background-color: rgba(243, 156, 18, 0.1);
        }

        /* Main Content Padding */
        main {
            padding-top: 80px; /* Account for fixed navbar */
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .review-card {
            background-color: #1a1a1a;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .movie-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #e50914;
        }

        .rating {
            color: #ffd700;
        }

        .review-content {
            color: #ccc;
            margin: 1rem 0;
        }

        .review-footer {
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="logo">Flick Fusion</a>
        <ul class="nav-links">
            <li><a href="user-dashboard.php">Explore</a></li>
            <li><a href="collections.php">Collections</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="user-profile.php?id=<?php echo $_SESSION['user_id']; ?>">Profile</a></li>
                <li><a href="../actions/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <div class="container">
            <h1>Movie Reviews</h1>
            <div class="reviews-grid">
                <?php while($review = $result->fetch_assoc()): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="movie-title"><?php echo htmlspecialchars($review['movie_title']); ?></div>
                            <div class="rating">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $review['rating']) {
                                        echo '<i class="fas fa-star"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="review-content">
                            <?php echo htmlspecialchars($review['review_text']); ?>
                        </div>
                        <div class="review-footer">
                            <span>By <?php echo htmlspecialchars($review['username']); ?></span>
                            <span><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>
</body>
</html>
