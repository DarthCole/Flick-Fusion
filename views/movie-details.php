<?php
session_start();
require_once '../db/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if movie_id is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: user-dashboard.php");
    exit();
}

$movie_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Get movie details
$movie_query = "
    SELECT m.*, g.name as genre_name,
           (SELECT AVG(rating) FROM Reviews WHERE movie_id = m.movie_id) as avg_rating,
           (SELECT COUNT(*) FROM Reviews WHERE movie_id = m.movie_id) as review_count
    FROM Movies m
    LEFT JOIN Genres g ON m.genre_id = g.genre_id
    WHERE m.movie_id = ?";

$stmt = $conn->prepare($movie_query);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();

if (!$movie) {
    header("Location: user-dashboard.php");
    exit();
}

// Check if movie is in user's collection
$collection_query = "SELECT * FROM Collections WHERE user_id = ? AND movie_id = ?";
$collection_stmt = $conn->prepare($collection_query);
$collection_stmt->bind_param("ii", $user_id, $movie_id);
$collection_stmt->execute();
$in_collection = $collection_stmt->get_result()->num_rows > 0;

// Get movie reviews
$reviews_query = "
    SELECT r.*, u.username
    FROM Reviews r
    INNER JOIN FFUsers u ON r.user_id = u.user_id
    WHERE r.movie_id = ?
    ORDER BY r.created_at DESC";

$reviews_stmt = $conn->prepare($reviews_query);
$reviews_stmt->bind_param("i", $movie_id);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <title><?php echo htmlspecialchars($movie['title']); ?> - Flick Fusion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
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

        .movie-details {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            background-color: #1a1a1a;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .movie-poster img {
            width: 100%;
            border-radius: 4px;
        }

        .movie-info h1 {
            margin: 0 0 1rem 0;
            color: #e50914;
        }

        .movie-meta {
            display: flex;
            gap: 2rem;
            margin-bottom: 1rem;
            color: #999;
        }

        .rating {
            color: #ffd700;
            margin-bottom: 1rem;
        }

        .description {
            color: #ccc;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #e50914;
            color: #fff;
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid #e50914;
            color: #e50914;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .reviews-section {
            margin-top: 2rem;
        }

        .review-form {
            background-color: #1a1a1a;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }

        .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border-radius: 4px;
            background-color: #333;
            border: 1px solid #444;
            color: #fff;
            resize: vertical;
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            cursor: pointer;
            color: #666;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #ffd700;
        }

        .reviews-list {
            display: grid;
            gap: 1rem;
        }

        .review-card {
            background-color: #1a1a1a;
            padding: 1.5rem;
            border-radius: 8px;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .review-content {
            color: #ccc;
            line-height: 1.6;
        }

        .review-footer {
            margin-top: 1rem;
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
            <div class="movie-details">
                <div class="movie-poster">
                    <?php if ($movie['poster']): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?> Poster">
                    <?php else: ?>
                        <img src="../assets/images/default-movie.jpg" alt="Default Movie Poster">
                    <?php endif; ?>
                </div>
                <div class="movie-info">
                    <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
                    <div class="movie-meta">
                        <span><?php echo $movie['release_year']; ?></span>
                        <span><?php echo htmlspecialchars($movie['genre_name']); ?></span>
                    </div>
                    <div class="rating">
                        <?php
                        $avg_rating = round($movie['avg_rating']);
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $avg_rating) {
                                echo '<i class="fas fa-star"></i>';
                            } else {
                                echo '<i class="far fa-star"></i>';
                            }
                        }
                        echo " (" . $movie['review_count'] . " reviews)";
                        ?>
                    </div>
                    <p class="description"><?php echo htmlspecialchars($movie['description']); ?></p>
                    <div class="action-buttons">
                        <?php if ($in_collection): ?>
                            <form action="../actions/remove-from-collection.php" method="POST">
                                <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
                                <button type="submit" class="btn btn-outline">
                                    <i class="fas fa-minus"></i> Remove from Collection
                                </button>
                            </form>
                        <?php else: ?>
                            <form action="../actions/add_collection.php" method="POST">
                                <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
                                <button type="submit" class="btn btn-outline">
                                    <i class="fas fa-plus"></i> Add to Collection
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="reviews-section">
                <h2>Reviews</h2>
                <div class="review-form">
                    <h3>Write a Review</h3>
                    <form action="../actions/add_review.php" method="POST">
                        <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
                        <div class="form-group">
                            <label>Rating:</label>
                            <div class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                                    <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="review">Your Review:</label>
                            <textarea name="review_text" id="review" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>

                <div class="reviews-list">
                    <?php while ($review = $reviews_result->fetch_assoc()): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="rating">
                                    <?php
                                    for ($i = 0; $i < 5; $i++) {
                                        if ($i < $review['rating']) {
                                            echo '<i class="fas fa-star"></i>';
                                        } else {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                                <span>By <?php echo htmlspecialchars($review['username']); ?></span>
                            </div>
                            <div class="review-content">
                                <?php echo htmlspecialchars($review['review_text']); ?>
                            </div>
                            <div class="review-footer">
                                <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
