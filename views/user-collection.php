<?php
session_start();
require_once '../db/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user_id is provided
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    header("Location: explore-collections.php");
    exit();
}

$viewer_id = $_SESSION['user_id'];
$collection_owner_id = intval($_GET['user_id']);

// Get collection owner's info
$user_query = "SELECT username FROM FFUsers WHERE user_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $collection_owner_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    header("Location: explore-collections.php");
    exit();
}

// Get all movies in the collection with details
$movies_query = "
    SELECT m.*, g.name as genre_name,
           (SELECT AVG(rating) FROM Reviews WHERE movie_id = m.movie_id) as avg_rating,
           (SELECT COUNT(*) FROM Reviews WHERE movie_id = m.movie_id) as review_count
    FROM Collections c
    INNER JOIN Movies m ON c.movie_id = m.movie_id
    LEFT JOIN Genres g ON m.genre_id = g.genre_id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC";

$movies_stmt = $conn->prepare($movies_query);
$movies_stmt->bind_param("i", $collection_owner_id);
$movies_stmt->execute();
$movies_result = $movies_stmt->get_result();

// Get collection stats
$stats_query = "
    SELECT 
        COUNT(DISTINCT c.movie_id) as total_movies,
        COUNT(DISTINCT g.genre_id) as unique_genres,
        GROUP_CONCAT(DISTINCT g.name) as genres
    FROM Collections c
    INNER JOIN Movies m ON c.movie_id = m.movie_id
    LEFT JOIN Genres g ON m.genre_id = g.genre_id
    WHERE c.user_id = ?";

$stats_stmt = $conn->prepare($stats_query);
$stats_stmt->bind_param("i", $collection_owner_id);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Collection - Flick Fusion</title>
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

        .collection-header {
            background-color: #1a1a1a;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .collector-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .collector-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #e50914;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .collection-stats {
            display: flex;
            gap: 2rem;
            color: #999;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #fff;
        }

        .genres {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .genre-tag {
            background-color: #333;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .movie-card {
            background-color: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s;
        }

        .movie-card:hover {
            transform: translateY(-5px);
        }

        .movie-poster {
            position: relative;
            aspect-ratio: 2/3;
        }

        .movie-poster img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .movie-info {
            padding: 1rem;
        }

        .movie-title {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .movie-meta {
            display: flex;
            justify-content: space-between;
            color: #999;
            font-size: 0.9rem;
        }

        .rating {
            color: #ffd700;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="home.php" class="logo">Flick Fusion</a>
        <ul class="nav-links">
            <li><a href="user-dashboard.php">Explore Movies</a></li>
            <li><a href="collections.php">My Collection</a></li>
            <li><a href="explore-collections.php" class="active">Discover Collections</a></li>
            <li><a href="reviews.php">Reviews</a></li>
            <li><a href="../actions/logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>
        <div class="container">
            <div class="collection-header">
                <div class="collector-info">
                    <div class="collector-avatar">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                    <div>
                        <h1><?php echo htmlspecialchars($user['username']); ?>'s Collection</h1>
                        <div class="collection-stats">
                            <div class="stat-item">
                                <span class="stat-value"><?php echo $stats['total_movies']; ?></span>
                                <span>Movies</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value"><?php echo $stats['unique_genres']; ?></span>
                                <span>Genres</span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($stats['genres']): ?>
                    <div class="genres">
                        <?php foreach (explode(',', $stats['genres']) as $genre): ?>
                            <span class="genre-tag"><?php echo htmlspecialchars($genre); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="movies-grid">
                <?php while ($movie = $movies_result->fetch_assoc()): ?>
                    <div class="movie-card">
                        <a href="movie-details.php?id=<?php echo $movie['movie_id']; ?>">
                            <div class="movie-poster">
                                <?php if ($movie['poster']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($movie['poster']); ?>" 
                                         alt="<?php echo htmlspecialchars($movie['title']); ?>">
                                <?php else: ?>
                                    <img src="../assets/images/default-movie.jpg" 
                                         alt="Default Movie Poster">
                                <?php endif; ?>
                            </div>
                            <div class="movie-info">
                                <div class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></div>
                                <div class="movie-meta">
                                    <span><?php echo $movie['release_year']; ?></span>
                                    <span class="rating">
                                        <?php 
                                        $avg_rating = round($movie['avg_rating']);
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $avg_rating) {
                                                echo '<i class="fas fa-star"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>
</body>
</html>
